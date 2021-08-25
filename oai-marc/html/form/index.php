<?php

session_start();

$id = uniqid();

$error = '';

$valid=False;
if (isset($_POST['code']) and isset($_SESSION['secret'])) {
	if ($_SESSION['secret'] == $_POST['code'])  { $valid=True; }
}

if ($valid) {
	$db = new SQLite3('db/form.db');

	if (!$db) {
		$error = 'Chyba čtení databáze.';
	} else {
		if ($_POST['type'] == 'article') {
			$query = $db->exec("INSERT INTO article (id,author,name,source,quote,note,link,public)"
			. "VALUES ('"
			. $id . "','"
			. str_replace("'", '_', $_POST['article-author']) . "','"
			. str_replace("'", '_', $_POST['article-name']) . "','"
			. str_replace("'", '_', $_POST['article-source']) . "','"
			. str_replace("'", '_', $_POST['article-quote']) . "','"
			. str_replace("'", '_', $_POST['note']) . "','"
			. str_replace("'", '_', $_POST['link']) . "','"
			. str_replace("'", '_', $_POST['public'])
			. "');");
			if (!$query) { $error = 'Chyba zápisu do databáze.'; }
		}
		if ($_POST['type'] == 'chapter') {
			$query = $db->exec("INSERT INTO chapter (id,author,name,src_author,src_name,src_place,src_publisher,src_year,note,link,public)"
			. "VALUES ('"
			. $id . "','"
			. str_replace("'", '_', $_POST['chapter-author']) . "','"
			. str_replace("'", '_', $_POST['chapter-name']) . "','"
			. str_replace("'", '_', $_POST['chapter-src-author']) . "','"
			. str_replace("'", '_', $_POST['chapter-src-name']) . "','"
			. str_replace("'", '_', $_POST['chapter-src-place']) . "','"
			. str_replace("'", '_', $_POST['chapter-src-publisher']) . "','"
			. str_replace("'", '_', $_POST['chapter-src-year']) . "','"
			. str_replace("'", '_', $_POST['note']) . "','"
			. str_replace("'", '_', $_POST['link']) . "','"
			. str_replace("'", '_', $_POST['public'])
			. "');");
			if (!$query) { $error = 'Chyba zápisu do databáze.'; }
		}
		if ($_POST['type'] == 'book') {
			$query = $db->exec("INSERT INTO book (id,author,name,place,publisher,year,note,link,public)"
			. "VALUES ('"
			. $id . "','"
			. str_replace("'", '_', $_POST['book-author']) . "','"
			. str_replace("'", '_', $_POST['book-name']) . "','"
			. str_replace("'", '_', $_POST['book-place']) . "','"
			. str_replace("'", '_', $_POST['book-publisher']) . "','"
			. str_replace("'", '_', $_POST['book-year']) . "','"
			. str_replace("'", '_', $_POST['note']) . "','"
			. str_replace("'", '_', $_POST['link']) . "','"
			. str_replace("'", '_', $_POST['public'])
			. "');");
			if (!$query) { $error = 'Chyba zápisu do databáze.'; }
		}
		if (isset($_FILES['file'])) {
			if ($_FILES['file']['error'] == 0) {
				$finfo = new finfo(FILEINFO_MIME_TYPE);
				$ftype = $finfo->file($_FILES['file']['tmp_name']);
				if ($ftype == 'application/pdf') {
					# escape dot, space, slash and quote
					$fn = preg_replace("/^\.+| |\/|'|\.+$/", '_', $_FILES['file']['name']);
					move_uploaded_file($_FILES['file']['tmp_name'], 'data/' . $id . '_' . $fn);
					$query = $db->exec("INSERT INTO file (id,name) VALUES ('" . $id . "','". $fn . "');");
					if (!$query) { $error = 'Chyba zápisu do databáze.'; }
				}
			}
		}
		$db->close();
	}
}

?>

<html>
<head>
<style>
body {
  font-family: Georgia, serif;
}
</style>
<script>
	function on_load() {
		document.getElementById("article-div").style.display = "block";
		document.getElementById("chapter-div").style.display = "none";
		document.getElementById("book-div").style.display = "none";
		document.getElementById("article-name").required = true;
		document.getElementById("chapter-name").required = false;
		document.getElementById("book-name").required = false;
	}
	function load_type() {
		if (document.getElementById('article').checked) {
			on_load();
		}
		if (document.getElementById('chapter').checked) {
			document.getElementById("article-div").style.display = "none";
			document.getElementById("chapter-div").style.display = "block";
			document.getElementById("book-div").style.display = "none";
			document.getElementById("article-name").required = false;
			document.getElementById("chapter-name").required = true;
			document.getElementById("book-name").required = false;
	
		}
		if (document.getElementById('book').checked) {
			document.getElementById("article-div").style.display = "none";
			document.getElementById("chapter-div").style.display = "none";
			document.getElementById("book-div").style.display = "block";
			document.getElementById("article-name").required = false;
			document.getElementById("chapter-name").required = false;
			document.getElementById("book-name").required = true;
		}
	}
</script>

</head>
<body bgcolor="lightgrey" onload="on_load()">
<div align="center">
<table><tr><td align="center"><img width="142" src="/form/sova.png"></td><td>Návrhy podkladů pro zpracování v ČLB</td></tr>
<tr><td colspan="2" width="750"><div align="justify"><font size="2">Tento formulář slouží pro zasílání návrhů dokumentů ke zpracování pro potřeby databází České literární bibliografie. Tímto způsobem jsou přednostně sbírány informace o publikacích mimo běžný excerpční záběr ČLB či publikacích obtížněji dostupných – přednostně jde o publikace vydané v zahraničí, malonákladové či regionální tiskoviny, články o literatuře v tiskovinách, které se literatuře a literárnímu dění systematicky nevěnují atp.
Pakliže daný dokument splňuje podmínky pro zařazení do bází ČLB, bude na základě dodaných podkladů vytvořen bibliografický záznam. Podmínkou pro vytvoření záznamu je dodání plného textu daného dokumentu či umožnění přístupu k němu, aby mohla být provedena obsahová analýza a ověřeny základní bibliografické údaje. Pokud navrhovatel neurčí jinak, ČLB se zavazuje plný text využít pouze pro účely zpracování bibliografického záznamu a nebude jej jakkoli dále distribuovat.
Návrhy dokumentů ke zpracování je možné zadat prostřednictvím formuláře níže.
V případě jakýchkoli dotazů nás prosím kontaktujte na adrese <a style="text-decoration:none; color:black;" href="mailto:clb@ucl.cas.cz">clb@ucl.cas.cz</a> .</div></td></tr>
</table>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>

<form method="post" action="." enctype="multipart/form-data">

<p><table><tr><td>
<input type="radio" name="type" id="article" value="article" onclick="load_type()" checked><label>Článek</label>
<input type="radio" name="type" id="chapter" value="chapter" onclick="load_type()"><label>Kapitola v knize</label>
<input type="radio" name="type" id="book" value="book" onclick="load_type()"><label>Kniha</label>
</td></tr></table></p>

<table width="550">
<tr height="8px"></tr>
<tr><td width="175" align="right"><u><b>Plný text</b></u></td></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Poznámka:</td><td><input type="text" name="note" size="30" value="
<?php if (!$valid and isset($_POST['note'])) { echo htmlspecialchars($_POST['note'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Odkaz:</td><td><input type="text" name="link" size="30" value="
<?php if (!$valid and isset($_POST['link'])) { echo htmlspecialchars($_POST['link'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Elektronická verze:</td><td><input style="background-color:#ffffff;width:332px;border-radius:5px;" type="file" name="file"></td><td><img src="/form/help.png" title='Pouze soubory typu PDF. Maximalní velikost 5MB.'></td></tr>
<tr><td align="right">Veřejný dokument</td><td><input type="radio" name="public" value="ano"><label>Ano</label> <input type="radio" name="public" value="ne" checked><label>Ne</label></td></tr>
<tr height="8px"></tr>
</table>

<div id="article-div">
<table width="550">
<tr><td width="175" align="right"><u><b>Základní údaje</b></u></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Autor:</td><td><input type="text" name="article-author" size="20" value="
<?php if (!$valid and isset($_POST['article-author'])) { echo htmlspecialchars($_POST['article-author'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Název:</td><td><input type="text" id="article-name" name="article-name" size="20" value="
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

<div id="chapter-div" style="display:none;">
<table width="550">
<tr><td width="175" align="right"><u><b>Text</b></u></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Autor:</td><td><input type="text" name="chapter-author" size="20" value="
<?php if (!$valid and isset($_POST['chapter-author'])) { echo htmlspecialchars($_POST['chapter-author'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Název:</td><td><input type="text" id="chapter-name" name="chapter-name" size="20" value="
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

<div id="book-div" style="display:none;">
<table width="550">
<tr><td width="175" align="right"><u><b>Základní údaje</b></u></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Autor:</td><td><input type="text" name="book-author" size="20" value="
<?php if (!$valid and isset($_POST['book-author'])) { echo htmlspecialchars($_POST['book-author'], ENT_QUOTES, 'UTF-8'); } ?>
"></td></tr>
<tr><td align="right">Název:</td><td><input type="text" id="book-name" name="book-name" size="20" value="
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
<tr><td width="175" align="right"><img src="validation.php"></td><td align="left"><input style="text-align:center;" type="text" name="code" size="3" required></td></tr>
<tr height="8px"></tr>
<tr><td></td><td align="left"><input type="submit" value="Odeslat"></td></tr>
</table>

</form>

<p><hr style="border-top: 0px; border-bottom:1px solid black;" width="500"></p>

<?php

if (isset($_POST['code'])) {
	if ($error) {
		echo '<font color="red">' .$error . '</font>';
	} elseif ($valid) {
		echo '<font color="red">Uloženo.</font>';
	} else {
		echo '<font color="red">Neplatný kód.</font>';
	}
}

?>

</div>
</body>
</html>

