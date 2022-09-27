<?php

session_start();

$_SESSION['page'] = 'form-data';

if(empty($_SESSION['auth'])) {
	header('Location: /');
	exit();
}

if(!in_array($_SESSION['group'], array('admin','form'))) {
        $_SESSION['error'] = True;
        header('Location: /main/');
        exit();
}

if (!isset($_GET['id'])) {
	header('HTTP/1.0 400 Bad Request', true, 400);
	exit();
}

header("Content-type: application/octet-stream; charset=UTF-8");
header("Content-disposition: attachment;filename=" . $_GET['id'] . ".txt");

$db = new SQLite3('/var/www/data/form/form.db');

if ($db) {
	if (isset($_GET['id']) and isset($_GET['type'])) {
		$data = $db->querySingle("SELECT * FROM data WHERE id = '". $_GET['id'] . "';)");
		if ($data) {
			$buff='';
			$row = $data->fetchArray(SQLITE3_ASSOC);
			if ($_GET['type'] == 'článek') {
				$buff.="id;author;name;source;quote;public;link;email;note\n";
				$buff.=implode(";", $row);
			}
			if ($_GET['type'] == 'část knihy') {
				$buff.="id;Autor kapitoly;Jméno kapitoly;Autor;Jméno;Místo;Nakladatel;Rok;Poznámka;Odkaz;Email;Veřejný;Zpracováno\n";
				$buff.=implode(";", $row);
			}
			if ($_GET['type'] == 'kniha') {
				$buff.="ID;Autor;Jméno;Místo;Nakladatel;Rok;Poznámka;Odkaz;Email;Veřejný;Zpracováno\n";
				$buff.=implode(";", $row);
			}
			echo $buff;
		}
	}
}

?>

