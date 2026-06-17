#!/bin/bash
CWD=$(pwd | tr '/' ' ' | awk '{print $NF}')
INSTALLDIR=$(pwd | sed 's/\/scripts/\/linux\//')
DISTRO=$(cat /etc/os-release | grep '^ID' | sed 's/ID=//' | head -n1)
PERLDEPS=$(find ../../.. -name 'perldeps.sh' | head -n1)
SURYSOURCES=$(find ../../.. -name 'sury_setup_deb.sh' | head -n1)
TEMPDIR="/tmp"

if [[ "$CWD" != "linux" ]]; then
	echo "must be ran from loa's scripts/linux dir (/install/scripts/linux)"
	exit 1
fi

if [[ "$USER" != "root" ]]; then
	echo "need root"
	exit 1
fi


function req_software() {
	echo "Found running $DISTRO"
	if [[ $DISTRO == "debian" || $DISTRO == "ubuntu" ]]; then
		apt install -y make gcc build-essential libterm-readkey-perl libconfig-inifiles-perl libfile-copy-recursive-perl
	else
		echo "nuh uh"
		exit 1
	fi
}

function cowrite() {
	COLOR="$1"
	MESSAGE="$2"
	OUTPUT=""

	if [[ "$COLOR" == "RED" ]]; then
		OUTPUT="\e[31m$MESSAGE\e[0m"
	elif [[ "$COLOR" == "GREEN" ]]; then
		OUTPUT="\e[32m$MESSAGE\e[0m"
	elif [[ "$COLOR" == "BLUE" ]]; then
		OUTPUT="\e[36m$MESSAGE\e[0m"
    elif [[ "$COLOR" == "YELLOW" ]]; then
        OUTPUT="\e[33m$MESSAGE\e[0m"
	else
		OUTPUT=$MESSAGE
	fi

	echo -e "$OUTPUT"
}

function sury() {
	cowrite "BLUE" "Running PHP Sury Repo setup..."
	
	if [[ "$DISTRO" == "debian" ]]; then
		bash sury_setup_deb.sh
	elif [[ "$DISTRO" == "ubuntu" ]]; then
		bash sury_setup_ubnt.sh
	fi
	
	check_rc;
}

function perldeps() {
	if [[ "$DISTRO" == "debian" || "$DISTRO" == "ubuntu" ]]; then
		cowrite "YELLOW" "Skipping perl deps as we've installed with apt"
		return 0
	fi

	cowrite "BLUE" "Installing perl dependencies via CPAN"
	bash perldeps.sh
	check_rc;
}

function do_config() {
    cd ../..

    if [[ -e 'config.ini.default' ]]; then
        cowrite "GREEN" "Found default config file"

        if [[ -e 'config.ini' ]]; then
            cowrite "YELLOW" "Also found config.ini though. If you already copied the"
            cowrite "YELLOW" "config.ini.default, answer no to this, otherwise it will"
            cowrite "YELLOW" "be overwritten with default values"

            echo -n "Copy default config file? [y/n]: "
            read ANSWER

            if [[ "$ANSWER" =~ [Yy][Ee]?[Ss]? ]]; then
                cp config.ini.default config.ini
            fi
        else
            cp config.ini.default config.ini
        fi
        
        cowrite "BLUE" "Configuration file initialized"
        cowrite "GREEN" "Bootstrap process complete, start AutoInstaller?"
        read ANSWER

        if [[ "$ANSWER" =~ [Yy][Ee]?[Ss]? ]]; then
            cowrite "BLUE" "Starting AutoInstaller, bye!"
            /usr/bin/env perl $INSTALLDIR/AutoInstaller.pl
        else
            cowrite "GREEN" "Please run autoinstaller when ready from the install directory"
        fi
    fi
}        

function check_rc() {
	if [[ $? -eq 0 ]]; then
		cowrite "GREEN" "Success"
	else
		cowrite "RED" "Error code $? - see above for details"
		cowrite "BLUE" "Continue? y/n: "
		echo -n "Choice: "
		read CHOICE

		if [[ "$CHOICE" == "n" || "$CHOICE" == "N" ]]; then
			exit 1;
		fi
	fi
}

function do_php_compile() {
	PHP_VERSION="8.5.6"
	SRC_DIR="$TEMPDIR/src"
	BUILD_DIR="$SRC_DIR/php-$PHP_VERSION"

	apt install -y \
		apache2 \
		autoconf \
		automake \
		bison \
		clang \
		libcurl4-openssl-dev \
		libcurl \
		libxml2 \
		make \
		openssl \
		pkg-config \
		re2c \
		sqlite3 \
		zlib1g-dev \
		libsqlite3-dev

	echo "========================================"
	echo " Preparing directories"
	echo "========================================"

	echo "making $SRC_DIR"
	mkdir -p "$SRC_DIR"
	cd "$SRC_DIR"

	echo "making $BUILD_DIR"
	mkdir -p "$BUILD_DIR"


	echo "========================================"
	echo " Downloading PHP source"
	echo "========================================"

	curl -Lo "${SRC_DIR}/php-${PHP_VERSION}.tar.gz" "https://www.php.net/distributions/php-${PHP_VERSION}.tar.gz" 
	tar -xzf "$SRC_DIR/php-${PHP_VERSION}.tar.gz"

	

	echo "========================================"
	echo " Cleaning configure cache"
	echo "========================================"
	rm -rf autom4te.cache config.cache

	echo "========================================"
	echo " Cleaning configure cache"
	echo "========================================"
	make all
	make install

	rm -rf "$BUILD_DIR"
	rm -f "php-$PHP_VERSION.tar.gz"
}

#req_software
#sury
#perldeps
#do_config
do_php_compile
