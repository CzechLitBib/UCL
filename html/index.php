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
	<link href="../custom.css" rel="stylesheet">
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

                if(empty($_SESSION['page'])) { $_SESSION['page'] = '/main/'; }// default page

                header('Location: ' . $_SESSION['page']);
                exit();
        }
}

?>

<svg width="120" fill="currentColor" class="bi bi-clb-logo my-4 ms-4 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 314.4 226.08"><path d="M232.76 10.3c-2.08 5.68-11.64 32-21.27 58.46-9.63 26.45-17.4 48.19-17.22 48.33.32.31 15.1 5.68 15.53 5.68.28 0 42.61-116.1 42.61-116.84 0-.32-14.6-5.9-15.52-5.93-.21 0-2.08 4.66-4.13 10.3zM296.76 17c-30.2 82.94-36.3 99.98-35.95 100.16 1.13.6 15.45 5.68 15.6 5.54.45-.46 38.23-104.88 37.99-105.1-.32-.31-14.93-5.6-15.42-5.6-.21 0-1.2 2.25-2.22 5zM192.93 28.5c-3.66 9.6-32.03 88.16-31.92 88.41.2.57 15.41 5.96 15.73 5.61.28-.28 32.46-88.51 32.46-88.97 0-.1-2.58-1.13-5.75-2.26-3.14-1.13-6.7-2.43-7.94-2.93l-2.22-.8zM259.19 29.03l-16.27 44.7c-8.64 23.74-15.59 43.29-15.45 43.43.39.35 15.6 5.71 15.74 5.57.24-.28 32.3-88.58 32.3-89 0-.22-2.32-1.27-5.18-2.3-2.89-1.02-6.45-2.33-7.93-2.89l-2.68-.99zM.25 36.9C.1 37.04 0 40.64 0 44.87v7.7h16.59v160.51H0l.07 6.42.1 6.45 46.93.11c55.8.1 59.58-.04 69.81-2.61a58.4 58.4 0 0 0 13.8-5.12c14.99-7.37 24.4-19.01 27.55-34 .91-4.3.91-14.71.03-19.58-.81-4.38-2.71-10.06-4.55-13.44-7.44-13.97-23.67-23.32-48.44-27.87-2.85-.53-3.2-.36 4.77-2.08 27.83-6.07 40.57-19.09 40.57-41.46 0-20.92-12.99-35.84-35.7-41.1-8.47-1.93-6.85-1.86-62.73-2-28.43-.08-51.82-.04-51.96.1zm83.08 16.65c12.38 2.97 20.56 10.4 23.63 21.6 1.03 3.8 1.27 11.25.5 15.16-2.47 12.1-11.05 20.82-24.13 24.41-2.44.67-3.85.78-13.55.92l-10.87.14V52.5l10.7.18c9.17.14 11.07.25 13.72.88zM76.94 133c23.81 2.36 39.97 20.1 38.63 42.47-1.02 17.22-11.08 30.02-27.62 35.28-5.44 1.7-8.43 2.05-19.05 2.22l-9.99.18V132.64h7.2c3.95 0 8.82.18 10.83.36z"/></svg>

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

