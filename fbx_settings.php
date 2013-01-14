<?
if(!isset($attributes['fuseaction'])) {
	$attributes['fuseaction'] = 'index.index';
}

if(!isset($GLOBALS['self'])) {
	$GLOBALS['self'] = 'index.php';
}

$Fusebox['layoutDir'] = 'layouts/';
$Fusebox['layoutFile'] = 'lay_default.php';
$XFA = array();

/***************** SITEWIDE CONFIG *********************/
require_once('_var/global.php');

if(SITE_DEBUGMODE) {
	error_reporting(E_ALL);
	ini_set('display_errors', 'on');
	require_once('lib/compiler.php');
}

/***************** CLASS AUTOLOADER ********************/
function __autoload($class) {
	require_once(SITE_DIR.'_classes/'.$class.'.php');
}

/***************** SITEWIDE OBJECTS ********************/
$mailObj = new class_mail(); // mail
$dbObj = new class_db(); // DAO
?>
