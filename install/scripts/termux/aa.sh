#!/data/data/com.termux/files/usr/bin/bash

#
# PHP 8.4.x Android/Termux compatibility patcher
#

set -e

PHP_DIR="${1:-$HOME/php-8.4.7}"

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
  ac_cv_func_getloadavg=no

echo
echo "========================================"
echo " Cleaning old files and compiling"
echo "========================================"

make clean
make -j\$(nproc)

echo
echo "========================================"
echo " DONE"
echo "========================================"

echo
echo "========================================"
echo " DONE"
echo "========================================"

