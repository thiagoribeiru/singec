<?require_once("session.php");?>
<html>
<head>
<title><?echo $title;?></title>
</head>
<body>
<?
//validação página
autorizaPagina(2);

//painel boas vindas
require_once("welcome.php");
echo "<div id=\"divmenu\">\n";
	require_once("menu.php");
echo "\n</div>";

//tabela
echo "<div>";
	
echo "</div>";
?>
</body>
</html>