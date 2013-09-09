<?
switch($Fusebox['fuseaction']) {
	case '400':
		include('dsp_400.php');
		break;

	case '401':
		include('dsp_401.php');
		break;

	case '403':
		include('dsp_403.php');
		break;

	case '404':
		include('dsp_404.php');
		break;

	case '500':
		include('dsp_500.php');
		break;


	default:
		header('location: /error.404');
		break;
}
?>
