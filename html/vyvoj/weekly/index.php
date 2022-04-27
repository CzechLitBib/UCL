<?php

session_start();

$_SESSION['page'] = 'weekly';

if(empty($_SESSION['auth'])) {
	header('Location: /vyvoj');
	exit();
}

if($_SESSION['group'] !== 'admin') {
        $_SESSION['error'] = True;
        header('Location: /vyvoj/main/');
        exit();
}

if(!isset($_SESSION['weekly'])) { $_SESSION['weekly'] = Null; }

if (!empty($_POST['date'])) {
        $_SESSION['weekly'] = $_POST['date'];
        header("Location: " . $_SERVER['REQUEST_URI']);
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

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#dc3545;">
	<div class="container-fluid">
		<a class="navbar-brand" href="/vyvoj/main">
		<svg width="32" fill="currentColor" class="bi mb-2 ms-2 me-3 d-inline-block align-text-top bi-clb-logo" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 314.4 226.08"><path d="M232.76 10.3c-2.08 5.68-11.64 32-21.27 58.46-9.63 26.45-17.4 48.19-17.22 48.33.32.31 15.1 5.68 15.53 5.68.28 0 42.61-116.1 42.61-116.84 0-.32-14.6-5.9-15.52-5.93-.21 0-2.08 4.66-4.13 10.3zM296.76 17c-30.2 82.94-36.3 99.98-35.95 100.16 1.13.6 15.45 5.68 15.6 5.54.45-.46 38.23-104.88 37.99-105.1-.32-.31-14.93-5.6-15.42-5.6-.21 0-1.2 2.25-2.22 5zM192.93 28.5c-3.66 9.6-32.03 88.16-31.92 88.41.2.57 15.41 5.96 15.73 5.61.28-.28 32.46-88.51 32.46-88.97 0-.1-2.58-1.13-5.75-2.26-3.14-1.13-6.7-2.43-7.94-2.93l-2.22-.8zM259.19 29.03l-16.27 44.7c-8.64 23.74-15.59 43.29-15.45 43.43.39.35 15.6 5.71 15.74 5.57.24-.28 32.3-88.58 32.3-89 0-.22-2.32-1.27-5.18-2.3-2.89-1.02-6.45-2.33-7.93-2.89l-2.68-.99zM.25 36.9C.1 37.04 0 40.64 0 44.87v7.7h16.59v160.51H0l.07 6.42.1 6.45 46.93.11c55.8.1 59.58-.04 69.81-2.61a58.4 58.4 0 0 0 13.8-5.12c14.99-7.37 24.4-19.01 27.55-34 .91-4.3.91-14.71.03-19.58-.81-4.38-2.71-10.06-4.55-13.44-7.44-13.97-23.67-23.32-48.44-27.87-2.85-.53-3.2-.36 4.77-2.08 27.83-6.07 40.57-19.09 40.57-41.46 0-20.92-12.99-35.84-35.7-41.1-8.47-1.93-6.85-1.86-62.73-2-28.43-.08-51.82-.04-51.96.1zm83.08 16.65c12.38 2.97 20.56 10.4 23.63 21.6 1.03 3.8 1.27 11.25.5 15.16-2.47 12.1-11.05 20.82-24.13 24.41-2.44.67-3.85.78-13.55.92l-10.87.14V52.5l10.7.18c9.17.14 11.07.25 13.72.88zM76.94 133c23.81 2.36 39.97 20.1 38.63 42.47-1.02 17.22-11.08 30.02-27.62 35.28-5.44 1.7-8.43 2.05-19.05 2.22l-9.99.18V132.64h7.2c3.95 0 8.82.18 10.83.36z"/></svg>Vývoj # Weekly</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
			</ul>
			<span clas="navbar-text"><b><?php echo $_SESSION['username'];?></b></span>
			<form class="d-flex align-items-center">
			<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-person-fill d-inline-block align-text-center mx-2" viewBox="0 0 16 16"><path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><svg/>
			</form>
		</div>
	</div>
</nav>
   
<main class="container">
<div class="row my-4 justify-content-center">
<div class="col col-md-8 my-2">

<form class="mb-4" method="post" action="." enctype="multipart/form-data">
	<div class="row gx-3 justify-content-md-center">
		<div class="col-8 col-md-4">

<?php

if (date('D') == 'Tue') {
	$default = date("Y-m-d", strtotime("Tuesday"));
} else {
	$default = date("Y-m-d", strtotime("last Tuesday"));
}

if (!empty($_SESSION['weekly'])){ $default = $_SESSION['weekly']; }
	echo '<input class="form-control" type="date" name="date" value="' . $default . '" max="' . date("Y-m-d", strtotime("Tuesday")) . '" step="7">';
?>

		</div>
		<div class="d-grid col col-md-2">
			<button class="btn btn-danger" type="submit">Zobrazit</button>
		</div>
	</div>
</form>

<?php

if (!empty($_SESSION['weekly'])){
	if (preg_match('/\d{4}-\d{2}-\d{2}/', $_SESSION['weekly'])) {

		$file = 'data/'
		. date("Y-m-d", strtotime($_SESSION['weekly']) - 8*24*3600) . '_'
		. date("Y-m-d", strtotime($_SESSION['weekly']) - 2*24*3600) . '.csv';

		if (file_exists($file)) {
			$csv = array();
			$row = 0;
			if (($handle = fopen($file, 'r')) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
					$num = count($data);
					for ($c=0;  $c < $num; $c++) {
						$csv[$row][] = $data[$c];
					}
					$row++;
				}
				fclose($handle);
			}

			echo '<table class="table"><thead><tr>';
			echo '<th class="text-center" scope="col">SysNo</th><th class="text-center" scope="col">SIF</th><th scope="col">Kód</th><th scope="col">Popis</th></tr></thead><tbody>';

			array_multisort(array_column($csv,0), SORT_DESC, SORT_NUMERIC, $csv);
			foreach($csv as $row) {
				echo '<tr><th class="text-center"><a class="text-dark text-decoration-none" target="_blank" href="https://aleph22.lib.cas.cz/F/?func=direct&doc_number=' . $row[0] . '&local_base=AV&format=001"><b>' . $row[0] . '</b></a></th>';
				echo '<td class="text-center">' . $row[1] . '</td>';
				echo '<td><a class="text-dark text-decoration-none" href="/vyvoj/error/#' . $row[2] . '"><b>' . $row[2] . '</b></a></td>';
				echo '<td>' . $row[3] . '</td></tr>';
			}
			echo '</tbody></table>';
		} else {
			echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">Žádná data.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
		}
	}
}

?>

</div>
</div>
</main>

<script src="../bootstrap.min.js"></script>

</body>
</html>

