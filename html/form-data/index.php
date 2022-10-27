<?php

session_start();

$_SESSION['page'] = '/form-data/';

if(empty($_SESSION['auth'])) {
	header('Location: /');
	exit();
}

if(!in_array($_SESSION['group'], array('admin','form'))) {
        $_SESSION['error'] = True;
        header('Location: /main/');
        exit();
}

if(!isset($_SESSION['form-data'])) { $_SESSION['form-data'] = Null; }

$error = '';

$FILE_PATH='/var/www/data/form/data/';

try {
	$db = new SQLite3('/var/www/data/form/form.db');
} catch (Exception $e) {
	$db = null;
}

if (!$db) { $error = 'Chyba databáze.'; }

// XHR

if ($_SERVER["CONTENT_TYPE"] == 'application/json') {
	$req = json_decode(file_get_contents('php://input'), True);
	$resp = [];
	if ($req['type'] == 'file') {
		$query = $db->querySingle("SELECT name FROM file WHERE id = '" . $req['data'] . "';", true);
		if ($query) {
			readfile($FILE_PATH . $query['name']);
		}
		exit();
	}
	if ($req['type'] == 'visible') {
		$query = $db->exec("UPDATE data SET visible = 0 WHERE id = '" . $req['data'] . "';");
		if ($query) {
			$resp['value'] = 'ok';
		}
	}
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($resp);
	exit();
}

?>

<!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ČLB Vývoj - Formulář / Data</title>
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
		<div class="col"><a class="navbar-brand nav-link active" href="/main/">Vývoj # Formulář / Data</a></div>
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
<div class="col col-md-6">

<?php
	if ($error) {
		echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">' . $error . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	} else {
		$data = $db->query("SELECT * FROM data WHERE visible = 1 ORDER BY id DESC;");

		if (!$data->fetchArray()) {
			echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">Žádná data.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
		} else {
			$data->reset();
			echo '<div class="container mt-4">';

			while ($row = $data->fetchArray(SQLITE3_ASSOC)) {
			
				$file = $db->querySingle("SELECT name FROM file WHERE ID = '" . $row['id'] . "';)");
				
				echo '<div id="' . $row['id'] . '">';
				echo '<hr class="m-1 p-0">';
				echo '<div class="row px-1 d-flex align-items-center">';
					echo '<div class="col col-auto"><svg xmlns="http://www.w3.org/2000/svg" onclick="toggle_data(' . "'" .   $row['id'] . "'" . ')" width="24" height="24" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg></div>';
					echo '<div class="col col-auto text-nowrap">' . date(" d.m Y", hexdec(substr($row['id'],0,8))) . '</div>';# ID
					echo '<div class="col">' . $row['format'] . '</div>';# FORMAT
					echo '<div class="col text-end"><button type="button" class="btn btn-secondary btn-sm" onclick="confirmation(' . "'" . $row['id'] . "'" . ')">Zpracováno</button></div>';
				echo '</div>';
				echo '<hr class="m-1 p-0">';
				echo '</div>';
	
				echo '<div class="collapse" id="collapse-' . $row['id'] . '">';
 				echo '<div class="card card-body bg-light">';
				echo '<table class="table table-borderless bg-danger m-0"><tbody>';
		
		#				# PUBLIC
		#				echo '<tr><td class="text-end align-middle col-2"><b>'
		##				. 'Veřejný'
		#				. '</b></td><td class="text-start align-middle">';
		#				if ($row[2])  {
		#					echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/></svg>';
		#				} else {
		#					echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>';
		#				}
		#				echo '</td></tr>';

	#					# DEDICATION
	#					echo '<tr><td class="text-end align-middle col-2"><b>'
	#					. 'Dedikace'
	#					. '</b></td><td class="text-start align-middle">';
	#					if ($row[3])  {
	#						echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16"><path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425a.267.267 0 0 1 .02-.022z"/></svg>';
	#					} else {
	#						echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>';
	#					}
	#					echo '</td></tr>';

	#					# ODKAZ
						echo '<tr><td class="text-end align-middle col-2 bg-secondary"><b>Odkaz</b></td>';
						echo '<td class="text-start align-middle"><a class="external-link" href="' . $row['link'] . '" target="_blank">'. $row['link'] . '</a></td></tr>';

	#					# SOUBOR
						echo '<tr><td class="text-end align-middle col-2"><b>'
						. 'Soubor'
						. '</b></td><td class="text-start align-middle">';
						echo '<svg xmlns="http://www.w3.org/2000/svg" onclick="get_pdf(' . "'" . $row['id'] . "'" . ')" width="24" height="24" fill="currentColor" class="bi bi-filetype-pdf" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M14 4.5V14a2 2 0 0 1-2 2h-1v-1h1a1 1 0 0 0 1-1V4.5h-2A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v9H2V2a2 2 0 0 1 2-2h5.5L14 4.5ZM1.6 11.85H0v3.999h.791v-1.342h.803c.287 0 .531-.057.732-.173.203-.117.358-.275.463-.474a1.42 1.42 0 0 0 .161-.677c0-.25-.053-.476-.158-.677a1.176 1.176 0 0 0-.46-.477c-.2-.12-.443-.179-.732-.179Zm.545 1.333a.795.795 0 0 1-.085.38.574.574 0 0 1-.238.241.794.794 0 0 1-.375.082H.788V12.48h.66c.218 0 .389.06.512.181.123.122.185.296.185.522Zm1.217-1.333v3.999h1.46c.401 0 .734-.08.998-.237a1.45 1.45 0 0 0 .595-.689c.13-.3.196-.662.196-1.084 0-.42-.065-.778-.196-1.075a1.426 1.426 0 0 0-.589-.68c-.264-.156-.599-.234-1.005-.234H3.362Zm.791.645h.563c.248 0 .45.05.609.152a.89.89 0 0 1 .354.454c.079.201.118.452.118.753a2.3 2.3 0 0 1-.068.592 1.14 1.14 0 0 1-.196.422.8.8 0 0 1-.334.252 1.298 1.298 0 0 1-.483.082h-.563v-2.707Zm3.743 1.763v1.591h-.79V11.85h2.548v.653H7.896v1.117h1.606v.638H7.896Z"/></svg>';
						echo '</td></tr>';

	#					# AUTOR
	#					echo '<tr><td class="text-end align-middle col-2"><b>'
	#					. 'Autor'
	#					. '</b></td><td class="text-start align-middle">';
	#					echo 'Jmeno Autora';
	#					echo '</td></tr>';

	#					# NAME
	#					echo '<tr><td class="text-end align-middle col-2"><b>'
	#					. 'Název'
	#					. '</b></td><td class="text-start align-middle">';
	#					echo 'Testovaci dilo';
	#					echo '</td></tr>';
					echo '</tbody></table>';
					echo '</div>';
					echo '</div>';
			}
			echo '</div>';
		}
	}
?>

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

<script src="../bootstrap.min.js"></script>
<script src="custom.js"></script>

</body>
</html>

