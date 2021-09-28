<?php

session_start();

if(empty($_SESSION['auth']) or !in_array($_SESSION['group'], array('admin','data'))) {
	header('Location: /');
	exit();
}

$_SESSION['page'] = 'form-data';

if (!isset($_GET['id'])) {
	header('HTTP/1.0 400 Bad Request', true, 400);
	exit();
}

header("Content-type: application/octet-stream");
header("Content-disposition: attachment;filename=" . $_GET['id'] . ".csv");

$db = new SQLite3('form.db');

if ($db) {
	if (isset($_GET['id']) and isset($_GET['type'])) {
		$data = $db->query("SELECT * FROM " . $_GET['type'] . " WHERE ID = '". $_GET['id'] . "';)");
		if ($data) {
			$buff='';
			$row = $data->fetchArray(1);// ASSOC
			if ($_GET['type'] == 'article') {
				$buff.="ID;Autor;Jméno;Zdroj;Citace;Poznámka;Odkaz;Email;Veřejný;Zpracováno\n";
				$buff.= implode(';', $row);
			}
			if ($_GET['type'] == 'chapter') {
				$buff.="ID;Autor kapitoly;Jméno kapitoly;Autor;Jméno;Místo;Nakladatel;Rok;Poznámka;Odkaz;Email;Veřejný;Zpracováno\n";
				$buff.= implode(';', $row);
			}
			if ($_GET['type'] == 'book') {
				$buff.="ID;Autor;Jméno;Místo;Nakladatel;Rok;Poznámka;Odkaz;Email;Veřejný;Zpracováno\n";
				$buff.= implode(';', $row);
			}
			echo $buff;
		}
	}
}

?>

