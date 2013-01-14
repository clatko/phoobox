<div id="login">
	<h1><a href="http://www.latko.org/">latko.org</a></h1>
	
	<?
	$error = $authObj->getValue('error');
	if($error!='') {
		$authObj->setValue('error','');
		echo '<div id="login_error"><strong>Error</strong>: '.$error.'</div>';
	}
	?>

	<form name="login_form" action="<?= SITE_ROOT; ?>login.validate" method="post" onsubmit="return login_validate(this);">
	<table width="270" cellpadding="0" cellspacing="0" border="0" class="main">
	<tr>
	<td width="70">Email</td>
	<td width="200"><input style="width:180px;" type="text" name="email" value="" maxlength="250" class="textbox" id="email_field"/></td>
	</tr>
	<tr>
	<td colspan="2"><img src="/images/0.gif" width="1" height="6" alt="" /></td>
	</tr>
	<tr>
	<td width="70">Password</td>
	<td width="200"><input style="width:180px;" type="password" name="password" value="" maxlength="250" class="textbox" /></td>
	</tr>
	<tr>
	<td colspan="2"><img src="/images/0.gif" width="1" height="6" alt="" /></td>
	</tr>
	<tr>
	<td width="70"></td>
	<td width="200"><input type="submit" name="submit" value="Sign In" class="button" style="width:80px;" /></td>
	</tr>
	</table>
	</form>
</div>
