<?
require 'jsmin.php';
require_once('../../_var/global.php');
require_once('../../fbx_listfunctions.php');
$d = dir(SITE_DIR.'lib/obfuscation/_src/');

if(!isset($_GET['script'])) {
	while(false !== ($entry = $d->read())) {
		if(!ListFindNoCase('.,..,.DS_Store,.svn',$entry)) {
			echo '<a href="obfuscator.php?script='.$entry.'">'.$entry.'</a><br/>';
		}
	}
	echo '<p><a href="obfuscator.php?script=ALL"><b>ALL</b></a></b>';
	return;
} elseif($_GET['script']=='ALL') {
	while(false !== ($entry = $d->read())) {
		if(!ListFindNoCase('.,..,.DS_Store,.svn',$entry)) {
			minify($entry);
		}
	}
	echo 'COMPILED ALL';
} else {
	$valid = false;
	while(false !== ($entry = $d->read())) {
		if($_GET['script'] == $entry) {
			$valid = true;
			break;
		}
	}
	if($valid) {
		minify($_GET['script']);
	} else {
		echo 'FUCK OFF!';
	}
}

$d->close();

function minify($script) {
	$javapath = trim(shell_exec('which java'));
	$content = shell_exec($javapath.' -jar '.SITE_DIR.'lib/obfuscation/custom_rhino.jar -c '.SITE_DIR.'lib/obfuscation/_src/'.$script.' 2>&1');
	echo 'COMPILED '.$script.'<br/>';
	$content = JSMin::minify($content);
	echo 'MINIFIED '.$script.'<br/>';
	$gzip = gzencode($content,9);
	echo 'GZIPPED '.$script.'<br/>';

	file_put_contents(SITE_DIR.'scripts/js/'.$script,$content);
	chmod(SITE_DIR.'scripts/js/'.$script,0644);

	file_put_contents(SITE_DIR.'scripts/js/'.$script.'.gz',$content);
	chmod(SITE_DIR.'scripts/js/'.$script.'.gz',0644);
}
?>
