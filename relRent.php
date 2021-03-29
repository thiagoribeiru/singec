<?
ini_set('memory_limit', '-1');
$popup = 1;
require_once("session.php");

//validação página
autorizaPagina(2);
teclaEsc();
?>
<html>
<head>
<title><?echo $title;?></title>
</head>
<style>
	body {
		margin: 0px;
	}
</style>
<?ob_start();?>
<style>
	body {
		font-family: 'Helvetica', 'Lucida Grande', 'Arial', sans-serif;
	}
	#pageNav {
		float: left;
		width: 800px;
		padding: 5px;
	}
	#tb1 tbody tr td div {
		height: 14px;
		overflow: auto;
	}
	#tb1 {
		font-size: 9px;
		border-collapse: collapse;
		border: 1px solid;
	}
	#tb1 thead tr th, #tb1 tbody tr td {
		border: 1px solid;
		border-color: #000000;
	}
</style>
<body>
<?
	echo "<div style=\"width: 1318px;\">";
	echo "<table id=\"tb1\">\n";
		echo "<thead><tr height=20px>\n";
			echo "<th width=50px><center>"."DT.PED"."</center></th>\n";
			echo "<th width=180px><center>"."CLIENTE"."</center></th>\n";
			echo "<th width=50px><center>"."REGIÃO"."</center></th>\n";
			echo "<th width=140px><center>"."REPRESENTANTE"."</center></th>\n";
			echo "<th width=50px><center>"."PROD."."</center></th>\n";
			echo "<th width=260px><center>"."DESCRIÇÃO"."</center></th>\n";
			echo "<th width=50px><center>"."QUANT."."</center></th>\n";
			echo "<th width=20px><center>"."UND"."</center></th>\n";
			echo "<th width=60px><center>"."VLR. PADR."."</center></th>\n";
			echo "<th width=60px><center>"."VLR. NF"."</center></th>\n";
			echo "<th width=52px><center>"."(-) ICMS"."</center></th>\n";
			echo "<th width=52px><center>"."(-) COMIS"."</center></th>\n";
			echo "<th width=80px><center>"."TOTAL FAT."."</center></th>\n";
			echo "<th width=80px><center>"."CONTRIBUIÇÃO"."</center></th>\n";
			echo "<th width=40px><center>"."MARG."."</center></th>\n";
			echo "<th width=50px><center>"."DT.FAT"."</center></th>\n";
		echo "</tr></thead><tbody>\n";
		
		$fechamento = unserialize($_SESSION['relatorio']);
		$quantreg = $_SESSION['quantfechamento'];
		
		$fechado = $fechamento['fechado'];
		$cod_rent = $fechamento['cod_rent'];
		$data_nf_array = $fechamento['data_nf_array'];
		$cliente = $fechamento['cliente'];
		$regiao = $fechamento['regiao'];
		$representante = $fechamento['representante'];
		$prod = $fechamento['prod'];
		$descricao = $fechamento['descricao'];
		$quant = $fechamento['quant'];
		$und = $fechamento['und'];
		$vlrpadrao = $fechamento['vlrpadrao'];
		$vlr_nf = $fechamento['vlr_nf'];
		$icms = $fechamento['icms'];
		$comissao_padrao = $fechamento['comissao_padrao'];
		$total_nf = $fechamento['total_nf'];
		$contribuicao = $fechamento['contribuicao'];
		$margem_item = $fechamento['margem_item'];
		$data_ped_array = $fechamento['data_ped'];
		$faturado = $fechamento['faturado'];
		
		for ($i=0;$i<$quantreg;$i++) {
			// if ($fechado[$i]==0) {
				echo "<tr height=20px>\n";
					if ($data_ped_array[$i]!="" and $data_ped_array[$i]!="-") $data_ped = date('d/m/y',strtotime($data_ped_array[$i]));
					else if ($data_ped_array[$i]=="-") $data_ped = "-";
					else $data_ped = "";
					echo "<td><center>".$data_ped."</td>";
					echo "<td $fundocli><div style=\"width: 140px; float: left;\" id=\"cliente$i\">".limitarTexto($cliente[$i],27)."</div>";
					echo "<div style=\"width: 18px; height: 14px; float: right;\" id=\"clienteseta$i\"></div></td>";
					echo "<td><div style=\"width: 30px; float: left;\" id=\"regiao$i\">".limitarTexto($regiao[$i],7)."</div>";
					echo "<div style=\"width: 18px; height: 14px; float: right;\" id=\"regiaoseta$i\"></div></td>";
					echo "<td $fundorep><div style=\"width: 120px; float: left;\" id=\"representante$i\">".limitarTexto($representante[$i],26)."</div>";
					echo "<div style=\"width: 18px; height: 14px; float: right;\" id=\"representanteseta$i\"></div></td>";
					echo "<td>".$prod[$i]."</td>";
					echo "<td><div style=\"width: 240px; float: left;\" id=\"descricao$i\">".limitarTexto($descricao[$i],45)."</div>";
					echo "<div style=\"width: 18px; height: 14px; float: right;\" id=\"descricaoseta$i\"></div></td>";
					echo "<td>".number_format($quant[$i],2,".","")."</td>";
					echo "<td>".$und[$i]."</td>";
					echo "<td>".number_format($vlrpadrao[$i],4,".","")."</td>";
					echo "<td>".number_format($vlr_nf[$i],4,".","")."</td>";
					echo "<td>".number_format($icms[$i],4,".","")."%</td>";
					echo "<td>".number_format($comissao_padrao[$i],4,".","")."%</td>";
					echo "<td align=\"right\">".number_format($total_nf[$i],4,".","")."</td>";
					$subTotalCont = $subTotalCont + $contribuicao[$i];
					echo "<td align=\"right\">".number_format($contribuicao[$i],4,".","")."</td>";
					echo "<td>".number_format($margem_item[$i],2,".","")."%</td>";
					if ($data_nf_array[$i]!="" and $data_nf_array[$i]!="-") $data_nf = date('d/m/y',strtotime($data_nf_array[$i]));
					else if ($data_nf_array[$i]=="-") $data_nf = "-";
					else $data_nf = "";
					echo "<td><center>".$data_nf."</td>";
				echo "</tr>\n";
			// }
		}
	echo "</tbody></table>\n";
	echo "</div>";
	
	echo "<div id=\"pageNav\" align=\"center\">";
	echo "</div>";
	echo "<div style=\"float: left; width: 210px; padding: 3px;\" align=\"right\"><table style=\"border: 1px solid; text-align: right; font-size:8px; font-family: 'Helvetica', 'Lucida Grande', 'Arial', sans-serif;\">";
		//TOTALIZADORES
		for ($t=0;$t<$quantreg;$t++) {
			// $total = mysqli_fetch_array($sql_conta);
			$totalFat = $totalFat + $total_nf[$t];
			// $vlrpadraoTot = somaSp($total['prod'])/((100-($total['icms']+$impostos['percentual']+$total['comissao_padrao']+$custo_fixo['percentual']+$margem['percentual']))/100);
			$totalCont = $totalCont + /*(($total['quant']*$total['vlr_nf'])-($total['quant']*$vlrpadraoTot))*/$contribuicao[$t];
		}
		echo "<tr><td><b>SubTotal:</b></td><td width=\"65px\"><b>".number_format($totalFat,2,",",".")."</b></td><td width=\"63px\"><b>".number_format($totalCont,2,",",".")."</b></td><td width=\"31px\"><b>".number_format(($totalCont/$totalFat)*100,2,",",".")."%</b></td></tr>";
		echo "</table>";
	echo "</div>";
?>
</body>
<?
$pdf = ob_get_contents();
ob_end_clean();
	
	include("biblios/mpdf60/mpdf.php");
	$mpdf=new mPDF('pt',array(205,295),3,'',8,8,10,10,9,9); 
	$mpdf->SetDisplayMode('fullwidth');
	// $css = file_get_contents("style.css");
	// $mpdf->WriteHTML($css,1);
	$mpdf->AddPage('L');
	$mpdf->WriteHTML($pdf);
	$diret = "temp/";
	if (!is_dir($diret)) mkdir($diret);
	$arquivo = $diret.'relRentU'.session_id().'.pdf';
	$mpdf->Output($arquivo,'F');
	
	echo "<object type=\"application/pdf\"  data=\"$arquivo?#zoom=100\" style=\"width: 100%; height: 100%;\">\n";
	echo "<a href=\"$arquivo?#zoom=100\">Ver PDF</a> <-- Para navegadores que não suportam object -->\n";
	echo "</object>";

?>
</html>