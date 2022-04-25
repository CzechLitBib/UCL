<?php

session_start();

# logged in redirect
if (!empty($_SESSION['auth']) and isset($_SESSION['page'])) {
	header('Location: ' . $_SESSION['page']);
	exit();
}

$_SESSION['auth'] = False;
$_SESSION['group'] = 'user';
$_SESSION['username'] = '';

$admin = ['xxx'];
$nkp   = ['xxx'];
$form  = ['xxx'];
$solr  = ['xxx'];

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

<!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ČLB Vývoj</title>
	<link href="bootstrap.min.css" rel="stylesheet">
	<!-- Favicons -->
	<link rel="apple-touch-icon" href="favicon/apple-touch-icon.png" sizes="180x180">
	<link rel="icon" href="favicon/favicon-32x32.png" sizes="32x32" type="image/png">
	<link rel="icon" href="favicon/favicon-16x16.png" sizes="16x16" type="image/png">
	<link rel="mask-icon" href="favicon/safari-pinned-tab.svg" color="#7952b3">
	<!-- Custom styles -->
</head>

<body class="bg-light text-center">
<main class="w-100 m-auto" style="max-width:330px; padding-top:35px;">

<?php

if (isset($_POST['name']) and isset($_POST['pass'])) {
        if (!$authorized) {
		echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">Přihlášení selhalo.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
        } else {
                $_SESSION['auth'] = True;
                $_SESSION['username'] = $_POST['name'];

                if (in_array($_POST['name'], $admin)) { $_SESSION['group'] =  'admin'; }
                if (in_array($_POST['name'], $form)) { $_SESSION['group'] =  'form'; }
                if (in_array($_POST['name'], $solr)) { $_SESSION['group'] =  'solr'; }
                if (in_array($_POST['name'], $nkp)) { $_SESSION['group'] =  'nkp'; }

                if(empty($_SESSION['page'])) { $_SESSION['page'] = '/vyvoj/main/'; }// default page

                header('Location: ' . $_SESSION['page']);
                exit();
        }
}

?>

<img src="/vyvoj/logo.png" alt="ČLB logo" width="209"/>

<h2 class="my-4">Přihlašte se prosím</h2>

<form action="." method="post">
	<div class="form-floating mt-4 mb-1">
		<input type="text" class="form-control" id="user" name="name" required autofocus>
		<label for="user">Uživatelské jméno</label>
	</div>
	<div class="form-floating mb-4">
		<input type="password" class="form-control" id="secret" name="pass" required>
		<label for="pass">Heslo</label>
	</div>
	<button class="btn btn-lg btn-danger w-100" type="submit">Přihlásit</button>
</form>

<p class="my-4 text-muted">&copy; 2021–<?php echo date("Y"); ?></p>

</main>
<script src="bootstrap.min.js"></script>

</body>
</html>

