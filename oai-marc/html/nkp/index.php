<?php

session_start();

$_SESSION['page'] = 'nkp';

if(empty($_SESSION['auth']) or ($_SESSION['group'] !== 'nkp' and $_SESSION['group'] !== 'admin')) {
	header('Location: /');
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

<html>
<head>
<style>
	body	{background-color: lightgrey;}
	a	{text-decoration: none; color: black;}
</style>
</head>
<body>
<div align="center">
<table><tr><td><img src="/sova.png"></td><td> NKP / Statistika podpole 7.</td></tr>
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
	if ($mon == $_SESSION['nkp_month']) {
		echo "<option selected>" . $mon . "</option>\n";
	} elseif (empty($_SESSION['nkp_month']) and $m == date('m', strtotime("-1 month"))) {
		echo "<option selected>" . $mon . "</option>\n";
	} else {
		echo "<option>" . $mon  . "</option>\n";
	}
}

echo "</select>\n";

echo "<label for='rok'>Rok: </label><select id='year' name='year'>\n";

foreach (range(2020,  date('Y', strtotime("-1 month"))) as $y) {
	if ($y == $_SESSION['nkp_year']) {
		echo "<option selected>" . $y . "</option>\n";
	} elseif (empty($_SESSION['nkp_year']) and $y == date('Y', strtotime("-1 month"))) {
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
	
		$dir =  'data/' . $_SESSION['nkp_year'] . '/' . array_search($_SESSION['nkp_month'],$month_map);

		# NEW

		$new = array_filter(scandir($dir), function ($var) { return preg_match('/\d{3}.(?!old).*/', $var); } );
		$tags = array_unique(array_map(function ($var) { return explode('.', $var)[0]; }, $new));
		$no_seven = 0;
		$seven = 0;

		if (!empty($tags)) { 
			echo "<u>Záznamy založené ve zvoleném datu.</u><br><br>";

			echo "<table width='600' style='border-collapse: collapse;' border='1px'>"
			. "<td></td><td colspan='5' align='center'><b>Podpole 7</b></td>"
			. "<td colspan='5' align='center'><b>Bez podpole 7</b></td></tr>";
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
					echo '<tr><td align="center"><b>'  . $tag . ' </b></td>'
					. '<td align="center"><a href="' . $dir . '/' . $tag . '.7.csv">CSV</a></td>'
					. '<td align="right">' . $has_seven . '</td>'
					. '<td align="center"><a href="' . $dir . '/' . $tag . '.7.stat.csv">STAT</a></td>'
					. '<td align="center"><a href="data.php?tag='. $tag . '&seven=1&new=1">HTML</a></td>'
					. '<td align="right">' . round($has_seven/($has_seven + $has_no_seven)*100) . '%</td>';
				} else {
					echo '<tr><td align="center"><b>'
					. $tag . '</b></td><td></td><td align="right">0</td><td></td><td align="right">0%</td>';
				}
				if (!empty($has_no_seven)) {
					echo '<td align="center"><a href="' . $dir . '/' . $tag . '.csv">CSV</a></td>'
					. '<td align="right">' . $has_no_seven . '</td>'
					. '<td align="center"><a href="' . $dir . '/' . $tag . '.stat.csv">STAT</a></td>'
					. '<td align="center"><a href="data.php?tag='. $tag . '&seven=0&new=1">HTML</a></td>'
					. '<td align="right">' . round($has_no_seven/($has_seven + $has_no_seven)*100) . '%</td></tr>';
				} else {
					echo "<td></td><td align='right'>0</td><td></td><td></td><td align='right'>0%</td></tr>\n";
				}
			}
			echo '</table><br>';
		
			echo '<table width="250">';
			echo '<tr><td align="right">Podpole 7</td><td align="right">'. $seven . '</td>'
			. '<td align="right">' . round($seven/($seven + $no_seven)*100) . "%</td></tr>\n";
			echo '<tr><td align="right">Bez Podpole 7</td><td align="right">'. $no_seven . '</td>'
			. '<td align="right">' . round($no_seven/($seven + $no_seven)*100) . "%</td></tr>\n";
			echo '<tr><td align="right">Celkem</td><td align="right">'. ($seven + $no_seven) . "</td></tr>\n";
			echo '</table>';
		}

		# OLD	
	
		$old = array_filter(scandir($dir), function ($var) { return preg_match('/\d{3}.old.*/', $var); } );
		$tags = array_unique(array_map(function ($var) { return explode('.', $var)[0]; }, $old));
		$no_seven = 0;
		$seven = 0;

		if (!empty($tags)) {
			if (!empty($new)) { echo '<br>'; }
			echo "<u>Záznamy založené před zvoleným datem.</u><br><br>";

			echo "<table width='600' style='border-collapse: collapse;' border='1px'>"
			. "<td></td><td colspan='4' align='center'><b>Podpole 7</b></td>"
			. "<td colspan='4' align='center'><b>Bez podpole 7</b></td></tr>";
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
					echo '<tr><td align="center"><b>'  . $tag . ' </b></td>'
					. '<td align="center"><a href="' . $dir . '/' . $tag . '.old.7.csv">CSV</a></td>'
					. '<td align="right">' . $has_seven . '</td>'
					. '<td align="center"><a href="' . $dir . '/' . $tag . '.old.7.stat.csv">STAT</a></td>'
					. '<td align="center"><a href="data.php?tag='. $tag . '&seven=1&new=0">HTML</a></td>'
					. '<td align="right">' . round($has_seven/($has_seven + $has_no_seven)*100) . '%</td>';
				} else {
					echo '<tr><td align="center"><b>'
					. $tag . '</b></td><td></td><td align="right">0</td><td></td><td align="right">0%</td>';
				}
				if (!empty($has_no_seven)) {
					echo '<td align="center"><a href="' . $dir . '/' . $tag . '.old.csv">CSV</a></td>'
					. '<td align="right">' . $has_no_seven . '</td>'
					. '<td align="center"><a href="' . $dir . '/' . $tag . '.old.stat.csv">STAT</a></td>'
					. '<td align="center"><a href="data.php?tag='. $tag . '&seven=0&new=0">HTML</a></td>'
					. '<td align="right">' . round($has_no_seven/($has_seven + $has_no_seven)*100) . '%</td></tr>';
				} else {
					echo "<td></td><td align='right'>0</td><td></td><td></td><td align='right'>0%</td></tr>\n";
				}
			}
			echo '</table><br>';
	
			echo '<table width="250">';
			echo '<tr><td align="right">Podpole 7</td><td align="right">'. $seven . '</td>'
			. '<td align="right">' . round($seven/($seven + $no_seven)*100) . "%</td></tr>\n";
			echo '<tr><td align="right">Bez Podpole 7</td><td align="right">'. $no_seven . '</td>'
			. '<td align="right">' . round($no_seven/($seven + $no_seven)*100) . "%</td></tr>\n";
			echo '<tr><td align="right">Celkem</td><td align="right">'. ($seven + $no_seven) . "</td></tr>\n";
			echo '</table>';
		}

		if (empty($new) and empty($old)) { echo "<font color='red'>Žádná data.</font>\n"; }
	}
}

?>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
</div>
</body>
</html>

