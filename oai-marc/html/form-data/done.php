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

$db = new SQLite3('form.db');

if ($db) {
	if (isset($_GET['id']) and isset($_GET['type'])) {
		$query = $db->exec("UPDATE " . $_GET['type'] . " SET done = 1 WHERE id = '". $_GET['id'] .  "';");
		if (!$query) {
			header('HTTP/1.0 304 Not Modified', true, 304);
			exit();
		}
	}
}

?>

