<?
require_once("session.php");
?>
<html>
<head>
	<link rel="stylesheet" href="jquery-ui.css"/>
    <!--<script src="jquery-1.8.2.js"></script>-->
    <script src="jquery-ui.js"></script> 
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

	$empresa = $_SESSION['UsuarioEmpresa'];
	//orientador de filtro
	if (!isset($_GET['filtro'])) {
		$filtro = array();
		$filtroSer = serialize($filtro);
		echo "<script>location.href=\"".$_SERVER['PHP_SELF']."?filtro=$filtroSer\"</script>";
		exit;
	} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
		$filtrodtped['inicial'] = $_POST['dtpedini'];
		$filtrodtped['final'] = $_POST['dtpedfin'];
		$filtrodtfat['inicial'] = $_POST['dtfatini'];
		$filtrodtfat['final'] = $_POST['dtfatfin'];
		$filtro['filtrodtped'] = $filtrodtped;
		$filtro['filtrodtfat'] = $filtrodtfat;
		$filtroSer = urlencode(serialize($filtro));
		echo "<script>location.href=\"".$_SERVER['PHP_SELF']."?filtro=$filtroSer\"</script>";
		exit;
	} else if ($_SERVER['REQUEST_METHOD'] != "POST") {
		$filtro = unserialize(urldecode($_GET['filtro']));
		$fDtPed = $filtro['filtrodtped']; //filtro de data do pedido
		$fDtFat = $filtro['filtrodtfat']; //filtro de data de faturamento
	}
	
	if (isset($frep) or isset($fcli) or isset($fDtPed) or isset($fDtFat) or isset($fequipe)) {
		if ($fDtPed['inicial']!="" and $fDtPed['final']!="") {
			$dtpedini = date("Y-m-d",strtotime(str_replace("/","-",$fDtPed['inicial'])));
			$dtpedfin = date("Y-m-d",strtotime(str_replace("/","-",$fDtPed['final'])));
			$buscadtped = " and data_ped >= '$dtpedini' and data_ped <= '$dtpedfin'";
		} else $buscadtped = "";
		
		if ($fDtFat['inicial']!="" and $fDtFat['final']!="") {
			$dtfatini = date("Y-m-d",strtotime(str_replace("/","-",$fDtFat['inicial'])));
			$dtfatfin = date("Y-m-d",strtotime(str_replace("/","-",$fDtFat['final'])));
			$buscadtfat = " and data_nf >= '$dtfatini' and data_nf <= '$dtfatfin' and faturado = 1";
		} else $buscadtfat = "";
		
	} else {
		$buscadtped = "";
		$buscadtfat = "";
	}
	
	echo "<div class=\"tabelacorpo\">";
	//tabela
	echo "<div style=\"width: 1215px;\">";
	echo "<div style=\"width: 280px; float: left; margin-bottom: 10px;\"><form action=\"".$_SERVER['PHP_SELF']."?pg=$pg&busca=$busca&order=$order&sent=$sent&filtro=".$_GET['filtro']."\" method=\"POST\" style=\"margin:0;\" id=\"formpesq\">";
		//filtro data pedido
		// echo "<div class=\"filtro_dtped\" id=\"divfiltrodtped\">";
		// 	echo "<label for=\"txDtpedIni\"><b>Filtro por Data de Entrada:</b></label><br>";
		// 	echo "De <input type=\"text\" name=\"dtpedini\" id=\"txDtpedIni\" value=\"".$fDtPed['inicial']."\" style=\"height:18px; width: 80px; bottom:-1px; position: relative;\" onKeyPress=\"return submitenter(this,event)\" onclick=\"this.select()\" maxlength=\"10\" onKeyUp=\"barra(this)\"/>";
		// 	echo " até <input type=\"text\" name=\"dtpedfin\" id=\"txDtpedFin\" value=\"".$fDtPed['final']."\" style=\"height:18px; width: 80px; bottom:-1px; position: relative;\" onKeyPress=\"return submitenter(this,event)\" onclick=\"this.select()\" maxlength=\"10\" onKeyUp=\"barra(this)\"/>";
		// 	echo "<img id=\"confirma\" src=\"images/filtrar.gif\" onclick=\"document.getElementById('formpesq').submit()\">";
		// 	echo "<img id=\"limpar\" src=\"images/limpar.png\" onclick=\"document.getElementById('txDtpedIni').value=''; document.getElementById('txDtpedFin').value=''; \">";
		// echo "</div>";
		// echo " | ";
		//filtro data faturamento
		echo "<div class=\"filtro_dtfat\" id=\"divfiltrodtfat\">";
			echo "<label for=\"txDtfatIni\"><b>Filtro por Data de Faturamento:</b></label><br>";
			echo "De <input type=\"text\" name=\"dtfatini\" id=\"txDtfatIni\" value=\"".$fDtFat['inicial']."\" style=\"height:18px; width: 80px; bottom:-1px; position: relative;\" onKeyPress=\"return submitenter(this,event)\" onclick=\"this.select()\" maxlength=\"10\" onKeyUp=\"barra(this)\"/>";
			echo " até <input type=\"text\" name=\"dtfatfin\" id=\"txDtfatFin\" value=\"".$fDtFat['final']."\" style=\"height:18px; width: 80px; bottom:-1px; position: relative;\" onKeyPress=\"return submitenter(this,event)\" onclick=\"this.select()\" maxlength=\"10\" onKeyUp=\"barra(this)\"/>";
			echo "<img id=\"confirma\" src=\"images/filtrar.gif\" onclick=\"document.getElementById('formpesq').submit()\">";
			echo "<img id=\"limpar\" src=\"images/limpar.png\" onclick=\"document.getElementById('txDtfatIni').value=''; document.getElementById('txDtfatFin').value=''; \">";
		echo "</div>";
	echo "</form></div>";
	
	ob_start();
	?>
	<style>
		#tb1 {
			font-size: 10px;
			border-collapse: collapse;
			/*border: solid 1px;*/
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
		}
	</style>
	<?
	
	$impostos = mysqli_fetch_array($sql->query("select percentual from outros_impostos where empresa = $empresa and ativo = 1"));
	$custo_fixo = mysqli_fetch_array($sql->query("select percentual from custo_fixo where empresa = $empresa and ativo = 1"));
	$margem = mysqli_fetch_array($sql->query("select percentual from margem_fixa where empresa = $empresa and ativo = 1"));
	
	    //Star date
	    $dateStart 		= $fDtFat['inicial'];
	    $dateStart 		= implode('-', array_reverse(explode('/', substr($dateStart, 0, 10)))).substr($dateStart, 10);
	    $dateStart 		= new DateTime($dateStart);
	 
	    //End date
	    $dateEnd 		= $fDtFat['final'];
	    $dateEnd 		= implode('-', array_reverse(explode('/', substr($dateEnd, 0, 10)))).substr($dateEnd, 10);
	    $dateEnd 		= new DateTime($dateEnd);
	 
	    //Prints days according to the interval
	    $dateRange = array();
	    while($dateStart <= $dateEnd){
	        $dateRange[] = $dateStart->format('Y-m-d');
	        $dateStart = $dateStart->modify('+1day');
	    }
	    
	    $posDados = 0;
	    $dateRange[count($dateRange)] = "datas";
	    $dados[$posDados] = $dateRange;
	    $posDados++;
	
	$somaRentTotal = 0;
	$somaFatTotal = 0;
	$titulo = 1;
	if (($fDtPed['inicial']!="" and $fDtPed['final']!="") or ($fDtFat['inicial']!="" and $fDtFat['final']!="")) {
		$pesquisaEquipes = $sql->query("select cod_equipe, equipe from equipes_de_venda where ativo = 1 and empresa = '$empresa' order by equipe") or die (mysqli_error($sql));
		if (mysqli_num_rows($pesquisaEquipes)>0) {
			$id_div = 0;
			echo "<div id=\"pdf\">\n";
			while ($equipe = mysqli_fetch_array($pesquisaEquipes)) {
				$pesquisaRepresentantes = $sql->query("select (select representante from clientes where ativo = 1 and empresa = $empresa and cod_cli = rentabilidade.cliente) as cod_rep, (select nome from representantes where ativo = 1 and empresa = $empresa and cod_rep = (select representante from clientes where ativo = 1 and empresa = $empresa and cod_cli = rentabilidade.cliente)) as representante from rentabilidade where (SELECT concat(',',group_concat(cod_equipe order by cod_equipe, ','),',') FROM representantes_equipes WHERE cod_rep = (select representante from clientes where ativo = 1 and empresa = '$empresa' and cod_cli = rentabilidade.cliente) and empresa = '$empresa' and ativo = 1 and dentro = 1) like '%,".$equipe['cod_equipe'].",%' and ativo = 1 and empresa = '$empresa' $buscadtfat group by cod_rep order by representante") or die (mysqli_error($sql));
				if (mysqli_num_rows($pesquisaRepresentantes)>0) {
					if ($titulo==1) {
						echo "Relatório de rentabilidade por equipe de vendas.\n";
						if ($fDtPed['inicial']!="" and $fDtPed['final']!="") echo "Período de entrada: de ".$fDtPed['inicial']." a ".$fDtPed['final'].".\n";
						if ($fDtFat['inicial']!="" and $fDtFat['final']!="") echo "Período de faturamento: de ".$fDtFat['inicial']." a ".$fDtFat['final'].".\n";
						echo "<br>\n";
						$titulo++;
						echo "<div style=\"float: left; width: 430px;\">\n";
					}
					echo "<br>\n";
					echo "Equipe de Vendas: ".$equipe['equipe'].".\n";
					echo "<table id=\"tb1\">\n";
						echo "<thead>\n";
							echo "<tr>\n";
								echo "<th>REPRESENTANTE</th>\n";
								echo "<th>$ RENTAB.</th>\n";
								echo "<th>$ FATUR.</th>\n";
								echo "<th>% IND.</th>\n";
								echo "<th>$ GER.</th>\n";
							echo "</tr>\n";
						echo "</thead>\n";
						$somaRent = 0;
						$somaFat = 0;
						$tab1representante = array();
						$tab1cod_rep = array();
						$tab1rentabilidade = array();
						$tab1faturamento = array();
						$grafNomes = array();
						$grafValores = array();
						while ($representante = mysqli_fetch_array($pesquisaRepresentantes)) {
							$tab1cod_rep[] = $representante['cod_rep'];
							$tab1representante[] = $representante['representante'];
							$pesqDatas = $sql->query("select data_nf from rentabilidade where (select representante from clientes where ativo = 1 and empresa = '$empresa' and cod_cli = rentabilidade.cliente) = '".$representante['cod_rep']."' and ativo = 1 and empresa = '$empresa' $buscadtfat group by data_nf") or die (mysqli_error($sql));
							$vecDatas = array();
							while ($data = mysqli_fetch_array($pesqDatas)) {
								$vecDatas[] = $data['data_nf'];
							}
							sort($vecDatas);
							// var_dump($vecDatas);
								$totalRent = 0;
								$totalFat = 0;
							for ($dt=0;$dt<count($vecDatas);$dt++) {
								$pesquisaRentabilidade = $sql->query("select fechado, quant, vlr_nf, (select icms from regioes where ativo = 1 and empresa = rentabilidade.empresa and cod_reg = (select regiao from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente)) as icms, (select comissao_padrao from representantes where ativo = 1 and empresa = rentabilidade.empresa and cod_rep = (select representante from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente)) as comissao_padrao, (select grupo from sp_dados where ativo = 1 and empresa = rentabilidade.empresa and cod_sp = rentabilidade.cod_sp) as grupo, cod_sp as prod, marg from rentabilidade where (select representante from clientes where ativo = 1 and empresa = '$empresa' and cod_cli = rentabilidade.cliente) = '".$representante['cod_rep']."' and ativo = 1 and empresa = '$empresa' and data_nf = '".$vecDatas[$dt]."' and faturado = 1") or die (mysqli_error($sql));
								$totalRentRent = 0;
								$totalFatFat = 0;
								while ($rentabilidade = mysqli_fetch_array($pesquisaRentabilidade)) {
									if ($rentabilidade['fechado']==0) {
										$comissPesq = $sql->query("select comiss from representantes_grupos where cod_rep = '".$representante['cod_rep']."' and ativo = 1 and empresa = '$empresa' and cod_grupo_prod = '".$rentabilidade['grupo']."'");
										if (mysqli_num_rows($comissPesq)>0) {
											$comissGrupo = mysqli_fetch_array($comissPesq);
											$comissao = $comissGrupo['comiss'];
										} else {
											$comissao = $rentabilidade['comissao_padrao'];
										}
										$vlrpadraovar = somaSp($rentabilidade['prod'])/((100-($rentabilidade['icms']+$impostos['percentual']+$comissao+$custo_fixo['percentual']+$margem['percentual']))/100);
										$margem_guia = ((($rentabilidade['vlr_nf']*(100-($rentabilidade['icms']+$impostos['percentual']+$comissao)))-($vlrpadraovar*(100-($rentabilidade['icms']+$impostos['percentual']+$comissao+$margem['percentual']))))/$rentabilidade['vlr_nf'])-$margem['percentual'];
										$totalRentRent = $totalRentRent + (($rentabilidade['quant']*$rentabilidade['vlr_nf'])*($margem_guia/100));
										// $totalFatFat = $totalFatFat + ($rentabilidade['quant']*$rentabilidade['vlr_nf']);
									} else if ($rentabilidade['fechado']==1) {
										$totalRentRent = $totalRentRent + (($rentabilidade['quant']*$rentabilidade['vlr_nf'])*($rentabilidade['marg']/100));
										// $totalFatFat = $totalFatFat + ($rentabilidade['quant']*$rentabilidade['vlr_nf']);
									}
									$totalFatFat = $totalFatFat + ($rentabilidade['quant']*$rentabilidade['vlr_nf']);
								}
								$totalRent = $totalRent + $totalRentRent;
								$totalFat = $totalFat + $totalFatFat;
								$grafNomes[$vecDatas[$dt]] = $vecDatas[$dt];
								$grafValores[$vecDatas[$dt]] = $grafValores[$vecDatas[$dt]] + $totalRentRent;
							}
							$somaRent = $somaRent + $totalRent;
							$somaFat = $somaFat + $totalFat;
							$tab1rentabilidade[] = $totalRent;
							$tab1faturamento[] = $totalFat;
						}
						// var_dump($grafNomes);
						// var_dump($grafValores);
						// var_dump($tab1representante);
						// var_dump($tab1cod_rep);
							$array = array();
							$acumulado = 0;
							for ($dd=0;$dd<count($dateRange);$dd++) {
								if ($dd<count($dateRange)-1) {
									if ($grafValores[$dateRange[$dd]]!=null) {
										$acumulado = $acumulado + $grafValores[$dateRange[$dd]];
										$array[$dd] = $acumulado;
									} else $array[$dd] = $acumulado;
								} else if ($dd==count($dateRange)-1) {
									$array[count($dateRange)-1] = $equipe['equipe'];
								}
								$dados[$posDados] = $array;
							}
							$posDados++;
						$somaRentTotal = $somaRentTotal + $somaRent;
						$somaFatTotal = $somaFatTotal + $somaFat;
						echo "<tfoot>\n";
							echo "<tr>";
								echo "<td align=\"right\" width=\"170px\"><div style=\"width: 170px; height: 15px; overflow: auto;\">TOTAL EQUIPE:</div></td>\n";
								echo "<td width=\"80px\" align=\"right\">".number_format($somaRent,4,",",".")."</td>\n";
								echo "<td width=\"80px\" align=\"right\">".number_format($somaFat,4,",",".")."</td>\n";
								echo "<td width=\"45px\" align=\"right\"></td>\n";
								echo "<td width=\"45px\" align=\"right\"></td>\n";
							echo "</tr>";
						echo "</tfoot>\n";
						echo "<tbody>\n";
							echo "<tr>\n";
							$linha = 0;
							while ($linha < count($tab1representante)) {
								echo "<tr>";
									echo "<td width=\"170px\"><div style=\"width: 170px; height: 15px; overflow: auto;\">".limitarTexto($tab1representante[$linha],30)."</div></td>\n";
									echo "<td width=\"80px\" align=\"right\">".number_format($tab1rentabilidade[$linha],4,",",".")."</td>\n";
									echo "<td width=\"80px\" align=\"right\">".number_format($tab1faturamento[$linha],4,",",".")."</td>\n";
									echo "<td width=\"45px\" align=\"right\">".number_format(($tab1rentabilidade[$linha]/$tab1faturamento[$linha])*100,2,",","")."%</td>\n";
									echo "<td width=\"45px\" align=\"right\">".number_format(($tab1rentabilidade[$linha]/$somaFatTotal)*100,2,",","")."%</td>\n";
								echo "</tr>";
								$linha++;
							}
							echo "</tr>\n";
						echo "</tbody>\n";
					echo "</table>\n";
					$id_div++;
				}
			}
			// echo "<div>\n";
				echo "<table id=\"tb2\">\n";
					echo "<tr>";
						echo "<td align=\"right\" width=\"170px\"><div style=\"width: 170px; height: 15px; overflow: auto;\"><b>TOTAL EQUIPES:</b></div></td>\n";
						echo "<td width=\"80px\" align=\"right\"><b>".number_format($somaRentTotal,4,",",".")."</b></td>\n";
						echo "<td width=\"80px\" align=\"right\"><b>".number_format($somaFatTotal,4,",",".")."</b></td>\n";
						echo "<td width=\"45px\" align=\"right\"><b>".number_format(($somaRentTotal/$somaFatTotal)*100,2,",","")."%</b></td>\n";
						// echo "<td width=\"40px\" align=\"right\"></td>\n";
					echo "</tr>";
				echo "</table>\n";
			echo "</div>\n";
			echo "<div style=\"float: left; margin-left: 10px; margin-top: 32px;\">\n";
				include("biblios/pChart/class/pData.class.php");
				include("biblios/pChart/class/pDraw.class.php");
				include("biblios/pChart/class/pImage.class.php");
				
				$myData = new pData();
				
				for ($m=0;$m<count($dados);$m++) {
					$array = $dados[$m];
					$nome = $array[count($array)-1];
					unset($array[count($array)-1]);
					if ($m==0) {
						for ($arr=0;$arr<count($array);$arr++) {
							$dia = explode("-",$array[$arr]);
							$array[$arr] = $dia[2];
						}
					}
					$myData->addPoints($array,$nome);
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
				
				$myPicture->Render("temp/grafrelrentequi".session_id().".png");
				echo "<img src=\"temp/grafrelrentequi".session_id().".png\">";
				
			echo "</div>\n";
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
			$arquivo = $diret.'relrentequi'.session_id().'.pdf';
			$mpdf->Output($arquivo,'F');
			
			echo "<object type=\"application/pdf\"  data=\"$arquivo?#zoom=75\" style=\"width: 935px; height: 75%;\">\n";
			echo "<a href=\"$arquivo?#zoom=75\">Ver PDF</a> <-- Para navegadores que não suportam object -->\n";
			echo "</object>";
		}
		echo "</div>\n";
		
		
	}
	
	echo "<div style=\"float: left; margin-left: 25px; margin-top: 15px;\">";
	// var_dump($dados);
	echo "</div>";
	echo "</div>";
	echo "</div>";

?>
<script>
$(function() {
    $( "#txDtpedIni" ).datepicker({
		dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
    	// showOn: "button",
    	showOn: "both",
        buttonImage: "images/icon-calendario11x11.png",
        buttonImageOnly: true, 
		showButtonPanel: true,
		currentText: "Hoje",
		closeText: "Fechar",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
        changeYear: true,
		// showOtherMonths: true,
        // selectOtherMonths: true,
		// numberOfMonths: 3,
    });
    $( "#txDtpedFin" ).datepicker({
		dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
    	// showOn: "button",
    	showOn: "both",
        buttonImage: "images/icon-calendario11x11.png",
        buttonImageOnly: true, 
		showButtonPanel: true,
		currentText: "Hoje",
		closeText: "Fechar",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
        changeYear: true,
		// showOtherMonths: true,
        // selectOtherMonths: true,
		// numberOfMonths: 3,
    });
    $( "#txDtfatIni" ).datepicker({
		dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
    	// showOn: "button",
    	showOn: "both",
        buttonImage: "images/icon-calendario11x11.png",
        buttonImageOnly: true, 
		showButtonPanel: true,
		currentText: "Hoje",
		closeText: "Fechar",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
        changeYear: true,
		// showOtherMonths: true,
        // selectOtherMonths: true,
		// numberOfMonths: 3,
    });
    $( "#txDtfatFin" ).datepicker({
		dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
    	// showOn: "button",
    	showOn: "both",
        buttonImage: "images/icon-calendario11x11.png",
        buttonImageOnly: true, 
		showButtonPanel: true,
		currentText: "Hoje",
		closeText: "Fechar",
		dateFormat: 'dd/mm/yy',
		changeMonth: true,
        changeYear: true,
		// showOtherMonths: true,
        // selectOtherMonths: true,
		// numberOfMonths: 3,
    });
});
</script>
</body>
</html>