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

if (!$db) { $error = 'Chyba databáze.'; }

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

	if ($error) {
		echo '<font color="red">' . $error . '</font>';
	} else {
	
		$count = $db->querySingle("SELECT COUNT (id) FROM article;");

		$data = $db->query("SELECT * FROM article ORDER BY id DESC LIMIT " . $per_page . " OFFSET " . $per_page * ($apage - 1) . ";)");

		if ($count == 0) {
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
					. '<td border="1" id="' . $row[0] . '_data" style="display:none;"></br>DETAILNI POPIS</br></br></td>'
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
	}
?>

</div>

<div id="chapter-div" style="display:none;">

<?php
	if (isset($_GET['cpage'])) {
		if (is_numeric(htmlspecialchars($_GET['cpage']))) {
			if ($_GET['cpage'] > 0) { $apage = $_GET['cpage']; }
		}
	}

	if ($error) {
		echo '<font color="red">' . $error . '</font>';
	} else {
	
		$count = $db->querySingle("SELECT COUNT (id) FROM chapter;");

		$data = $db->query("SELECT * FROM chapter ORDER BY id DESC LIMIT " . $per_page . " OFFSET " . $per_page * ($cpage - 1) . ";)");

		if ($count == 0) {
			echo '<font color="red">Žádná data.</font>';
		} else {

			$index = $per_page * ($cpage - 1) + 1;
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
					. '<td border="1" id="' . $row[0] . '_data" style="display:none;"></br>DETAILNI POPIS</br></br></td>'
					. '<td></td>'
					. '<td></td>'
					. '</tr>';		
				$index++;
			}
			echo '</table>';
			if ($count > $per_page) {
				if ($cpage * $per_page > $per_page) { echo '<a href="?apage=' . ($cpage - 1) . '"><img src="left.png"></a>'; }
				if ($cpage * $per_page < $count) { echo '<a href="?apage=' . ($cpage + 1) . '"><img src="right.png"></a>'; }
			}
		}
	}
?>

</div>

<div id="book-div" style="display:none;">

<?php
	if (isset($_GET['bpage'])) {
		if (is_numeric(htmlspecialchars($_GET['bpage']))) {
			if ($_GET['bpage'] > 0) { $apage = $_GET['bpage']; }
		}
	}

	if ($error) {
		echo '<font color="red">' . $error . '</font>';
	} else {
	
		$count = $db->querySingle("SELECT COUNT (id) FROM book;");

		$data = $db->query("SELECT * FROM book ORDER BY id DESC LIMIT " . $per_page . " OFFSET " . $per_page * ($bpage - 1) . ";)");

		if ($count == 0) {
			echo '<font color="red">Žádná data.</font>';
		} else {

			$index = $per_page * ($bpage - 1) + 1;
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
					. '<td border="1" id="' . $row[0] . '_data" style="display:none;"></br>DETAILNI POPIS</br></br></td>'
					. '<td></td>'
					. '<td></td>'
					. '</tr>';		
				$index++;
			}
			echo '</table>';
			if ($count > $per_page) {
				if ($bpage * $per_page > $per_page) { echo '<a href="?apage=' . ($bpage - 1) . '"><img src="left.png"></a>'; }
				if ($bpage * $per_page < $count) { echo '<a href="?apage=' . ($bpage + 1) . '"><img src="right.png"></a>'; }
			}
		}
	}
?>

</div>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>
<table width='500'><tr><td width="450" align="right"><a href="/main"><img src="/back.png"></a></td></tr></table>
</div>
</body>
</html>

