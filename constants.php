<?php
    define('MAIN_SITE_BASEURL', 'https://loa.kali.local');
    define('SYSTEM_EMAIL_ADDRESS', "webmaster@kali.local");
    define('WEBROOT', '/var/www/html/kali.local/loa');
    
	define('UPLOAD_DIRECTORY', WEBROOT . '/uploads');
    define('LOG_DIRECTORY', WEBROOT . '/system/logs');
	define('WHITELABEL_DIRECTORY', UPLOAD_DIRECTORY . '/whitelabel');
	define('TEMP_DIRECTORY', UPLOAD_DIRECTORY . '/temp');
	define('GENERATED_DIRECTORY', WEBROOT . '/img/generated');
	
	define('RELATIVE_UPLOAD_DIRECTORY', '/uploads');
	define('RELATIVE_WHITELABEL_DIRECTORY', '/uploads/whitelabel');
	define('RELATIVE_SYSTEM_DIRECTORY', '/system');

	
    define('PATH_ADMINROOT', WEBROOT . '/admini/strator');
    define('WEB_ADMINROOT', '/admini/strator');


	
	const MAX_STARTING_INVWEIGHT = 500;
	const MAX_STARTING_INVSLOTS = 30;
	const MAX_ASSIGNABLE_AP = 40;
	const REGEN_PER_TICK = 3;