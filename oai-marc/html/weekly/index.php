<?php

session_start();

$_SESSION['page'] = 'weekly';

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
<table><tr><td><img src="/sova.png"><td><td>Kontrola Aleph protokolem OAI-PMH.</td></tr></table>
<p><hr width="500"></p>
<form method='post' action='.' enctype='multipart/form-data'>

<?php

$default = date("Y-m-d", strtotime("Tuesday"));
if (!empty($_POST['date'])){ $default = $_POST['date']; }

echo "<input type='date' name='date' value='" . $default . "' max='" . date("Y-m-d", strtotime("Tuesday")) . "' step='7'>\n";

?>

<input type='submit' value='Zobrazit'>
</form>
<table>

<?php

if (!empty($_POST['date'])){
	if (preg_match('/\d{4}-\d{2}-\d{2}/', $_POST['date'])) {

		$file = 'data/'
		. date("Y-m-d", strtotime($_POST['date']) - 8*24*3600) . '_'
		. date("Y-m-d", strtotime($_POST['date']) - 2*24*3600) . '.csv';

		if (($csv = fopen($file, "r")) !== FALSE) {
	
			echo "<table>\n";

			while (($data = fgetcsv($csv, 1000, ";")) !== FALSE) {
				echo "<tr><td><a target='_blank' href='" . "https://aleph22.lib.cas.cz/F/?func=direct&doc_number="
					. $data[0] . "&local_base=AV" . "'><b>" . $data[0] . "</b></a></td><td align='right'>"
					. "" . $data[1] . "</td><td>" . "[<a href='../error/#" . $data[2] . "'><b>" . $data[2] . "</b></a>"
					. "]</td><td>" . $data[3] . "</td></tr>\n";
			}
			fclose($csv);
			echo "</table>\n";
			
		}
	}
}

?>
<p><hr width="500"></p>
</div>
</body>
</html>
