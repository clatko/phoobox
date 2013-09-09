<?
/* CONFIG *********************************************************/
ini_set('max_execution_time',180000);
/******************************************************************/


/* INCLUDES *******************************************************/
require_once(SITE_DIR.'lib/obfuscation/jsmin.php');
require_once(SITE_DIR.'lib/css/class.csstidy.php');
/******************************************************************/


/* VARS ***********************************************************/
$ignore = '.,..,.DS_Store,.svn';
$js_ignore = $ignore.',ajax.6.js';
$js_src = SITE_DIR.'lib/obfuscation/_src/';
$js_dir = SITE_DIR.'scripts/js/';
$css_src = SITE_DIR.'lib/css/_src/';
$css_dir = SITE_DIR.'scripts/css/';

$cssObj = new csstidy();
$cssObj->set_cfg('timestamp',false);
$cssObj->set_cfg('merge_selectors',2);
$cssObj->set_cfg('optimise_shorthands',1);
$cssObj->set_cfg('compress_colors',true);
$cssObj->set_cfg('compress_font-weight',true);
$cssObj->set_cfg('compress_fw',true);
$cssObj->set_cfg('lowercase_s',false); // this needs to be true for CSS2.1 compliance
$cssObj->set_cfg('remove_bslash',true);
$cssObj->set_cfg('remove_last_;',true);
$cssObj->set_cfg('discard_invalid_properties',false);
$cssObj->load_template('highest_compression');
/******************************************************************/


// js compilation
$d = dir($js_src);
while($entry = $d->read()) {
	if(!ListFindNoCase($js_ignore,$entry)) {
		$src_time = filemtime($d->path.$entry);
		$js_time = (file_exists($js_dir.$entry)) ? filemtime($js_dir.$entry): '0';
		if($src_time>$js_time) {
			compileJS($js_src,$js_dir,$entry);
		}
	}
}

// css compilation (need to do url rewrite)
$d = dir($css_src);
while($entry = $d->read()) {
	if(!ListFindNoCase($ignore,$entry)) {
		$src_time = filemtime($d->path.$entry);
		$css_time = (file_exists($css_dir.$entry)) ? filemtime($css_dir.$entry): '0';
		if($src_time>$css_time) {
			compileCSS($cssObj,$css_src,$css_dir,$entry);
		}
	}
}





function compileJS($src_dir,$js_dir,$script) {
	$javapath = trim(shell_exec('which java'));
	$content = shell_exec('export DYLD_LIBRARY_PATH=""; '.$javapath.' -jar '.SITE_DIR.'lib/obfuscation/custom_rhino.jar -c '.$src_dir.$script.' 2>&1');
	$content = JSMin::minify($content);
	$gzip = gzencode($content,9);
	file_put_contents($js_dir.$script,$content);
	chmod($js_dir.$script,0644);
	file_put_contents($js_dir.$script.'.gz',$gzip);
	chmod($js_dir.$script.'.gz',0644);
}

function compileCSS($cssObj,$src_dir,$css_dir,$script) {
	$cssObj->parse_from_url($src_dir.$script);
	$content = $cssObj->print->plain();
	$gzip = gzencode($content,9);
	file_put_contents($css_dir.$script,$content);
	file_put_contents($css_dir.$script.'.gz',$gzip);
}
?>
