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

<img src="/vyvoj/logo.png" alt="ČLB logo" width="209"/>

<h2 class="my-4">Přihlašte se prosím</h2>

<form action="." method="post">
	<div class="form-floating mt-4 mb-1">
		<input type="text" class="form-control" id="user" name="user" required autofocus>
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

</body>
</html>

