<?php
	define('MAIN_SITE_BASEURL', 'http://loa.dongues.local');
	define('SYSTEM_EMAIL_ADDRESS', "webmaster@dongues.local");
	define('WEBROOT', '/var/www/html/dongues.local/loa');
	
	define('UPLOAD_DIRECTORY', WEBROOT . '/uploads');
	define('LOG_DIRECTORY', WEBROOT . '/system/logs');
	define('WHITELABEL_DIRECTORY', UPLOAD_DIRECTORY . '/whitelabel');
	define('TEMP_DIRECTORY', UPLOAD_DIRECTORY . '/temp');
	
	define('RELATIVE_UPLOAD_DIRECTORY', '/uploads');
	define('RELATIVE_WHITELABEL_DIRECTORY', '/uploads/whitelabel');
	define('RELATIVE_SYSTEM_DIRECTORY', '/system');

	define('ADMIN_WEBROOT', '/var/www/html/dongues.local/admini/strator');
	
	const MAX_STARTING_INVWEIGHT = 500;
	const MAX_STARTING_INVSLOTS = 30;
	const MAX_ASSIGNABLE_AP = 40;
	const REGEN_PER_TICK = 3;
