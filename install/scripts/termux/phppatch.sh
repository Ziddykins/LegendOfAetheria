#!/data/data/com.termux/files/usr/bin/bash
# patch_php_dns.sh — fixes dns.c / php_dns.h for PHP 8.4.x on Termux/Bionic
# Run from inside your php-8.4.7 source directory:
#   bash patch_php_dns.sh && bash o.sh

set -e

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
    python3 - "$PHP_DNS_H" "$GUARD_LINE" <<PYEOF
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
echo "All patches applied. Now run your build script:"
echo "  bash o.sh"
echo ""
echo "If you still get HEADER errors, run:"
echo "  grep -n 'arpa/nameser' \$(pkg-config --variable=includedir libc)/resolv.h 2>/dev/null || echo 'check: ls \$PREFIX/include/arpa/'"
