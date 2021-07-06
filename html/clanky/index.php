<?php

session_start();

//get uniqe ID
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
	foreach($value as $val) {
		if (isset($_POST[$val])) {
			$val == 'public' ? $output .= $_POST[$val] : $output .= $_POST[$val] . ';';
		} else {
			$output .= ';';
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
<head></head>
<body bgcolor="lightgrey">
<div align="center">
<table><tr><td><img src="/sova.png"></td><td>Formulář pro zaslání článku.</td></tr></table>
<p><hr width="500"></p>
<form method="post" action="." enctype="multipart/form-data">
<table>
<tr><td align="right"><u><b>Základní údaje</b></u></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Autor:</td><td><input type="text" name="author" size="20" value="

<?php if (!$valid and isset($_POST['author'])) { echo htmlspecialchars($_POST['author'], ENT_QUOTES, 'UTF-8'); } ?>

"></td></tr>
<tr><td align="right">Název:</td><td><input type="text" name="name" size="20" value="

<?php if (!$valid and isset($_POST['name'])) { echo htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'); } ?>

"></td></tr>
<tr><td align="right">Zdrojový dokument [časopis]:</td><td><input type="text" name="source" size="20" value="

<?php if (!$valid and isset($_POST['source'])) { echo htmlspecialchars($_POST['source'], ENT_QUOTES, 'UTF-8'); } ?>

"></td></tr>
<tr><td align="right">Bibliografická citace:</td><td><input type="text" name="quote" size="30" value="

<?php if (!$valid and isset($_POST['quote'])) { echo htmlspecialchars($_POST['quote'], ENT_QUOTES, 'UTF-8'); } ?>

"></td></tr>
<tr height="8px"></tr>
<tr><td align="right"><u><b>Nakladatelské údaje</b></u></td></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Místo:</td><td><input type="text" name="place" size="20" value="

<?php if (!$valid and isset($_POST['place'])) { echo htmlspecialchars($_POST['place'], ENT_QUOTES, 'UTF-8'); } ?>

"></td></tr>
<tr><td align="right">Nakladatelství:</td><td><input type="text" name="publisher" size="20" value="

<?php if (!$valid and isset($_POST['publisher'])) { echo htmlspecialchars($_POST['publisher'], ENT_QUOTES, 'UTF-8'); } ?>

"></td></tr>
<tr><td align="right">Rok:</td><td><input type="text" name="year" size="4" value="

<?php if (!$valid and isset($_POST['year'])) { echo htmlspecialchars($_POST['year'], ENT_QUOTES, 'UTF-8'); } ?>

"></td></tr>
<tr height="8px"></tr>
<tr><td align="right"><u><b>Ostatní</b></u></td></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Odkaz:</td><td><input type="text" name="link" size="30" value="

<?php if (!$valid and isset($_POST['link'])) { echo htmlspecialchars($_POST['link'], ENT_QUOTES, 'UTF-8'); } ?>

"></td></tr>
<tr><td align="right">Elektronická verze:</td><td><input style="background-color:#ffffff;width:332px;border-radius:5px;" type="file" name="file"></td><td>   <img src="/clanky/help.png" title='Pouze soubory typu PDF. Maximalní velikost 2MB.'></td></tr>
<tr><td align="right">Veřejný dokument</td><td><input type="radio" name="public" value="ano"><label>Ano</label> <input type="radio" name="public" value="ne" checked><label>Ne</label></td></tr>
<tr height="8px"></tr>
<tr><td align="right"><img src="validation.php"></td><td align="left"><input style="text-align:center;" type="text" name="code" size="3"></td></tr>
<tr height="8px"></tr>
<tr><td></td><td align="left"><input type="submit" value="Odeslat"></td></tr>
</table>
</form>
<p><hr width="500"></p>

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

