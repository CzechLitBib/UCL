<?php

session_start();

$_SESSION['page'] = '/cat/';

if(empty($_SESSION['auth'])) {
	header('Location: /');
	exit();
}

try {
	$db = new SQlite3('/var/www/data/devel.db');
} catch (Exception $e) {
	$db = null;
}

if ($db) {
	$access = "SELECT * FROM module_group WHERE module = 'cat' AND access_group = '" . $_SESSION['group'] . "';";
	if (!$db->querySingle($access)) {
		$_SESSION['error'] = 'Nedostatečné oprávnění.';
		header('Location: /main/');
		exit();
	}
} else {
	$_SESSION['error'] = 'Chyba čtení databáze.';
	header('Location: /main/');
	exit();
}

$month_map = [
	'01' => 'Leden',
	'02' => 'Únor',
	'03' => 'Březen',
	'04' => 'Duben',
	'05' => 'Květen',
	'06' => 'Červen',
	'07' => 'Červenec',
	'08' => 'Srpen',
	'09' => 'Září',
	'10' => 'Říjen',
	'11' => 'Listopad',
	'12' => 'Prosinec',
];


if(!isset($_SESSION['cat_month'])) { $_SESSION['cat_month'] = Null; }
if(!isset($_SESSION['cat_year'])) { $_SESSION['cat_year'] = Null; }

if (!empty($_POST['month']) and !empty($_POST['year'])) {
	$_SESSION['cat_month'] = $_POST['month'];
	$_SESSION['cat_year'] = $_POST['year'];
	header("Location: " . $_SERVER['REQUEST_URI']);
	exit();
}

$data = null;
if (!empty($_SESSION['cat_month']) and !empty($_SESSION['cat_year'])) {
	if (preg_match('/\d{2}/', array_search($_SESSION['cat_month'], $month_map)) and preg_match('/\d{4}/', $_SESSION['cat_year'])) {
		$file = 'data/' . $_SESSION['cat_year'] . '/' . array_search($_SESSION['cat_month'],$month_map) . '/data.json';
		if (file_exists($file)) {
			$data = json_decode(file_get_contents($file), true);
		}
	}
}

# XHR POST

if (json_decode(file_get_contents('php://input'))) {
	$req = json_decode(file_get_contents('php://input'), True);
	$resp = [];
	if ($req['type'] == 'cipher') {
		$res = [];
		if (!empty($data)) {
			$res['new'] = $data[$req['data']]['new_count'];
			$res['fix'] = $data[$req['data']]['fix_count'];
			$res['other'] = $data[$req['data']]['fix_other_count'];
		}
		$resp['value'] = $res;
	}

	if ($req['type'] == 'chart') {
		$res = [];
		if (!empty($data)) {
			if ($req['data'] == 'A') {
				uasort($data, function ($v1, $v2) { return $v1['new_count'] <= $v2['new_count']; });
				$res['label'] = array_keys($data);
				$res['data'] = array_column($data, 'new_count');
			}
			if ($req['data'] == 'B') {
				$pre = [];
				foreach ($data as $key => $val) { $pre[$key] = $val['fix_count'] + $val['fix_other_count']; }
				arsort($pre);
				$res['label'] = array_keys($pre);
				$res['data'] = array_values($pre);
			}
		}
		$resp['value'] = $res;
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
	<title>ČLB Vývoj - CAT</title>
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
		<div class="col"><a class="navbar-brand nav-link active" href="/main/">Vývoj # CAT</a></div>
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
<div class="row mt-4 justify-content-center">
<div class="col col-md-8">

<form class="mb-4" method="post" action="." enctype="multipart/form-data">
	<div class="row gx-3 justify-content-md-center">
		<div class="col col-md-3">
			<div class="form-floating">
				<select class="form-select" id="month" name="month" aria-label="floating label select example">

<?php

foreach($month_map as $m => $mon) {
	if ($mon == $_SESSION['cat_month']) {
		echo "<option selected>" . $mon . "</option>";
	} elseif (empty($_SESSION['cat_month']) and $m == date('m', strtotime("-1 month"))) {
		echo "<option selected>" . $mon . "</option>";
	} else {
		echo "<option>" . $mon . "</option>";
	}
}

?>

				</select>
				<label for="month">Měsíc</label>
			</div>
		</div>
		<div class="col col-md-3">
			<div class="form-floating">
				<select class="form-select" id="year" name="year" aria-label="floating label select example">

<?php

foreach (range(2020, date('Y', strtotime("-1 month"))) as $y) {
	if ($y == $_SESSION['cat_year']) {
		echo "<option selected>" . $y . "</option>";
	} elseif (empty($_SESSION['cat_year']) and $y == date('Y', strtotime("-1 month"))) {
		echo "<option selected>" . $y . "</option>";
	} else {
		echo "<option>" . $y . "</option>";
	}
}

?>
	
				</select>
				<label for="year">Rok</label>
			</div>
		</div>
		<div class="d-grid col col-md-2 align-self-center">
			<button class="btn btn-danger" type="submit">Zobrazit</button>
		</div>
	</div>
</form>

</div>
</div>

<?php 

if (!empty($_SESSION['cat_month']) and !empty($_SESSION['cat_year'])) {
	if (!empty($data)) {
		$new_total = 0;
		$fix_total = 0;

		foreach ($data as $sif => $vals) {
			$new_total += $vals['new_count'];
			$fix_total += $vals['fix_count'] + $vals['fix_other_count'];
		}
echo '
<div class="row my-4 justify-content-center">
  	<div class="col col-md-3 mx-md-3 text-center">
		<span class="fs-5">Nové záznamy</span>
		<span class="badge fs-5 ms-1 text-dark border">'. $new_total . '</span>
		<canvas class="mx-auto d-block my-4" id="A-doughnut" width="300" height="300"></canvas>
	</div>
	<div class="col col-md-3 mx-md-3 text-center">
		<span class="fs-5">Opravy</span>
		<span class="badge fs-5 ms-1 text-dark border">' . $fix_total . '</span>
		<canvas class="mx-auto d-block my-4" id="B-doughnut" width="300" height="300"></canvas>
	</div>
</div>
';

	}
}

?>

<div class="row justify-content-center">
<div class="col col-md-5">

<?php

if (!empty($_SESSION['cat_month']) and !empty($_SESSION['cat_year'])) {
	if (empty($data)) {
		echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">Žádná data.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
	} else {
		echo '<table class="table table-sm align-middle text-center"><thead><tr><th>Šifra</th><th>Hodnoty</th><th>Záznamy</th></tr></thead><tbody><tr>';
		// sif
		echo '<td class="col-3">';
			echo '<select class="form-select" size="10" aria-label="group select" id="cipher-option" name="cipher-option" onchange="cipher_on_change()">';
				foreach (array_keys($data) as $sif) { echo '<option value="' . $sif . '">' . $sif . '</option>'; }
			echo '</select></td>';
		// numbers
		echo '<td class="col-6">'
			. '<div class="d-grid my-3 gap-2 d-sm-flex align-items-center justify-content-center">'
				. '<div class="col col-md-7 ms-2">nové záznamy</div><div class="col"><div class="badge fs-5 text-dark border" id="cipher-new">0</div></div>'
			. '</div>'
			. '<div class="d-grid my-3 gap-2 d-sm-flex align-items-center justify-content-center">'
				. '<div class="col col-md-7 ms-2">vlastní opravy</div><div class="col"><div class="badge fs-5 text-dark border" id="cipher-fix">0</div></div>'
			. '</div>'
			. '<div class="d-grid my-3 gap-2 d-sm-flex align-items-center justify-content-center">'
				. '<div class="col col-md-7 ms-2">ostatní opravy</div><div class="col"><div class="badge fs-5 text-dark border" id="cipher-other">0</div></div>'
			. '</div>'
		. '</td>';
		// download
		echo '<td><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" onclick="cipher_on_record()" fill="currentColor" class="bi bi-justify" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M2 12.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/></svg></td>';
		echo '</tr></tbody></table>';
	}
}

?>

</div>
</div>
</main>

<script src="../bootstrap.min.js"></script>
<script src="chart.umd.js"></script>
<script src="chartjs-plugin-labels.min.js"></script>
<script src="custom.js"></script>

</body>
</html>

