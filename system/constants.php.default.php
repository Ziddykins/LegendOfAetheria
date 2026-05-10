<?php
	define('MAIN_SITE_BASEURL', 'http://localhost');
	define('SYSTEM_EMAIL_ADDRESS', "your_email@example.com");
	define('WEBROOT', '/var/www/html');
    
	define('UPLOAD_DIRECTORY', WEBROOT . '/uploads');
	define('LOG_DIRECTORY', WEBROOT . '/system/logs');
	define('WHITELABEL_DIRECTORY', UPLOAD_DIRECTORY . '/whitelabel');
	define('TEMP_DIRECTORY', UPLOAD_DIRECTORY . '/temp');
	define('SYSTEM_DIRECTORY', WEBROOT . '/system');

	define('ADMIN_WEBROOT', WEBROOT . '/admini/strator');

	const VERIFICATION_CODE_LENGTH =   32;
	const STARTING_ASSIGNABLE_AP   =   40;
	const STARTING_INVWEIGHT       =  500;
	const STARTING_INVSLOTS        =   30;
	const STARTING_GOLD            = 1000;
	const REGEN_PER_TICK           =    3;
	
	
	
	
