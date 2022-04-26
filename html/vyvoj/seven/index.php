<?php

session_start();

$_SESSION['page'] = 'seven';

if(empty($_SESSION['auth'])) {
	header('Location: /vyvoj');
	exit();
}

if($_SESSION['group'] !== 'admin') {
	$_SESSION['error'] = True;
	header('Location: /vyvoj/main/');
	exit();
}


if(!isset($_SESSION['seven_month'])) { $_SESSION['seven_month'] = Null; }
if(!isset($_SESSION['seven_year'])) { $_SESSION['seven_year'] = Null; }

if (!empty($_POST['month']) and !empty($_POST['year'])) {
        $_SESSION['seven_month'] = $_POST['month'];
        $_SESSION['seven_year'] = $_POST['year'];
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

</head>

<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#dc3545;">
	<div class="container-fluid">
		<a class="navbar-brand" href="/vyvoj/main">
		<img src="../logo.png" alt="ČLB" width="60" height="35" class="d-inline-block align-text-center">Vývoj # 7</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
			</ul>
			<span clas="navbar-text">Username</span>
			<form class="d-flex align-items-center">
			<img class="d-inline-block align-text-center mx-2" src="../icons/person-fill.svg" alt="User" width="32" height="32"> 
			</form>
		</div>
	</div>
</nav>
   
<main class="container">
<div class="row my-4 justify-content-center">
<div class="col col-md-8">

<form class="mb-4"  method="post" action="." enctype="multipart/form-data">
	<div class="row justify-content-center">
		<div class="col col-5">
			<div class="form-floating">
			<select class="form-select" id="floatingselect" name="month" aria-label="floating label select example">

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
	if ($mon == $_SESSION['seven_month']) {
		echo "<option selected>" . $mon . "</option>";
	} elseif (empty($_SESSION['seven_month']) and $m == date('m', strtotime("-1 month"))) {
		echo "<option selected>" . $mon . "</option>";
	} else {
		echo "<option>" . $mon  . "</option>";
	}
}

?>

			</select>
			<label for="floatingselect">Měsíc</label>
		</div>
		</div>
		<div class="col col-5">
			<div class="form-floating">
			<select class="form-select" id="floatingselect" name="year" aria-label="floating label select example">

<?php

foreach (range(2020,  date('Y', strtotime("-1 month"))) as $y) {
	if ($y == $_SESSION['seven_year']) {
		echo "<option selected>" . $y . "</option>";
	} elseif (empty($_SESSION['seven_year']) and $y == date('Y', strtotime("-1 month"))) {
		echo "<option selected>" . $y . "</option>";
	} else {
		echo "<option>" . $y . "</option>";
	}
}

?>
	
			</select>
			<label for="floatingselect">Rok</label>
		</div>
		</div>
		<div class="col col-2 align-self-center">
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

if (!empty($_SESSION['seven_month']) and !empty($_SESSION['seven_year'])) {
	if (preg_match('/\d{2}/', array_search($_SESSION['seven_month'], $month_map)) and preg_match('/\d{4}/', $_SESSION['seven_year'])) {
	
		$dir =  'data/' . $_SESSION['seven_year'] . '/' . array_search($_SESSION['seven_month'],$month_map);

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

					echo'<tr><th scope="row" class="align-middle">' . $tag . '</th>'
					. '<td><a href="' . $dir  . '/' . $tag .  '.7.csv">'
					. '<img class="bi text-muted flex-shrink-0 me-3" src="../icons/file-earmark-binary.svg" alt="CSV" width="24" height="24"></a></td>'
					. '<td><a href="/vyvoj/seven/data/' . $tag . '/"><img class="bi text-muted flex-shrink-0 me-3" src="../icons/bar-chart.svg" alt="STAT" width="24" height="24"></a></td>'
					. '<td>' . $has_seven . '</td>'
					. '<td>'. round($has_seven/($has_seven + $has_no_seven)*100) . '%</td>';
				} else {
					echo'<tr><th scope="row" class="align-middle">' . $tag . '</th><td colspan="3"></td><td>0%</td>';
				}
				if (!empty($has_no_seven)) {
					echo '<td><a href="' . $dir  . '/' . $tag .  '.csv">'
					. '<img class="bi text-muted flex-shrink-0 me-3" src="../icons/file-earmark-binary.svg" alt="CSV" width="24" height="24"></a></td>'
					. '<td><a href="/vyvoj/seven/data/' . $tag . '/"><img class="bi text-muted flex-shrink-0 me-3" src="../icons/bar-chart.svg" alt="STAT" width="24" height="24"></a></td>'
					. '<td>' . $has_no_seven . '</td>'
					. '<td>'. round($has_no_seven/($has_seven + $has_no_seven)*100) . '%</td></tr>';
				} else {
					echo'<td colspan="3"></td><td>0%</td></tr>';
				}
			}
			
			echo '</tbody><tfoot><tr><th scope="col" colspan="3" class="text-start">Celkem</th>'
			. '<th scope="col">' . $seven . '</th>'
			. '<th scope="col">' . round($seven/($seven + $no_seven)*100) . '%</th>'
			. '<th scope="col" colspan="2"></th>'
			. '<th scope="col">' . $no_seven . '</th>'
			. '<th scope="col">' . round($no_seven/($seven + $no_seven)*100) .  '%</th></tr>'
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

					echo'<tr><th scope="row" class="align-middle">' . $tag . '</th>'
					. '<td><a href="' . $dir  . '/' . $tag .  '.old.7.csv">'
					. '<img class="bi text-muted flex-shrink-0 me-3" src="../icons/file-earmark-binary.svg" alt="CSV" width="24" height="24"></a></td>'
					. '<td><a href="/vyvoj/seven/data/' . $tag . '/"><img class="bi text-muted flex-shrink-0 me-3" src="../icons/bar-chart.svg" alt="STAT" width="24" height="24"></a></td>'
					. '<td>' . $has_seven . '</td>'
					. '<td>'. round($has_seven/($has_seven + $has_no_seven)*100) . '%</td>';
				} else {
					echo'<tr><th scope="row" class="align-middle">' . $tag . '</th><td colspan="3"></td><td>0%</td>';
				}
				if (!empty($has_no_seven)) {
					echo '<td><a href="' . $dir  . '/' . $tag .  '.old.csv">'
					. '<img class="bi text-muted flex-shrink-0 me-3" src="../icons/file-earmark-binary.svg" alt="CSV" width="24" height="24"></a></td>'
					. '<td><a href="/vyvoj/seven/data/' . $tag . '/"><img class="bi text-muted flex-shrink-0 me-3" src="../icons/bar-chart.svg" alt="STAT" width="24" height="24"></a></td>'
					. '<td>' . $has_no_seven . '</td>'
					. '<td>'. round($has_no_seven/($has_seven + $has_no_seven)*100) . '%</td></tr>';
				} else {
					echo'<td colspan="3"></td><td>0%</td></tr>';
				}
			}
			
			echo '</tbody><tfoot><tr><th scope="col" colspan="3" class="text-start">Celkem</th>'
			. '<th scope="col">' . $seven . '</th>'
			. '<th scope="col">' . round($seven/($seven + $no_seven)*100) . '%</th>'
			. '<th scope="col" colspan="2"></th>'
			. '<th scope="col">' . $no_seven . '</th>'
			. '<th scope="col">' . round($no_seven/($seven + $no_seven)*100) .  '%</th></tr>'
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

