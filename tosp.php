<?$popup = 1;
require_once("session.php");?>
<html>
<head>
<title><?echo $title." - Filtro";?></title>
<!--<script src="jQuery1.12.1.min.js"></script>-->
<script>
	var link = 0;
	document.onkeydown = function(e){
		var keychar;
		// Internet Explorer
		try {
			keychar = String.fromCharCode(event.keyCode);
			e = event;
		}
		// Firefox, Opera, Chrome, etc...
		catch(err) {
			keychar = String.fromCharCode(e.keyCode);
		}
		if (e.keyCode==40) {
			link = link + 1;
			document.getElementById('a'+link).focus();
			return false;
		}
		if (e.keyCode==38) {
			link = link - 1;
			document.getElementById('a'+link).focus();
			return false;
		}
	}
	$(document).ready(function(){
	
	    //Esconde preloader
	    $(window).load(function(){
	        // $('#preloader').fadeOut(1500);//1500 é a duração do efeito (1.5 seg)
	        document.getElementById('preloader').style.display = "none";
	    });
	
	});
</script>
<style>
	#preloader {
	    position: absolute;
	    left: 0px;
	    right: 0px;
	    bottom: 0px;
	    top: 0px;
	    background: rgba(211,211,211,0.8);
	}
	#preloader img {
		width: 30px;
		position: absolute;
		top: 50%;
		left: 50%;
		margin-top: -15px;
		margin-left: -15px;
	}
</style>
</head>
<body onLoad="document.getElementById('txBusca').focus();" bgcolor="#ECF6CE">
	<div id="preloader"><img src="images/loading.gif"></img></div>
<?
//validação página
autorizaPagina(2);
teclaEsc();

//orientador de metodo para trabalho com busca
	$add=$_GET['add'];
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$busca=$_POST['busca'];
		$cod_sp=$_POST['cod_sp'];
		$add=$_POST['add'];
		// header ("Location: ".$_SERVER['PHP_SELF']."?cod_sp=".$_POST['cod_sp']."&busca=$busca&add=$add");
		echo "<script>location.href=\"".$_SERVER['PHP_SELF']."?cod_sp=".$_POST['cod_sp']."&busca=$busca&add=$add\"</script>";
		exit;
	} else if (!isset($_GET['busca'])) $busca = "";
	else $busca=$_GET['busca'];
	if ($add=="MP")
		if ($busca!="") $textBusca=" and (descricao like '%".str_replace("*","%",$busca)."%' or cod_mp like '%".str_replace("*","%",$busca)."%')"; else $textBusca="";
	else if ($add=="PR")
		if ($busca!="") $textBusca=" and (descricao like '%".str_replace("*","%",$busca)."%' or cod_pr like '%".str_replace("*","%",$busca)."%')"; else $textBusca="";
	else if ($add=="SP")
		if ($busca!="") $textBusca=" and (descricao like '%".str_replace("*","%",$busca)."%' or cod_sp like '%".str_replace("*","%",$busca)."%')"; else $textBusca="";
//final orientador

if ($add=="MP")
	$pesquisa = $sql->query("select cod_mp as cod_x, descricao from mp where ativo = 1 and estado = 1 and empresa = ".$_SESSION['UsuarioEmpresa'].$textBusca." order by descricao") or die (mysqli_error($sql));
else if ($add=="PR")
	$pesquisa = $sql->query("select cod_pr as cod_x, descricao from processos where ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa'].$textBusca." order by descricao") or die (mysqli_error($sql));
else if ($add=="SP")
	$pesquisa = $sql->query("select cod_sp as cod_x, descricao from sp_dados where ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa'].$textBusca." and cod_sp != '".$_GET['cod_sp']."' order by descricao") or die (mysqli_error($sql));

$linhas = mysqli_num_rows($pesquisa);

echo "<div tabindex=\"-1\">";
	echo "<div style=\"float: left; width: 95%;\">";
		echo "<form action=\"".$_SERVER['PHP_SELF']."?cod_sp=$cod_sp&busca=$busca&add=$add\" method=\"POST\" style=\"margin:1px;\">\n";
			echo "<label for=\"txBusca\">Filtro: </label>\n";
			echo "<input type=\"text\" name=\"busca\" id=\"txBusca\" value=\"$busca\" style=\"height:18px; width:490px;\" onblur=\"document.getElementById('a1').focus();\"><input type=\"hidden\" name=\"cod_sp\" value=\"".$_GET['cod_sp']."\">\n";
			echo "<input type=\"hidden\" name=\"add\" value=\"".$_GET['add']."\">\n";
		echo "</form>\n";
	echo "</div><div style=\"float: right; width: 5%;\">";
		if ($add=="MP") {
			echo "<a href=\"#\" onclick=\"abrirPopup('cadmp.php',495,330);\"><img src=\"images/new_file.jpg\" width=20px></a>";
		}
		if ($add=="PR") {
			echo "<a href=\"#\" onclick=\"abrirPopup('cadpr.php',495,330);\"><img src=\"images/new_file.jpg\" width=20px></a>";
		}
		if ($add=="SP") {
			echo "<a href=\"#\" onclick=\"abrirPopup('cadsp.php',940,535);\"><img src=\"images/new_file.jpg\" width=20px></a>";
		}
	echo "</div>";
echo "</div>";

echo "<div class=\"divtosp\" tabindex=\"-1\"><table class=\"tabletosp\" border=\"0px\" cellpadding=\"1px\" cellspacing=\"0px\">\n";
for ($i=1;$i<=$linhas;$i++) {
	$lista = mysqli_fetch_array($pesquisa);
	
	if ($add=="MP")
		echo "<tr onclick=\"quantidade('quantidade.php?cod_sp=".$_GET['cod_sp']."&cod_mp=".$lista['cod_x']."')\" class=\"linhaLink\" id=\"link$i\" onmouseover=\"document.getElementById('link$i').style.background='#EEEEEE';\" onmouseout=\"document.getElementById('link$i').style.background='white';\"><a id=\"a$i\" tabindex=\"$i\" onfocus=\"document.getElementById('link$i').style.background='#EEEEEE';\" onblur=\"document.getElementById('link$i').style.background='white';\" href=\"#\" onclick=\"quantidade('quantidade.php?cod_sp=".$_GET['cod_sp']."&cod_mp=".$lista['cod_x']."')\"></a><td width=\"70px\">\n";
	else if ($add=="PR")
		echo "<tr onclick=\"quantidade('quantidade.php?cod_sp=".$_GET['cod_sp']."&cod_pr=".$lista['cod_x']."')\" class=\"linhaLink\" id=\"link$i\" onmouseover=\"document.getElementById('link$i').style.background='#EEEEEE';\" onmouseout=\"document.getElementById('link$i').style.background='white';\"><a id=\"a$i\" tabindex=\"$i\" onfocus=\"document.getElementById('link$i').style.background='#EEEEEE';\" onblur=\"document.getElementById('link$i').style.background='white';\" href=\"#\" onclick=\"quantidade('quantidade.php?cod_sp=".$_GET['cod_sp']."&cod_pr=".$lista['cod_x']."')\"></a><td width=\"70px\">\n";
	else if ($add=="SP")
		echo "<tr onclick=\"quantidade('quantidade.php?cod_sp=".$_GET['cod_sp']."&sp=".$lista['cod_x']."')\" class=\"linhaLink\" id=\"link$i\" onmouseover=\"document.getElementById('link$i').style.background='#EEEEEE';\" onmouseout=\"document.getElementById('link$i').style.background='white';\"><a id=\"a$i\" tabindex=\"$i\" onfocus=\"document.getElementById('link$i').style.background='#EEEEEE';\" onblur=\"document.getElementById('link$i').style.background='white';\" href=\"#\" onclick=\"quantidade('quantidade.php?cod_sp=".$_GET['cod_sp']."&sp=".$lista['cod_x']."')\"></a><td width=\"70px\">\n";
		
		echo $lista['cod_x']."</td><td class=\"tddescricao\" width=\"490px\">".$lista['descricao']."\n";
	echo "</td></tr>\n";
}
echo "</table></div>\n";

?>
</body>
</html>