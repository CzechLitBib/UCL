
<?php

session_start();

?>

<html>
<head></head>
<body bgcolor="lightgrey">
<div align="center">
<table><tr><td><img src="sova.png"><td><td>UCL Vývoj </td></tr></table>
<form action='main.php' method="post">
<label>Login:</label><input size="4" type="text" name="name" required>
<label>Heslo:</label><input size="4" type="password" name="pass" required>
<input type="submit" value="Odeslat">
</form>
</div>
</body>
</html>

