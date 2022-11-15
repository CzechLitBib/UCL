<?php
  
session_start();

$_SESSION['page'] = '/access/';

if(empty($_SESSION['auth'])) {
        header('Location: /');
        exit();
}

if($_SESSION['group'] !== 'admin') {
        $_SESSION['error'] = True;
        header('Location: /main/');
        exit();
}

if (!isset($_SESSION['result'])) { $_SESSION['result'] = null; }

$db = new SQLite3('/var/www/data/devel.db');

if (!$db) { $_SESSION['result'] = 'Chyba čtení databáze.'; }

# XHR POST

if ($_SERVER["CONTENT_TYPE"] == 'application/json') {
	$req = json_decode(file_get_contents('php://input'), True);
	$resp = [];
	if ($req['type'] == 'user') {
		$query = $db->query("SELECT user FROM user_group WHERE access_group = '" . $req['data'] . "' ORDER BY user;");
		if ($query) {
			$data=[];
			while ($res = $query->fetchArray(SQLITE3_ASSOC)) {
				array_push($data, $res['user']);
			}
			$resp['value'] = $data;
		}
	}
	if ($req['type'] == 'module') {
		$query = $db->query("SELECT module FROM module_group WHERE access_group = '" . $req['data'] . "';");
		if ($query) {
			$data=[];
			while ($res = $query->fetchArray(SQLITE3_ASSOC)) {
				array_push($data, $res['module']);
			}
			$resp['value'] = $data;
		}
	
	}
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($resp);
	exit();
}

# PHP POST

if (!empty($_POST)) {
	if (isset($_POST['group-save'])) {
		if (!empty($_POST['group-new'])) {
			$query = $db->exec("INSERT INTO access_group (name) VALUES ('"
				. $db->escapeString($_POST['group-new']) . "');");
			if(!$query) {
				$_SESSION['result'] = "Zápis skupiny " . $_POST['group-new'] . " selhal.";
			} else {
				$_SESSION['result'] = "Skupina " . $_POST['group-new'] . " uložena."; 
			}
		} else {
			$_SESSION['result'] = "Prázdný název skupiny.";
		}
	}
	if (isset($_POST['group-delete'])) {
		if (!empty($_POST['group-new'])) {
			$query_group = $db->exec("DELETE FROM access_group WHERE name = '" . $_POST['group-new'] . "';");
			$query_module = $db->exec("DELETE FROM module_group WHERE access_group = '" . $_POST['group-new'] . "';");
			$query_user = $db->exec("DELETE FROM user_group WHERE access_group = '" . $_POST['group-new'] . "';");
			if(!($query_group and $query_module and $query_user)) {
				 $_SESSION['result'] = "Odstranění skupiny " . $_POST['group-new'] . " selhalo.";
			} else {
				$_SESSION['result'] = "Skupina " . $_POST['group-new'] . " odstraněna."; 
			}
		} else {
			$_SESSION['result'] = "Prázdný název skupiny.";
		}
	}
	if (isset($_POST['module-save'])) {
		if (!empty($_POST['module-description'])) {
			if (!empty($_POST['module-new'])) {
				$query = $db->exec("INSERT INTO module (name,description) VALUES ('"
					. $db->escapeString($_POST['module-new']) . "','"
					. $db->escapeString($_POST['module-description']) . "');");
				if(!$query) {
					$_SESSION['result'] = "Zápis modulu " . $_POST['module-new'] . " selhal.";
				} else {
					$_SESSION['result'] = "Modul " . $_POST['module-new'] . " uložen."; 
				}
			} else { $_SESSION['result'] = "Prázdný název modulu."; }
		} else { $_SESSION['result'] = "Prázdný popis modulu."; }
	}
	if (isset($_POST['module-delete'])) {
		if (!empty($_POST['module-new'])) {
			$query_module = $db->exec("DELETE FROM module WHERE name = '" . $_POST['module-new'] . "';");
			$query_group = $db->exec("DELETE FROM module_group WHERE module = '" . $_POST['module-new'] . "';");
			if(!($query_module and $query_group)) {
				 $_SESSION['result'] = "Odstranění modulu " . $_POST['module-new'] . " selhalo.";
			} else {
				$_SESSION['result'] = "Modul " . $_POST['module-new'] . " odstraněn."; 
			}
		} else { $_SESSION['result'] = "Prázdný název modulu."; }
	}
	if (!empty($_POST['group-option']) && !empty($_POST['access-save'])) {
		# Store users
		if (!empty($_POST['user-list'])) {
			$data = explode("\n", $db->escapeString(trim($_POST['user-list'])));
			$query_user = $db->query("SELECT user FROM user_group WHERE access_group = '" . $_POST['group-option'] .  "';");
			if ($query_user->fetchArray()) {
				$query_user->reset();
				while ($res = $query_user->fetchArray(SQLITE3_ASSOC)) {
					if(!in_array($res['user'], $data)) {
						$query = $db->exec("DELETE FROM user_group WHERE user = '" . $res['user'] . "';");
						if (!$query) { $_SESSION['result'] = "Odstranění uživatele " . $res['user'] . " selhalo."; }
					}
				}
			}
			$db->exec('BEGIN;');
			$query = $db->prepare("INSERT OR REPLACE INTO user_group (user,access_group) VALUES (?,?);");
			foreach($data as $user) {
				if (trim($user)) {
					$query->bindValue(1, trim($user));
					$query->bindValue(2, $_POST['group-option']);
					$query->execute();
				}
			}
			$transaction = $db->exec('COMMIT;');
			if (!$transaction) { $_SESSION['result'] = "Zápis uživatelů selhal."; }
		} else {
			$query = $db->exec("DELETE FROM user_group WHERE access_group = '" . $_POST['group-option'] ."';");
			if(!$query) { $_SESSION['result'] = "Zápis uživatelů selhal."; }
		}
		# Store modules
		if (isset($_POST['module-list'])) {
			$query_module = $db->query("SELECT module FROM module_group WHERE access_group = '" . $_POST['group-option'] .  "';");
			if ($query_module->fetchArray()) {
				$query_module->reset();
				while ($res = $query_module->fetchArray(SQLITE3_ASSOC)) {
					if(!in_array($res['module'], $_POST['module-list'])) {
						$query = $db->exec("DELETE FROM module_group WHERE module = '" . $res['module'] . "' AND access_group = '" . $_POST['group-option'] .  "';");
						if (!$query) { $_SESSION['result'] = "Odstranění modulu " . $res['module'] . " selhalo."; }
					}
				}
			}
			$db->exec('BEGIN;');
			$query = $db->prepare("INSERT OR IGNORE INTO module_group (module,access_group) VALUES (?,?);");
			
			foreach($_POST['module-list'] as $module) {
				$query->bindValue(1, $module);
				$query->bindValue(2, $_POST['group-option']);
				$query->execute();
			}
			$transaction = $db->exec('COMMIT;');
			if (!$transaction) { $_SESSION['result'] = "Zápis modulů selhal."; }
		} else {
			$query = $db->exec("DELETE FROM module_group WHERE access_group = '" . $_POST['group-option'] ."';");
			if(!$query) { $_SESSION['result'] = "Zápis modulů selhal."; }
		}
		if (empty($_SESSION['result'])) { $_SESSION['result'] = "Skupina " . $_POST['group-option'] . " uložena."; }
	}

	header('Location: /access/');
	exit();
}

?>

<!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ČLB Vývoj - Přístup</title>
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
		<div class="col"><a class="navbar-brand nav-link active" href="/main/">Vývoj # Přístup</a></div>
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

if (isset($_SESSION['result'])) {
	echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">'. $_SESSION['result'] . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	$_SESSION['result'] = null;
}

// Row size based on module count..
$query = $db->querySingle("SELECT COUNT(*) FROM module;");
$query ? $row_size = $query : $row_size = 1;

?>

<h3>Nastavení přístupu</h3>

<form method="post" action="." enctype="multipart/form-data">
<table class="table my-4">
	<thead>
		<tr>
			<th scope="col">Skupina</th>
			<th scope="col">Uživatel</th>
			<th scope="col">Modul</th>
			<th scope="col"></th>
		</tr>
	</thead>
	<tbody>
	<tr>
	<td class="align-middle col">
		<select class="form-select" size="<?php echo $row_size;?>" aria-label="group select" id="group-option" name="group-option" onchange="group_on_change()">

<?php

if ($db) {

	$query = $db->query("SELECT name FROM access_group ORDER BY name;");
	while ($result = $query->fetchArray(SQLITE3_ASSOC)) {
		echo '<option value="' . $result['name'] . '">' . $result['name'] . '</option>';
        }
}

?>

		</select>
	</td>
	<td class="align-middle">
		<textarea class="form-control" style="white-space: pre; overflow: auto; word-wrap: normal;" rows="<?php echo ($row_size - 1); ?>" id="user-list" name="user-list"></textarea>
	</td>
	<td class="align-middle">

<?php

if ($db) {

	$query = $db->query("SELECT name,description FROM module;");
	while ($result = $query->fetchArray(SQLITE3_ASSOC)) {
		echo '<div class=" form-check form-switch">'
		. '<input class="form-check-input" type="checkbox" role="switch" name="module-list[]" value="' . $result['name'] . '" id="' . $result['name'] . '">'
		. '<label class="form-check-label" for="' . $result['name'] . '">' . $result['description'] . '</label></div>';
        }
}

?>

	</td>
	<td class="align-middle">
			<div class="col p-0 mx-2 mt-1 mb-2 text-center">
			<input type="submit" id="access-save" name="access-save" value="access-save" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="access_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-square" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>	
			</div>
	</td>
	</tr>
</tbody>
</table>
</form>

<h3>Skupiny a moduly</h3>

<table class="table my-4">
	<thead>
	<tr>
		<th scope="col">Skupina</th>
		<th scope="col"></th>
		<th scope="col">Modul</th>
		<td scope="col"><div class="fw-bold text-secondary"> Modul / Popis</div></td>
		<th scope="col"></th>
	</tr>
	</thead>
	<tbody>
	<tr>
	<form method="post" action="." enctype="multipart/form-data">
	<td class="align-middle"><input class="form-control text-center" id="group-new" name="group-new" type="text" value="" size="2"></td>
	<td class="col align-middle">
		<div class="row g-1">
			<div class="col p-0 text-center">
			<input type="submit" id="group-save" name="group-save" value="group-save" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="group_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-square" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>	
			</div>
			<div class="col p-0 text-center">
			<input type="submit" id="group-delete" name="group-delete" value="group-delete" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="group_on_delete()" width="24" height="24" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
			</div>
		</div>
	</td>
	</form>
	<form method="post" action="." enctype="multipart/form-data">
	<td class="align-middle"><input class="form-control text-center" id="module-new" name="module-new" type="text" value="<?php if (isset($_POST['module-new'])) { echo htmlspecialchars($_POST['module-new'], ENT_QUOTES, 'UTF-8'); } ?>" size="2"></td>
	<td class="align-middle"><input type="text" class="form-control" id="module-description" size="10" name="module-description" value="<?php if (isset($_POST['module-description'])) { echo htmlspecialchars($_POST['module-description'], ENT_QUOTES, 'UTF-8'); } ?>"></td>
	<td class="align-middle">
		<div class="row g-1">
			<div class="col p-0 text-center">
			<input type="submit" id="module-save" name="module-save" value="module-save" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="module_on_save()" width="24" height="24" fill="currentColor" class="bi bi-arrow-down-square" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 2a1 1 0 0 0-1-1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2zM0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm8.5 2.5a.5.5 0 0 0-1 0v5.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V4.5z"/></svg>	
			</div>
			<div class="col p-0 text-center">
			<input type="submit" id="module-delete" name="module-delete" value="module-delete" hidden>
			<svg xmlns="http://www.w3.org/2000/svg" onclick="module_on_delete()" width="24" height="24" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
			</div>
		</div>
	</td>
	</form>
	</tr>
</tbody>
</table>

</div>
</div>
</main>

<!-- MODAL --!>

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

