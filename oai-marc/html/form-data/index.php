<?php

session_start();

$_SESSION['page'] = 'form-data';

if(empty($_SESSION['auth']) or $_SESSION['group'] !== 'admin') {
	header('Location: /');
	exit();
}

if(!isset($_SESSION['form-data'])) { $_SESSION['form-data'] = Null; }

$error = '';

$apage = 1; // article page
$bpage = 1; // book page
$cpage = 1; // chapter page

$per_page = 5;

$db = new SQLite3('../form/db/form.db');

if (!$db) {
	$error = 'Chyba databáze.';
}
//	}
//	if ($_POST['type'] == 'chapter') {
//		$query = $db->exec("INSERT INTO chapter (id,author,name,src_author,src_name,src_place,src_publisher,src_year,note,link,public)"
//		. "VALUES ('"
//		. $id . "','"
//		. str_replace("'", '_', $_POST['chapter-author']) . "','"
//		. str_replace("'", '_', $_POST['chapter-name']) . "','"
//		. str_replace("'", '_', $_POST['chapter-src-author']) . "','"
//		. str_replace("'", '_', $_POST['chapter-src-name']) . "','"
//		. str_replace("'", '_', $_POST['chapter-src-place']) . "','"
//		. str_replace("'", '_', $_POST['chapter-src-publisher']) . "','"
//		. str_replace("'", '_', $_POST['chapter-src-year']) . "','"
//		. str_replace("'", '_', $_POST['note']) . "','"
//		. str_replace("'", '_', $_POST['link']) . "','"
//		. str_replace("'", '_', $_POST['public'])
//		. "');");
//		if (!$query) { $error = 'Chyba zápisu do databáze.'; }
//	}
//	if ($_POST['type'] == 'book') {
//		$query = $db->exec("INSERT INTO book (id,author,name,place,publisher,year,note,link,public)"
//		. "VALUES ('"
//		. $id . "','"
//		. str_replace("'", '_', $_POST['book-author']) . "','"
//		. str_replace("'", '_', $_POST['book-name']) . "','"
//		. str_replace("'", '_', $_POST['book-place']) . "','"
//		. str_replace("'", '_', $_POST['book-publisher']) . "','"
//		. str_replace("'", '_', $_POST['book-year']) . "','"
//		. str_replace("'", '_', $_POST['note']) . "','"
//		. str_replace("'", '_', $_POST['link']) . "','"
//		. str_replace("'", '_', $_POST['public'])
//		. "');");
//		if (!$query) { $error = 'Chyba zápisu do databáze.'; }
//	}
//	$query = $db->exec("INSERT INTO file (id,name) VALUES ('" . $id . "','". $fn . "');");
//	if (!$query) { $error = 'Chyba zápisu do databáze.'; }
//	$db->close();
//	}
//}

?>

<html>
<head>
<script>
	function on_load() {
		document.getElementById("article-div").style.display = "block";
		document.getElementById("chapter-div").style.display = "none";
		document.getElementById("book-div").style.display = "none";
	}
	function load_type() {
		if (document.getElementById('article').checked) {
			document.getElementById("article-div").style.display = "block";
			document.getElementById("chapter-div").style.display = "none";
			document.getElementById("book-div").style.display = "none";
		}
		if (document.getElementById('chapter').checked) {
			document.getElementById("article-div").style.display = "none";
			document.getElementById("chapter-div").style.display = "block";
			document.getElementById("book-div").style.display = "none";
		}
		if (document.getElementById('book').checked) {
			document.getElementById("article-div").style.display = "none";
			document.getElementById("chapter-div").style.display = "none";
			document.getElementById("book-div").style.display = "block";
		}
	}
	function display(id) {
		if (document.getElementById(id + '_data').style.display == "none") {
			document.getElementById(id + '_data').style.display = "block";
		} else {
			document.getElementById(id + '_data').style.display = "none";
		}
	}
</script>

</head>
<body bgcolor="lightgrey" onload="on_load()">
<div align="center">
<table><tr><td><img src="/sova.png"></td><td>Vstupní formulář - Data</td></tr></table>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>

<!--<form method="post" action="." enctype="multipart/form-data">--!>

<p><table><tr><td>
<input type="radio" name="type" id="article" value="article" onclick="load_type()" checked><label>Článek</label>
<input type="radio" name="type" id="chapter" value="chapter" onclick="load_type()"><label>Kapitola v knize</label>
<input type="radio" name="type" id="book" value="book" onclick="load_type()"><label>Kniha</label>
</td></tr></table></p>

<div id="article-div">

<?php
	if (isset($_GET['apage'])) {
		if (is_numeric(htmlspecialchars($_GET['apage']))) {
			if ($_GET['apage'] > 0) { $apage = $_GET['apage']; }
		}
	}

	$count = $db->querySingle("SELECT COUNT (id) FROM article;");

	$data = $db->query("SELECT * FROM article ORDER BY id DESC LIMIT " . $per_page . " OFFSET " . $per_page * ($apage - 1) . ";)");
	// $id . "','"
	//. str_replace("'", '_', $_POST['article-author']) . "','"
	//. str_replace("'", '_', $_POST['article-name']) . "','"
	//. str_replace("'", '_', $_POST['article-source']) . "','"
	//. str_replace("'", '_', $_POST['article-quote']) . "','"
	//. str_replace("'", '_', $_POST['note']) . "','"
	//. str_replace("'", '_', $_POST['link']) . "','"
	//. str_replace("'", '_', $_POST['public'])
	//. "');");
	if (!$data) {
		echo '<font color="red">Žádná data.</font>';
	} else {

		$index = $per_page * ($apage - 1) + 1;
		echo '<table width="500">';
		echo '<tr><td></td><td align="center"><b><u>Jméno</u></b></td><td></td><td></td><tr>';
		while ($row = $data->fetchArray()) {
			$file = $db->querySingle("SELECT name FROM file WHERE ID = '" . $row[0] . "';)");
			// LABEL
			if ($file) {
				echo '<tr>'
				. '<td width="25">' . $index . '.</td>'
				. '<td>' . $row[2] . '</td>'
				. '<td width="30" align="center">'
					. '<img onclick="display('. "'" . $row[0] . "'" . ')" src="text.png">'
				. '</td>'
				. '<td width="30" align="center">'
					. '<a target="_blank" href="../form/data/' . $row[0] . '_' . $file . '"><img src="pdf.png"></a>'
				. '</td>'
				. '</tr>';
			} else {
				echo '<tr>'
				. '<td width="25">' . $index . '.</td>'
				. '<td>' . $row[2] . '</td>'
				. '<td width="30" align="center">'
					. '<img onclick="display('. "'" . $row[0] . "'" . ')" src="text.png">'
				. '</td>'
				. '<td width="30"></td>'
				. '</tr>';
			}
			// DATA
			echo '<tr>'
				. '<td></td>'
				. '<td border="1" id="' . $row[0] . '_data" style="display:none;">...</br>DETAILNI DATA...</br>....</td>'
				. '<td></td>'
				. '<td></td>'
				. '</tr>';		
			$index++;
		}
		echo '</table>';
		if ($count > $per_page) {
			if ($apage * $per_page > $per_page) { echo '<a href="?apage=' . ($apage - 1) . '"><img src="left.png"></a>'; }
			if ($apage * $per_page < $count) { echo '<a href="?apage=' . ($apage + 1) . '"><img src="right.png"></a>'; }
		}
	}
?>

</div>

<div id="chapter-div" style="display:none;">
</div>

<div id="book-div" style="display:none;">
</div>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<table width='500'><tr><td width="450" align="right"><a href="/main"><img src="/back.png"></a></td></tr></table>
</div>
</body>
</html>

