<?php

session_start();

if(empty($_SESSION['auth']) or !in_array($_SESSION['group'], array('admin','data'))) {
	header('Location: /');
	exit();
}

$_SESSION['page'] = 'form-data';

$from = 'xxx';
$target = 'xxx';
$server = 'xxx';

if (!isset($_GET['id'])) {
	header('HTTP/1.0 400 Bad Request', true, 400);
	exit();
}

$db = new SQLite3('form.db');

if ($db) {
	if (isset($_GET['id']) and isset($_GET['type'])) {
		$query = $db->exec("UPDATE " . $_GET['type'] . " SET done = 1 WHERE id = '". $_GET['id'] . "';");
		if (!$query) {
			header('HTTP/1.0 304 Not Modified', true, 304);
			exit();
		} else {
			$data = $db->querySingle("SELECT * FROM " . $_GET['type'] .  " WHERE id = '" . $_GET['id'] . "';", True);

			print_r($data);

			$headers="MIME-Version: 1.0\r\n";
			$headers.="From: UCL Vyvoj <".$from.">\r\n";
			$headers.="Reply-To: ".$from."\r\n";
			$headers.="Content-type: text/html; charset=utf-8\r\n";

			$subject="=?utf-8?B?".base64_encode("Formulář - Data")."?=";

			$text='<html><head><meta charset="utf-8"></head><body><br>Dobrý den,<br><br>';

			if ($_GET['type'] == 'article') { $text.='Byl zpracován článek:<br><br>'; }
			if ($_GET['type'] == 'chapter') { $text.='Byla zpracována nová kapitola v knize:<br><br>'; }
			if ($_GET['type'] == 'book') { $text.='Byla zpracována nová kniha:<br><br>'; }

			if(!empty($data)) { $text.=$data['author'] . ' ' . $data['name']; }

			$text.='<br><br><a target="_blank" href="https://vyvoj.ucl.cas.cz/form-data/">https://vyvoj.ucl.cas.cz/form-data/<a>
			<br><br>--------------------------------<br>TATO ZPRÁVA BYLA VYGENEROVÁNA AUTOMATICKY, NEODPOVÍDEJTE NA NI.
			</body></html>';

			mail($target, $subject, $text, $headers, '-f '.$from);
		}
	}
}

?>

