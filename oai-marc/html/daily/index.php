<?php

session_start();

$_SESSION['page'] = 'daily';

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
<table><tr><td><img src="/sova.png"></td><td>Kontrola Aleph protokolem OAI-PMH.</td></tr>
</table>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<form method='post' action='.' enctype='multipart/form-data'>

<?php

$default = date("Y-m-d", strtotime("-1 day"));
if (!empty($_POST['date'])) { $default = $_POST['date']; }

echo "<input type='date' name='date' value='" . $default . "' min='2020-03-02' max='" . date("Y-m-d", strtotime("-1 day")) . "'>\n";

?>

<input type='submit' value='Zobrazit'>
</form>

<?php

if (!empty($_POST['date'])){
	if (preg_match('/\d{4}-\d{2}-\d{2}/', $_POST['date'])) {
	
		$file =  'data/' . preg_replace('/(\d{4})-(\d{2})-\d{2}/', '${1}/${2}', $_POST['date']) . '/' . $_POST['date'] . '.csv';
		
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
		} else {
			echo "<font color='red'>Žádná data.</font>\n";
		}
	}
}

?>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
</div>
</body>
</html>

