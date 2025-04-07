<?php
    define('MAIN_SITE_BASEURL', 'https://loa.kali.local');
    define('APEX_DOMAIN', 'kali.local');
    define('SYSTEM_EMAIL_ADDRESS', "noreply@" . APEX_DOMAIN);
    define('WEBROOT', '/var/www/html/kali.local/loa');
    
	define('UPLOAD_DIRECTORY', WEBROOT . '/uploads');
    define('LOG_DIRECTORY', WEBROOT . '/system/logs');
	define('WHITELABEL_DIRECTORY', UPLOAD_DIRECTORY . '/whitelabel');
	define('TEMP_DIRECTORY', UPLOAD_DIRECTORY . '/temp');
	
	define('RELATIVE_UPLOAD_DIRECTORY', '/uploads');
	define('RELATIVE_WHITELABEL_DIRECTORY', '/uploads/whitelabel');
	define('RELATIVE_SYSTEM_DIRECTORY', '/system');
