<?php

session_start();

$id = uniqid();

$common = ['note','link','public'];

$type['article'] = ['article-author','article-name','article-source','article-quote'];
$type['chapter'] = ['chapter-author','chapter-name','chapter-src-author','chapter-src-name',
		'chapter-src-place','chapter-src-publisher','chapter-src-year'];
$type['book'] = ['book-author','book-name','book-place','book-publisher','book-year'];

$output='';

// validation
$valid=False;
if (isset($_POST['code']) and isset($_SESSION['secret'])) {
	if ($_SESSION['secret'] == $_POST['code'])  { $valid=True; }
}

if ($valid) {
	// CSV
	$output .= $id . '|';
	foreach($type[$_POST['type']] as $v) {
		isset($_POST[$v]) ? $output .= $_POST[$v] . '|' : $output .= '|';
	}
	foreach($common as $v) {
		if (isset($_POST[$v])) {
			$v == 'public' ? $output .= $_POST[$v] : $output .= $_POST[$v] . '|';
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
</script>

</head>
<body bgcolor="lightgrey" onload="on_load()">
<div align="center">
<table><tr><td><img src="/clanky/sova.png"></td><td>Vstupní formulář.</td></tr></table>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>

<form method="post" action="." enctype="multipart/form-data">

<p><table><tr><td>
<input type="radio" name="type" id="article" value="article" onclick="load_type()" checked><label>Článek</label>
<input type="radio" name="type" id="chapter" value="chapter" onclick="load_type()"><label>Kapitola v knize</label>
<input type="radio" name="type" id="book" value="book" onclick="load_type()"><label>Kniha</label>
</td></tr></table></p>

<div id="article-div">
<table width="550">
<tr><td width="175" align="right"><u><b>Základní údaje</b></u></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Autor:</td><td><input type="text" name="article-author" size="20" value="
<?php if (!$valid and isset($_POST['article-author'])) { echo htmlspecialchars($_POST['article-author'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Název:</td><td><input type="text" name="article-name" size="20" value="
<?php if (!$valid and isset($_POST['article-name'])) { echo htmlspecialchars($_POST['article-name'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Zdrojový dokument:</td><td><input type="text" name="article-source" size="20" value="
<?php if (!$valid and isset($_POST['article-source'])) { echo htmlspecialchars($_POST['article-source'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Bibliografická citace:</td><td><input type="text" name="article-quote" size="30" value="
<?php if (!$valid and isset($_POST['article-quote'])) { echo htmlspecialchars($_POST['article-quote'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
</table>
</div>

<div id="chapter-div">
<table width="550">
<tr><td width="175" align="right"><u><b>Text</b></u></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Autor:</td><td><input type="text" name="chapter-author" size="20" value="
<?php if (!$valid and isset($_POST['chapter-author'])) { echo htmlspecialchars($_POST['chapter-author'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Název:</td><td><input type="text" name="chapter-name" size="20" value="
<?php if (!$valid and isset($_POST['chapter-name'])) { echo htmlspecialchars($_POST['chapter-name'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr height="8px"></tr>
<tr><td align="right"><u><b>Zdrojový dokument</b></u></td></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Autor:</td><td><input type="text" name="chapter-src-author" size="20" value="
<?php if (!$valid and isset($_POST['chapter-src-author'])) { echo htmlspecialchars($_POST['chapter-src-author'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Název:</td><td><input type="text" name="chapter-src-name" size="20" value="
<?php if (!$valid and isset($_POST['chapter-src-name'])) { echo htmlspecialchars($_POST['chapter-src-name'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Místo:</td><td><input type="text" name="chapter-src-place" size="20" value="
<?php if (!$valid and isset($_POST['chapter-src-place'])) { echo htmlspecialchars($_POST['chapter-src-place'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Nakladatelství:</td><td><input type="text" name="chapter-src-publisher" size="20" value="
<?php if (!$valid and isset($_POST['chapter-src-publisher'])) { echo htmlspecialchars($_POST['chapter-src-publisher'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Rok:</td><td><input type="text" name="chapter-src-year" size="4" value="
<?php if (!$valid and isset($_POST['chapter-src-year'])) { echo htmlspecialchars($_POST['chapter-src-year'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
</table>
</div>

<div id="book-div">
<table width="550">
<tr><td width="175" align="right"><u><b>Základní údaje</b></u></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Autor:</td><td><input type="text" name="book-author" size="20" value="
<?php if (!$valid and isset($_POST['book-author'])) { echo htmlspecialchars($_POST['book-author'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Název:</td><td><input type="text" name="book-name" size="20" value="
<?php if (!$valid and isset($_POST['book-name'])) { echo htmlspecialchars($_POST['book-name'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Místo:</td><td><input type="text" name="book-place" size="20" value="
<?php if (!$valid and isset($_POST['book-place'])) { echo htmlspecialchars($_POST['book-place'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Nakladatelství:</td><td><input type="text" name="book-publisher" size="20" value="
<?php if (!$valid and isset($_POST['book-publisher'])) { echo htmlspecialchars($_POST['book-publisher'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Rok:</td><td><input type="text" name="book-year" size="4" value="
<?php if (!$valid and isset($_POST['book-year'])) { echo htmlspecialchars($_POST['book-year'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
</table>
</div>

<table width="550">
<tr height="8px"></tr>
<tr><td width="175" align="right"><u><b>Ostatní</b></u></td></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Poznámka:</td><td><input type="text" name="note" size="30" value="
<?php if (!$valid and isset($_POST['quote'])) { echo htmlspecialchars($_POST['note'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Odkaz:</td><td><input type="text" name="link" size="30" value="
<?php if (!$valid and isset($_POST['link'])) { echo htmlspecialchars($_POST['link'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Elektronická verze:</td><td><input style="background-color:#ffffff;width:332px;border-radius:5px;" type="file" name="file"></td><td><img src="/clanky/help.png" title='Pouze soubory typu PDF. Maximalní velikost 5MB.'></td></tr>
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

