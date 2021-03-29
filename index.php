<? require_once("session.php");?>
<html>
<head>
<title><?echo $title;?></title>
</head>
<body onunload="window.opener.location.reload();">
<?php
require_once("welcome.php");

echo "<div id=\"divmenu\">";
	require_once("menu.php");
echo "</div>";
?>
</body>
</html>