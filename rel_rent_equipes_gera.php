<?
$popup = 1;
require_once("session.php");

//validação página
autorizaPagina(2);

$empresa = $_SESSION['UsuarioEmpresa'];
logMsg("rel_rent_equipes_gera.php: Inicada a criação do relatório.");
ob_start();
?>
<style>
	#tb1 {
		font-size: 10px;
		border-collapse: collapse;
	}
	#tb1 thead tr th, #tb1 tbody tr td {
		border: 1px solid;
		border-color: #000000;
	}
	#tb2 {
		font-size: 10px;
	}
	#pdf {
		font-family: 'Helvetica', 'Lucida Grande', 'Arial', sans-serif;
		font-size: 12px;
		float: left;
		width: 430px;
	}
</style>
<?

echo "<div id=\"pdf\">\n";
//orientador de filtro
if ((!empty($_GET) or !isset($_GET))) {
	// var_dump($_GET);
	$dtfatini = date("Y-m-d",strtotime(str_replace("/","-",$_GET['dtfatini'])));
	$dtfatfin = date("Y-m-d",strtotime(str_replace("/","-",$_GET['dtfatfin'])));
	$buscadtfat = " and data_nf between '$dtfatini' and '$dtfatfin' and faturado = 1";
	
	$dtpedini = date("Y-m-d",strtotime(str_replace("/","-",$_GET['dtpedini'])));
	$dtpedfin = date("Y-m-d",strtotime(str_replace("/","-",$_GET['dtpedfin'])));
	$buscadtped = " and data_ped between '$dtpedini' and '$dtpedfin'";
	
	if ($_GET['rel']=="faturamento") $dtStr = $buscadtfat;
	else if ($_GET['rel']=="entrada") $dtStr = $buscadtped;
	
	//Prints days according to the interval
	// if ($_GET['rel']=="faturamento") {
		// $dateStart 		= new DateTime(implode('-', array_reverse(explode('/', substr($_GET['dtfatini'], 0, 10)))).substr($_GET['dtfatini'], 10));
		// $dateEnd 		= new DateTime(implode('-', array_reverse(explode('/', substr($_GET['dtfatfin'], 0, 10)))).substr($_GET['dtfatfin'], 10));
	// } else if ($_GET['rel']=="entrada") {
		// $dateStart 		= new DateTime(implode('-', array_reverse(explode('/', substr($_GET['dtpedini'], 0, 10)))).substr($_GET['dtpedini'], 10));
		// $dateEnd 		= new DateTime(implode('-', array_reverse(explode('/', substr($_GET['dtpedfin'], 0, 10)))).substr($_GET['dtpedfin'], 10));
	// }
	// $dateRange = array();
	// while($dateStart <= $dateEnd){
		// $dateRange[] = $dateStart->format('Y-m-d');
		// $dateStart = $dateStart->modify('+1day');
	// }
	if ($_GET['rel']=="faturamento") {
		$dateStart = strtotime(str_replace("/","-",$_GET['dtfatini']));
		$dateEnd = strtotime(str_replace("/","-",$_GET['dtfatfin']));
	} else if ($_GET['rel']=="entrada") {
		$dateStart = strtotime(str_replace("/","-",$_GET['dtpedini']));
		$dateEnd = strtotime(str_replace("/","-",$_GET['dtpedfin']));
	}
	$dateRange = array();
	$contador = 0;
	$ultimaData = '';
	while($dateStart <= $dateEnd and $contador<=30){
		$ultimaData = date('Y-m-d',$dateStart);
		$dateRange[] = $ultimaData;
		$dateStart = strtotime('+1day',$dateStart);
		// echo $dateStart."-";
		// $contador++;
	}
	if ($ultimaData!=date('Y-m-d',$dateEnd)) $dateRange[] = date('Y-m-d',$dateEnd);
	// echo $dateEnd;
	// var_dump($dateRange);
	// exit;
	
	$filtro = $buscaFaturados.$textBusca.$buscarep.$buscacli.$buscaequipe.$buscaCodProd." ".$dtStr;
	$dtc = "ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."'";
	
	$stringEquipes = "select (select cod_equipe from representantes_equipes where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente) and dentro = '1') as cod_equipe, (select equipe from equipes_de_venda where $dtc and cod_equipe = (select cod_equipe from representantes_equipes where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente) and dentro = '1')) as equipe from rentabilidade where $dtc ".$filtro." group by equipe order by equipe";
	
	logMsg("rel_rent_equipes_gera.php: Começo da query sql.");
	$sqlEquipes = $sql->query($stringEquipes) or die (mysqli_error($sql));
	logMsg("rel_rent_equipes_gera.php: Finalizada query sql.");
	logMsg("rel_rent_equipes_gera.php: Inicio montagem php.");
	if (mysqli_num_rows($sqlEquipes)>0) {
			$impostos = mysqli_fetch_array($sql->query("select percentual from outros_impostos where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1"));
			$custo_fixo = mysqli_fetch_array($sql->query("select percentual from custo_fixo where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1"));
			$margem = mysqli_fetch_array($sql->query("select percentual from margem_fixa where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1"));
		if ($_GET['rel']=="faturamento") echo "Relatório de rentabilidade por equipe de vendas. Período de faturamento: de ".$_GET['dtfatini']." a ".$_GET['dtfatfin'].".<br><br>\n";
		else if ($_GET['rel']=="entrada") echo "Relatório de rentabilidade por equipe de vendas. Período de faturamento: de ".$_GET['dtpedini']." a ".$_GET['dtpedfin'].".<br><br>\n";
		$contribuicaoGeral = 0;
		$faturamentoGeral = 0;
		for ($i=0;$i<mysqli_num_rows($sqlEquipes);$i++) {
			$equipe = mysqli_fetch_array($sqlEquipes);
			if ($equipe['cod_equipe']!='') {
				echo "Equipe de vendas: ".$equipe['equipe'].".<br>\n";
				
				$stringRep = "select (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente) as cod_rep, if(fechado='0',(select nome from representantes where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente)),representantevar) as rep from rentabilidade where $dtc ".$filtro." and (select cod_equipe from representantes_equipes where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente) and dentro = '1') = '".$equipe['cod_equipe']."' group by rep order by rep";
				
				$sqlRep = $sql->query($stringRep) or die (mysqli_error($sql));
				echo "<table id=\"tb1\">\n";
					echo "<thead>\n";
						echo "<tr>\n";
							echo "<th width=170>REPRESENTANTE</th>\n";
							echo "<th width=80>$ RENTAB.</th>\n";
							echo "<th width=80>$ FATUR.</th>\n";
							echo "<th width=50>% IND.</th>\n";
							// echo "<th width=50>$ GER.</th>\n";
						echo "</tr>\n";
					echo "</thead>\n";
					echo "<tbody>\n";
					$contribuicaoTotal = 0;
					$faturamentoTotal = 0;
					$contribuicaoPorData = array();
					for ($ii=0;$ii<count($dateRange);$ii++) {
						$contribuicaoPorData[$ii] = 0;
					}
					for ($j=0;$j<mysqli_num_rows($sqlRep);$j++) {
						$rep = mysqli_fetch_array($sqlRep);
						echo "<tr>\n";
							echo "<td>\n";
								echo limitarTexto($rep['rep'],30);
							echo "</td>\n";
							$string = "select fechado, 
							if(data_ped='0000-00-00','-',date_format(data_ped,'%d/%m/%Y')), 
							'', 
							'', 
							'', 
							cod_sp, 
							'', 
							quant, 
							'', 
							vlr_padr, 
							vlr_nf, 
							if(fechado='0',(select icms from regioes where $dtc and cod_reg = (select regiao from clientes where $dtc and cod_cli = rentabilidade.cliente)),icms), 
							if(fechado='0',(select if((select count(comiss) from representantes_grupos where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente) and cod_grupo_prod = (select grupo from sp_dados where $dtc and cod_sp = rentabilidade.cod_sp))>0,(select comiss from representantes_grupos where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente) and cod_grupo_prod = (select grupo from sp_dados where $dtc and cod_sp = rentabilidade.cod_sp)),(select comissao_padrao from representantes where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente)))),comis), 
							'',
							'',
							marg , 
							if(faturado='0','-',date_format(data_nf,'%d/%m/%Y'))
							from rentabilidade where $dtc ".$filtro." and (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente) = '".$rep['cod_rep']."' order by data_cri";
							$faturamento = 0;
							$contribuicao = 0;
							$sqll = $sql->query($string) or die (mysqli_error($sql));
							for ($l=0;$l<mysqli_num_rows($sqll);$l++) {
								$item = mysqli_fetch_row($sqll);
								if ($item[0]=='0' and $item[9]=='0') {
									$soma = somaSp($item[5]);
									$item[9] = $soma/((100-($item[11]+$impostos['percentual']+$item[12]+$custo_fixo['percentual']+$margem['percentual']))/100);
								}
								$item[13] = $item[7]*$item[10]; //faturamento
								$faturamento += $item[13];
								if ($item[0]=='0') $item[15] = (($soma/$item[9])-($soma/$item[10]))*100; //margem
								$item[14] = $item[13]*($item[15]/100); //contribuição
								$contribuicao += $item[14];
								if ($_GET['rel']=="faturamento") $pos = array_search(date('Y-m-d',strtotime(str_replace("/","-",$item[16]))),$dateRange);
								else if ($_GET['rel']=="entrada") $pos = array_search(date('Y-m-d',strtotime(str_replace("/","-",$item[1]))),$dateRange);
								for ($mm=$pos;$mm<=count($dateRange);$mm++) {
									$contribuicaoPorData[$mm] += $item[14];
								}
							}
							
							echo "<td align=\"right\">\n";
								echo number_format($contribuicao,4,",",".");
								$contribuicaoTotal += $contribuicao;
							echo "</td>\n";
							echo "<td align=\"right\">\n";
								echo number_format($faturamento,4,",",".");
								$faturamentoTotal += $faturamento;
							echo "</td>\n";
							echo "<td align=\"right\">\n";
								echo number_format(($contribuicao/$faturamento)*100,2,",",".")."%";
							echo "</td>\n";
						echo "</tr>\n";
					}
					echo "</tbody>\n";
					echo "<tfoot>\n";
						echo "<tr>\n";
							echo "<td align=\"right\">TOTAL EQUIPE:</td><td align=\"right\">".number_format($contribuicaoTotal,4,",",".")."</td><td align=\"right\">".number_format($faturamentoTotal,4,",",".")."</td>\n";
							$contribuicaoGeral += $contribuicaoTotal;
							$faturamentoGeral += $faturamentoTotal;
						echo "</tr>\n";
					echo "</tfoot>\n";
				echo "</table>\n";
				// var_dump($contribuicaoPorData);
				$totais[] = array('nome' => $equipe['equipe'],'array'=>$contribuicaoPorData);
				if ($i != mysqli_num_rows($sqlEquipes)-1) echo "<br>\n";
			} else $repNull = true;
		}
		// var_dump($totais);
		echo "<table id=\"tb1\">\n";
			echo "<tfoot>\n";
				echo "<tr>\n";
					echo "<td width=\"170\" align=\"right\"><b>TOTAL GERAL EQUIPES:</b></td>\n";
					echo "<td width=\"80\" align=\"right\"><b>".number_format($contribuicaoGeral,4,",",".")."</b></td>\n";
					echo "<td width=\"80\" align=\"right\"><b>".number_format($faturamentoGeral,4,",",".")."</b></td>\n";
					echo "<td width=\"50\" align=\"right\"><b>".number_format(($contribuicaoGeral/$faturamentoGeral)*100,2,",","")."%</b></td>\n";
				echo "</tr>\n";
			echo "</tfoot>\n";
		echo "</table>\n";
		if ($repNull) echo "*Existem representantes sem equipe selecionada, com faturamento no período selecionado.<br>**Este relatório não considera o faturamento destes representantes.";
	}
echo "</div>\n";

echo "<div style=\"float: left; margin-left: 10px; margin-top: 32px;\">\n";
	logMsg("rel_rent_equipes_gera.php: Inicio montagem grafico.");
	include("biblios/pChart/class/pData.class.php");
	include("biblios/pChart/class/pDraw.class.php");
	include("biblios/pChart/class/pImage.class.php");
	
	$myData = new pData();
	
	for ($arr=0;$arr<count($dateRange);$arr++) {
		$dia = explode("-",$dateRange[$arr]);
		$array[$arr] = $dia[2];
	}
	$myData->addPoints($array,'datas');
	$myData->setSerieDescription('$datas','datas');
	$myData->setSerieOnAxis('datas',0);
	
	for ($m=0;$m<count($totais);$m++) {
		$array = $totais[$m];
		$nome = $array['nome'];
		$myData->addPoints($array['array'],$nome);
		$myData->setSerieDescription($nome,$nome);
		$myData->setSerieOnAxis($nome,0);
	}
	$myData->setAbscissa("datas");
	
	$myData->setAxisPosition(0,AXIS_POSITION_LEFT);
	$myData->setAxisName(0,"RENTABILIDADE");
	$myData->setAxisUnit(0,"");
	
	$myPicture = new pImage(600,350,$myData);
	$myPicture->drawRectangle(0,0,599,349,array("R"=>0,"G"=>0,"B"=>0));
	
	$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>20));
	
	$myPicture->setShadow(FALSE);
	$myPicture->setGraphArea(50,20,579,310);
	$myPicture->setFontProperties(array("R"=>0,"G"=>0,"B"=>0,"FontName"=>"biblios/pChart/fonts/pf_arma_five.ttf","FontSize"=>6));
	
	$Settings = array("Pos"=>SCALE_POS_LEFTRIGHT
	, "Mode"=>SCALE_MODE_FLOATING
	, "LabelingMethod"=>LABELING_ALL
	, "GridR"=>255, "GridG"=>255, "GridB"=>255, "GridAlpha"=>50, "TickR"=>0, "TickG"=>0, "TickB"=>0, "TickAlpha"=>50, "LabelRotation"=>0, "CycleBackground"=>1, "DrawXLines"=>1, "DrawSubTicks"=>1, "SubTickR"=>255, "SubTickG"=>0, "SubTickB"=>0, "SubTickAlpha"=>50, "DrawYLines"=>ALL);
	$myPicture->drawScale($Settings);
	
	$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>10));
	
	$Config = "";
	$myPicture->drawLineChart($Config);
	
	$Config = array("FontR"=>0, "FontG"=>0, "FontB"=>0, "FontName"=>"biblios/pChart/fonts/pf_arma_five.ttf", "FontSize"=>6, "Margin"=>4, "Alpha"=>30, "BoxSize"=>5, "Style"=>LEGEND_NOBORDER
	, "Mode"=>LEGEND_HORIZONTAL
	, "Family"=>LEGEND_FAMILY_CIRCLE
	);
	$myPicture->drawLegend(40,333,$Config);
	
	$random = session_id().mt_rand();
	$myPicture->Render("temp/grafrelrentequi".$random.".png");
	echo "<img src=\"temp/grafrelrentequi".$random.".png\">";
	logMsg("rel_rent_equipes_gera.php: Final montagem grafico.");
echo "</div>\n";

	$pdf = ob_get_contents();
	ob_end_clean();
	logMsg("rel_rent_equipes_gera.php: Final montagem php.");
	
	logMsg("rel_rent_equipes_gera.php: Inicio montagem pdf.");
	include("biblios/mpdf60/mpdf.php");
	$mpdf=new mPDF('pt',array(205,295),3,'',8,8,10,10,9,9); 
	$mpdf->SetDisplayMode('fullwidth');
	// $css = file_get_contents("style.css");
	// $mpdf->WriteHTML($css,1);
	$mpdf->AddPage('L');
	$mpdf->WriteHTML($pdf);
	$diret = "temp/";
	if (!is_dir($diret)) mkdir($diret);
	$arquivo = $diret.'relrentequi'.$random.'.pdf';
	$mpdf->Output($arquivo,'F');
	
	echo "<object type=\"application/pdf\"  data=\"$arquivo?#zoom=75\" style=\"width: 100%; height: 100%;\">\n";
	echo "<a href=\"$arquivo?#zoom=75\">Ver PDF</a> <-- Para navegadores que não suportam object -->\n";
	echo "</object>";
	logMsg("rel_rent_equipes_gera.php: Final montagem pdf.");
}
// if ((!empty($_GET) or !isset($_GET)) and $_GET['rel']=="entrada") {
// 	var_dump($_GET);
// }
// echo "</div>\n";
logMsg("rel_rent_equipes_gera.php: finalizado processo de relatorio.");
?>