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

$error = '';

$db = new SQLite3('devel.db');

if (!$db) { $error = 'Chyba databáze.'; }

# XHR POST

# PHP POST

if (isset($_POST)){
	if (isset($_POST['error-code'])) {
		if (isset($_POST['error-delete'])) {
			$query = $db->exec("DELETE FROM error WHERE code = '" . $_POST['error-code'] . "';");
			! $query ? $error = "Odstranění selhalo." : $error = "Hotovo."; 
		}
		if (isset($_POST['error-save'])) {
			if (isset($_POST['error-label']) and isset($_POST['error-text'])) {
				$query = $db->exec("INSERT INTO error (code, label, text) VALUES ('"
					. $db->escapeString($_POST['error-code']) . "','"
					. $db->escapeString($_POST['error-label']) . "','"
					. $db->escapeString($_POST['error-text'])
					. "') ON CONFLICT (code) DO UPDATE SET label=excluded.label, text=excluded.text;");
				! $query ? $error = "Zápis selhal." : $error = "Hotovo."; 
			} else {
				$error = "Prázdný vstup."; 
			}
		}
	}
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

<?php 

if (!empty($error)) {
	echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">'. $error . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
}

?>

<h3>Chybové zprávy</h3>

<?php

if ($db) {

	$error_code = '';
	$error_label = '';
	$error_text = '';

	$query = $db->query("SELECT code,label,text FROM error limit 1;");

	while ($result = $query->fetchArray(SQLITE3_ASSOC)) {
		$error_code = $result['code'];
		$error_label = $result['label'];
		$error_text = $result['text'];
        }
	
}

?>

<form method="post" action="." enctype="multipart/form-data">
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
		<input class="form-control text-center" id="error-code" name="error-code" maxlength="3" type"text" value="<?php echo $error_code;?>" size="2" list="error-code-list">
		<datalist id="error-code-list">

<?php

if ($db) {

	$query = $db->query("SELECT code FROM error;");
	while ($result = $query->fetchArray(SQLITE3_ASSOC)) {
		echo '<option value="' . $result['code'] . '">';
        }
}

?>

		</datalist>

	</td>
	<td class="align-middle"><textarea class="form-control" id="error-label" name="error-label"><?php echo $error_label;?></textarea></td>
	<td class="align-middle"><textarea class="form-control" id="error-text" name="error-text"><?php echo $error_text;?></textarea></td>
	<td class="align-middle">
		<div class="row-3 mb-3">	
			<input type="submit" id="error-save" name="error-save" value="error-save" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="error_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-circle"viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>
		</div>
		<div class="row-3 mt-3">
			<input type="submit" id="error-delete" name="error-delete" value="1" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="error_on_delete()" width="24" height="24" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
		</div>
	</td>
	</tr>
</tbody>
</table>
</form>

<h3>Uživatelé</h3>

<?php

if ($db) {

	$user_code = '';
	$user_aleph = '';
	$user_email = '';

	$query = $db->query("SELECT code,aleph,email FROM user limit 1;");
	while ($result = $query->fetchArray(SQLITE3_ASSOC)) {
		$user_code = $result['code'];
		$user_aleph = $result['aleph'];
		$user_email = $result['email'];
        }
	
}

?>

<form method="post" action="." enctype="multipart/form-data">
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
		<input class="form-control text-center" id="user-code" name="user-code" maxlength="3" type"text" value="<?php echo $user_code;?>" size="2" list="user-list">
		<datalist id="user-list">

<?php

if ($db) {

	$query = $db->query("SELECT code FROM user;");
	while ($result = $query->fetchArray(SQLITE3_ASSOC)) {
		echo '<option value="' . $result['code'] . '">';
        }
}

?>

		</datalist>
	</td>
	<td class="align-middle"><input type="text" class="form-control" id="aleph" name="aleph" value="<?php echo $user_aleph;?>"></td>
	<td class="align-middle"><input type="email" class="form-control" id="email" name="email" value="<?php echo $user_email;?>"></td>
	<td class="align-middle">
		<div class="row-3 mb-3">
			<input type="submit" id="user-save" name="user-save" value="user-save" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="user_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-circle"viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>
		</div>
		<div class="row-3 mt-3">
			<input type="submit" id="user-delete" name="user-delete" value="user-delete" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="user_on_delete()" width="24" height="24" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
		</div>
	</td>
	</tr>
</tbody>
</table>
</form>

<h3>Recenze</h3>

<?php

if ($db) {

	$review_authority = '';
	$review_name = '';

	$query = $db->query("SELECT authority,name FROM review limit 1;");
	while ($result = $query->fetchArray(SQLITE3_ASSOC)) {
		$review_authority = $result['authority'];
		$review_name = $result['name'];
        }
	
}

?>

<form method="post" action="." enctype="multipart/form-data">
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
		<input class="form-control text-center" id="review-authority" name="review-authority" type"text" value="<?php echo $review_authority;?>" size="15" list="review-list">
		<datalist id="review-list">

<?php

if ($db) {

	$query = $db->query("SELECT authority FROM review;");
	while ($result = $query->fetchArray(SQLITE3_ASSOC)) {
		echo '<option value="' . $result['authority'] . '">';
        }
}

?>

		</datalist>
	</td>
	<td class="align-middle"><input type="text" class="form-control" id="review-name" size="40" name="review-name" value="<?php echo $review_name;?>"></td>
	<td class="align-middle">
		<div class="row-3 mb-3">
			<input type="submit" id="review-save" name="review-save" value="review-save" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="review_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-circle"viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>
		</div>
		<div class="row-3 mt-3">
			<input type="submit" id="review-delete" name="review-delete" value="review-delete" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="review_on_delete()" width="24" height="24" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
		</div>
	</td>
	</tr>
</tbody>
</table>
</form>

<h3>Kódy</h3>

<form method="post" action="." enctype="multipart/form-data">
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
	<td class="align-middle"><textarea class="form-control" id="coutry-code" name="country" rows="5">
<?php

if ($db) {

	$query = $db->query("SELECT code FROM country;");
	while ($result = $query->fetchArray(SQLITE3_ASSOC)) {
		echo $result['code'] . "\n";
        }
}

?>
</textarea></td>
	<td class="align-middle"><textarea class="form-control" id="language-code" name="language" rows="5">
<?php

if ($db) {

	$query = $db->query("SELECT code FROM language;");
	while ($result = $query->fetchArray(SQLITE3_ASSOC)) {
		echo $result['code'] . "\n";
        }
}

?>
</textarea></td>
	<td class="align-middle"><textarea class="form-control"id="role-code" name="role" rows="5">
<?php

if ($db) {

	$query = $db->query("SELECT code FROM role;");
	while ($result = $query->fetchArray(SQLITE3_ASSOC)) {
		echo $result['code'] . "\n";
        }
}

?>
</textarea></td>
	<td class="align-middle">
		<input type="submit" id="code-save" name="code-save" value="code-save" hidden>
		<svg xmlns="http://www.w3.org/2000/svg" onclick="code_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-circle"viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a7 7 0 1 0 14 0A7 7 0 0 0 1 8zm15 0A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>
	</td>
	</tr>
</tbody>
</table>
</form>

</div>
</div>
</main>

<?php

$db->close();

?>

<script src="../bootstrap.min.js"></script>
<script src="custom.js"></script>

</body>
</html>

