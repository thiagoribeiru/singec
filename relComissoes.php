<?
$popup = 1;
require_once("session.php");
//validação página
autorizaPagina(2);


if (isset($_POST) and $_POST['tipoData']!='' and $_POST['dataDe']!='' and $_POST['dataAte']!='' and $_POST['representantes']!='' and $_SERVER['REQUEST_METHOD']=='POST') {
	$tipoData = $_POST['tipoData'];
	$dataDe = $_POST['dataDe'];
	$dataAte = $_POST['dataAte'];
	$representantes = $_POST['representantes'];
	$empresa = $_SESSION['UsuarioEmpresa'];
	$empresaNome = $_SESSION['UsuarioEmpresaNome'];
	$filtro = '';
	$dtc = "ativo = '1' and empresa = '".$empresa."'";
	
	if ($tipoData=='entrada') {
		$filtro .= " and (data_ped between '$dataDe' and '$dataAte')";
	} else if ($tipoData=='faturamento') {
		$filtro .= " and (data_nf between '$dataDe' and '$dataAte') and faturado = '1'";
	}
	
	$stringPesquisa = "select indice, fechado, cliente, data, cod_sp, comis, quant, vlr_nf from rentabilidade where $dtc".$filtro;
	
	$linhaSql = $sql->query($stringPesquisa) or die (mysqli_error($sql));
	if (mysqli_num_rows($linhaSql)>0) {
		$contLinhas = 0;
		for($i=0;$i<mysqli_num_rows($linhaSql);$i++) {
			$linha = mysqli_fetch_array($linhaSql);
			$cliente = $linha['cliente'];
			$data = $linha['data'];
			$cod_sp = $linha['cod_sp'];
			if ($linha['fechado']=='0') {
				$representanteSql = mysqli_fetch_array($sql->query("SELECT representante FROM clientes where cod_cli in ('".$cliente."') and ativo = '1' and empresa = '".$empresa."'")) or die (mysqli_error($sql));
				$representante = $representanteSql['representante'];
			} else if ($linha['fechado']=='1') {
				$repQuery = $sql->query("SELECT representante FROM clientes where cod_cli in ('".$cliente."') and data <= '".$data."' and empresa = '".$empresa."' ORDER BY data DESC LIMIT 1");
				if (mysqli_num_rows($repQuery)<='0') {
					$repQuery = $sql->query("SELECT representante FROM clientes where cod_cli in ('".$cliente."') and data >= '".$data."' and empresa = '".$empresa."' ORDER BY data ASC LIMIT 1");
				}
				$representanteSql = mysqli_fetch_array($repQuery) or die (mysqli_error($sql));
				$representante = $representanteSql['representante'];
			}
			if (in_array($representante,$representantes) or $representantes[0]=='all') {
				$contLinhas++;
				if ($linha['fechado']=='0') {
					$grupo = mysqli_fetch_array($sql->query("select grupo from sp_dados where ativo = 1 and empresa = '".$empresa."' and cod_sp = '".$cod_sp."'")) or die (mysqli_error($sql));
					$comissPesq = $sql->query("select comiss from representantes_grupos where cod_rep = '".$representante."' and ativo = 1 and empresa = '".$empresa."' and cod_grupo_prod = '".$grupo['grupo']."'");
					if (mysqli_num_rows($comissPesq)>0) {
						$comissGrupo = mysqli_fetch_array($comissPesq);
						$comissao = $comissGrupo['comiss'];
					} else {
						$comissPadrao = mysqli_fetch_array($sql->query("select comissao_padrao from representantes where ativo = 1 and empresa = '".$empresa."' and cod_rep = '".$representante."'")) or die (mysqli_error($sql));
						$comissao = $comissPadrao['comissao_padrao'];
					}
				} else if ($linha['fechado']=='1') {
					$comissao = $linha['comis'];
				}
				$faturamento = $linha['quant']*$linha['vlr_nf'];
				$valorComissao = $faturamento*($comissao/100);
				$arrayValores[$representante]['faturamento'] = $arrayValores[$representante]['faturamento']+$faturamento;
				$arrayValores[$representante]['comissao'] = $arrayValores[$representante]['comissao']+$valorComissao;
			}
		}
		if ($contLinhas==0) echo "Nenhum item encontrado!";
		else {
			for ($j=0;$j<count($arrayValores);$j++) {
				$key = key($arrayValores);
				$nomeRepresentante = mysqli_fetch_array($sql->query("select nome from representantes where cod_rep = '".$key."' and ativo = '1' and empresa = '".$empresa."'")) or die (mysqli_error($sql));
				$arrayFinal[$nomeRepresentante['nome']] = $arrayValores[$key];
				next($arrayValores);
			}
			unset($arrayValores);
			ksort($arrayFinal);
			ob_start();
			?>
			<style>
				#pdf {
					/*font-family: 'Helvetica', 'Lucida Grande', 'Arial', sans-serif;*/
					font-size: 12px;
				}
				table {
					font-size: 10px;
					border-collapse: collapse;
				}
				table tr td {
					border-bottom: 1px solid;
				}
				table tr.dif td {
					background: #F2F2F2;
				}
				table .rep {
					width: 840px;
					text-align: left;
				}
				table .fat, table .com {
					width: 105px;
				}
				table .alinRight {
					text-align: right;
				}
				table tfoot tr td {
					font-weight: bold;
				}
			</style>
			<?
			echo "<div id=\"pdf\">\n";
				echo "<table>\n";
					echo "<thead>\n";
						echo "<tr>\n";
							echo "<th class=\"rep\">REPRESENTANTE</th>\n";
							echo "<th class=\"fat\">FATURAMENTO</th>\n";
							echo "<th class=\"com\">COMISSÃO</th>\n";
						echo "</tr>\n";
					echo "</thead>\n";
					echo "<tbody>\n";
					$faturamentoTotal = 0;
					$comissaoTotal = 0;
					for ($j=0;$j<count($arrayFinal);$j++) {
						$key = key($arrayFinal);
						if ($j % 2 == 0) $dif = "class=\"dif\"";
						else $dif = "";
						echo "<tr $dif>\n";
							echo "<td>".$key."</td>\n";
							echo "<td class=\"alinRight\">R$ ".number_format($arrayFinal[$key]['faturamento'],4,",",".")."</td>\n";
							echo "<td class=\"alinRight\">R$ ".number_format($arrayFinal[$key]['comissao'],4,",",".")."</td>\n";
						echo "</tr>\n";
						$faturamentoTotal += $arrayFinal[$key]['faturamento'];
						$comissaoTotal += $arrayFinal[$key]['comissao'];
						next($arrayFinal);
					}
					echo "</tbody>\n";
					echo "<tfoot>\n";
						if ($j % 2 == 0) $dif = "class=\"dif\"";
						else $dif = "";
						echo "<tr $dif>\n";
							echo "<td class=\"alinRight\">TOTAL:</td>\n";
							echo "<td class=\"alinRight\">R$ ".number_format($faturamentoTotal,4,",",".")."</td>\n";
							echo "<td class=\"alinRight\">R$ ".number_format($comissaoTotal,4,",",".")."</td>\n";
						echo "</tr>\n";
					echo "</tfoot>\n";
				echo "</table>\n";
			echo "</div>\n";
			
			$pdf = ob_get_contents();
			ob_end_clean();
			// echo $pdf;
			
			include("biblios/mpdf60/mpdf.php");
			$mpdf=new mPDF('pt',array(205,295),3,'',8,8,10,10,5,5,'L'); 
			// $mpdf->AddPage('L');
			$mpdf->SetDisplayMode('fullwidth');
			$dataDeExplode = explode("-",$dataDe);
			$dataDeTit = $dataDeExplode[2]."/".$dataDeExplode[1]."/".$dataDeExplode[0];
			$dataAteExplode = explode("-",$dataAte);
			$dataAteTit = $dataAteExplode[2]."/".$dataAteExplode[1]."/".$dataAteExplode[0];
			if ($tipoData=='entrada')
				$mpdf->setHeader($empresaNome."|Relatório de comissões por representante. Período de entrada de ".$dataDeTit." até ".$dataAteTit."|Emissão: {DATE d/m/Y}");
			else if ($tipoData=='faturamento')
				$mpdf->setHeader($empresaNome."|Relatório de comissões por representante. Período de faturamento de ".$dataDeTit." até ".$dataAteTit."|Emissão: {DATE d/m/Y}");
			$mpdf->setFooter("Pág. {PAGENO} de {nb}");
			// $css = file_get_contents("style.css");
			// $mpdf->WriteHTML($css,1);
			$mpdf->WriteHTML($pdf);
			$diret = "temp/";
			if (!is_dir($diret)) mkdir($diret);
			$arquivo = $diret.'relcomisrep'.$random.'.pdf';
			$mpdf->Output($arquivo,'F');
			
			echo "<object type=\"application/pdf\"  data=\"$arquivo?#zoom=75\" style=\"width: 100%; height: 100%;\">\n";
			echo "<a href=\"$arquivo?#zoom=75\">Ver PDF</a> <-- Para navegadores que não suportam object -->\n";
			echo "</object>";
		}
	} else {
		echo "Nenhum item encontrado!";
	}
} else {
	echo "ERRO: OPERAÇÃO NÃO PERMITIDA!";
}
?>