<?php

session_start();

if(empty($_SESSION['auth']) or $_SESSION['group'] !== 'admin') {
	header('Location: /');
	exit();
}

$_SESSION['page'] = 'seven';

if(!isset($_SESSION['seven_month'])) { $_SESSION['seven_month'] = Null; }
if(!isset($_SESSION['seven_year'])) { $_SESSION['seven_year'] = Null; }

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
<table><tr><td><img src="/sova.png"></td><td>Statistika podpole 7.</td></tr>
</table>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>

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

if (!empty($_SESSION['seven_month']) and !empty($_SESSION['seven_year'])) {
	if (isset($_GET['tag']) and isset($_GET['seven']) and isset($_GET['new'])) {
		
		$fn = $_GET['tag'];
		if (!$_GET['new']) { $fn .= '.old'; }
		if ($_GET['seven']) { $fn .= '.7'; }
		$fn .= '.stat.csv';	

		$file = 'data/'	. $_SESSION['seven_year'] . '/' . array_search($_SESSION['seven_month'],$month_map) . '/' . $fn;

		if (file_exists($file)) {
			$csv = array();
			$row = 0;
			if (($handle = fopen($file, 'r')) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, '|')) !== FALSE) {
					$num = count($data);
					for ($c=0;  $c < $num; $c++) {
						$csv[$row][] = $data[$c];
					}
					$row++;
				}
				fclose($handle);
			}
			echo '<table>';
			foreach($csv as $row) {
				echo '<tr><td width="50"><b>' . $row[0] . '</b></td><td>' . $row[2] . '</td></tr>';
			}
			echo '</table>';

		} else {
			echo "<font color='red'>Žádná data.</font>";
		}
	}
}

?>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<table width='500'><tr><td width="450" align="right"><a href="/seven"><img src="/back.png"></a></td></tr></table>
</div>
</body>
</html>

