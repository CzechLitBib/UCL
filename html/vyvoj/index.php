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

if (isset($_POST['user']) and isset($_POST['pass'])) {
	
	$ldap_dn = 'xxx';
	$ldap_conn = ldap_connect('xxx');

	ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);
	ldap_set_option($ldap_conn, LDAP_OPT_NETWORK_TIMEOUT, 10);

	$ldap_bind = @ldap_bind($ldap_conn, $ldap_dn, $_POST['pass']);

	if ($ldap_bind) { $authorized = True; }
}
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Vývoj - ČLB</title>
	<link rel="stylesheet" type="text/css" href="/vyvoj/bootstrap.min.css"/>
	<link rel="stylesheet" type="text/css" href="/vyvoj/signin.css"/>
	<link rel="stylesheet" href="/vyvoj/bootstrap.min.js"/>
	<link rel="icon" href="favicon.ico">
</head>
<body class="text-center">
<main class="form-signin">
<form action="." method="post">
	<img class="mb-2" src="logo3.png" alt="Logo" width="209"/>
	<h2 class="h3 mb-3 fw-normal">Přihlašte se prosím</h2>

	<div class="form-floating">
		<input type="text" class="form-control" id="floatingInput" name="user" placeholder="uživatelské jméno" required autofocus>
		<label for="floatingInput">Uživatelské jméno</label>
	</div>
	<div class="form-floating">
		<input type="password" class="form-control" id="floatingPassword" name="pass" placeholder="heslo" required>
		<label for="floatingPassword">Heslo</label>
	</div>
	<button class="w-100 btn btn-lg btn-primary" type="submit">Přihlásit</button>
	<p class="mt-3 text-muted">&copy; 2021–<?php echo date("Y"); ?></p>
</form>

<?php

if (isset($_POST['user']) and isset($_POST['pass'])) {
	if (!$authorized) {
		echo '<div class="alert alert-danger" role="alert">Přihlášení selhalo.</div></div>';
	} else {
		$_SESSION['auth'] = True;

		if (in_array($_POST['name'], $admin)) { $_SESSION['group'] =  'admin'; }
		if (in_array($_POST['name'], $form)) { $_SESSION['group'] =  'form'; }
		if (in_array($_POST['name'], $solr)) { $_SESSION['group'] =  'solr'; }
		if (in_array($_POST['name'], $nkp)) { $_SESSION['group'] =  'nkp'; }

		if(empty($_SESSION['page'])) { $_SESSION['page'] = '/main/'; }// default page

		header('Location: ' . $_SESSION['page']);
		exit();
	}	
}

?>

</main>
</body>
</html>

