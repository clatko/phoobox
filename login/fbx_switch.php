<?
$attributes['layout']='login';
switch($Fusebox['fuseaction']) {
	case 'login':
		include('dsp_login.php');
		break;

	case 'validate':
		include('act_validate.php');
		break;

	case 'fail':
		include('dsp_fail.php');
		break;

	case 'logout':
		include('act_logout.php');
		break;

	default:
		$Fusebox['circuit'] = 'error';
		include('../error/dsp_404.php');
		break;
}
?>
