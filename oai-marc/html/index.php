
<?php

session_start();

# logged in redirect
if (!empty($_SESSION['auth']) and isset($_SESSION['page'])) {
	header('Location: ' . $_SESSION['page']);
	exit();
}

$_SESSION['auth'] = False;
$_SESSION['group'] = 'user';

$admin = [];
$nkp   = [];
$form  = [];
$solr  = [];

if (!isset($_SESSION['error'])) { $_SESSION['error'] = False; }

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
<head><title>UCL Vývoj</title>
</head>
<body bgcolor="lightgrey">
<div align="center">
<table><tr><td><img src="sova.png"></td><td>UCL Vývoj</td></tr></table>
<form action='.' method="post">
<label>Login:</label><input size="10" id="name" type="text" name="name" required autofocus>
<label>Heslo:</label><input size="10" type="password" name="pass" required>
<input type="submit" value="Odeslat">
</form>

<?php

if (isset($_POST['name']) and isset($_POST['pass'])) {
	if (!$authorized) {
		echo '<font color="red">Přihlášení selhalo.</font>';
	} else {
		$_SESSION['auth'] = True;

		if (in_array($_POST['name'], $admin)) { $_SESSION['group'] =  'admin'; }
		if (in_array($_POST['name'], $form)) { $_SESSION['group'] =  'form'; }
		if (in_array($_POST['name'], $solr)) { $_SESSION['group'] =  'solr'; }
		if (in_array($_POST['name'], $nkp)) { $_SESSION['group'] =  'nkp'; }

		if(empty($_SESSION['page'])) { $_SESSION['page'] = 'main'; }// default page

		header('Location: ' . $_SESSION['page']);
		exit();
	}	
}

?>

</div>
</body>
</html>

