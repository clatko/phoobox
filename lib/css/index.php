<?
require('class.csstidy.php');
require_once('../../_var/global.php');
require_once('../../fbx_listfunctions.php');
$d = dir(SITE_DIR.'lib/css/_src/');

$css = new csstidy();
$css->set_cfg('timestamp',false);
$css->set_cfg('merge_selectors',2);
$css->set_cfg('optimise_shorthands',1);
$css->set_cfg('compress_colors',true);
$css->set_cfg('compress_font-weight',true);
$css->set_cfg('compress_fw',true);
$css->set_cfg('lowercase_s',false); // this needs to be true for CSS2.1 compliance
$css->set_cfg('remove_bslash',true);
$css->set_cfg('remove_last_;',true);
$css->set_cfg('discard_invalid_properties',false);
$css->load_template('highest_compression');
//$css->load_template('high_compression');

while(false !== ($entry = $d->read())) {
	if(!ListFindNoCase('.,..,.DS_Store,.svn',$entry)) {
		$css->parse_from_url(SITE_DIR.'lib/css/_src/'.$entry);
		$content = $css->print->plain();
		file_put_contents(SITE_DIR.'scripts/css/'.$entry,$content);
		echo 'MINIFIED '.$entry.'<br/>';
	}
}

?>
