<style type="text/css">
	.pgoff {font-family: Verdana, Arial, Helvetica; font-size: 11px; color: #FF0000; text-decoration: none}a.pg {font-family: Verdana, Arial, Helvetica; font-size: 11px; color: #003366; text-decoration: none}a:hover.pg {font-family: Verdana, Arial, Helvetica; font-size: 11px; color: #0066cc; text-decoration:underline}
</style>
<?php
	$buscapp = urlencode($busca);
	$quant_pg = ceil($quantreg/$numreg);
	$quant_pg++;
	//controle de data se houver
	if (!isset($_GET['filtro'])) {
		$data = "";
	} else {
		// $dtIniPg = $_GET['inicio'];
		// $dtFinPg = $_GET['final'];
		$filtroPg = unserialize(urldecode($_GET['filtro']));
		$filtroPg2 = urlencode(serialize($filtroPg));
		$data = "&filtro=$filtroPg2&order=$order&sent=$sent";
	}
	if (isset($_GET['buscaCodComp']) and  $_GET['buscaCodComp'] != "") $buscapp .= "&buscaCodComp=".urlencode($_GET['buscaCodComp']);
	if (isset($_GET['buscaDescComp']) and  $_GET['buscaDescComp'] != "") $buscapp .= "&buscaDescComp=".urlencode($_GET['buscaDescComp']);
	//final controle de data.
	if ($pg>$quant_pg-2) {echo "<meta http-equiv=\"refresh\" content=\"0;url=".$_SERVER['PHP_SELF']."?pg=".($quant_pg-2)."&busca=$buscapp"."$data\">";}
	// Verifica se esta na primeira página, se nao estiver ele libera o link para anterior
	if ( $pg > 0) {
 		echo "<a href=".$_SERVER['PHP_SELF']."?pg=".($pg-1) ."&busca=$buscapp"."$data class=pg><b>&laquo; anterior</b></a>";
	} else {
		echo "<font color=#CCCCCC>&laquo; anterior</font>";
	}
	// Faz aparecer os numeros das página entre o ANTERIOR e PROXIMO
	for($i_pg=1;$i_pg<$quant_pg;$i_pg++) {
		// Verifica se a página que o navegante esta e retira o link do número para identificar visualmente
		if ($pg == ($i_pg-1)) {
			if ($i_pg<10) echo "&nbsp;<span class=pgoff>[0$i_pg]</span>&nbsp;";
			else echo "&nbsp;<span class=pgoff>[$i_pg]</span>&nbsp;";
		} else {
			// controla limite de aparecer no máximo 7 páginas a menos e a mais da atual
			$i_pg2 = $i_pg-1;
			if ($i_pg>=($pg-6) and $i_pg<=($pg+8)) {
				if ($i_pg<10) echo "&nbsp;<a href=".$_SERVER['PHP_SELF']."?pg=$i_pg2"."&busca=$buscapp"."$data class=pg><b>0$i_pg</b></a>&nbsp;";
				else echo "&nbsp;<a href=".$_SERVER['PHP_SELF']."?pg=$i_pg2"."&busca=$buscapp"."$data class=pg><b>$i_pg</b></a>&nbsp;";
			} else if ($i_pg==1) {
				echo "&nbsp;<a href=".$_SERVER['PHP_SELF']."?pg=$i_pg2"."&busca=$buscapp"."$data class=pg><b>0$i_pg ... </b></a>&nbsp;";
			} else if ($i_pg==($quant_pg-1)) {
				echo "&nbsp;<a href=".$_SERVER['PHP_SELF']."?pg=$i_pg2"."&busca=$buscapp"."$data class=pg><b> ... $i_pg</b></a>&nbsp;";
			}
		}
	}
	// Verifica se esta na ultima página, se nao estiver ele libera o link para próxima
		if (($pg+2) < $quant_pg) {
			echo "<a href=".$_SERVER['PHP_SELF']."?pg=".($pg+1)."&busca=$buscapp"."$data class=pg><b>próximo &raquo;</b></a>";
		} else {
			echo "<font color=#CCCCCC>próximo &raquo;</font>";
		}
?>