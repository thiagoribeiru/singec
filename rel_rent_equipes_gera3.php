<?
$popup = 1;
require_once("session.php");

//validação página
autorizaPagina(2);

ob_start();
?>
<style>
	#tb1 {
		font-size: 10px;
		border-collapse: collapse;
	}
	#tb1 .thead {
		border: 1px solid;
		border-color: #000000;
		font-weight: bold;
		text-align: center;
	}
	#tb1 .tbodyleft {
		border: 1px solid;
		border-color: #000000;
	}
	#tb1 .tbodyright {
		border: 1px solid;
		border-color: #000000;
		text-align: right;
	}
	#tb1 .tfootright {
		border-color: #000000;
		text-align: right;
		padding: 2px;
	}
	#tb1 .tfootTright {
		border-color: #000000;
		text-align: right;
		font-weight: bold;
		padding: 2px;
	}
	#tb2 {
		font-size: 10px;
	}
	#pdf {
		font-family: 'Helvetica', 'Lucida Grande', 'Arial', sans-serif;
		font-size: 12px;
		float: left;
		/*width: 430px;*/
		/*background: red;*/
	}
	#esquerda {
		float: left;
		display: table-cell;
		width: 430px;
		padding-top: 1px;
		/*width: 39%;*/
		/*background: green;*/
	}
	#direita {
		float: left;
		display: table-cell;
		text-align: center;
		padding-top: 15px;
		/*width: 59%;*/
		/*background: yellow;*/
	}
</style>
<?

// echo "<div id=\"pdf\">\n";
//orientador de filtro
if ((!empty($_GET) or !isset($_GET))) {
	if (isset($_GET['point'])) {
		$empresa = $_GET['point'];
		$empSql = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = '".$empresa."'"));
		$empresaNome = $empSql['nome'];
	} else {
		$empresa = $_SESSION['UsuarioEmpresa'];
		$empresaNome = $_SESSION['UsuarioEmpresaNome'];
	}
	$dtfatini = date("Y-m-d",strtotime(str_replace("/","-",$_GET['dtfatini'])));
	$dtfatfin = date("Y-m-d",strtotime(str_replace("/","-",$_GET['dtfatfin'])));
	$buscadtfat = " and data_nf between '$dtfatini' and '$dtfatfin' and faturado = 1";
	
	$dtpedini = date("Y-m-d",strtotime(str_replace("/","-",$_GET['dtpedini'])));
	$dtpedfin = date("Y-m-d",strtotime(str_replace("/","-",$_GET['dtpedfin'])));
	$buscadtped = " and data_ped between '$dtpedini' and '$dtpedfin'";
	
	if ($_GET['rel']=="faturamento") $dtStr = $buscadtfat;
	else if ($_GET['rel']=="entrada") $dtStr = $buscadtped;
	
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
	}
	if ($ultimaData!=date('Y-m-d',$dateEnd)) $dateRange[] = date('Y-m-d',$dateEnd);
	// var_dump($dateRange);
	
	$filtro = $buscaFaturados.$textBusca.$buscarep.$buscacli.$buscaequipe.$buscaCodProd." ".$dtStr;
	$dtc = "ativo = '1' and empresa = '".$empresa."'";
	
	$stringEquipes = "select indice, cliente, data, fechado, vlr_nf, cod_sp, quant, marg, data_ped, data_nf from rentabilidade where $dtc ".$filtro;
	
	$sqlEquipes = $sql->query($stringEquipes) or die (mysqli_error($sql));
	if (mysqli_num_rows($sqlEquipes)>0) {
		echo "<div id=\"pdf\">\n";
			$impostos = mysqli_fetch_array($sql->query("select percentual from outros_impostos where empresa = ".$empresa." and ativo = 1"));
			$custo_fixo = mysqli_fetch_array($sql->query("select percentual from custo_fixo where empresa = ".$empresa." and ativo = 1"));
			$margem = mysqli_fetch_array($sql->query("select percentual from margem_fixa where empresa = ".$empresa." and ativo = 1"));
			// if ($_GET['rel']=="faturamento") echo "Relatório de rentabilidade por equipe de vendas. Período de faturamento: de ".$_GET['dtfatini']." a ".$_GET['dtfatfin'].".<br><br>\n";
			// else if ($_GET['rel']=="entrada") echo "Relatório de rentabilidade por equipe de vendas. Período de entrada: de ".$_GET['dtpedini']." a ".$_GET['dtpedfin'].".<br><br>\n";
			
			for ($i=0;$i<mysqli_num_rows($sqlEquipes);$i++) {
				$linha = mysqli_fetch_array($sqlEquipes);
				$indice = $linha['indice'];
				$cliente = $linha['cliente'];
				$data = $linha['data'];
				$fechado = $linha['fechado'];
				$cod_sp = $linha['cod_sp'];
				if ($_GET['rel']=="faturamento") $dataLinha = $linha['data_nf'];
				else if ($_GET['rel']=="entrada") $dataLinha = $linha['data_ped'];
				if ($fechado=='1') {
					$repQuery = $sql->query("SELECT representante FROM clientes where cod_cli in ('".$cliente."') and data <= '".$data."' and empresa = '".$empresa."' ORDER BY data DESC LIMIT 1");
					if (mysqli_num_rows($repQuery)<='0') {
						$repQuery = $sql->query("SELECT representante FROM clientes where cod_cli in ('".$cliente."') and data >= '".$data."' and empresa = '".$empresa."' ORDER BY data ASC LIMIT 1");
					}
					$representanteSql = mysqli_fetch_array($repQuery) or die (mysqli_error($sql));
					$representante = $representanteSql['representante'];
					$nomeRepresentante = mysqli_fetch_array($sql->query("select nome from representantes where cod_rep = '".$representante."' and ativo = '1' and empresa = '".$empresa."'")) or die (mysqli_error($sql));
					$equipesDoHist = $sql->query("SELECT cod_equipe FROM representantes_equipes where cod_rep = '".$representante."' and data <= '".$data."' and empresa = '".$empresa."' group by cod_equipe order by data desc") or die (mysqli_error($sql));
					if (mysqli_num_rows($equipesDoHist)<='0') {
						$equipesDoHist = $sql->query("SELECT cod_equipe FROM representantes_equipes where cod_rep = '".$representante."' and data >= '".$data."' and empresa = '".$empresa."' ORDER BY data ASC LIMIT 1") or die (mysqli_error($sql));
					}
					$equipes = array();
					for($j=0;$j<mysqli_num_rows($equipesDoHist);$j++) {
						$result = mysqli_fetch_array($equipesDoHist);
						$numEq = $result['cod_equipe'];
						$dentroQuery = $sql->query("SELECT dentro FROM representantes_equipes where cod_rep = '".$representante."' and data <= '".$data."' and cod_equipe = '".$numEq."' and empresa = '".$empresa."' order by data desc limit 1");
						if (mysqli_num_rows($dentroQuery)<=0) {
							$dentroQuery = $sql->query("SELECT dentro FROM representantes_equipes where cod_rep = '".$representante."' and data >= '".$data."' and cod_equipe = '".$numEq."' and empresa = '".$empresa."' order by data ASC limit 1");
						}
						$dentro = mysqli_fetch_array($dentroQuery) or die (mysqli_error($sql));
						if($dentro['dentro']=='1') {
							$equipes[] = $numEq;
						}
					}
					$rentabilidade = ($linha['quant']*$linha['vlr_nf'])*($linha['marg']/100);
					$faturamento = $linha['quant']*$linha['vlr_nf'];
				} else {
					$representanteSql = mysqli_fetch_array($sql->query("SELECT representante FROM clientes where cod_cli in ('".$cliente."') and ativo = '1' and empresa = '".$empresa."'")) or die (mysqli_error($sql));
					$representante = $representanteSql['representante'];
					$nomeRepresentante = mysqli_fetch_array($sql->query("select nome from representantes where cod_rep = '".$representante."' and ativo = '1' and empresa = '".$empresa."'")) or die (mysqli_error($sql));
					$equipesDoHist = $sql->query("SELECT cod_equipe FROM representantes_equipes where cod_rep = '".$representante."' and ativo = '1' and empresa = '".$empresa."'") or die (mysqli_error($sql));
					$equipes = array();
					for($j=0;$j<mysqli_num_rows($equipesDoHist);$j++) {
						$result = mysqli_fetch_array($equipesDoHist);
						$numEq = $result['cod_equipe'];
						$dentro = mysqli_fetch_array($sql->query("SELECT dentro FROM representantes_equipes where cod_rep = '".$representante."' and ativo = '1' and cod_equipe = '".$numEq."' and empresa = '".$empresa."'")) or die (mysqli_error($sql));
						if($dentro['dentro']=='1') {
							$equipes[] = $numEq;
						}
					}
					$icmsSql = mysqli_fetch_array($sql->query("select icms from regioes where ativo = 1 and empresa = '".$empresa."' and cod_reg = (select regiao from clientes where ativo = 1 and empresa = '".$empresa."' and cod_cli = '".$cliente."')")) or die (mysqli_error($sql));
					$icms = $icmsSql['icms'];
					$grupo = mysqli_fetch_array($sql->query("select grupo from sp_dados where ativo = 1 and empresa = '".$empresa."' and cod_sp = '".$cod_sp."'")) or die (mysqli_error($sql));
					$comissPesq = $sql->query("select comiss from representantes_grupos where cod_rep = '".$representante."' and ativo = 1 and empresa = '".$empresa."' and cod_grupo_prod = '".$grupo['grupo']."'");
					if (mysqli_num_rows($comissPesq)>0) {
						$comissGrupo = mysqli_fetch_array($comissPesq);
						$comissao = $comissGrupo['comiss'];
					} else {
						$comissPadrao = mysqli_fetch_array($sql->query("select comissao_padrao from representantes where ativo = 1 and empresa = '".$empresa."' and cod_rep = '".$representante."'")) or die (mysqli_error($sql));
						$comissao = $comissPadrao['comissao_padrao'];
					}
					$vlrpadraovar = somaSp($cod_sp)/((100-($icms+$impostos['percentual']+$comissao+$custo_fixo['percentual']+$margem['percentual']))/100);
					$margem_guia = ((($linha['vlr_nf']*(100-($icms+$impostos['percentual']+$comissao)))-($vlrpadraovar*(100-($icms+$impostos['percentual']+$comissao+$margem['percentual']))))/$linha['vlr_nf'])-$margem['percentual'];
					$rentabilidade = ($linha['quant']*$linha['vlr_nf'])*($margem_guia/100);
					$faturamento = $linha['quant']*$linha['vlr_nf'];
				}
				for($l=0;$l<count($equipes);$l++) {
					$tabela[$equipes[$l]][$representante]['nome'] = $nomeRepresentante['nome'];
					$tabela[$equipes[$l]][$representante]['rentabilidade'] += $rentabilidade;
					$tabela[$equipes[$l]][$representante]['faturamento'] += $faturamento;
					$arrayTotais[$equipes[$l]][$dataLinha] += $rentabilidade;
				}
				// break;
			}
			// var_dump($arrayTotais);
			
			echo "<div id=\"esquerda\">";
				// var_dump($arrayTotais);
				// echo "<table id=\"tb1\">\n";
				for ($m=0;$m<count($tabela);$m++) {
					$keyEquipe = key($tabela);
					$nomeEquipe = mysqli_fetch_array($sql->query("SELECT equipe FROM equipes_de_venda where ativo = 1 and empresa = '".$empresa."' and cod_equipe = '".$keyEquipe."'")) or die (mysqli_error($sql));
					$tabelaNomes[$nomeEquipe['equipe']] = $tabela[$keyEquipe];
					next($tabela);
				}
				unset($tabela);
				ksort($tabelaNomes);
				$rentTotal = 0;
				$fatTotal = 0;
				for ($m=0;$m<count($tabelaNomes);$m++) {
					$keyEquipe = key($tabelaNomes);
					echo "<div style=\"display: table-cell; margin-top: 5px; page-break-inside: avoid;\"><table id=\"tb1\">\n";
					echo "<tr><td colspan=\"4\">Equipe de vendas: ".$keyEquipe."</td></tr>\n";
					echo "<tr>\n";
						echo "<td class=\"thead\" style=\"width: 180px;\">REPRESENTANTE</td>\n";
						echo "<td class=\"thead\" style=\"width: 90px;\">\$ RENTAB.</td>\n";
						if ($_GET['rel']=="faturamento") echo "<td class=\"thead\" style=\"width: 105px;\">\$ FATUR.</td>\n";
						else if ($_GET['rel']=="entrada") echo "<td class=\"thead\" style=\"width: 105px;\">\$ ENTR.</td>\n";
						echo "<td class=\"thead\" style=\"width: 45px;\">% IND.</td>\n";
					echo "</tr>\n";
					$arrayTemp = array();
					for ($n=0;$n<count($tabelaNomes[$keyEquipe]);$n++) {
						$keyRep = key($tabelaNomes[$keyEquipe]);
						$arrayTemp[$tabelaNomes[$keyEquipe][$keyRep]['nome']] = $tabelaNomes[$keyEquipe][$keyRep];
						next($tabelaNomes[$keyEquipe]);
					}
					ksort($arrayTemp);
					$rentGrupo = 0;
					$fatGrupo = 0;
					for ($n=0;$n<count($arrayTemp);$n++) {
						$keyRep = key($arrayTemp);
						echo "<tr>\n";
							echo "<td class=\"tbodyleft\">".limitarTexto($arrayTemp[$keyRep]['nome'],30)."</td>\n";
							echo "<td class=\"tbodyright\">R\$ ".number_format($arrayTemp[$keyRep]['rentabilidade'],4,",",".")."</td>\n";
							echo "<td class=\"tbodyright\">R\$ ".number_format($arrayTemp[$keyRep]['faturamento'],4,",",".")."</td>\n";
							echo "<td class=\"tbodyright\">".number_format(($arrayTemp[$keyRep]['rentabilidade']/$arrayTemp[$keyRep]['faturamento'])*100,2,",",".")." %</td>\n";
						echo "</tr>\n";
						$rentGrupo += $arrayTemp[$keyRep]['rentabilidade'];
						$fatGrupo += $arrayTemp[$keyRep]['faturamento'];
						next($arrayTemp);
					}
					echo "<tr>\n";
						echo "<td class=\"tfootright\">TOTAL EQUIPE:</td>\n";
						echo "<td class=\"tfootright\">R\$ ".number_format($rentGrupo,4,",",".")."</td>\n";
						echo "<td class=\"tfootright\">R\$ ".number_format($fatGrupo,4,",",".")."</td>\n";
						echo "<td class=\"tfootright\">".number_format(($rentGrupo/$fatGrupo)*100,2,",",".")." %</td>\n";
					echo "</tr>\n";
					// echo "<tr><td colspan=\"4\"><br></td></tr>\n";
					$rentTotal += $rentGrupo;
					$fatTotal += $fatGrupo;
					$grafFat[$keyEquipe] = $fatGrupo;
					$grafRent[$keyEquipe] = $rentGrupo;
					arsort($grafRent);
					next($tabelaNomes);
					echo "</table></div>\n";
				}
				echo "<table id=\"tb1\">\n";
				echo "<tr>\n";
					echo "<td class=\"tfootTright\" style=\"width: 180px;\">TOTAL GERAL EQUIPES:</td>\n";
					echo "<td class=\"tfootTright\" style=\"width: 90px;\">R\$ ".number_format($rentTotal,4,",",".")."</td>\n";
					echo "<td class=\"tfootTright\" style=\"width: 105px;\">R\$ ".number_format($fatTotal,4,",",".")."</td>\n";
					echo "<td class=\"tfootTright\" style=\"width: 45px;\">".number_format(($rentTotal/$fatTotal)*100,2,",",".")." %</td>\n";
				echo "</tr>\n";
				echo "</table>\n";
				
				// echo "</table>\n";
			echo "</div>\n";
			echo "<div id=\"direita\">\n";
				include("biblios/pChart/class/pData.class.php");
				include("biblios/pChart/class/pDraw.class.php");
				include("biblios/pChart/class/pImage.class.php");
				if (count($dateRange)>1) {
					for ($i=0;$i<count($arrayTotais);$i++) {
						$keyEquipe = key($arrayTotais);
						$nomeEquipe = mysqli_fetch_array($sql->query("SELECT equipe FROM equipes_de_venda where ativo = 1 and empresa = '".$empresa."' and cod_equipe = '".$keyEquipe."'")) or die (mysqli_error($sql));
						for ($j=0;$j<count($dateRange);$j++) {
							$equipesTotais[$nomeEquipe['equipe']][$dateRange[$j]] = 0;
						}
						$soma = 0;
						for ($j=0;$j<count($equipesTotais[$nomeEquipe['equipe']]);$j++) {
							$keyData = key($equipesTotais[$nomeEquipe['equipe']]);
							if (isset($arrayTotais[$keyEquipe][$keyData]) or $arrayTotais[$keyEquipe][$keyData]!=null or $arrayTotais[$keyEquipe][$keyData]) {
								$soma += $arrayTotais[$keyEquipe][$keyData];
							}
							$equipesTotais[$nomeEquipe['equipe']][$keyData] = $soma;
							next($equipesTotais[$nomeEquipe['equipe']]);
						}
						next($arrayTotais);
					}
					ksort($equipesTotais);
					// var_dump($equipesTotais);
					
					$myData = new pData();
					
					for ($arr=0;$arr<count($dateRange);$arr++) {
						$dia = explode("-",$dateRange[$arr]);
						$array[$arr] = $dia[2];
					}
					$myData->addPoints($array,'datas');
					$myData->setSerieDescription('$datas','datas');
					$myData->setSerieOnAxis('datas',0);
					
					for ($m=0;$m<count($equipesTotais);$m++) {
						$key = key($equipesTotais);
						$nome = $key;
						$myData->addPoints($equipesTotais[$key],$nome);
						$myData->setSerieDescription($nome,$nome);
						$myData->setSerieOnAxis($nome,0);
						next($equipesTotais);
					}
					$myData->setAbscissa("datas");
					
					$myData->setAxisPosition(0,AXIS_POSITION_LEFT);
					$myData->setAxisName(0,"RENTABILIDADE");
					$myData->setAxisUnit(0,"");
					
					$myPicture = new pImage(600,230,$myData);
					// $myPicture->drawRectangle(0,0,599,229,array("R"=>0,"G"=>0,"B"=>0));
					
					$myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>50,"G"=>50,"B"=>50,"Alpha"=>20));
					
					$myPicture->setShadow(FALSE);
					$myPicture->setGraphArea(50,20,579,190);
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
					$myPicture->drawLegend(40,215,$Config);
					
					// $myPicture->setFontProperties(array("FontName"=>"biblios/pChart/fonts/Forgotte.ttf","FontSize"=>11));
					$myPicture->drawText(300,15,"Evolução da contribuição por equipes de vendas",array("FontSize"=>6,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));
					
					$random = session_id().mt_rand();
					$myPicture->Render("temp/grafrelrentequi".$random.".png");
					echo "<img src=\"temp/grafrelrentequi".$random.".png\">";
				}
				//grafico pizza
				// var_dump($grafFat);
				include("biblios/pChart/class/pPie.class.php");
				
				$data2 = new pData();   
 				
 				$array1 = $grafFat;
 				$array2 = array();
 				for ($i=0;$i<count($array1);$i++) {
 					$key = key($array1);
 					$array2[] = $key;
 					next($array1);
 				}
 				$data2->addPoints($array2,"Equipe");
				$data2->addPoints($array1,"Faturamento"); 
				$data2->setAbscissa("Equipe"); 
				 
				$myPicture2 = new pImage(295,200,$data2); 
				// $myPicture2->drawRectangle( 0 , 0 , 294 , 199 , array( "R"=>0 , "G"=>0 , "B"=>0 ) ); 
				$myPicture2->setFontProperties( array( "FontName"=>"biblios/pChart/fonts/pf_arma_five.ttf" , "FontSize"=>6 ) );
				if ($_GET['rel']=="faturamento")
					$myPicture2->drawText(147,15,"Grafico de representação do faturamento",array("FontSize"=>6,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));
				else if ($_GET['rel']=="entrada")
					$myPicture2->drawText(147,15,"Grafico de representação da entrada",array("FontSize"=>6,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));
				 
				$PieChart2 = new pPie($myPicture2,$data2);
				$PieChart2->draw3DPie( 147 , 135 , array( "DrawLabels"=>TRUE , "Border"=>TRUE ) );  
				// $PieChart2->drawPieLegend( 450 , 50 , array( "Style"=>LEGEND_NOBORDER , "Mode"=>LEGEND_VERTICAL ) ); 
				// $myPicture->autoOutput();
				$random2 = session_id().mt_rand();
				$myPicture2->Render("temp/grafrelrentequi".$random2.".png");
				echo "<img src=\"temp/grafrelrentequi".$random2.".png\">";
				
				//grafico barras
				$data3 = new pData(); 
				$array3 = $grafRent;
 				$array4 = array();
 				for ($i=0;$i<count($array3);$i++) {
 					$key = key($array3);
 					$array4[] = $key;
 					next($array3);
 				}
				$data3->addPoints( $array3 , "Rentabilidade" );
				$data3->addPoints( $array4 , "Equipe" ); 
				
				$data3->setAbscissa("Equipe"); 
				$data3->setAxisName(0,"Rentabilidade"); 
				 
				$myPicture3 = new pImage( 600 , 230 , $data3 ); 
				// $myPicture3->drawRectangle( 0 , 0 , 599 , 229 , array( "R"=>0 , "G"=>0 , "B"=>0 ) ); 
				 
				$myPicture3->setFontProperties( array( "FontName"=>"biblios/pChart/fonts/pf_arma_five.ttf" , "FontSize"=>6 ) ); 
				 
				$myPicture3->setGraphArea(60,40,550,200); 
				 
				$scaleSettings3 = array( "GridR"=>200 , "GridG"=>200 , "GridB"=>200 , "CycleBackground"=>TRUE );  
				$myPicture3->drawScale($scaleSettings3); 
				$myPicture3->drawText(300,15,"Grafico de representação da rentabilidade",array("FontSize"=>6,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));
				 
				// $myPicture3->drawLegend( 480 , 12 , array( "Style"=>LEGEND_NOBORDER , "Mode"=>LEGEND_HORIZONTAL ) ); 
				 
				$myPicture3->drawBarChart(); 
				 
				// $myPicture3->autoOutput('barra.png');
				$random3 = session_id().mt_rand();
				$myPicture3->Render("temp/grafrelrentequi".$random3.".png");
				echo "<img src=\"temp/grafrelrentequi".$random3.".png\">";
			echo "</div>\n";
		
		echo "</div>";

		$pdf = ob_get_contents();
		ob_end_clean();
		// echo $pdf;
		
		include("biblios/mpdf60/mpdf.php");
		$mpdf=new mPDF('pt',array(205,295),3,'',8,8,10,10,5,5,'L'); 
		// $mpdf->AddPage('L');
		$mpdf->SetDisplayMode('fullwidth');
		if ($_GET['rel']=="faturamento") 
			$mpdf->setHeader($empresaNome."|Relatório de rentabilidade por equipe de vendas. Período de faturamento: de ".$_GET['dtfatini']." a ".$_GET['dtfatfin']."|Emissão: {DATE d/m/Y}");
		else if ($_GET['rel']=="entrada") 
			$mpdf->setHeader($empresaNome."|Relatório de rentabilidade por equipe de vendas. Período de entrada: de ".$_GET['dtpedini']." a ".$_GET['dtpedfin']."|Emissão: {DATE d/m/Y}");
		$mpdf->setFooter("Pág. {PAGENO} de {nb}");
		// $css = file_get_contents("style.css");
		// $mpdf->WriteHTML($css,1);
		$mpdf->WriteHTML($pdf);
		$diret = "temp/";
		if (!is_dir($diret)) mkdir($diret);
		$arquivo = $diret.'relrentequi'.$random.'.pdf';
		$mpdf->Output($arquivo,'F');
		
		echo "<object type=\"application/pdf\"  data=\"$arquivo?#zoom=75\" style=\"width: 100%; height: 100%;\">\n";
		echo "<a href=\"$arquivo?#zoom=75\">Ver PDF</a> <-- Para navegadores que não suportam object -->\n";
		echo "</object>";
	} else {
		ob_end_clean();
		echo "Sem dados para mostrar!";
	}
}
?>