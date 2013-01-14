<?
define('EXPIREINSECONDS',3600 * 60);

switch($_SERVER['SERVER_NAME']) {
	case 'localhost':
		define('EMAIL_ADMINNAME','Administrator');
		define('EMAIL_ADMINEMAIL','chris+fubox@latko.org');
		define('EMAIL_ADMINRECEIVER','chris+fubox@latko.org');
		
		define('SITE_DIR','/Library/WebServer/Documents/www.example.com/');
		define('SITE_ROOT','http://localhost/');
		define('SITE_DEBUGMODE',true);

		define('MEDIA_DIR',SITE_DIR.'media/');

		define('DB_HOST','localhost');
		define('DB_DB','PM_dev_main');
		define('DB_USER','xxx');
		define('DB_PASSWORD','xxx');

		// for setting read-only dbs
		define('DB_R_HOST','localhost');
		define('DB_R_DB','PM_dev_main');
		define('DB_R_USER','xxx');
		define('DB_R_PASSWORD','xxx');
	break;
}
?>
