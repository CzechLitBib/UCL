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

if(!isset($_SESSION['form-data'])) { $_SESSION['form-data'] = Null; }

$error = '';

$apage = 1; // article page
$bpage = 1; // book page
$cpage = 1; // chapter page

$per_page = 5;

$db = new SQLite3('form.db');

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
	function ajax(id,type) {
		const xhttp = new XMLHttpRequest();
		xhttp.onload = function() {
			if (this.status == 200) {
				document.getElementById(id).style.display = "none";
			}
		}
		xhttp.open("GET", "done.php?id=" + id + "&type=" + type, true);
		xhttp.send();
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
			echo '<table width="600">';
			echo '<tr><td></td><td align="center"><b><u>Autor</u></b></td><td align="center"><b><u>Název</u></b></td><td></td><td></td><td></td><tr>';
			while ($row = $data->fetchArray()) {
				$file = $db->querySingle("SELECT name FROM file WHERE ID = '" . $row[0] . "';)");
				// LABEL
				echo '<tr height="32">'
				. '<td width="25">' . $index . '.</td>'
				. '<td align="center">' . $row[1] . '</td>'
				. '<td align="center">' . $row[2] . '</td>'
				. '<td width="30" align="center">'
				. '<a href="download.php?id=' . $row[0] . '&type=article"><img src="text.png"></a>'
				. '</td>';
				if ($file) {
					echo '<td width="30" align="center">'
					. '<a target="_blank" href="../form/data/' . $row[0] . '_' . $file . '"><img src="pdf.png"></a>'
					. '</td>';
				} else {
					echo '<td width="30"></td>';
				}
				if (end($row)) {// Done.
					echo '<td width="120"></td>';
				} else {
					echo '<td width="120"><input type="submit" onclick="ajax(' . "'" . $row[0] . "','article')" . '" id="' . $row[0] . '" value="Zpracováno"></td>';
				}
				echo '</tr>';
				$index++;
			}
			echo '</table>';
			if ($count > $per_page) {
				echo '<br><table><tr width="60">';
				if ($apage * $per_page > $per_page) { echo '<td width="20"><a href="?apage=' . ($apage - 1) . '"><img src="left.png"></a></td>'; } else { echo '<td width="20"></td>'; }
				echo '<td width="20"></td>';
				if ($apage * $per_page < $count) { echo '<td width="20"><a href="?apage=' . ($apage + 1) . '"><img src="right.png"></a></td>'; } else { echo '<td width="20"></td>'; }
				echo '</tr></table>';
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
			echo '<table width="600">';
			echo '<tr><td></td><td align="center"><b><u>Autor</u></b></td><td align="center"><b><u>Název</u></b></td><td></td><td></td><td></td><tr>';
			while ($row = $data->fetchArray()) {
				$file = $db->querySingle("SELECT name FROM file WHERE ID = '" . $row[0] . "';)");
				// LABEL
				echo '<tr height="32">'
				. '<td width="25">' . $index . '.</td>'
				. '<td align="center">' . $row[1] . '</td>'
				. '<td align="center">' . $row[2] . '</td>'
				. '<td width="30" align="center">'
				. '<a href="download.php?id=' . $row[0] . '&type=chapter"><img src="text.png"></a>'
				. '</td>';
				if ($file) {
					echo '<td width="30" align="center">'
					. '<a target="_blank" href="../form/data/' . $row[0] . '_' . $file . '"><img src="pdf.png"></a>'
					. '</td>';
				} else {
					echo '<td width="30"></td>';
				}
				if (end($row)) {// Done.
					echo '<td width="120"></td>';
				} else {
					echo '<td width="120"><input type="submit" onclick="ajax(' . "'" . $row[0] . "','chapter')" . '" id="' . $row[0] . '" value="Zpracováno"></td>';
				}
				echo '</tr>';
				$index++;
			}
			echo '</table>';
			if ($count > $per_page) {
				echo '<br><table><tr width="60">';
				if ($cpage * $per_page > $per_page) { echo '<td width="20"><a href="?apage=' . ($cpage - 1) . '"><img src="left.png"></a></td>'; } else { echo '<td width="20"></td>'; }
				echo '<td width="20"></td>';
				if ($cpage * $per_page < $count) { echo '<td width="20"><a href="?apage=' . ($cpage + 1) . '"><img src="right.png"></a></td>'; } else { echo '<td width="20"></td>'; }
				echo '</tr></table>';
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
			echo '<table width="600">';
			echo '<tr><td></td><td align="center"><b><u>Autor</u></b></td><td align="center"><b><u>Název</u></b></td><td></td><td></td><td></td><tr>';
			while ($row = $data->fetchArray()) {
				$file = $db->querySingle("SELECT name FROM file WHERE ID = '" . $row[0] . "';)");
				// LABEL
				echo '<tr height="32">'
				. '<td width="25">' . $index . '.</td>'
				. '<td align="center">' . $row[1] . '</td>'
				. '<td align="center">' . $row[2] . '</td>'
				. '<td width="30" align="center">'
				. '<a href="download.php?id=' . $row[0] . '&type=book"><img src="text.png"></a>'
				. '</td>';
				if ($file) {
					echo '<td width="30" align="center">'
					. '<a target="_blank" href="../form/data/' . $row[0] . '_' . $file . '"><img src="pdf.png"></a>'
					. '</td>';
				} else {
					echo '<td width="30"></td>';
				}
				if (end($row)) {// Done.
					echo '<td width="120"></td>';
				} else {
					echo '<td width="120"><input type="submit" onclick="ajax(' . "'" . $row[0] . "','book')" . '" id="' . $row[0] . '" value="Zpracováno"></td>';
				}
				echo '</tr>';
				// DATA
				$index++;
			}
			echo '</table>';
			if ($count > $per_page) {
				echo '<br><table><tr width="60">';
				if ($bpage * $per_page > $per_page) { echo '<td width="20"><a href="?apage=' . ($bpage - 1) . '"><img src="left.png"></a></td>'; } else { echo '<td width="20"></td>'; }
				echo '<td width="20"></td>';
				if ($bpage * $per_page < $count) { echo '<td width="20"><a href="?apage=' . ($bpage + 1) . '"><img src="right.png"></a></td>'; } else { echo '<td width="20"></td>'; }
				echo '</tr></table>';
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
