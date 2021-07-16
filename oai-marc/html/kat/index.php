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

$default = date("Y-m", strtotime("-1 day"));
if (!empty($_POST['date'])) { $default = $_POST['date']; }

echo "<input type='month' name='month' value='" . $default . "' min='2021-07' max='" . date("Y-m", strtotime("-1 day")) . "'>\n";

?>

<input type='submit' value='Zobrazit'>
</form>

<?php

if (!empty($_POST['date'])){
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
}

?>

<p><hr width="500"></p>
</div>
</body>
</html>

