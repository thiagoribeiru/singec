<?
require_once("session.php");
?>
<html>
<head>
	<link rel="stylesheet" href="jquery-ui.css"/>
    <!--<script src="jquery-1.8.2.js"></script>-->
    <script src="jquery-ui.js"></script>
    <script src="js/gera_rel_equipes.js"></script>
<title><?echo $title;?></title>
<style>
	.filtro_dtped, .filtro_dtfat {
		margin-left: 10px;
		font-size: 11px;
		float: left;
		border: solid 1px #A4A4A4;
		background: #D8D8D8;
		padding: 3px;
	}
	.filtro_dtfat {
		margin-top: 10px;
	}
	#confirma, #limpar {
		height: 16px;
		vertical-align: bottom;
		margin-left: 3px;
		cursor: pointer;
	}
</style>
</head>
<body onunload="window.opener.location.reload();">
<?
//validação página
autorizaPagina(2);

//painel boas vindas
require_once("welcome.php");
echo "<div id=\"divmenu\">\n";
	require_once("menu.php");
echo "\n</div>";
	
	echo "<div class=\"tabelacorpo\">";
		//tabela
		echo "<div style=\"width: 1215px;\">";
			
			echo "<div style=\"width: 280px; float: left; margin-bottom: 10px;\"><form action=\"rel_rent_equipes_gera3.php\" method=\"POST\" style=\"margin:0;\" id=\"formpesq\">";
				//filtro data pedido
				echo "<div class=\"filtro_dtped\" id=\"divfiltrodtped\">";
					echo "<label for=\"txDtpedIni\"><b>Filtro por Data de Entrada:</b></label><br>";
					echo "De <input type=\"text\" name=\"dtpedini\" id=\"txDtpedIni\" value=\"".$fDtPed['inicial']."\" style=\"height:18px; width: 80px; bottom:-1px; position: relative;\" onclick=\"this.select()\" maxlength=\"10\"/>";
					echo " até <input type=\"text\" name=\"dtpedfin\" id=\"txDtpedFin\" value=\"".$fDtPed['final']."\" style=\"height:18px; width: 80px; bottom:-1px; position: relative;\" onclick=\"this.select()\" maxlength=\"10\"/>";
					// echo "<img id=\"confirma\" src=\"images/filtrar.gif\" onclick=\"document.getElementById('formpesq').submit()\">";
					echo "<img id=\"confirma\" src=\"images/filtrar.gif\">";
					echo "<img id=\"limpar\" src=\"images/limpar.png\" onclick=\"document.getElementById('txDtpedIni').value=''; document.getElementById('txDtpedFin').value=''; \">";
				echo "</div>";
				//filtro data faturamento
				echo "<div class=\"filtro_dtfat\" id=\"divfiltrodtfat\">";
					echo "<label for=\"txDtfatIni\"><b>Filtro por Data de Faturamento:</b></label><br>";
					echo "De <input type=\"text\" name=\"dtfatini\" id=\"txDtfatIni\" value=\"".$fDtFat['inicial']."\" style=\"height:18px; width: 80px; bottom:-1px; position: relative;\" onclick=\"this.select()\" maxlength=\"10\"/>";
					echo " até <input type=\"text\" name=\"dtfatfin\" id=\"txDtfatFin\" value=\"".$fDtFat['final']."\" style=\"height:18px; width: 80px; bottom:-1px; position: relative;\" onclick=\"this.select()\" maxlength=\"10\"/>";
					// echo "<img id=\"confirma\" src=\"images/filtrar.gif\" onclick=\"document.getElementById('formpesq').submit()\">";
					echo "<img id=\"confirma\" src=\"images/filtrar.gif\">";
					echo "<img id=\"limpar\" src=\"images/limpar.png\" onclick=\"document.getElementById('txDtfatIni').value=''; document.getElementById('txDtfatFin').value=''; \">";
				echo "</div>";
			echo "</form></div>";
			
			echo "<div id=\"relatoriopdf\" style=\"float: left; width: 935px; height: 500px;\">\n";
				
			echo "</div>\n";
		
			echo "<div style=\"float: left; margin-left: 25px; margin-top: 15px;\">";
			// var_dump($dados);
			echo "</div>";
			
		echo "</div>";
	echo "</div>";

?>
</body>
</html>