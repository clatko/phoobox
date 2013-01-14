<?
$email = (isset($_POST['email'])) ? $_POST['email']: '';
$password = (isset($_POST['password'])) ? $_POST['password']: '';

$success = (isset($_POST['redirect'])) ? $_POST['redirect']: '';

$authObj->login($email,$password,true,$success);
exit;
?>
