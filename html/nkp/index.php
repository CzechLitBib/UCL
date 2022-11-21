<?php

session_start();

$_SESSION['page'] = '/nkp/';

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
	$access = "SELECT * FROM module_group WHERE module = 'nkp' AND access_group = '" . $_SESSION['group'] . "';";
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

if(!isset($_SESSION['nkp_month'])) { $_SESSION['nkp_month'] = Null; }
if(!isset($_SESSION['nkp_year'])) { $_SESSION['nkp_year'] = Null; }

if (!empty($_POST['month']) and !empty($_POST['year'])) {
	$_SESSION['nkp_month'] = $_POST['month'];
	$_SESSION['nkp_year'] = $_POST['year'];
	header("Location: " . $_SERVER['REQUEST_URI']);
	exit();
}

?>

<!doctype html>
<html lang="cs">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ČLB Vývoj - NKP</title>
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
		<div class="col"><a class="navbar-brand nav-link active" href="/main/">Vývoj # NKP</a></div>
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
<div class="col col-md-8">

<form class="mb-4" method="post" action="." enctype="multipart/form-data">
	<div class="row gx-3 justify-content-md-center">
		<div class="col col-md-3">
			<div class="form-floating">
			<select class="form-select" id="month" name="month" aria-label="floating label select example">

<?php

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

foreach($month_map as $m => $mon) {
	if ($mon == $_SESSION['nkp_month']) {
		echo "<option selected>" . $mon . "</option>";
	} elseif (empty($_SESSION['nkp_month']) and $m == date('m', strtotime("-1 month"))) {
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
	if ($y == $_SESSION['nkp_year']) {
		echo "<option selected>" . $y . "</option>";
	} elseif (empty($_SESSION['nkp_year']) and $y == date('Y', strtotime("-1 month"))) {
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

<?php

function getLines($file)
{
	$f = fopen($file, 'rb');
	$lines = 0;
	while (!feof($f)) {
		$lines += substr_count(fread($f, 8192), "\n");
	}
	fclose($f);
	return $lines;
}

if (!empty($_SESSION['nkp_month']) and !empty($_SESSION['nkp_year'])) {
	if (preg_match('/\d{2}/', array_search($_SESSION['nkp_month'], $month_map)) and preg_match('/\d{4}/', $_SESSION['nkp_year'])) {
	
		$dir = 'data/' . $_SESSION['nkp_year'] . '/' . array_search($_SESSION['nkp_month'],$month_map);

		# NEW

		$new = array_filter(scandir($dir), function ($var) { return preg_match('/\d{3}.(?!old).*/', $var); } );
		$tags = array_unique(array_map(function ($var) { return explode('.', $var)[0]; }, $new));
		$no_seven = 0;
		$seven = 0;

		if (!empty($tags)) {

			echo '<table class="table table-sm caption-top text-center"><caption>Záznamy založené ve zvoleném datu</caption>';
			echo '<thead class="table-light"><tr><th scope="col">#</th><th scope="col" colspan="4">Podpole 7</th>';
			echo '<th scope="col" colspan="4">Bez podpole 7</th></tr></thead><tbody>';

			foreach ($tags as $tag)	{
				$has_seven = 0;
				$has_no_seven = 0;
				if(in_array($tag . '.7.csv', $new)) {
					$has_seven = getLines($dir . '/' . $tag . '.7.csv');
					$seven += $has_seven;
				}
				if(in_array($tag . '.csv', $new)) {
					$has_no_seven = getLines($dir . '/' . $tag . '.csv');
					$no_seven += $has_no_seven;
				}
				if (!empty($has_seven)) {

					echo'<tr><td scope="row" class="align-middle"><b>' . $tag . '</b></td>'
					. '<td><a href="' . $dir . '/' . $tag . '.7.csv">'
					. '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-binary" viewBox="0 0 16 16"><path d="M7.05 11.885c0 1.415-.548 2.206-1.524 2.206C4.548 14.09 4 13.3 4 11.885c0-1.412.548-2.203 1.526-2.203.976 0 1.524.79 1.524 2.203zm-1.524-1.612c-.542 0-.832.563-.832 1.612 0 .088.003.173.006.252l1.559-1.143c-.126-.474-.375-.72-.733-.72zm-.732 2.508c.126.472.372.718.732.718.54 0 .83-.563.83-1.614 0-.085-.003-.17-.006-.25l-1.556 1.146zm6.061.624V14h-3v-.595h1.181V10.5h-.05l-1.136.747v-.688l1.19-.786h.69v3.633h1.125z"/><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/></svg></a></td>'
					. '<td><a href="/nkp/data.php?tag=' . $tag . '&seven=1&new=1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16"><path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/></svg></a></td>'
					. '<td>' . $has_seven . '</td>'
					. '<td>'. round($has_seven/($has_seven + $has_no_seven)*100) . '%</td>';
				} else {
					echo'<tr><td scope="row" class="align-middle"><b>' . $tag . '</b></td><td colspan="3"></td><td>0%</td>';
				}
				if (!empty($has_no_seven)) {
					echo '<td><a href="' . $dir . '/' . $tag . '.csv">'
					. '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-binary" viewBox="0 0 16 16"><path d="M7.05 11.885c0 1.415-.548 2.206-1.524 2.206C4.548 14.09 4 13.3 4 11.885c0-1.412.548-2.203 1.526-2.203.976 0 1.524.79 1.524 2.203zm-1.524-1.612c-.542 0-.832.563-.832 1.612 0 .088.003.173.006.252l1.559-1.143c-.126-.474-.375-.72-.733-.72zm-.732 2.508c.126.472.372.718.732.718.54 0 .83-.563.83-1.614 0-.085-.003-.17-.006-.25l-1.556 1.146zm6.061.624V14h-3v-.595h1.181V10.5h-.05l-1.136.747v-.688l1.19-.786h.69v3.633h1.125z"/><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/></svg></a></td>'
					. '<td><a href="/nkp/data.php?tag=' . $tag . '&seven=0&new=1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16"><path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/></svg></a></td>'
					. '<td>' . $has_no_seven . '</td>'
					. '<td>'. round($has_no_seven/($has_seven + $has_no_seven)*100) . '%</td></tr>';
				} else {
					echo'<td colspan="3"></td><td>0%</td></tr>';
				}
			}
			
			echo '</tbody><tfoot><tr><td scope="col" colspan="3" class="text-start"><b>Celkem</b></td>'
			. '<td scope="col"><b>' . $seven . '</b></td>'
			. '<td scope="col"><b>' . round($seven/($seven + $no_seven)*100) . '%</b></td>'
			. '<td scope="col" colspan="2"></td>'
			. '<td scope="col"><b>' . $no_seven . '</b></td>'
			. '<td scope="col"><b>' . round($no_seven/($seven + $no_seven)*100) . '%</b></td></tr>'
			. '</tfoot></table>';
		}

		# OLD	

		$old = array_filter(scandir($dir), function ($var) { return preg_match('/\d{3}.old.*/', $var); } );
		$tags = array_unique(array_map(function ($var) { return explode('.', $var)[0]; }, $old));
		$no_seven = 0;
		$seven = 0;

		if (!empty($tags)) {

			echo '<table class="table table-sm caption-top text-center"><caption>Záznamy založené před zvoleným datem</caption>';
			echo '<thead class="table-light"><tr><th scope="col">#</th><th scope="col" colspan="4">Podpole 7</th>';
			echo '<th scope="col" colspan="4">Bez podpole 7</th></tr></thead><tbody>';

			foreach ($tags as $tag)	{
				$has_seven = 0;
				$has_no_seven = 0;
				if(in_array($tag . '.old.7.csv', $old)) {
					$has_seven = getLines($dir . '/' . $tag . '.old.7.csv');
					$seven += $has_seven;
				}
				if(in_array($tag . '.old.csv', $old)) {
					$has_no_seven = getLines($dir . '/' . $tag . '.old.csv');
					$no_seven += $has_no_seven;
				}
				if (!empty($has_seven)) {

					echo'<tr><td scope="row" class="align-middle"><b>' . $tag . '</b></td>'
					. '<td><a href="' . $dir . '/' . $tag . '.old.7.csv">'
					. '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-binary" viewBox="0 0 16 16"><path d="M7.05 11.885c0 1.415-.548 2.206-1.524 2.206C4.548 14.09 4 13.3 4 11.885c0-1.412.548-2.203 1.526-2.203.976 0 1.524.79 1.524 2.203zm-1.524-1.612c-.542 0-.832.563-.832 1.612 0 .088.003.173.006.252l1.559-1.143c-.126-.474-.375-.72-.733-.72zm-.732 2.508c.126.472.372.718.732.718.54 0 .83-.563.83-1.614 0-.085-.003-.17-.006-.25l-1.556 1.146zm6.061.624V14h-3v-.595h1.181V10.5h-.05l-1.136.747v-.688l1.19-.786h.69v3.633h1.125z"/><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/></svg></a></td>'
					. '<td><a href="/nkp/data.php?tag=' . $tag . '&seven=1&new=0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16"><path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/></svg></a></td>'
					. '<td>' . $has_seven . '</td>'
					. '<td>'. round($has_seven/($has_seven + $has_no_seven)*100) . '%</td>';
				} else {
					echo'<tr><td scope="row" class="align-middle"><b>' . $tag . '</b></td><td colspan="3"></td><td>0%</td>';
				}
				if (!empty($has_no_seven)) {
					echo '<td><a href="' . $dir . '/' . $tag . '.old.csv">'
					. '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-binary" viewBox="0 0 16 16"><path d="M7.05 11.885c0 1.415-.548 2.206-1.524 2.206C4.548 14.09 4 13.3 4 11.885c0-1.412.548-2.203 1.526-2.203.976 0 1.524.79 1.524 2.203zm-1.524-1.612c-.542 0-.832.563-.832 1.612 0 .088.003.173.006.252l1.559-1.143c-.126-.474-.375-.72-.733-.72zm-.732 2.508c.126.472.372.718.732.718.54 0 .83-.563.83-1.614 0-.085-.003-.17-.006-.25l-1.556 1.146zm6.061.624V14h-3v-.595h1.181V10.5h-.05l-1.136.747v-.688l1.19-.786h.69v3.633h1.125z"/><path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2zM9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/></svg></a></td>'
					. '<td><a href="/nkp/data.php?tag=' . $tag . '&seven=0&new=0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16"><path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/></svg></a></td>'
					. '<td>' . $has_no_seven . '</td>'
					. '<td>'. round($has_no_seven/($has_seven + $has_no_seven)*100) . '%</td></tr>';
				} else {
					echo'<td colspan="3"></td><td>0%</td></tr>';
				}
			}
			
			echo '</tbody><tfoot><tr><td scope="col" colspan="3" class="text-start"><b>Celkem</b></td>'
			. '<td scope="col"><b>' . $seven . '</b></td>'
			. '<td scope="col"><b>' . round($seven/($seven + $no_seven)*100) . '%</b></td>'
			. '<td scope="col" colspan="2"></td>'
			. '<td scope="col"><b>' . $no_seven . '</b></td>'
			. '<td scope="col"><b>' . round($no_seven/($seven + $no_seven)*100) . '%</b></td></tr>'
			. '</tfoot></table>';
		}

		if (empty($new) and empty($old)) {
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

