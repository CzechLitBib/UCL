
<?php

session_start();

$_SESSION['auth'] = False;

$authorized = False;

if (isset($_POST['name']) and isset($_POST['pass'])) {
	
	$ldap_dn = 'xxx';
	$ldap_conn = ldap_connect('xxx');

	ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);
	ldap_set_option($ldap_conn, LDAP_OPT_NETWORK_TIMEOUT, 10);

	$ldap_bind = @ldap_bind($ldap_conn, $ldap_dn, $_POST['pass']);

	if ($ldap_bind) { $authorized = True; }
}

?>

<html>
<head></head>
<body bgcolor="lightgrey">
<div align="center">
<table><tr><td><img src="sova.png"><td><td>UCL Vývoj </td></tr></table>
<form action='.' method="post">
<label>Login:</label><input size="4" type="text" name="name" required>
<label>Heslo:</label><input size="4" type="password" name="pass" required>
<input type="submit" value="Odeslat">
</form>

<?php

if (isset($_POST['name']) and isset($_POST['pass'])) {
	if (!$authorized) {
		echo '<font color="red">Přihlášení selhalo.</font>';
	} else {
		$_SESSION['auth'] = True;
		if (!isset($_SESSION['page'])) { $_SESSION['page'] = 'main.php'; }
		header('Location: ' . $_SESSION['page']);
		exit();
	}	
}

?>

</div>
</body>
</html>

