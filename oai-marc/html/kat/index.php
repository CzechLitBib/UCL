<?php

session_start();

$_SESSION['page'] = 'kat';

if(empty($_SESSION['auth']) or $_SESSION['group'] !== 'admin') {
	header('Location: /');
	exit();
}

if (!isset($_SESSION['kat_month'])) { $_SESSION['kat_month'] = null; }
if (!isset($_SESSION['kat_year'])) { $_SESSION['kat_year'] = null; }

?>

<html>
<head>
<style>
	body	{background-color: lightgrey;}
	a	{text-decoration: none; color: black;}
</style>
</head>
<body>
<div align="center">
<table><tr><td><img src="/sova.png"></td><td>Statistika pole CAT/KAT.</td></tr>
</table>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<form method='post' action='.' enctype='multipart/form-data'>

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

echo "<label for='month'>Měsíc: </label><select id='month' name='month'>\n";

foreach($month_map as $m => $mon) {
	if ($mon == $_SESSION['kat_month']) {
		echo "<option selected>" . $mon . "</option>\n";
	} elseif ($m == date('m', strtotime("-1 month"))) {
		echo "<option selected>" . $mon . "</option>\n";
	} else {
		echo "<option>" . $mon  . "</option>\n";
	}
}

echo "</select>\n";

echo "<label for='rok'>Rok: </label><select id='year' name='year'>\n";

foreach (range(2021,  date('Y', strtotime("-1 month"))) as $y) {
	if ($y == $_SESSION['kat_year']) {
		echo "<option selected>" . $y . "</option>\n";
	} elseif ($y == date('Y', strtotime("-1 month"))) {
		echo "<option selected>" . $y . "</option>\n";
	} else {
		echo "<option>" . $y . "</option>\n";
	}
}

echo "</select>\n";

?>

<input type='submit' value='Zobrazit'>
</form>

<?php

if (!empty($_POST['month']) and  !empty($_POST['year'])){
	if (preg_match('/\d{2}/', array_search($_POST['month'], $month_map)) and preg_match('/\d{4}/', $_POST['year'])) {
	
		$file =  'data/' . $_POST['year'] . '/' . array_search($_POST['month'],$month_map) . '/data.json';

		if (file_exists($file)) {
			$data = json_decode(file_get_contents($file), true);
			echo "<table style='border-collapse: collapse;' border='1px'>\n";
			# header
			echo '<tr><td></td><td><b>SIF</b></td><td><b>KAT</b></td><td><b>SIF+KAT</b></td>';
			foreach (array_keys($data) as $sif) { echo '<td width="35"><b>' . $sif . '</b></td>';	}
			echo "</tr>\n";
			# line
			foreach (array_keys($data) as $sif) {
				echo '<tr><td><b>' . $sif . '</b></td><td>' . $data[$sif]['sif_count'] . '</td><td>' . $data[$sif]['cat_count'] . '</td><td>' . $data[$sif]['sif_cat_count'] . '</td>';
				foreach (array_keys($data) as $other) {
					if (!array_key_exists($other, $data[$sif]['other'])) {
						echo '<td>0</td>';
					} else { echo '<td>'. $data[$sif]['other'][$other] . '</td>'; }
				}
				echo "</tr>\n";
			}
			echo "</table>\n";

			$_SESSION['kat_month'] = $_POST['month'];
			$_SESSION['kat_year'] = $_POST['year'];
		}

	}
}

?>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
</div>
</body>
</html>

