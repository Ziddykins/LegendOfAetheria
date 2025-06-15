<?php
	define('MAIN_SITE_BASEURL', 'https://loa.d0nguesV2.local');
	define('SYSTEM_EMAIL_ADDRESS', "webmaster@loa.d0nguesV2.local");
	define('WEBROOT', '/var/www/html/dongues.local/loa');
    
	define('UPLOAD_DIRECTORY', WEBROOT . '/uploads');
	define('LOG_DIRECTORY', WEBROOT . '/system/logs');
	define('WHITELABEL_DIRECTORY', UPLOAD_DIRECTORY . '/whitelabel');
	define('TEMP_DIRECTORY', UPLOAD_DIRECTORY . '/temp');
	define('SYSTEM_DIRECTORY', WEBROOT . '/system');
	
	define('ADMIN_WEBROOT', '/var/www/html/dongues.local/loa/admini/strator');

	# System constants
	const REGEN_PER_TICK = 3;
	const VERIFICATION_CODE_LENGTH = 15;

	# Game constants

	const STARTING_GOLD = 1000;
	const STARTING_INVWEIGHT = 500;
	const STARTING_INVSLOTS = 30;
	const STARTING_ASSIGNABLE_AP = 40;
