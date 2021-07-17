<?php

session_start();

$_SESSION['page'] = 'kat';

if(empty($_SESSION['auth'])) {
	header('Location: /');
	exit();
}

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
<p><hr width="500"></p>
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
	if (preg_match('/\d{4}-\d{2}/', $_POST['date'])) {
	
		$file =  'data/' . preg_replace('/(\d{4})-(\d{2})/', '${1}/${2}', $_POST['date']) . '/' . $_POST['date'] . '.json';
		
		if (($json = fopen($file, "r")) !== FALSE) {
			#print_r(json.decode(fread($data)));
			$data = json.decode(fread($json));
			fclose($json);
			echo "<table>\n";
			# header
			echo '<tr><td></td><td>SIF</td><td>KAT</td><td>SIF+KAT</td>';
			foreach ($data as $sif) { echo '<td>' . $sif . '</td>';	}
			echo "</tr>\n";
			# line
			foreach ($data as $sif) {
				echo '<tr><td>' . $sif . '</td><td>' . $sif['sif_count'] . '</td><td>' . $sif['cat_count'] . '</td><td>' . $sif['sif_cat_count'] . '</td>';
				foreach(array_values($sif['other']) as $other) {
					echo '<td>' . $other . '</td>';
				}
				echo "</tr>\n";
			}
			echo "</table>\n";
		}
	}
	$_SESSION['kat_month'] = $_POST['month'];
	$_SESSION['kat_year'] = $_POST['year'];
}

?>

<p><hr width="500"></p>
</div>
</body>
</html>

