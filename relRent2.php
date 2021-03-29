<?
ini_set('memory_limit', '-1');
set_time_limit(60);
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
<?
	// ob_start();
	$html = "<style>td {padding: 0px 7px 0px 7px;}</style>";

$html .="<body style=\"font-family: 'Helvetica', 'Lucida Grande', 'Arial', sans-serif;	font-size: 7px;\">";
	$filtro = unserialize(urldecode($_GET['filtro']));
	$order = $_GET['order'];
	$sent = $_GET['sent'];
	$dtc = "ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."'";
	
	if (str_replace(" ","",$filtro)!="") {
	$html .= "<div style=\"width: 100%;\">";
	
	$html .= "<table style=\"border: none; border-collapse: none;\">\n";
		$html .= "<thead>\n";
			$html .= "<tr style=\"width: 100%; font-weight: bold;\">\n";
				$html .= "<th><center>"."DT.PED"."</center></th>\n";
				$html .= "<th><center>"."CLIENTE"."</center></th>\n";
				$html .= "<th><center>"."REGIÃO"."</center></th>\n";
				$html .= "<th><center>"."REPRESENTANTE"."</center></th>\n";
				$html .= "<th><center>"."PROD."."</center></th>\n";
				$html .= "<th><center>"."DESCRIÇÃO"."</center></th>\n";
				$html .= "<th><center>"."QUANT."."</center></th>\n";
				$html .= "<th><center>"."UND"."</center></th>\n";
				$html .= "<th><center>"."VLR. PADR."."</center></th>\n";
				$html .= "<th><center>"."VLR. NF"."</center></th>\n";
				$html .= "<th><center>"."(-) ICMS"."</center></th>\n";
				$html .= "<th><center>"."(-) COMIS"."</center></th>\n";
				$html .= "<th><center>"."TOTAL FAT."."</center></th>\n";
				$html .= "<th><center>"."CONTRIBUIÇÃO"."</center></th>\n";
				$html .= "<th><center>"."MARG."."</center></th>\n";
				$html .= "<th><center>"."DT.FAT"."</center></th>\n";
			$html .= "</tr>\n";
		$html .= "</thead>\n";
		
		$impostos = mysqli_fetch_array($sql->query("select percentual from outros_impostos where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1"));
		$custo_fixo = mysqli_fetch_array($sql->query("select percentual from custo_fixo where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1"));
		$margem = mysqli_fetch_array($sql->query("select percentual from margem_fixa where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1"));
		$string = "select fechado, if(data_ped='0000-00-00','-',date_format(data_ped,'%d/%m/%Y')), if(fechado='0',(select cliente from clientes where $dtc and cod_cli = rentabilidade.cliente),clientevar), if(fechado='0',(select regiao from regioes where $dtc and cod_reg = (select regiao from clientes where $dtc and cod_cli = rentabilidade.cliente)),regiaovar), if(fechado='0',(select nome from representantes where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente)),representantevar), cod_sp, if(fechado='0',(select descricao from sp_dados where $dtc and cod_sp = rentabilidade.cod_sp),descricaovar), quant, if(fechado='0',(select unidade from sp_dados where $dtc and cod_sp = rentabilidade.cod_sp),undvar), vlr_padr, vlr_nf, if(fechado='0',(select icms from regioes where $dtc and cod_reg = (select regiao from clientes where $dtc and cod_cli = rentabilidade.cliente)),icms), if(fechado='0',(select if((select count(comiss) from representantes_grupos where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente) and cod_grupo_prod = (select grupo from sp_dados where $dtc and cod_sp = rentabilidade.cod_sp))>0,(select comiss from representantes_grupos where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente) and cod_grupo_prod = (select grupo from sp_dados where $dtc and cod_sp = rentabilidade.cod_sp)),(select comissao_padrao from representantes where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente)))),comis), '','',marg , if(faturado='0','-',date_format(data_nf,'%d/%m/%Y')) from rentabilidade where $dtc ".$filtro." order by data_cri";
		
		$itemPesq = $sql->query($string) or die (mysqli_error($sql));
		$numLinhas = mysqli_num_rows($itemPesq);
		
		if ($numLinhas>0) {
			for ($i=0;$i<$numLinhas;$i++) {
				$item = mysqli_fetch_row($itemPesq);
				if ($item[0]=='0' and $item[9]=='0') {
					$soma = somaSp($item[5]);
					$item[9] = $soma/((100-($item[11]+$impostos['percentual']+$item[12]+$custo_fixo['percentual']+$margem['percentual']))/100);
				}
				$item[2] = limitarTexto($item[2],30);
				$item[3] = limitarTexto($item[3],10);
				$item[4] = limitarTexto($item[4],30);
				$item[6] = limitarTexto($item[6],60);
				$item[13] = $item[7]*$item[10];
				$fatTotal += $item[13];
				if ($item[0]=='0') $item[15] = (($soma/$item[9])-($soma/$item[10]))*100;
				$item[14] = $item[13]*($item[15]/100);
				$contrTotal += $item[14];
				for ($m=1;$m<count($item);$m++) {
					$vetor[$m][$i] = $item[$m];
					if ($m==1 or $m==16) $vetdatas[$m][$i] = date("Y-m-d",strtotime(str_replace("/","-",$item[$m])));
				}
			}
			//ordenador
			if ($order!="" and count($vetor[1])>0) {
				if ($order=="cliente" and $sent=="desc") {
					array_multisort($vetor[2], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="cliente" and $sent=="asc") {
					array_multisort($vetor[2], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="regiao" and $sent=="desc") {
					array_multisort($vetor[3], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="regiao" and $sent=="asc") {
					array_multisort($vetor[3], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="representante" and $sent=="desc") {
					array_multisort($vetor[4], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="representante" and $sent=="asc") {
					array_multisort($vetor[4], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="prod" and $sent=="desc") {
					array_multisort($vetor[5], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="prod" and $sent=="asc") {
					array_multisort($vetor[5], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="descricao" and $sent=="desc") {
					array_multisort($vetor[6], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="descricao" and $sent=="asc") {
					array_multisort($vetor[6], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="quant" and $sent=="desc") {
					array_multisort($vetor[7], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="quant" and $sent=="asc") {
					array_multisort($vetor[7], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="und" and $sent=="desc") {
					array_multisort($vetor[8], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="und" and $sent=="asc") {
					array_multisort($vetor[8], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="vlr_padr" and $sent=="desc") {
					array_multisort($vetor[9], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="vlr_padr" and $sent=="asc") {
					array_multisort($vetor[9], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="vlr_nf" and $sent=="desc") {
					array_multisort($vetor[10], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="vlr_nf" and $sent=="asc") {
					array_multisort($vetor[10], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="icms" and $sent=="desc") {
					array_multisort($vetor[11], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="icms" and $sent=="asc") {
					array_multisort($vetor[11], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="comis" and $sent=="desc") {
					array_multisort($vetor[12], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="comis" and $sent=="asc") {
					array_multisort($vetor[12], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="total" and $sent=="desc") {
					array_multisort($vetor[13], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="total" and $sent=="asc") {
					array_multisort($vetor[13], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="vlr_contr" and $sent=="desc") {
					array_multisort($vetor[14], SORT_ASC, SORT_REGULAR, $vetor[1], $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[16], $vetor[15]);}
				else if ($order=="vlr_contr" and $sent=="asc") {
					array_multisort($vetor[14], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="margem" and $sent=="desc") {
					array_multisort($vetor[15], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="margem" and $sent=="asc") {
					array_multisort($vetor[15], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="dataped" and $sent=="desc") {
					array_multisort($vetdatas[1], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="dataped" and $sent=="asc") {
					array_multisort($vetdatas[1], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="data" and $sent=="desc") {
					array_multisort($vetdatas[16], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
				else if ($order=="data" and $sent=="asc") {
					array_multisort($vetdatas[16], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[1], $vetor[16]);}
			}
			//final da ordenação
			$html .= "<tbody>\n";
				$contLinhas = 0;
				$linhasPag = 80;
				$loops = 0;
				while(($contLinhas < count($vetor[1])) and ($loops<100)) {
					if (count($vetor[1])>($contLinhas+$linhasPag)) $linlin = $contLinhas+$linhasPag;
					else $linlin = count($vetor[1]);
					$html .= "<tr>\n";
					$contcont = $contLinhas;
						// for ($o=1;$o<=count($vetor[0]);$o++) {
						for ($o=1;$o<=count($vetor);$o++) {
							if ($o==7 or $o==9 or $o==10 or $o==11 or $o==12 or $o==13 or $o==14 or $o==15) $format = " style=\"text-align: right;\"";
							else if ($o==1 or $o==8 or $o==16) $format = " style=\"text-align: center;\"";
							else $format = "";
							$html .= "<td$format>\n";
							for ($p=(0+$contcont);$p<$linlin;$p++) {
								if ($o==7) $html .= number_format($vetor[$o][$p],2,",","")."<br>\n";
								else if ($o==9 or $o==10) $html .= number_format($vetor[$o][$p],4,",","")."<br>\n";
								else if ($o==11 or $o==12) $html .= number_format($vetor[$o][$p],4,",","")."%<br>\n";
								else if ($o==13 or $o==14) $html .= number_format($vetor[$o][$p],4,",",".")."<br>\n";
								else if ($o==15) $html .= number_format($vetor[$o][$p],2,",","")."%<br>\n";
								else if (($o==1 or $o==16) and $vetor[$o][$p]!="-") $html .= date("d/m/y",strtotime(str_replace("/","-",$vetor[$o][$p])))."<br>\n";
								else $html .= $vetor[$o][$p]."<br>\n";
								if ($o==1) $contLinhas++;
							}
							$html .= "</td>\n";
						}
					$html .= "</tr>\n";
					$loops++;
				}
			$html .= "</tbody>\n";
			
			$html .= "<tfoot>\n";
				$html .= "<tr>\n";
					$html .= "<td colspan=\"12\" align=\"right\" style=\"font-weight: bold;\">Total:</td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\">".number_format($fatTotal,2,',','.')."</td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\">".number_format($contrTotal,2,',','.')."</td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\">".number_format(($contrTotal/$fatTotal)*100,2,',','')."%</td>\n";
				$html .= "</tr>\n";
			$html .= "</tfoot>\n";
		}
		$html .= "</table>";
		// var_dump($vetor);
		
	$html .= "</div>";
	}
	
$html .= "
</body>
";
// $pdf = ob_get_contents();
// ob_end_clean();
// $pdf = $html;
	
	// echo $html;
	include("biblios/mpdf60/mpdf.php");
	// $mpdf=new mPDF('pt',array(205,295),3,'',8,8,10,10,9,9); 
	$mpdf=new mPDF('pt',array(205,295),3,'',15,15,10,10,9,9); 
	$mpdf->SetDisplayMode('fullwidth');
	// $css = file_get_contents("style.css");
	// $mpdf->WriteHTML($css,1);
	$mpdf->AddPage('L');
	// $mpdf->WriteHTML($pdf);
	$mpdf->WriteHTML($html);
	$diret = "temp/";
	if (!is_dir($diret)) mkdir($diret);
	$arquivo = $diret.'relRentU'.session_id().'.pdf';
	$mpdf->Output($arquivo,'F');
	
	// echo "<object type=\"application/pdf\"  data=\"$arquivo?#zoom=100\" style=\"width: 100%; height: 100%;\">\n";
	// echo "<a href=\"$arquivo?#zoom=100\">Ver PDF</a> <-- Para navegadores que não suportam object -->\n";
	// echo "</object>";
	$retorno['arquivo'] = $arquivo;
	echo json_encode($retorno);

?>
</html>