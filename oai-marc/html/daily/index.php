<?php

session_start();

$_SESSION['page'] = 'daily';

if(empty($_SESSION['auth']) or $_SESSION['group'] !== 'admin') {
	header('Location: /');
	exit();
}

if(!isset($_SESSION['daily'])) { $_SESSION['daily'] = Null; }

if (!empty($_POST['date'])) {
	$_SESSION['daily'] = $_POST['date'];
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
<table><tr><td><img src="/sova.png"></td><td>Kontrola Aleph protokolem OAI-PMH.</td></tr>
</table>
<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<form method='post' action='.' enctype='multipart/form-data'>

<?php

$default = date("Y-m-d", strtotime("-1 day"));
if (!empty($_SESSION['daily'])) { $default = $_SESSION['daily']; }

echo "<input type='date' name='date' value='" . $default . "' min='2020-03-02' max='" . date("Y-m-d", strtotime("-1 day")) . "'>\n";

?>

<input type='submit' value='Zobrazit'>
</form>

<?php

if (!empty($_SESSION['daily'])){
	if (preg_match('/\d{4}-\d{2}-\d{2}/', $_SESSION['daily'])) {
	
		$file =  'data/' . preg_replace('/(\d{4})-(\d{2})-.*/', '${1}/${2}', $_SESSION['daily']) . '/' . $_SESSION['daily'] . '.csv';
		
		if (file_exists($file)) {
			$csv = array();
			$row = 0;
			if (($handle = fopen($file, 'r')) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
					$num = count($data);
					for ($c=0;  $c < $num; $c++) {
						$csv[$row][] = $data[$c];
					}
					$row++;
				}
				fclose($handle);
			}
			echo "<table>\n";
			array_multisort(array_column($csv,0), SORT_DESC, SORT_NUMERIC, $csv);
			foreach($csv as $row) {
				echo "<tr><td><a target='_blank' href='" . "https://aleph22.lib.cas.cz/F/?func=direct&doc_number="
					. $row[0] . "&local_base=AV" . "'><b>" . $row[0] . "</b></a></td><td align='right'>"
					. "" . $row[1] . "</td><td>" . "[<a href='../error/#" . $row[2] . "'><b>" . $row[2] . "</b></a>"
					. "]</td><td>" . $row[3] . "</td></tr>\n";
			}
			echo "</table>\n";
		} else {
			echo "<font color='red'>Žádná data.</font>\n";
		}
	}
}

?>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<table width='500'><tr><td width="450" align="right"><a href="/main"><img src="/back.png"></a></td></tr></table>
</div>
</body>
</html>

