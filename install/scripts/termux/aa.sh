#!/data/data/com.termux/files/usr/bin/bash

#
# PHP 8.4.x Android/Termux compatibility patcher
#

PHP_VERSION="8.4.7"
PREFIX="${PREFIX:-/data/data/com.termux/files/usr}"
SRC_DIR="$HOME/src"
BUILD_DIR="$SRC_DIR/php-$PHP_VERSION"

echo "========================================"
echo " Updating Termux packages"
echo "========================================"

pkg update -y
pkg upgrade -y

echo "========================================"
echo " Installing dependencies"
echo "========================================"

pkg install -y \
    apache2 \
    autoconf \
    automake \
    bison \
    clang \
    curl \
    libcurl \
    libxml2 \
    make \
    openssl \
    openssl-tool \
    pkg-config \
    re2c \
    sqlite \
    libsqlite \
    zlib \
    libandroid-spawn

echo "========================================"
echo " Preparing directories"
echo "========================================"

mkdir -p "$SRC_DIR"
cd "$SRC_DIR"

rm -rf "$BUILD_DIR"

if [ ! -f "php-$PHP_VERSION.tar.gz" ]; then
	echo "========================================"
	echo " Downloading PHP source"
	echo "========================================"
	curl -LO "https://www.php.net/distributions/php-$PHP_VERSION.tar.gz"
else
	echo "PHP found already, re-download?"
	echo -n "y/n> "
	read CHOICE

	if [ "$CHOICE" == "y" ]; then
		rm -f "php-$PHP_VERSION.tar.gz"
		curl -LO "https://www.php.net/distributions/php-$PHP_VERSION.tar.gz"
	fi
fi

tar xzf php-$PHP_VERSION.tar.gz

PHP_DIR="${1:-$HOME/src/php-8.4.7}"

echo "Using source dir: $PHP_DIR"

cd "$PHP_DIR"

echo
echo "========================================"
echo " Patching pthread_cancel issues"
echo "========================================"

if [ -f sapi/phpdbg/phpdbg_watch.c ]; then
    sed -i '
        s/pthread_setcanceltype([^;]*);/#ifdef __ANDROID__\
\/\* disabled on android \*\/\
#else\
&\
#endif/g
    ' sapi/phpdbg/phpdbg_watch.c || true

    sed -i '
        s/pthread_cancel([^;]*);/#ifdef __ANDROID__\
\/\* disabled on android \*\/\
#else\
&\
#endif/g
    ' sapi/phpdbg/phpdbg_watch.c || true
fi

echo
echo "========================================"
echo " Patching memfd_create"
echo "========================================"

if ! grep -q "ANDROID_MEMFD_PATCH" ext/opcache/zend_shared_alloc.c; then
    sed -i '/#include <fcntl.h>/a\
\
#ifdef __ANDROID__\
#define ANDROID_MEMFD_PATCH\
#define memfd_create(name, flags) (-1)\
#endif\
' ext/opcache/zend_shared_alloc.c
fi

echo
echo "========================================"
echo " Patching getloadavg"
echo "========================================"

if ! grep -q "ANDROID_GETLOADAVG_PATCH" ext/standard/basic_functions.c; then
    sed -i '/#include "zend_smart_str.h"/a\
\
#ifdef __ANDROID__\
#define ANDROID_GETLOADAVG_PATCH\
int getloadavg(double loadavg[], int nelem) {\
    return -1;\
}\
#endif\
' ext/standard/basic_functions.c
fi

echo
echo "========================================"
echo " Patching backtrace"
echo "========================================"

find . -type f \( -name "*.c" -o -name "*.h" \) | while read -r file; do
    sed -i 's/#include <execinfo.h>/\/\/ execinfo disabled on android/g' "$file" || true
done

echo
echo "========================================"
echo " Patching clock_gettime checks"
echo "========================================"

export ac_cv_func_clock_gettime=yes

echo
echo "========================================"
echo " Patching SysV shm/semaphore"
echo "========================================"

export ac_cv_func_shmget=no
export ac_cv_func_shmat=no
export ac_cv_func_semget=no

BF="ext/standard/basic_functions.c"
[[ -f "$BF" ]] || { echo "ERROR: $BF not found. Run from php source root."; exit 1; }

[[ -f "${BF}.orig" ]] && cp "${BF}.orig" "$BF" && echo "Restored $BF from orig"
cp "$BF" "${BF}.orig"

python3 << PYEOF
import re

path = "ext/standard/basic_functions.c"
src  = open(path).read()

# Insert a getloadavg stub before the first #include so it's always available.
# Bionic doesn't have getloadavg; our stub always returns -1 which causes
# PHP's sys_getloadavg() to return false — safe and correct behaviour.
STUB = """\
/* ---- Termux/Bionic compat: patch_basic_functions.sh ---- */
#if defined(__ANDROID__) && !defined(HAVE_GETLOADAVG)
static inline int getloadavg(double loadavg[], int nelem) {
    (void)loadavg; (void)nelem;
    return -1; /* not available on Android; sys_getloadavg() returns false */
}
#endif
/* ---- end Termux/Bionic compat ---- */
"""

first_inc = src.find('#include')
if first_inc == -1:
    print("ERROR: no #include found"); raise SystemExit(1)

src = src[:first_inc] + STUB + '\n' + src[first_inc:]
open(path, 'w').write(src)
print("  → basic_functions.c patched OK")
PYEOF



echo
echo "=========================≈==="
echo "      Patch: Step 3 / 3"
echo "=========================≈==="
DNS_C="ext/standard/dns.c"
PHP_DNS_H="ext/standard/php_dns.h"

for f in "$DNS_C" "$PHP_DNS_H"; do
    [[ -f "$f" ]] || { echo "ERROR: $f not found. Run from php source root."; exit 1; }
done

# ── restore from originals if they exist ─────────────────────────────────────
echo "[1/3] Restoring originals if they exist..."
[[ -f "${DNS_C}.orig" ]]     && cp "${DNS_C}.orig"     "$DNS_C"     && echo "  → dns.c restored"
[[ -f "${PHP_DNS_H}.orig" ]] && cp "${PHP_DNS_H}.orig" "$PHP_DNS_H" && echo "  → php_dns.h restored"

echo "[2/3] Backing up clean originals..."
cp "$DNS_C"     "${DNS_C}.orig"
cp "$PHP_DNS_H" "${PHP_DNS_H}.orig"

# ─────────────────────────────────────────────────────────────────────────────
# STEP A: Completely replace php_dns.h with a version that works on Bionic.
#
# The original file conditionally defines the resolver backend based on what
# autoconf detected. On Bionic none of the reentrant symbols exist, so we
# replace the ENTIRE backend selection block with one that:
#   - uses res_search() (the non-reentrant form Bionic DOES have)
#   - uses h_errno instead of handle->res_h_errno
#   - is a no-op for free/close
# ─────────────────────────────────────────────────────────────────────────────
echo "[3/3] Rewriting php_dns.h and dns.c via Python..."

python3 << 'PYEOF'
import re, sys

# ══════════════════════════════════════════════════════════════════════════════
# php_dns.h — replace the macro backend block in-place
# ══════════════════════════════════════════════════════════════════════════════
path_h = "ext/standard/php_dns.h"
src_h  = open(path_h).read()

# The block we need to replace begins right after the #include guards and
# the DNS_LOCAL_BUFLEN define, and ends before the closing #endif of the file.
# Rather than brittle line-number hacks, we locate the first occurrence of
# the resolver-macro block by a reliable anchor string and replace everything
# from there to (but not including) the final #endif /*PHP_DNS_H*/.

# Anchor: the line that starts the backend selection
ANCHOR_RE = re.compile(
    r'(/\*.*?resolver.*?\*/\s*)?'           # optional comment
    r'#\s*if\s+defined\(HAVE_RES_NSEARCH\).*?'  # the #if chain
    r'(?=#\s*endif\s*/\*\s*PHP_DNS_H)',     # stop before closing guard
    re.DOTALL | re.IGNORECASE
)

# Fallback anchor if HAVE_RES_NSEARCH not present — look for first #define php_dns_search
ANCHOR_RE2 = re.compile(
    r'(#\s*define\s+php_dns_search\b.*?)'
    r'(?=#\s*endif\s*/\*\s*PHP_DNS_H)',
    re.DOTALL
)

REPLACEMENT = r"""/* ---- Termux/Bionic compat: rewritten by patch_php_dns_v3.sh ---- */
/*
 * Bionic libc does not expose the reentrant resolver API (_n variants).
 * We use the non-reentrant res_search() / global _res instead.
 * dns_get_record() / checkdnsrr() still compile and link; on Android they
 * call res_search() directly through the macros below.
 */
#ifndef BIND_8_COMPAT
# define BIND_8_COMPAT 1
#endif
#include <sys/types.h>
#include <netinet/in.h>
#include <arpa/nameser.h>
#include <arpa/nameser_compat.h>
#include <resolv.h>

/* res_state: Bionic doesn't typedef this, use struct __res_state* */
/* We pass NULL as the handle; macros below ignore it */
#define php_dns_search(res, dname, class, type, answer, anslen) \
            res_search((dname), (class), (type), (answer), (anslen))

#define php_dns_free_handle(res)    do { (void)(res); } while (0)

#define php_dns_errno(handle)       h_errno

/* dn_skipname: present in nameser_compat.h; declare extern if needed */
#ifndef dn_skipname
static inline int _php_dn_skipname(const unsigned char *ptr,
                                    const unsigned char *eom) {
    const unsigned char *p = ptr;
    int n;
    while (p < eom) {
        n = *p++;
        if (n == 0)              return (int)(p - ptr);
        if ((n & 0xc0) == 0xc0) { p++; return (int)(p - ptr); }
        p += (unsigned)n;
    }
    return -1;
}
# define dn_skipname(ptr, eom) _php_dn_skipname((const unsigned char*)(ptr), \
                                                 (const unsigned char*)(eom))
#endif

"""

replaced = False
m = ANCHOR_RE.search(src_h)
if m:
    src_h = src_h[:m.start()] + REPLACEMENT + src_h[m.end():]
    replaced = True
else:
    m = ANCHOR_RE2.search(src_h)
    if m:
        src_h = src_h[:m.start()] + REPLACEMENT + src_h[m.end():]
        replaced = True

if not replaced:
    # Last resort: just prepend our block after the opening #ifndef guard
    lines = src_h.splitlines(keepends=True)
    insert_at = 0
    for i, l in enumerate(lines):
        if l.strip().startswith('#define PHP_DNS_H') or l.strip().startswith('#pragma once'):
            insert_at = i + 1
            break
    lines.insert(insert_at, REPLACEMENT)
    src_h = ''.join(lines)
    print("  → php_dns.h: used fallback insertion (no HAVE_RES_NSEARCH block found)")

open(path_h, 'w').write(src_h)
print("  → php_dns.h rewritten OK")

# ══════════════════════════════════════════════════════════════════════════════
# dns.c — four targeted fixes
# ══════════════════════════════════════════════════════════════════════════════
path_c = "ext/standard/dns.c"
src_c  = open(path_c).read()

# ── Fix 1: add includes at top so HEADER / C_IN / dn_expand are visible ─────
INCLUDE_BLOCK = """\
/* ---- Termux/Bionic compat: added by patch_php_dns_v3.sh ---- */
#if defined(__ANDROID__)
# ifndef BIND_8_COMPAT
#  define BIND_8_COMPAT 1
# endif
# include <sys/types.h>
# include <netinet/in.h>
# include <arpa/nameser.h>
# include <arpa/nameser_compat.h>
# include <resolv.h>
#endif /* __ANDROID__ */
/* ---- end Termux/Bionic compat ---- */
"""
first_inc = src_c.find('#include')
if first_inc != -1:
    src_c = src_c[:first_inc] + INCLUDE_BLOCK + '\n' + src_c[first_inc:]
    print("  → dns.c: include block inserted")

# ── Fix 2: struct __res_state state; → replaced with NULL void* on Android ───
# The handle pointer is declared right after as:
#   struct __res_state *handle = &state;
# We replace the pair together so we don't create a dangling &state.
#
# Pattern A (two separate lines):  "struct __res_state state;\n...struct __res_state *handle = &state;"
# Pattern B (some versions use):   "struct __res_state state;"  alone, handle declared elsewhere

PAIR_RE = re.compile(
    r'struct\s+__res_state\s+state\s*;\s*\n'
    r'(\s*)struct\s+__res_state\s+\*handle\s*=\s*&state\s*;'
)
PAIR_REPL = (
    '#if defined(__ANDROID__)\n'
    r'\g<1>void *state = NULL;\n'
    r'\g<1>void *handle = NULL; /* Bionic: macros use global _res */\n'
    '#else\n'
    r'\g<1>struct __res_state state;\n'
    r'\g<1>struct __res_state *handle = &state;\n'
    '#endif'
)
src_c, n = PAIR_RE.subn(PAIR_REPL, src_c)
print(f"  → dns.c: replaced {n} (state+handle) pair(s)")

# Also fix any remaining lone "struct __res_state state;" lines
LONE_RE = re.compile(r'(\t*)struct\s+__res_state\s+state\s*;')
LONE_REPL = (
    '#if defined(__ANDROID__)\n'
    r'\g<1>void *state = NULL;\n'
    '#else\n'
    r'\g<1>struct __res_state state;\n'
    '#endif'
)
src_c, n2 = LONE_RE.subn(LONE_REPL, src_c)
if n2: print(f"  → dns.c: replaced {n2} lone struct __res_state line(s)")

# ── Fix 3: res_ninit(handle) / res_ninit(&state) → res_init() ───────────────
INIT_RE = re.compile(r'\bres_ninit\s*\(\s*(?:handle|&state)\s*\)')
def init_repl(m):
    return (
        '/* Bionic */ res_init() /* end Bionic */'
    )
src_c, n = INIT_RE.subn(init_repl, src_c)
print(f"  → dns.c: replaced {n} res_ninit() call(s)")

# ── Fix 4: res_ndestroy(handle) → nothing on Android ────────────────────────
DESTROY_RE = re.compile(r'\bres_ndestroy\s*\(\s*handle\s*\)\s*;')
src_c, n = DESTROY_RE.subn('/* res_ndestroy: no-op on Bionic (handled by macro) */', src_c)
if n: print(f"  → dns.c: neutered {n} res_ndestroy() call(s)")

open(path_c, 'w').write(src_c)
print("  → dns.c patched OK")
PYEOF

sed -i 's/#ifndef PHP_DNS_H/#ifndef PHP_DNS_H\n#define PHO_DNS_H\n#endif\n/' /data/data/com.termux/files/home/src/php-8.4.7/ext/standard/php_dns.h

# ── Quick compile check ───────────────────────────────────────────────────────
echo ""
echo "Checking that HEADER + C_IN + res_search are visible..."
cat > $TMPDIR/dns_check.c << 'EOF'
#ifndef BIND_8_COMPAT
# define BIND_8_COMPAT 1
#endif
#include <sys/types.h>
#include <netinet/in.h>
#include <arpa/nameser.h>
#include <arpa/nameser_compat.h>
#include <resolv.h>
int check(void) {
    HEADER h;
    unsigned char buf[512];
    int c = C_IN;
    int r = res_search("example.com", c, T_A, buf, sizeof(buf));
    (void)h; (void)r;
    return 0;
}
EOF
if cc -x c $TMPDIR/dns_check.c -c -o $TMPDIR/dns_check.o 2>$TMPDIR/dns_check_err.txt; then
    echo "  ✓ HEADER, C_IN, res_search all visible — good to build."
else
    echo "  ✗ Compile check failed. Errors:"
    cat $TMPDIR/dns_check_err.txt
    echo ""
    echo "  Dumping what resolv.h exports:"
    grep -E 'res_search|res_init|HEADER|__res_state' \
        "$PREFIX/include/resolv.h" 2>/dev/null | head -20 || true
fi
rm -f $TMPDIR/dns_check.c $TMPDIR/dns_check.o $TMPDIR/dns_check_err.txt

echo "  (dns_get_record/checkdnsrr gracefully use res_search on Android;"
echo "   apache, fpm, mysqli, curl, openssl are all unaffected)"

echo "Done."
DNS_C="ext/standard/dns.c"
PHP_DNS_H="ext/standard/php_dns.h"

# ── safety checks ────────────────────────────────────────────────────────────
[[ -f "$DNS_C" ]]    || { echo "ERROR: $DNS_C not found. Run from php source root."; exit 1; }
[[ -f "$PHP_DNS_H" ]] || { echo "ERROR: $PHP_DNS_H not found."; exit 1; }

echo "[1/3] Backing up originals..."
cp -n "$DNS_C"    "${DNS_C}.orig"
cp -n "$PHP_DNS_H" "${PHP_DNS_H}.orig"

# ── patch php_dns.h ──────────────────────────────────────────────────────────
# Replace the entire DNS-backend detection block with a stub that uses
# getaddrinfo only (already works on Bionic). The real dns_get_record()
# path still needs the resolver; we disable that path below.
echo "[2/3] Patching $PHP_DNS_H..."

# Check if already patched
if grep -q "TERMUX_BIONIC_PATCH" "$PHP_DNS_H"; then
    echo "  → already patched, skipping."
else
    # Prepend compat header block right after the first #ifndef guard line
    GUARD_LINE=$(grep -n "^#ifndef\|^#pragma once" "$PHP_DNS_H" | head -1 | cut -d: -f1)
    if [[ -z "$GUARD_LINE" ]]; then
        GUARD_LINE=1
    fi

    # Build the compat block
    COMPAT_BLOCK='/* TERMUX_BIONIC_PATCH: resolver compat for Android/Bionic */
#include <sys/types.h>
#include <netinet/in.h>
#include <arpa/nameser.h>
#include <resolv.h>

/* Bionic exposes res_init but not always the reentrant variants.
   Map the reentrant calls back to the thread-local global _res. */
#ifndef HEADER
typedef HEADER ns_msg_dummy_t; /* force arpa/nameser.h inclusion */
#endif

/* If HEADER is still missing (very old Bionic), define a minimal stand-in */
#ifndef T_A
# include <arpa/nameser_compat.h>
#endif
/* END TERMUX_BIONIC_PATCH */'

    # Use Python for safe multi-line insertion (always available in Termux)
    python - "$PHP_DNS_H" "$GUARD_LINE" <<PYEOF
import sys
path, line_no = sys.argv[1], int(sys.argv[2])
lines = open(path).readlines()
block = r"""$COMPAT_BLOCK
"""
lines.insert(line_no, block + "\n")
open(path, "w").writelines(lines)
PYEOF
    echo "  → done."
fi

# ── patch dns.c ──────────────────────────────────────────────────────────────
echo "[3/3] Patching $DNS_C..."

if grep -q "TERMUX_BIONIC_PATCH" "$DNS_C"; then
    echo "  → already patched, skipping."
else
    # 1. Add compat includes at the very top (after the first #ifdef PHP_WIN32 guard or after first #include)
    FIRST_INCLUDE=$(grep -n "^#include" "$DNS_C" | head -1 | cut -d: -f1)

    INCLUDE_BLOCK='/* TERMUX_BIONIC_PATCH */
#if defined(__ANDROID__)
# include <sys/types.h>
# include <netinet/in.h>

/* arpa/nameser.h on modern NDK is present but may need this guard */
# ifndef BIND_8_COMPAT
#  define BIND_8_COMPAT 1
# endif
# include <arpa/nameser.h>
# include <arpa/nameser_compat.h>
# include <resolv.h>

/* Bionic __res_state is an opaque forward decl in some NDK versions.
   Pull in the concrete definition via the private header if needed. */
# if !defined(_RESOLV_H_) && !defined(__res_state_defined)
   typedef struct __res_state* res_state;
# endif

/* Map non-reentrant names used in php dns.c to Bionic equivalents */
# ifndef C_IN
#  define C_IN  ns_c_in
# endif

static inline int _php_res_ninit(res_state s)  { return res_init();  }
static inline void _php_res_nclose(res_state s) { /* no-op on Bionic */ }
static inline int _php_res_nsearch(res_state s, const char *n, int c, int t,
                                   unsigned char *a, int al) {
    return res_search(n, c, t, a, al);
}
}

# undef  php_dns_search
# define php_dns_search(res,dname,class,type,answer,anslen) \
            _php_res_nsearch(res,dname,class,type,answer,anslen)
# undef  php_dns_free_handle
# define php_dns_free_handle(res) _php_res_nclose(res)

/* Stub out res_ninit so the configure-cache bypass still compiles */
# define res_ninit(s)   _php_res_ninit(s)
# define res_nclose(s)  _php_res_nclose(s)
# define res_nsearch    _php_res_nsearch

#endif /* __ANDROID__ */
/* END TERMUX_BIONIC_PATCH */'

    python3 - "$DNS_C" "$FIRST_INCLUDE" <<PYEOF
import sys
path, line_no = sys.argv[1], int(sys.argv[2])
lines = open(path).readlines()
block = r"""$INCLUDE_BLOCK
"""
lines.insert(line_no - 1, block + "\n")   # insert before first #include
open(path, "w").writelines(lines)
PYEOF

    # 2. Fix the two "struct __res_state state;" declarations → use res_state typedef
    #    Also fix the HEADER *hp declarations (HEADER is defined by arpa/nameser_compat.h,
    #    but if the compiler still chokes on the struct, replace with ns_msg equivalent)
    python3 - "$DNS_C" <<'PYEOF'
import sys, re

path = "ext/standard/dns.c"
src  = open(path).read()

# struct __res_state state  →  struct __res_state state (keep, but ensure resolv.h included)
# If the build still fails on this, replace with res_state pointer approach:
src = src.replace(
    "struct __res_state state;",
    "#if defined(__ANDROID__)\n\tstruct __res_state state; memset(&state,0,sizeof(state));\n#else\n\tstruct __res_state state;\n#endif"
)

# Deduplicate any double-memset that might arise from multiple replacements
# (crude but safe)
seen = {}
lines = src.splitlines(keepends=True)
out   = []
for l in lines:
    key = l.strip()
    if key == "struct __res_state state; memset(&state,0,sizeof(state));":
    	if key in seen:
            # replace subsequent with just the struct decl
            out.append(l.replace("; memset(&state,0,sizeof(state))", ""))
            continue
        seen[key] = True
    out.append(l)

open(path, "w").writelines(out)
print("  → dns.c struct patches applied.")
PYEOF

    echo "  → done."
fi

echo ""
echo "If you still get HEADER errors, run:"
echo "  grep -n 'arpa/nameser' \$(pkg-config --variable=includedir libc)/resolv.h 2>/dev/null || echo 'check: ls \$PREFIX/include/arpa/'"
echo
echo "========================================"
echo " Running ./configure script"
echo "========================================"

./configure \
  --prefix=\$PREFIX \
  --with-config-file-path=\$PREFIX/etc \
  --with-apxs=\$PREFIX/bin/apxs \
  --disable-phpdbg \
  --disable-opcache \
  --without-iconv \
  --enable-fpm \
  --enable-mbstring \
  --enable-bcmath \
  --enable-sockets \
  --with-openssl \
  --with-zlib \
  --with-curl \
  --with-mysqli \
  --with-pdo-mysql \
  --with-pdo-sqlite \
  --with-sqlite3 \
  ac_cv_func_pthread_cancel=no \
  ac_cv_func_pthread_setcanceltype=no \
  ac_cv_func_getloadavg=no \
  ac_cv_func_posix_spawn_file_actions_addchdir_np=no \
  ac_cv_func_posix_spawn_file_actions_addchdir=no


echo
echo "========================================"
echo " Cleaning old files and compiling"
echo "========================================"

make clean
make -j $(nproc)

echo
echo "========================================"
echo " DONE"
echo "========================================"

echo
echo "========================================"
echo " DONE"
echo "========================================"

