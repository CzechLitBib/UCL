<?php

session_start();

$id = uniqid();
$value = ['author','name','source','quote','place','publisher','year','link','public'];
$output='';

// validation
$valid=False;
if (isset($_POST['code']) and isset($_SESSION['secret'])) {
	if ($_SESSION['secret'] == $_POST['code'])  { $valid=True; }
}

if ($valid) {
	// CSV
	$output .= $id . '|';
	foreach($value as $val) {
		if (isset($_POST[$val])) {
			$val == 'public' ? $output .= $_POST[$val] : $output .= $_POST[$val] . '|';
		} else {
			$output .= '|';
		}
	}
	file_put_contents('data/' . $id . '.csv', $output . "\n");
	// FILE
	if (isset($_FILES['file'])) {
		if ($_FILES['file']['error'] == 0) {
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$ftype = $finfo->file($_FILES['file']['tmp_name']);
			if ($ftype == 'application/pdf') {
				move_uploaded_file($_FILES['file']['tmp_name'], 'data/' . $id . '_' . preg_replace('/^\.+|\/|\.+$/', '_', $_FILES['file']['name']));
			}
		}
	}
}

?>

<html>
<head>
<script>
	function on_load() {
		document.getElementById("review-div").style.display = "block";
		document.getElementById("bookpart-div").style.display = "none";
		document.getElementById("book-div").style.display = "none";
	}
	function load_type() {
		if (document.getElementById('review').checked) {
			document.getElementById("review-div").style.display = "block";
			document.getElementById("bookpart-div").style.display = "none";
			document.getElementById("book-div").style.display = "none";
		}
		if (document.getElementById('bookpart').checked) {
			document.getElementById("review-div").style.display = "none";
			document.getElementById("bookpart-div").style.display = "block";
			document.getElementById("book-div").style.display = "none";
		}
		if (document.getElementById('book').checked) {
			document.getElementById("review-div").style.display = "none";
			document.getElementById("bookpart-div").style.display = "none";
			document.getElementById("book-div").style.display = "block";
		}
	}
</script>

</head>
<body bgcolor="lightgrey" onload="on_load()">
<div align="center">
<table><tr><td><img src="/clanky/sova.png"></td><td>Vstupní formulář.</td></tr></table>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>

<p><table><tr><td>
<input type="radio" name="datatype" id="review" value="review" onclick="load_type()" checked><label>Článek</label>
<input type="radio" name="datatype" id="bookpart" value="bookpart" onclick="load_type()"><label>Kapitola v knize</label>
<input type="radio" name="datatype" id="book" value="book" onclick="load_type()"><label>Kniha</label>
</td></tr></table></p>

<form method="post" action="." enctype="multipart/form-data">

<div id="review-div">
<table border="1" width="550">
<tr><td align="right">Autor:</td><td><input type="text" name="review-author" size="20" value="
<?php if (!$valid and isset($_POST['author'])) { echo htmlspecialchars($_POST['author'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Název:</td><td><input type="text" name="review-name" size="20" value="
<?php if (!$valid and isset($_POST['name'])) { echo htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'); } ?>
" required></td></tr>
<tr><td align="right">Zdrojový dokument:</td><td><input type="text" name="review-source" size="20" value="
<?php if (!$valid and isset($_POST['source'])) { echo htmlspecialchars($_POST['source'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Bibliografická citace:</td><td><input type="text" name="review-quote" size="30" value="
<?php if (!$valid and isset($_POST['quote'])) { echo htmlspecialchars($_POST['quote'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Poznámka:</td><td><input type="text" name="review-note" size="30" value="
<?php if (!$valid and isset($_POST['quote'])) { echo htmlspecialchars($_POST['note'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
</table>
</div>

<div id="bookpart-div">
<table border="1" width="550">
<tr><td align="right"><u><b>Text</b></u></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Autor:</td><td><input type="text" name="part-author" size="20" value="
<?php if (!$valid and isset($_POST['author'])) { echo htmlspecialchars($_POST['author'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Název:</td><td><input type="text" name="part-name" size="20" value="
<?php if (!$valid and isset($_POST['name'])) { echo htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'); } ?>
" required></td></tr>
<tr height="8px"></tr>
<tr><td align="right"><u><b>Zdrojový dokument</b></u></td></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Autor:</td><td><input type="text" name="part-source-author" size="20" value="
<?php if (!$valid and isset($_POST['author'])) { echo htmlspecialchars($_POST['author'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Název:</td><td><input type="text" name="part-source-name" size="20" value="
<?php if (!$valid and isset($_POST['name'])) { echo htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'); } ?>
" required></td></tr>
<tr><td align="right">Místo:</td><td><input type="text" name="part-source-place" size="20" value="
<?php if (!$valid and isset($_POST['place'])) { echo htmlspecialchars($_POST['place'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Nakladatelství:</td><td><input type="text" name="part-source-publisher" size="20" value="
<?php if (!$valid and isset($_POST['publisher'])) { echo htmlspecialchars($_POST['publisher'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Rok:</td><td><input type="text" name="part-source-year" size="4" value="
<?php if (!$valid and isset($_POST['year'])) { echo htmlspecialchars($_POST['year'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Poznámka:</td><td><input type="text" name="part-note" size="30" value="
<?php if (!$valid and isset($_POST['quote'])) { echo htmlspecialchars($_POST['note'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
</table>
</div>

<div id="book-div">
<table border="1" width="550">
<tr><td align="right">Autor:</td><td><input type="text" name="book-author" size="20" value="
<?php if (!$valid and isset($_POST['author'])) { echo htmlspecialchars($_POST['author'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Název:</td><td><input type="text" name="book-name" size="20" value="
<?php if (!$valid and isset($_POST['name'])) { echo htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'); } ?>
" required></td></tr>
<tr><td align="right">Místo:</td><td><input type="text" name="book-place" size="20" value="
<?php if (!$valid and isset($_POST['place'])) { echo htmlspecialchars($_POST['place'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Nakladatelství:</td><td><input type="text" name="book-publisher" size="20" value="
<?php if (!$valid and isset($_POST['publisher'])) { echo htmlspecialchars($_POST['publisher'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Rok:</td><td><input type="text" name="book-year" size="4" value="
<?php if (!$valid and isset($_POST['year'])) { echo htmlspecialchars($_POST['year'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Poznámka:</td><td><input type="text" name="nook-note" size="30" value="
<?php if (!$valid and isset($_POST['quote'])) { echo htmlspecialchars($_POST['note'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
</table>
</div>

<table border="1" width="550">
<tr><td align="right">Odkaz:</td><td><input type="text" name="link" size="30" value="
<?php if (!$valid and isset($_POST['link'])) { echo htmlspecialchars($_POST['link'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Elektronická verze:</td><td><input style="background-color:#ffffff;width:332px;border-radius:5px;" type="file" name="file"></td><td><img src="/clanky/help.png" title='Pouze soubory typu PDF. Maximalní velikost 2MB.'></td></tr>
<tr><td align="right">Veřejný dokument</td><td><input type="radio" name="public" value="ano"><label>Ano</label> <input type="radio" name="public" value="ne" checked><label>Ne</label></td></tr>
<tr height="8px"></tr>
<tr><td align="right"><img src="validation.php"></td><td align="left"><input style="text-align:center;" type="text" name="code" size="3" required></td></tr>
<tr height="8px"></tr>
<tr><td></td><td align="left"><input type="submit" value="Odeslat"></td></tr>
</table>

</form>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>

<?php

if (isset($_POST['code'])) {
	if ($valid) {
		echo '<font color="red">Uloženo.</font>';
	} else  {
		echo '<font color="red">Neplatný kód.</font>';
	}
}

?>

</div>
</body>
</html>

