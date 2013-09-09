<?
// I tell fusebox which layout to use, we only have "main" for
// this app so this variable is useless.
$attributes['layout']='main';


// I'm the big long switch statement that goes in every circuit
switch($Fusebox['fuseaction']) {
	case 'index':
	case 'php':
		include('act_index.php');
		include('dsp_index.php');
		break;

	default:
		header('location: /error.404');
		break;
}
?>
