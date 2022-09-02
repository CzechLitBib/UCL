<?php
  
session_start();

$_SESSION['page'] = '/settings/';

if(empty($_SESSION['auth'])) {
        header('Location: /');
        exit();
}

#if($_SESSION['group'] !== 'admin') {
if($_SESSION['username'] !== 'bruna') {
        $_SESSION['error'] = True;
        header('Location: /main/');
        exit();
}

$error = '';

$db = new SQLite3('devel.db');

if (!$db) { $error = 'Chyba čtení databáze.'; }

# XHR POST

if ($_SERVER["CONTENT_TYPE"] == 'application/json') {
	$req = json_decode(file_get_contents('php://input'), True);
	$resp = [];
	if ($req['data'] == 'export') {
		if ($req['type'] == 'error') {
			$query = $db->query("SELECT * FROM error;");
			if ($query) {
				while ($res = $query->fetchArray(SQLITE3_ASSOC)) {
					echo $res['code'] . ';' . $res['label'] . ';' . $res['text'] . "\n";
				}
			} else { echo 'DB error.'; }
		}
		if ($req['type'] == 'user') {
			$query = $db->query("SELECT * FROM user ORDER BY code;");
			if ($query) {
				while ($res = $query->fetchArray(SQLITE3_ASSOC)) {
					echo $res['code'] . ';' . $res['aleph'] . ';' . $res['email'] . "\n";
				}
			} else { echo 'DB error.'; }
		}
		if ($req['type'] == 'review') {
			$query = $db->query("SELECT * FROM review ORDER BY authority;");
			if ($query) {
				while ($res = $query->fetchArray(SQLITE3_ASSOC)) {
					echo $res['authority'] . ';' . $res['name'] . "\n";
				}
			} else { echo 'DB error.'; }
		}
		exit();
	}
	if ($req['data'] == 'list') {
		if ($req['type'] == 'error') {
			$query = $db->query("SELECT * FROM error;");
			if ($query) {
				// LIST
				echo '<div class="container"><div class="row"><div class="col">'
				. '<table class="table table-striped table-bordered">'
				. '<thead><tr><th>Kód</th><th>Text</th><th>Popis</th></tr></thead>'
				. '<tbody>';

				while ($res = $query->fetchArray(SQLITE3_ASSOC)) {
					echo '<tr><td class="text-center">' . $res['code'] . '</td>'
					. '<td>' . $res['label'] . '</td>'
					. '<td>' . $res['text'] . '</td></tr>';
				}

				echo '</tbody></table></div></div></div>';
				
			} else { echo 'DB error.'; }
		}
		if ($req['type'] == 'user') {
			$query = $db->query("SELECT * FROM user ORDER BY code;");
			if ($query) {
				echo '<div class="container"><div class="row"><div class="col">'
				. '<table class="table table-striped table-bordered">'
				. '<thead><tr><th>Kód</th><th>Aleph</th><th>E-mail</th></tr></thead>'
				. '<tbody>';

				while ($res = $query->fetchArray(SQLITE3_ASSOC)) {
					echo '<tr><td>' . $res['code'] . '</td>'
					. '<td>' . $res['aleph'] . '</td>'
					. '<td>' . $res['email'] . '</td></tr>';
				}

				echo '</tbody></table></div></div></div>';
				
			} else { echo 'DB error.'; }
		}
		if ($req['type'] == 'review') {
			$query = $db->query("SELECT * FROM review ORDER BY authority;");
			if ($query) {
				echo '<div class="container"><div class="row"><div class="col">'
				. '<table class="table table-striped table-bordered">'
				. '<thead><tr><th>Kód</th><th>Jméno</th></tr></thead>'
				. '<tbody>';

				while ($res = $query->fetchArray(SQLITE3_ASSOC)) {
					echo '<tr><td>' . $res['authority'] . '</td>'
					. '<td>' . $res['name'] . '</td></tr>';
				}

				echo '</tbody></table></div></div></div>';
				
			} else { echo 'DB error.'; }
		}
		exit();
	}

	if ($req['type'] == 'error') {
		$query = $db->querySingle("SELECT * FROM error WHERE code = '" . $req['data'] . "';", true);
		if ($query) {
			$resp['value'] = $query;
		}
	}
	if ($req['type'] == 'user') {
		$query = $db->querySingle("SELECT * FROM user WHERE code = '" . $req['data'] . "';", true);
		if ($query) {
			$resp['value'] = $query;
		}
	}
	if ($req['type'] == 'review') {
		$query = $db->querySingle("SELECT * FROM review WHERE authority = '" . $req['data'] . "';", true);
		if ($query) {
			$resp['value'] = $query;
		}
	}
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($resp);
	exit();
}

# PHP POST

if (isset($_POST)){
	if (!empty($_POST['error-code'])) {
		if (isset($_POST['error-delete'])) {
			$query = $db->exec("DELETE FROM error WHERE code = '" . $_POST['error-code'] . "';");
			if(!$query) {
				$error = "Odstranění chybového kódu " . $_POST['error-code'] . " selhalo.";
			} else {
				$error = "Chybový kód " . $_POST['error-code'] . " odstraněn."; 
			}
		}
		if (isset($_POST['error-save'])) {
			if (!empty($_POST['error-label']) and !empty($_POST['error-text'])) {
				$query = $db->exec("INSERT INTO error (code, label, text) VALUES ('"
					. $db->escapeString($_POST['error-code']) . "','"
					. $db->escapeString($_POST['error-label']) . "','"
					. $db->escapeString($_POST['error-text'])
					. "') ON CONFLICT (code) DO UPDATE SET label=excluded.label, text=excluded.text;");
				if (!$query) {
					$error = "Zápis chybového kódu " . $_POST['error-code'] . " selhal.";
				} else {
					$error = "Chybový kód " . $_POST['error-code'] . " uložen."; 
				}
			} else {
				$error = "Prázdý vstup chybové zprávy."; 
			}
		}
	}

	if (!empty($_POST['user-code'])) {
		if (isset($_POST['user-delete'])) {
			$query = $db->exec("DELETE FROM user WHERE code = '" . $_POST['user-code'] . "';");
			if(!$query) {
				 $error = "Odstranění uživatele " . $_POST['user-code'] . " selhalo.";
			} else {
				$error = "Uživatel " . $_POST['user-code'] . " odstraněn."; 
			}
		}
		if (isset($_POST['user-save'])) {
			if (!(empty($_POST['aleph']) and empty($_POST['email']))) {
				$query = $db->exec("INSERT INTO user (code, aleph, email) VALUES ('"
					. $db->escapeString($_POST['user-code']) . "','"
					. $db->escapeString($_POST['aleph']) . "','"
					. $db->escapeString($_POST['email'])
					. "') ON CONFLICT (code) DO UPDATE SET aleph=excluded.aleph, email=excluded.email;");
				if(!$query) {
					$error = "Zápis uživatele " . $_POST['user-code'] . " selhal.";
				} else {
					$error = "Uživatel " . $_POST['user-code'] . " uložen."; 
				}
			} else {
				$error = "Prázdný vstup uživatele."; 
			}
		}
	}

	if (!empty($_POST['review-authority'])) {
		if (isset($_POST['review-delete'])) {
			$query = $db->exec("DELETE FROM review WHERE authority = '" . $_POST['review-authority'] . "';");
			if(!$query) {
				$error = "Odstranění recenze " . $_POST['review-authority'] . " selhalo.";
			} else {
				$error = "Recenze " . $_POST['review-authority'] . " odstraněna."; 
			}
		}
		if (isset($_POST['review-save'])) {
			if (!empty($_POST['review-authority']) and !empty($_POST['review-name'])) {
				$query = $db->exec("INSERT INTO review (authority, name) VALUES ('"
					. $db->escapeString($_POST['review-authority']) . "','"
					. $db->escapeString($_POST['review-name'])
					. "') ON CONFLICT (authority) DO UPDATE SET  name=excluded.name;");
				if(!$query) {
					$error = "Zápis recenze " . $_POST['review-authority'] . " selhal.";
				} else {
					$error = "Recenze " . $_POST['review-authority'] . " uložena."; 
				}
			} else {
				$error = "Prázdný vstup recenze."; 
			}
		}
	}

	if (!empty($_POST['country-code']) and !empty($_POST['language-code']) and !empty($_POST['role-code'])) {
		if (isset($_POST['code-save'])) {
			$country = explode("\n", trim($_POST['country-code']));
			$query = $db->exec("DELETE FROM country;");
			$val = "('" . implode("'),('", array_map('trim', $country)) .  "')";
			$query = $db->exec("INSERT INTO country (code) VALUES " . $val . " ON CONFLICT (code) DO NOTHING;");
			! $query ? $error = "Zápis kódů selhal." : $error = "Kódy uloženy."; 

			$language = explode("\n", trim($_POST['language-code']));
			$query = $db->exec("DELETE FROM language;");
			$val = "('" . implode("'),('", array_map('trim', $language)) .  "')";
			$query = $db->exec("INSERT INTO language (code) VALUES " . $val . " ON CONFLICT (code) DO NOTHING;");
			! $query ? $error = "Zápis kódů selhal." : $error = "Kódy uloženy."; 
		
			$role = explode("\n", trim($_POST['role-code']));
			$query = $db->exec("DELETE FROM role;");
			$val = "('" . implode("'),('", array_map('trim', $role)) .  "')";
			$query = $db->exec("INSERT INTO role (code) VALUES " . $val . " ON CONFLICT (code) DO NOTHING;");
			! $query ? $error = "Zápis kódů selhal." : $error = "Kódy uloženy."; 
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
<div class="col col-md-9 m-2">

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
			<svg xmlns="http://www.w3.org/2000/svg" onclick="error_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-square" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>	
		</div>
		<div class="row-3 mt-3">
			<input type="submit" id="error-delete" name="error-delete" value="1" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="error_on_delete()" width="24" height="24" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
		</div>
	</td>
	<td class="align-middle">
		<div class="row-3 mb-3">
			<input type="submit" id="error-display" name="error-display" value="error-display" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="on_display('error')" width="24" height="24" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg>
		</div>
		<div class="row-3 mt-3">
			<input type="submit" id="error-export" name="error-export" value="error-export" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="on_export('error')" width="24" height="24" fill="currentColor" class="bi bi-filetype-csv" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM3.517 14.841a1.13 1.13 0 0 0 .401.823c.13.108.289.192.478.252.19.061.411.091.665.091.338 0 .624-.053.859-.158.236-.105.416-.252.539-.44.125-.189.187-.408.187-.656 0-.224-.045-.41-.134-.56a1.001 1.001 0 0 0-.375-.357 2.027 2.027 0 0 0-.566-.21l-.621-.144a.97.97 0 0 1-.404-.176.37.37 0 0 1-.144-.299c0-.156.062-.284.185-.384.125-.101.296-.152.512-.152.143 0 .266.023.37.068a.624.624 0 0 1 .246.181.56.56 0 0 1 .12.258h.75a1.092 1.092 0 0 0-.2-.566 1.21 1.21 0 0 0-.5-.41 1.813 1.813 0 0 0-.78-.152c-.293 0-.551.05-.776.15-.225.099-.4.24-.527.421-.127.182-.19.395-.19.639 0 .201.04.376.122.524.082.149.2.27.352.367.152.095.332.167.539.213l.618.144c.207.049.361.113.463.193a.387.387 0 0 1 .152.326.505.505 0 0 1-.085.29.559.559 0 0 1-.255.193c-.111.047-.249.07-.413.07-.117 0-.223-.013-.32-.04a.838.838 0 0 1-.248-.115.578.578 0 0 1-.255-.384h-.765ZM.806 13.693c0-.248.034-.46.102-.633a.868.868 0 0 1 .302-.399.814.814 0 0 1 .475-.137c.15 0 .283.032.398.097a.7.7 0 0 1 .272.26.85.85 0 0 1 .12.381h.765v-.072a1.33 1.33 0 0 0-.466-.964 1.441 1.441 0 0 0-.489-.272 1.838 1.838 0 0 0-.606-.097c-.356 0-.66.074-.911.223-.25.148-.44.359-.572.632-.13.274-.196.6-.196.979v.498c0 .379.064.704.193.976.131.271.322.48.572.626.25.145.554.217.914.217.293 0 .554-.055.785-.164.23-.11.414-.26.55-.454a1.27 1.27 0 0 0 .226-.674v-.076h-.764a.799.799 0 0 1-.118.363.7.7 0 0 1-.272.25.874.874 0 0 1-.401.087.845.845 0 0 1-.478-.132.833.833 0 0 1-.299-.392 1.699 1.699 0 0 1-.102-.627v-.495Zm8.239 2.238h-.953l-1.338-3.999h.917l.896 3.138h.038l.888-3.138h.879l-1.327 4Z"/></svg>
		</div>
	</td>
	</tr>
</tbody>
</table>
</form>

<h3>Vyjímky</h3>

<form method="post" action="." enctype="multipart/form-data">
<table class="table my-4">
	<thead>
	<tr>
		<th scope="col">SysNo</th>
		<th scope="col">Kód</th>
	</tr>
	</thead>
	<tbody>
	<tr>
	<td class="col-3 align-middle">
		<input class="form-control text-center" id="exception-sysno" name="exception-sysno" maxlength="9" type="text" value="<?php echo $ex_sysno;?>" size="8" list="exception-list">
		<datalist id="exception-list">

		</datalist>
	</td>
	<td class="col-10 align-middle"><input type="text" class="form-control" id="exception-list" size="47" name="exception-list" value="<?php echo $exception_list;?>"></td>
	<td class="col align-middle">
		<div class="row flex-nowrap">
			<div class="col p-0 mx-2 mb-1 text-center">
			<svg xmlns="http://www.w3.org/2000/svg" onclick="exception_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-square" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>	
			</div>
			<div class="col p-0 mx-2 mb-1 text-center">
			<svg xmlns="http://www.w3.org/2000/svg" onclick="exception_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-square" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>	
			</div>
		</div>
		<div class="row flex-nowrap">
			<div class="col p-0 mx-2 mt-1 text-center">
			<svg xmlns="http://www.w3.org/2000/svg" onclick="exception_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-square" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>	
			</div>
			<div class="col p-0 mx-2 mt-1 text-center">
			<svg xmlns="http://www.w3.org/2000/svg" onclick="exception_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-square" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>	
			</div>
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
			<svg xmlns="http://www.w3.org/2000/svg" onclick="user_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-square" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>	
		</div>
		<div class="row-3 mt-3">
			<input type="submit" id="user-delete" name="user-delete" value="user-delete" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="user_on_delete()" width="24" height="24" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
		</div>
	</td>
	<td class="align-middle">
		<div class="row-3 mb-3">
			<input type="submit" id="user-display" name="user-display" value="user-display" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="on_display('user')" width="24" height="24" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg>
		</div>
		<div class="row-3 mt-3">
			<input type="submit" id="user-export" name="user-export" value="user-export" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="on_export('user')" width="24" height="24" fill="currentColor" class="bi bi-filetype-csv" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM3.517 14.841a1.13 1.13 0 0 0 .401.823c.13.108.289.192.478.252.19.061.411.091.665.091.338 0 .624-.053.859-.158.236-.105.416-.252.539-.44.125-.189.187-.408.187-.656 0-.224-.045-.41-.134-.56a1.001 1.001 0 0 0-.375-.357 2.027 2.027 0 0 0-.566-.21l-.621-.144a.97.97 0 0 1-.404-.176.37.37 0 0 1-.144-.299c0-.156.062-.284.185-.384.125-.101.296-.152.512-.152.143 0 .266.023.37.068a.624.624 0 0 1 .246.181.56.56 0 0 1 .12.258h.75a1.092 1.092 0 0 0-.2-.566 1.21 1.21 0 0 0-.5-.41 1.813 1.813 0 0 0-.78-.152c-.293 0-.551.05-.776.15-.225.099-.4.24-.527.421-.127.182-.19.395-.19.639 0 .201.04.376.122.524.082.149.2.27.352.367.152.095.332.167.539.213l.618.144c.207.049.361.113.463.193a.387.387 0 0 1 .152.326.505.505 0 0 1-.085.29.559.559 0 0 1-.255.193c-.111.047-.249.07-.413.07-.117 0-.223-.013-.32-.04a.838.838 0 0 1-.248-.115.578.578 0 0 1-.255-.384h-.765ZM.806 13.693c0-.248.034-.46.102-.633a.868.868 0 0 1 .302-.399.814.814 0 0 1 .475-.137c.15 0 .283.032.398.097a.7.7 0 0 1 .272.26.85.85 0 0 1 .12.381h.765v-.072a1.33 1.33 0 0 0-.466-.964 1.441 1.441 0 0 0-.489-.272 1.838 1.838 0 0 0-.606-.097c-.356 0-.66.074-.911.223-.25.148-.44.359-.572.632-.13.274-.196.6-.196.979v.498c0 .379.064.704.193.976.131.271.322.48.572.626.25.145.554.217.914.217.293 0 .554-.055.785-.164.23-.11.414-.26.55-.454a1.27 1.27 0 0 0 .226-.674v-.076h-.764a.799.799 0 0 1-.118.363.7.7 0 0 1-.272.25.874.874 0 0 1-.401.087.845.845 0 0 1-.478-.132.833.833 0 0 1-.299-.392 1.699 1.699 0 0 1-.102-.627v-.495Zm8.239 2.238h-.953l-1.338-3.999h.917l.896 3.138h.038l.888-3.138h.879l-1.327 4Z"/></svg>
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
			<svg xmlns="http://www.w3.org/2000/svg" onclick="review_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-square" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>	
		</div>
		<div class="row-3 mt-3">
			<input type="submit" id="review-delete" name="review-delete" value="review-delete" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="review_on_delete()" width="24" height="24" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
		</div>
	</td>
	<td class="align-middle">
		<div class="row-3 mb-3">
			<input type="submit" id="review-display" name="review-display" value="review-display" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="on_display('review')" width="24" height="24" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg>
		</div>
		<div class="row-3 mt-3">
			<input type="submit" id="review-export" name="review-export" value="review-export" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="on_export('review')" width="24" height="24" fill="currentColor" class="bi bi-filetype-csv" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM3.517 14.841a1.13 1.13 0 0 0 .401.823c.13.108.289.192.478.252.19.061.411.091.665.091.338 0 .624-.053.859-.158.236-.105.416-.252.539-.44.125-.189.187-.408.187-.656 0-.224-.045-.41-.134-.56a1.001 1.001 0 0 0-.375-.357 2.027 2.027 0 0 0-.566-.21l-.621-.144a.97.97 0 0 1-.404-.176.37.37 0 0 1-.144-.299c0-.156.062-.284.185-.384.125-.101.296-.152.512-.152.143 0 .266.023.37.068a.624.624 0 0 1 .246.181.56.56 0 0 1 .12.258h.75a1.092 1.092 0 0 0-.2-.566 1.21 1.21 0 0 0-.5-.41 1.813 1.813 0 0 0-.78-.152c-.293 0-.551.05-.776.15-.225.099-.4.24-.527.421-.127.182-.19.395-.19.639 0 .201.04.376.122.524.082.149.2.27.352.367.152.095.332.167.539.213l.618.144c.207.049.361.113.463.193a.387.387 0 0 1 .152.326.505.505 0 0 1-.085.29.559.559 0 0 1-.255.193c-.111.047-.249.07-.413.07-.117 0-.223-.013-.32-.04a.838.838 0 0 1-.248-.115.578.578 0 0 1-.255-.384h-.765ZM.806 13.693c0-.248.034-.46.102-.633a.868.868 0 0 1 .302-.399.814.814 0 0 1 .475-.137c.15 0 .283.032.398.097a.7.7 0 0 1 .272.26.85.85 0 0 1 .12.381h.765v-.072a1.33 1.33 0 0 0-.466-.964 1.441 1.441 0 0 0-.489-.272 1.838 1.838 0 0 0-.606-.097c-.356 0-.66.074-.911.223-.25.148-.44.359-.572.632-.13.274-.196.6-.196.979v.498c0 .379.064.704.193.976.131.271.322.48.572.626.25.145.554.217.914.217.293 0 .554-.055.785-.164.23-.11.414-.26.55-.454a1.27 1.27 0 0 0 .226-.674v-.076h-.764a.799.799 0 0 1-.118.363.7.7 0 0 1-.272.25.874.874 0 0 1-.401.087.845.845 0 0 1-.478-.132.833.833 0 0 1-.299-.392 1.699 1.699 0 0 1-.102-.627v-.495Zm8.239 2.238h-.953l-1.338-3.999h.917l.896 3.138h.038l.888-3.138h.879l-1.327 4Z"/></svg>
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
	<td class="align-middle"><textarea class="form-control" id="country-code" name="country-code" rows="5">
<?php

if ($db) {

	$query = $db->query("SELECT code FROM country;");
	while ($result = $query->fetchArray(SQLITE3_ASSOC)) {
		echo $result['code'] . "\n";
        }
}

?>
</textarea></td>
	<td class="align-middle"><textarea class="form-control" id="language-code" name="language-code" rows="5">
<?php

if ($db) {

	$query = $db->query("SELECT code FROM language;");
	while ($result = $query->fetchArray(SQLITE3_ASSOC)) {
		echo $result['code'] . "\n";
        }
}

?>
</textarea></td>
	<td class="align-middle"><textarea class="form-control" id="role-code" name="role-code" rows="5">
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
		<div class="row-3 mb-3">
			<input type="submit" id="code-save" name="code-save" value="code-save" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="code_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-square" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>	
		</div>
		<div class="row-3 mt-3">
			<input type="submit" id="code-export" name="code-export" value="code-export" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="on_export('code')" width="24" height="24" fill="currentColor" class="bi bi-filetype-csv" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM3.517 14.841a1.13 1.13 0 0 0 .401.823c.13.108.289.192.478.252.19.061.411.091.665.091.338 0 .624-.053.859-.158.236-.105.416-.252.539-.44.125-.189.187-.408.187-.656 0-.224-.045-.41-.134-.56a1.001 1.001 0 0 0-.375-.357 2.027 2.027 0 0 0-.566-.21l-.621-.144a.97.97 0 0 1-.404-.176.37.37 0 0 1-.144-.299c0-.156.062-.284.185-.384.125-.101.296-.152.512-.152.143 0 .266.023.37.068a.624.624 0 0 1 .246.181.56.56 0 0 1 .12.258h.75a1.092 1.092 0 0 0-.2-.566 1.21 1.21 0 0 0-.5-.41 1.813 1.813 0 0 0-.78-.152c-.293 0-.551.05-.776.15-.225.099-.4.24-.527.421-.127.182-.19.395-.19.639 0 .201.04.376.122.524.082.149.2.27.352.367.152.095.332.167.539.213l.618.144c.207.049.361.113.463.193a.387.387 0 0 1 .152.326.505.505 0 0 1-.085.29.559.559 0 0 1-.255.193c-.111.047-.249.07-.413.07-.117 0-.223-.013-.32-.04a.838.838 0 0 1-.248-.115.578.578 0 0 1-.255-.384h-.765ZM.806 13.693c0-.248.034-.46.102-.633a.868.868 0 0 1 .302-.399.814.814 0 0 1 .475-.137c.15 0 .283.032.398.097a.7.7 0 0 1 .272.26.85.85 0 0 1 .12.381h.765v-.072a1.33 1.33 0 0 0-.466-.964 1.441 1.441 0 0 0-.489-.272 1.838 1.838 0 0 0-.606-.097c-.356 0-.66.074-.911.223-.25.148-.44.359-.572.632-.13.274-.196.6-.196.979v.498c0 .379.064.704.193.976.131.271.322.48.572.626.25.145.554.217.914.217.293 0 .554-.055.785-.164.23-.11.414-.26.55-.454a1.27 1.27 0 0 0 .226-.674v-.076h-.764a.799.799 0 0 1-.118.363.7.7 0 0 1-.272.25.874.874 0 0 1-.401.087.845.845 0 0 1-.478-.132.833.833 0 0 1-.299-.392 1.699 1.699 0 0 1-.102-.627v-.495Zm8.239 2.238h-.953l-1.338-3.999h.917l.896 3.138h.038l.888-3.138h.879l-1.327 4Z"/></svg>
		</div>
	</td>

	</tr>
</tbody>
</table>
</form>

<h3>Slovníky</h3>

<form method="post" action="." enctype="multipart/form-data">
<table class="table my-4">
	<thead>
	<tr>
		<th scope="col">Pole</th>
		<th scope="col">Hodnoty</th>
	</tr>
	</thead>
	<tbody>
	<tr>
	<td class="align-middle col-4 text-center">
		<input type="radio" class="btn-check" name="options" id="option1" autocomplete="off" checked>
		<label class="btn btn-outline-dark m-1" for="option1">260/264a</label>
		<input type="radio" class="btn-check" name="options" id="option2" autocomplete="off">
		<label class="btn btn-outline-dark m-1" for="option2">260/264b</label>
		<input type="radio" class="btn-check" name="options" id="option3" autocomplete="off">
		<label class="btn btn-outline-dark m-1" for="option3">490a</label>
		<input type="radio" class="btn-check" name="options" id="option4" autocomplete="off">
		<label class="btn btn-outline-dark m-1" for="option4">773t</label>
		<input type="radio" class="btn-check" name="options" id="option5" autocomplete="off">
		<label class="btn btn-outline-dark m-1" for="option5">336</label>
		<input type="radio" class="btn-check" name="options" id="option6" autocomplete="off">
		<label class="btn btn-outline-dark m-1" for="option6">337</label>
		<input type="radio" class="btn-check" name="options" id="option7" autocomplete="off">
		<label class="btn btn-outline-dark m-1" for="option7">338</label>
	</td>
	<td class="align-middle col-8"><textarea class="form-control" id="dictionary-data" name="dictionary-data" rows="5">
</textarea></td>
	<td class="align-middle">
		<div class="row-3 mb-3">
			<input type="submit" id="code-save" name="code-save" value="code-save" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="dict_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-square" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>	
		</div>
		<div class="row-3 mt-3">
			<input type="submit" id="code-export" name="code-export" value="code-export" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="on_export('code')" width="24" height="24" fill="currentColor" class="bi bi-filetype-csv" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM3.517 14.841a1.13 1.13 0 0 0 .401.823c.13.108.289.192.478.252.19.061.411.091.665.091.338 0 .624-.053.859-.158.236-.105.416-.252.539-.44.125-.189.187-.408.187-.656 0-.224-.045-.41-.134-.56a1.001 1.001 0 0 0-.375-.357 2.027 2.027 0 0 0-.566-.21l-.621-.144a.97.97 0 0 1-.404-.176.37.37 0 0 1-.144-.299c0-.156.062-.284.185-.384.125-.101.296-.152.512-.152.143 0 .266.023.37.068a.624.624 0 0 1 .246.181.56.56 0 0 1 .12.258h.75a1.092 1.092 0 0 0-.2-.566 1.21 1.21 0 0 0-.5-.41 1.813 1.813 0 0 0-.78-.152c-.293 0-.551.05-.776.15-.225.099-.4.24-.527.421-.127.182-.19.395-.19.639 0 .201.04.376.122.524.082.149.2.27.352.367.152.095.332.167.539.213l.618.144c.207.049.361.113.463.193a.387.387 0 0 1 .152.326.505.505 0 0 1-.085.29.559.559 0 0 1-.255.193c-.111.047-.249.07-.413.07-.117 0-.223-.013-.32-.04a.838.838 0 0 1-.248-.115.578.578 0 0 1-.255-.384h-.765ZM.806 13.693c0-.248.034-.46.102-.633a.868.868 0 0 1 .302-.399.814.814 0 0 1 .475-.137c.15 0 .283.032.398.097a.7.7 0 0 1 .272.26.85.85 0 0 1 .12.381h.765v-.072a1.33 1.33 0 0 0-.466-.964 1.441 1.441 0 0 0-.489-.272 1.838 1.838 0 0 0-.606-.097c-.356 0-.66.074-.911.223-.25.148-.44.359-.572.632-.13.274-.196.6-.196.979v.498c0 .379.064.704.193.976.131.271.322.48.572.626.25.145.554.217.914.217.293 0 .554-.055.785-.164.23-.11.414-.26.55-.454a1.27 1.27 0 0 0 .226-.674v-.076h-.764a.799.799 0 0 1-.118.363.7.7 0 0 1-.272.25.874.874 0 0 1-.401.087.845.845 0 0 1-.478-.132.833.833 0 0 1-.299-.392 1.699 1.699 0 0 1-.102-.627v-.495Zm8.239 2.238h-.953l-1.338-3.999h.917l.896 3.138h.038l.888-3.138h.879l-1.327 4Z"/></svg>
		</div>
	</td>

	</tr>
</tbody>
</table>
</form>

</div>
</div>
</main>

<!-- MODAL --!>

<div class="modal" tabindex="-1" id="modal-list">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width: fit-content;"> <!--style="max-width: 80%;">-->
		<div class="modal-content">
			<!--<div class="modal-header"></div>-->
			<div class="modal-body my-4" id="modal-list-data"></div>
			<!--<div class="modal-footer"></div>-->
		</div>
	</div>
</div>

<div class="modal" id="modal" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered">
	<div class="modal-content shadow">
		<div class="container-fluid">
			<div class="row my-2">
				<div class="col my-2">
					<span class="align-middle" id="modal-text"></span>
					<span class="align-middle"><b id="modal-text-bold"></b></span>
					<span class="align-middle">?</span>
				</div>
				<div class="col-3 d-flex align-items-center">
					<button class="btn btn-sm btn-danger w-100" onclick="on_confirm()">Ano</button>
				</div>
				<div class="col-1 d-flex align-items-center me-2">
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
			</div>
		</div>
	</div>
	</div>
</div>

<?php

$db->close();

?>


<script src="../bootstrap.min.js"></script>
<script src="custom.js"></script>

</body>
</html>

