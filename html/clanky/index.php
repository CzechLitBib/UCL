<?php

$id = uniqid();
$value = ['author','name','source','quote','place','publisher','year','link','public'];
$output = '';

if (!empty($_POST)) {

	foreach($value as $v) {
		if (isset($_POST[$v])) {
			$v == 'public' ? $output .= $_POST[$v] : $output .= $_POST[$v] . ';';
		} else {
			$output .= ';';
		}
	}
	// write CSV
	file_put_contents('data/'. $id . '.csv', $output . "\n");

	if (isset($_FILES['file'])) {
		if ($_FILES['file']['error'] == 0) {
			// test PDF
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$ftype = $finfo->file($_FILES['file']['tmp_name']);
			// write file
			if ($ftype == 'application/pdf') {
				move_uploaded_file($_FILES['file']['tmp_name'], 'data/' . $id . '_' . base64_encode($_FILES['file']['name']));
			}
		}
	}
}
?>

<html>
<head></head>
<body bgcolor="lightgrey">
<div align="center">
<table><tr><td><img src="/clanky/sova.png"></td><td>Formulář pro zaslání článku.</td></tr></table>

<?php
	if (!empty($_POST)) { echo '<font color="red"><b>Uloženo.</b></font>'; }
?>

<p><hr width="500"></p>
<form method="post" action="." enctype="multipart/form-data">
<table>
<tr><td align="right"><u><b>Základní údaje</b></u></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Autor:</td><td><input type="text" name="author" size="20"></td></tr>
<tr><td align="right">Název:</td><td><input type="text" name="name" size="20"></td></tr>
<tr><td align="right">Zdrojový dokument [časopis]:</td><td><input type="text" name="source" size="20"></td></tr>
<tr><td align="right">Bibliografická citace:</td><td><input type="text" name="quote" size="30"></td></tr>
<tr height="8px"></tr>
<tr><td align="right"><u><b>Nakladatelské údaje</b></u></td></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Místo:</td><td><input type="text" name="place" size="20"></td></tr>
<tr><td align="right">Nakladatelství:</td><td><input type="text" name="publisher" size="20"></td></tr>
<tr><td align="right">Rok:</td><td><input type="text" name="year" size="4"></td></tr>
<tr height="8px"></tr>
<tr><td align="right"><u><b>Ostatní</b></u></td></td></tr>
<tr height="8px"></tr>
<tr><td align="right">Odkaz:</td><td><input type="text" name="link" size="30"></td></tr>
<tr><td align="right">Elektronická verze:</td><td><input style="background-color:#ffffff;width:332px;border-radius:5px;" type="file" name="file">   <img src="/clanky/help.png" title='Pouze soubory typu PDF. Maximalní velikost 2MB.'></td></tr>
<tr><td align="right">Veřejný dokument</td><td><input type="radio" name="public" value="ano"><label>Ano</label> <input type="radio" name="public" value="ne" checked><label>Ne</label></td></tr>
<tr height="8px"></tr>
<tr><td align="right"><input type="submit" value="Odeslat"></td></tr>
</table>
</form>
<p><hr width="500"></p>
</div>
</body>
</html>

