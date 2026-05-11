#!/data/data/com.termux/files/usr/bin/bash

set -euo pipefail

#
# Stable PHP build for Termux + Apache + FPM
# Target: PHP 8.4.x
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
rm -f "php-$PHP_VERSION.tar.gz"

echo "========================================"
echo " Downloading PHP source"
echo "========================================"

curl -LO "https://www.php.net/distributions/php-$PHP_VERSION.tar.gz"

tar -xzf "php-$PHP_VERSION.tar.gz"

cd "$BUILD_DIR"

echo "========================================"
echo " Cleaning configure cache"
echo "========================================"

rm -rf autom4te.cache config.cache

echo "========================================"
echo " Patching Android incompatibilities"
echo "========================================"

#
# Disable getloadavg() on Android
#

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

echo "========================================"
echo " Configuring PHP"
echo "========================================"

./configure \
    --prefix="$PREFIX" \
    --with-config-file-path="$PREFIX/etc" \
    --with-config-file-scan-dir="$PREFIX/etc/php.d" \
    --with-apxs="$PREFIX/bin/apxs" \
    --enable-fpm \
    --enable-mbstring \
    --enable-session \
    --enable-filter \
    --enable-sockets \
    --with-openssl \
    --with-zlib \
    --with-curl \
    --with-mysqli \
    --with-pdo-mysql \
    --disable-cgi \
    --disable-phpdbg \
    --disable-opcache \
    --without-iconv \
    ac_cv_func_getloadavg=no \
    ac_cv_func_pthread_cancel=no \
    ac_cv_func_pthread_setcanceltype=no

echo "========================================"
echo " Building PHP"
echo "========================================"

make clean || true
make -j$(nproc)

echo "========================================"
echo " Installing PHP"
echo "========================================"

make install

echo "========================================"
echo " Installing php.ini"
echo "========================================"

mkdir -p "$PREFIX/etc/php.d"

cp php.ini-development "$PREFIX/etc/php.ini"

echo "========================================"
echo " Configuring Apache"
echo "========================================"

HTTPD_CONF="$PREFIX/etc/apache2/httpd.conf"

if ! grep -q "libphp.so" "$HTTPD_CONF"; then

cat <<EOF >> "$HTTPD_CONF"

LoadModule php_module libexec/apache2/libphp.so

<FilesMatch \.php$>
    SetHandler application/x-httpd-php
</FilesMatch>

DirectoryIndex index.php index.html

EOF

fi

echo "========================================"
echo " Configuring PHP-FPM"
echo "========================================"

mkdir -p "$PREFIX/var/run"
mkdir -p "$PREFIX/var/log"

cat <<EOF > "$PREFIX/etc/php-fpm.conf"
[global]
pid = $PREFIX/var/run/php-fpm.pid
error_log = $PREFIX/var/log/php-fpm.log

[www]
listen = 127.0.0.1:9000

user = nobody
group = nobody

pm = dynamic
pm.max_children = 4
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
EOF

echo "========================================"
echo " DONE"
echo "========================================"

echo
echo "PHP installed:"
echo "  $PREFIX/bin/php"
echo
echo "Apache module:"
echo "  $PREFIX/libexec/apache2/libphp.so"
echo
echo "PHP-FPM:"
echo "  $PREFIX/sbin/php-fpm"
echo
echo "Start Apache:"
echo "  apachectl start"
echo
echo "Start FPM:"
echo "  php-fpm"
echo
echo "Open browser:"
echo "  http://127.0.0.1:8080"
echo
echo "Test PHP:"
echo "  php -v"
