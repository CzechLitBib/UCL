<?php

session_start();

$_SESSION['page'] = 'clo';

if(empty($_SESSION['auth']) or $_SESSION['group'] !== 'admin') {
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
<table><tr><td><img src="/sova.png"></td><td>CLO / Statistika podpole 7.</td></tr>
</table>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>

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

$dir = 'data';

$files = array_filter(scandir($dir), function ($var) { return preg_match('/\d{3}.*/', $var); } );
$tags = array_unique(array_map(function ($var) { return explode('.', $var)[0]; }, $files));

$no_seven = 0;
$seven = 0;

if (!empty($tags)) {
	echo '<p><u>Poslední záznam</u>: 27.11.2020</p>';
	echo "<table width='500' style='border-collapse: collapse;' border='1px'>"
	. "<td></td><td colspan='4' align='center'><b>Podpole 7</b></td>"
	. "<td colspan='4' align='center'><b>Bez podpole 7</b></td></tr>";
	foreach ($tags as $tag)	{

		$has_seven = 0;
		$has_no_seven = 0;

		if(in_array($tag . '.7.csv', $files)) {
			$has_seven = getLines($dir . '/' . $tag . '.7.csv');
			$seven += $has_seven;
		}
		if(in_array($tag . '.csv', $files)) {
			$has_no_seven = getLines($dir . '/' . $tag . '.csv');
			$no_seven += $has_no_seven;
		}
		if (!empty($has_seven)) {
			echo '<tr><td align="center"><b>'  . $tag . ' </b></td>'
			. '<td align="center"><a href="' . $dir . '/' . $tag . '.7.csv">CSV</a></td>'
			. '<td align="right">' . $has_seven . '</td>'
			. '<td align="center"><a href="' . $dir . '/' . $tag . '.7.stat.csv">STAT</a></td>'
			. '<td align="right">' . round($has_seven/($has_seven + $has_no_seven)*100) . '%</td>';
		} else {
			echo '<tr><td align="center"><b>'
			. $tag . '</b></td><td></td><td align="right">0</td><td></td><td align="right">0%</td>';
		}
		if (!empty($has_no_seven)) {
			echo '<td align="center"><a href="' . $dir . '/' . $tag . '.csv">CSV</a></td>'
			. '<td align="right">' . $has_no_seven . '</td>'
			. '<td align="center"><a href="' . $dir . '/' . $tag . '.stat.csv">STAT</a></td>'
			. '<td align="right">' . round($has_no_seven/($has_seven + $has_no_seven)*100) . '%</td></tr>';
		} else {
			echo "<td></td><td align='right'>0</td><td></td><td align='right'>0%</td></tr>\n";
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

?>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<table width='500'><tr><td width="450" align="right"><a href="/main"><img src="/back.png"></a></td></tr></table>
</div>
</body>
</html>

