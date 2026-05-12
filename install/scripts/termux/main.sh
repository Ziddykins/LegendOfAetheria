#!/data/data/com.termux/files/usr/bin/bash



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
echo "======================"
echo "  Patch: Step 2 / 2"
echo "======================"

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

echo "Done."
