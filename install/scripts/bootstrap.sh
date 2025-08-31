#!/bin/bash
CWD=$(pwd | tr '/' ' ' | awk '{print $NF}')
DISTRO=$(cat /etc/os-release | grep '^ID')

if [[ "$CWD" != "scripts" ]]; then
	echo "must be ran from loa's script dir (/install/scripts)"
	exit 1
fi

if [[ "$USER" != "root" ]]; then
	echo "need root"
	exit 1
fi

function req_software() {
	if [[ "$DISTRO" == "debian" || "$DISTRO" == "ubuntu" ]]; then
		apt install -y make
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
	cowrite "BLUE" "Installing perl dependencies via CPAN"
	bash perldeps.sh
	check_rc;
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

sury
perldeps

