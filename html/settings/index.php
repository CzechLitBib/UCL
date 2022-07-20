<?php
  
session_start();

$_SESSION['page'] = '/settings/';

if(empty($_SESSION['auth'])) {
        header('Location: /');
        exit();
}

if($_SESSION['username'] !== 'bruna') {
        $_SESSION['error'] = True;
        header('Location: /main/');
        exit();
}

?>

<!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ČLB Vývoj</title>
	<link href="../bootstrap.min.css" rel="stylesheet">
	<!-- Favicons -->
	<link rel="apple-touch-icon" href="../favicon/apple-touch-icon.png" sizes="180x180">
	<link rel="icon" href="../favicon/favicon-32x32.png" sizes="32x32" type="image/png">
	<link rel="icon" href="../favicon/favicon-16x16.png" sizes="16x16" type="image/png">
	<link rel="mask-icon" href="../favicon/safari-pinned-tab.svg" color="#7952b3">
	<!-- Custom styles -->
	<link href="../custom.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar container-fluid navbar-expand-md navbar-dark" style="background-color:#dc3545;">
	<div class="row align-items-center gx-0">
		<div class="col">
			<svg width="32" height="32" fill="currentColor" class="bi bi-clb-logo ms-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 314.4 226.08"><path d="M232.76 10.3c-2.08 5.68-11.64 32-21.27 58.46-9.63 26.45-17.4 48.19-17.22 48.33.32.31 15.1 5.68 15.53 5.68.28 0 42.61-116.1 42.61-116.84 0-.32-14.6-5.9-15.52-5.93-.21 0-2.08 4.66-4.13 10.3zM296.76 17c-30.2 82.94-36.3 99.98-35.95 100.16 1.13.6 15.45 5.68 15.6 5.54.45-.46 38.23-104.88 37.99-105.1-.32-.31-14.93-5.6-15.42-5.6-.21 0-1.2 2.25-2.22 5zM192.93 28.5c-3.66 9.6-32.03 88.16-31.92 88.41.2.57 15.41 5.96 15.73 5.61.28-.28 32.46-88.51 32.46-88.97 0-.1-2.58-1.13-5.75-2.26-3.14-1.13-6.7-2.43-7.94-2.93l-2.22-.8zM259.19 29.03l-16.27 44.7c-8.64 23.74-15.59 43.29-15.45 43.43.39.35 15.6 5.71 15.74 5.57.24-.28 32.3-88.58 32.3-89 0-.22-2.32-1.27-5.18-2.3-2.89-1.02-6.45-2.33-7.93-2.89l-2.68-.99zM.25 36.9C.1 37.04 0 40.64 0 44.87v7.7h16.59v160.51H0l.07 6.42.1 6.45 46.93.11c55.8.1 59.58-.04 69.81-2.61a58.4 58.4 0 0 0 13.8-5.12c14.99-7.37 24.4-19.01 27.55-34 .91-4.3.91-14.71.03-19.58-.81-4.38-2.71-10.06-4.55-13.44-7.44-13.97-23.67-23.32-48.44-27.87-2.85-.53-3.2-.36 4.77-2.08 27.83-6.07 40.57-19.09 40.57-41.46 0-20.92-12.99-35.84-35.7-41.1-8.47-1.93-6.85-1.86-62.73-2-28.43-.08-51.82-.04-51.96.1zm83.08 16.65c12.38 2.97 20.56 10.4 23.63 21.6 1.03 3.8 1.27 11.25.5 15.16-2.47 12.1-11.05 20.82-24.13 24.41-2.44.67-3.85.78-13.55.92l-10.87.14V52.5l10.7.18c9.17.14 11.07.25 13.72.88zM76.94 133c23.81 2.36 39.97 20.1 38.63 42.47-1.02 17.22-11.08 30.02-27.62 35.28-5.44 1.7-8.43 2.05-19.05 2.22l-9.99.18V132.64h7.2c3.95 0 8.82.18 10.83.36z"/></svg>
		</div>
		<div class="col"><a class="navbar-brand nav-link active" href="/main/">Vývoj # Nastavení</a></div>
	</div>
	<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<a href="/logout.php" class="btn btn-sm mx-1 ms-auto btn-outline-light">Odhlásit</a>
		<span class="mx-2 align-middle"><b><?php echo $_SESSION['username'];?></b></span>
		<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person-fill" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><svg/>
	</div>
</nav>

<main class="container">
<div class="row my-4 justify-content-center">
<div class="col col-md-8 m-2">

<h3>Chybové zprávy</h3>

	<table class="table my-4">
	<thead>
	<tr>
		<th scope="col">Kód</th>
		<th scope="col">Text</th>
		<th scope="col">Popis</th>
	</tr>
	</thead>
	<tbody>

	<tr>

<td class="align-middle">

<input class="form-control text-center" id="errors" maxlength="3" type"text" value="000" size="2" list="booo">
<datalist id="booo">

<?php

for ($i = 0; $i <= 200; $i++) {
	echo '<option value="' . str_pad($i, 3, '0', STR_PAD_LEFT) . '">';
}

?>
</datalist>

</td>
		<td class="align-middle"><textarea class="form-control" id="foo" name="bar">Chybný indikátor, v podpoli 'c'</textarea></td>
		<td class="align-middle"><textarea class="form-control" id="note" name="note">Pole 773 neobsahuje podpole 'g' a zároveň obsah podpole 't' není součástí podpole 'g' předchozího pole 773, nebo podpole 't' obsahuje hodnotu 'příloha' a podpole 'g' předchozího pole 773 obsahuje hodnotu 'příl.', nebo podpole 't' nemá hodnotu '[samizdat]'.</textarea></td>
		<td class="align-middle">

<div class="row-3 mb-3">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-circle"viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>
</div>

<div class="row-3 mt-3">

<svg xmlns="http://www.w3.org/2000/svg" style="min-width: 24px;" width="24" height="24" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
</div>

</td>
	</tr>

	</tbody>
	</table>

<h3>Uživatelé</h3>

<table class="table my-4">
	<thead>
	<tr>
		<th scope="col">Kód</th>
		<th scope="col">Aleph</th>
		<th scope="col">E-mail</th>
	</tr>
	</thead>
	<tbody>

	<tr>

<td class="align-middle">

<input class="form-control text-center" id="errors" maxlength="3" type"text" value="ag" size="2" list="fooo">
<datalist id="fooo">
<option value="ag">
<option value="dm">
<option value="dan">
<option value="gr">
<option value="nús">
<option value="jch">
<option value="js">
<option value="ls">
<option value="luv">
</datalist>

</td>
		<td class="align-middle"><input type="text" class="form-control" id="foo" name="name" value="UCLAG"></td>
		<td class="align-middle"><input type="email" class="form-control" id="note" name="note" value="tomas.okurka@email.cz"></td>
		<td class="align-middle">

<div class="row-3 mb-3">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-circle"viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>
</div>

<div class="row-3 mt-3">

<svg xmlns="http://www.w3.org/2000/svg" style="min-width: 24px;" width="24" height="24" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
</div>

</td>
	</tr>

	</tbody>
	</table>

<h3>Recenze</h3>

	<table class="table my-4">
	<thead>
	<tr>
		<th scope="col">Kód</th>
		<th scope="col">Jméno</th>
	</tr>
	</thead>
	<tbody>

	<tr>

<td class="align-middle">

<input class="form-control text-center" id="errors" type"text" value="jn334543" size="15" list="hooo">
<datalist id="hooo">
<option value="jn1231312">
<option value="aut23423432">
</datalist>

</td>
		<td class="align-middle"><input type="text" class="form-control" id="foo" size="40" name="name" value="Thomas, Cook"></td>
		<td class="align-middle">

<div class="row-3 mb-3">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-circle"viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>
</div>

<div class="row-3 mt-3">

<svg xmlns="http://www.w3.org/2000/svg" style="min-width: 24px;" width="24" height="24" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
</div>

</td>
	</tr>

	</tbody>
	</table>

<h3>Kódy</h3>

<table class="table my-4">
	<thead>
	<tr>
		<th scope="col">Země</th>
		<th scope="col">Jazyk</th>
		<th scope="col">Role</th>
	</tr>
	</thead>
	<tbody>

	<tr>

<td class="align-middle"><textarea class="form-control">en cz au</textarea></td>
<td class="align-middle"><textarea class="form-control">en cz au</textarea></td>
<td class="align-middle"><textarea class="form-control">en cz au</textarea></td>

<td class="align-middle">

<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-circle"viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>

</td>
	</tr>

	</tbody>
	</table>


</div>
</div>
</main>

<script src="../bootstrap.min.js"></script>

</body>
</html>
