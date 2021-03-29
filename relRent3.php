<?
ini_set('memory_limit', '-1');
set_time_limit(90);
require_once("config.php");
require_once("funcoes.php");
if (!isset($_SESSION)) session_start();
	// ob_start();
	$html = "<style>td {padding: 0px 7px 0px 7px;}</style>";

$html .="<body style=\"font-family: 'Helvetica', 'Lucida Grande', 'Arial', sans-serif;	font-size: 7px;\">";
	$filtro = unserialize(urldecode($_GET['filtro']));
	$order = $_GET['order'];
	$sent = $_GET['sent'];
	if (!isset($_GET['point'])) {
		$empresa = $_SESSION['UsuarioEmpresa'];
		$point = false;
	} else {
		$empresa = $_GET['point'];
		$point = $empresa;
	}
	$dtc = "ativo = '1' and empresa = '".$empresa."'";
	
	if (str_replace(" ","",$filtro)!="") {
	$html .= "<div style=\"width: 100%;\">";
	$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = '".$empresa."'"));
	// $html .= '<div style="width: 50%; float: left; font-size: 12px; font-weight: bold;">';
	// 	$html .= $nomeEmpresa['nome'].'<br>';
	// $html .= '</div>';
	// $html .= '<div style="width: 50%; float: left; text-align: right; font-size: 10px;">';
	// 	$html .= 'Emissão: '.date('d/m/Y',time()).'<br>';
	// $html .= '</div>';
	// $html .= "<div style=\"width: 1070px; background: #FF0000;\">s</div>";
	
	$html .= "<table style=\"border: none; border-collapse: none; width: 100%;\">\n";
		$html .= "<thead>\n";
			$html .= "<tr style=\"width: 100%; font-weight: bold;\">\n";
				$html .= "<th colspan=\"9\" style=\"text-align: left; font-size: 13px; font-weight: bold;\">\n";
					// $html .= $nomeEmpresa['nome'];
					$html .= "<br>";
				$html .= "</th>\n";
				$html .= "<th colspan=\"8\" style=\"text-align: right; font-size: 10px; font-weight: normal;\">\n";
					// $html .= 'Emissão: '.date('d/m/Y',time());
				$html .= "</th>\n";
			$html .= "</tr>\n";
			$html .= "<tr style=\"font-weight: bold;\">\n";
				$html .= "<th width=\"35\"><center>"."DT.PED"."</center></th>\n";
				$html .= "<th width=\"120px\"><center>"."CLIENTE"."</center></th>\n";
				$html .= "<th width=\"50\"><center>"."REGIÃO"."</center></th>\n";
				$html .= "<th width=\"120px\"><center>"."REPRESENTANTE"."</center></th>\n";
				$html .= "<th width=\"40\"><center>"."PROD."."</center></th>\n";
				$html .= "<th width=\"250px\"><center>"."DESCRIÇÃO"."</center></th>\n";
				$html .= "<th width=\"40\"><center>"."QUANT."."</center></th>\n";
				$html .= "<th width=\"15\"><center>"."UND"."</center></th>\n";
				$html .= "<th width=\"55\"><center>"."VLR. PADR."."</center></th>\n";
				$html .= "<th width=\"55\"><center>"."PREÇO IDEAL."."</center></th>\n";
				$html .= "<th width=\"50\"><center>"."VLR. NF"."</center></th>\n";
				$html .= "<th width=\"30\"><center>"."(-) ICMS"."</center></th>\n";
				$html .= "<th width=\"30\"><center>"."(-) COMIS"."</center></th>\n";
				$html .= "<th width=\"60\"><center>"."TOTAL FAT."."</center></th>\n";
				$html .= "<th width=\"50\"><center>"."CONTRIBUIÇÃO"."</center></th>\n";
				$html .= "<th width=\"35\"><center>"."MARG."."</center></th>\n";
				$html .= "<th width=\"35\"><center>"."DT.FAT"."</center></th>\n";
			$html .= "</tr>\n";
		$html .= "</thead>\n";
		
		$impostos = mysqli_fetch_array($sql->query("select percentual from outros_impostos where empresa = ".$empresa." and ativo = 1"));
		$custo_fixo = mysqli_fetch_array($sql->query("select percentual from custo_fixo where empresa = ".$empresa." and ativo = 1"));
		$margem = mysqli_fetch_array($sql->query("select percentual from margem_fixa where empresa = ".$empresa." and ativo = 1"));
		$imprenda = mysqli_fetch_array($sql->query("select percentual from imposto_de_renda where empresa = ".$empresa." and ativo = 1"));
		$metalucro = mysqli_fetch_array($sql->query("select percentual from meta_lucro where empresa = ".$empresa." and ativo = 1"));
		$string = "select fechado, 
		if(data_ped='0000-00-00','-',date_format(data_ped,'%d/%m/%Y')), 
		if(fechado='0',(select cliente from clientes where $dtc and cod_cli = rentabilidade.cliente),clientevar), 
		if(fechado='0',(select regiao from regioes where $dtc and cod_reg = (select regiao from clientes where $dtc and cod_cli = rentabilidade.cliente)),regiaovar), 
		if(fechado='0',(select nome from representantes where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente)),representantevar), 
		cod_sp, 
		if(fechado='0',(select descricao from sp_dados where $dtc and cod_sp = rentabilidade.cod_sp),descricaovar), 
		quant, 
		if(fechado='0',(select unidade from sp_dados where $dtc and cod_sp = rentabilidade.cod_sp),undvar), 
		vlr_padr, 
		'', 
		vlr_nf, if(fechado='0',(select icms from regioes where $dtc and cod_reg = (select regiao from clientes where $dtc and cod_cli = rentabilidade.cliente)),icms), if(fechado='0',(select if((select count(comiss) from representantes_grupos where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente) and cod_grupo_prod = (select grupo from sp_dados where $dtc and cod_sp = rentabilidade.cod_sp))>0,(select comiss from representantes_grupos where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente) and cod_grupo_prod = (select grupo from sp_dados where $dtc and cod_sp = rentabilidade.cod_sp)),(select comissao_padrao from representantes where $dtc and cod_rep = (select representante from clientes where $dtc and cod_cli = rentabilidade.cliente)))),comis), '','',marg , if(faturado='0','-',date_format(data_nf,'%d/%m/%Y')) from rentabilidade where $dtc ".$filtro." order by data_cri";
		
		$itemPesq = $sql->query($string) or die (mysqli_error($sql));
		$numLinhas = mysqli_num_rows($itemPesq);
		
		if ($numLinhas>0) {
			for ($i=0;$i<$numLinhas;$i++) {
				$item = mysqli_fetch_row($itemPesq);
				if ($item[0]=='0' and $item[9]=='0') {
					$soma = somaSp($item[5],$point);
					$item[9] = $soma/((100-($item[12]+$impostos['percentual']+$item[13]+$custo_fixo['percentual']+$margem['percentual']))/100);
				}
				$item[2] = limitarTexto($item[2],23);
				$item[3] = limitarTexto($item[3],9);
				$item[4] = limitarTexto($item[4],23);
				$item[6] = limitarTexto($item[6],55);
				$item[14] = $item[7]*$item[11];
				$fatTotal += $item[14];
				if ($item[0]=='0') $item[16] = (($soma/$item[9])-($soma/$item[11]))*100;
				$item[15] = $item[14]*($item[16]/100);
				$contrTotal += $item[15];
				$item[10] = $soma/((100-($item[12]+$impostos['percentual']+$item[13]+$custo_fixo['percentual']+$margem['percentual']+($metalucro['percentual']/((100-$imprenda['percentual'])/100))))/100);
				for ($m=1;$m<count($item);$m++) {
					$vetor[$m][$i] = $item[$m];
					if ($m==1 or $m==17) $vetdatas[$m][$i] = date("Y-m-d",strtotime(str_replace("/","-",$item[$m])));
				}
			}
			//ordenador
			if ($order!="" and count($vetor[1])>0) {
				if ($order=="cliente" and $sent=="desc") {
					array_multisort($vetor[2], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="cliente" and $sent=="asc") {
					array_multisort($vetor[2], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="regiao" and $sent=="desc") {
					array_multisort($vetor[3], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="regiao" and $sent=="asc") {
					array_multisort($vetor[3], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="representante" and $sent=="desc") {
					array_multisort($vetor[4], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="representante" and $sent=="asc") {
					array_multisort($vetor[4], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="prod" and $sent=="desc") {
					array_multisort($vetor[5], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="prod" and $sent=="asc") {
					array_multisort($vetor[5], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="descricao" and $sent=="desc") {
					array_multisort($vetor[6], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="descricao" and $sent=="asc") {
					array_multisort($vetor[6], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="quant" and $sent=="desc") {
					array_multisort($vetor[7], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="quant" and $sent=="asc") {
					array_multisort($vetor[7], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="und" and $sent=="desc") {
					array_multisort($vetor[8], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="und" and $sent=="asc") {
					array_multisort($vetor[8], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="vlr_padr" and $sent=="desc") {
					array_multisort($vetor[9], $vetor[10], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="vlr_padr" and $sent=="asc") {
					array_multisort($vetor[9], $vetor[10], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="vlr_nf" and $sent=="desc") {
					array_multisort($vetor[11], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="vlr_nf" and $sent=="asc") {
					array_multisort($vetor[11], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="icms" and $sent=="desc") {
					array_multisort($vetor[12], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="icms" and $sent=="asc") {
					array_multisort($vetor[12], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="comis" and $sent=="desc") {
					array_multisort($vetor[13], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="comis" and $sent=="asc") {
					array_multisort($vetor[13], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="total" and $sent=="desc") {
					array_multisort($vetor[14], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="total" and $sent=="asc") {
					array_multisort($vetor[14], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="vlr_contr" and $sent=="desc") {
					array_multisort($vetor[15], SORT_ASC, SORT_REGULAR, $vetor[1], $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[17], $vetor[16]);}
				else if ($order=="vlr_contr" and $sent=="asc") {
					array_multisort($vetor[15], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="margem" and $sent=="desc") {
					array_multisort($vetor[16], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="margem" and $sent=="asc") {
					array_multisort($vetor[16], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="dataped" and $sent=="desc") {
					array_multisort($vetdatas[1], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="dataped" and $sent=="asc") {
					array_multisort($vetdatas[1], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="data" and $sent=="desc") {
					array_multisort($vetdatas[16], SORT_ASC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
				else if ($order=="data" and $sent=="asc") {
					array_multisort($vetdatas[16], SORT_DESC, SORT_REGULAR, $vetor[2], $vetor[3], $vetor[4], $vetor[5], $vetor[6], $vetor[7], $vetor[8], $vetor[9], $vetor[10], $vetor[11], $vetor[12], $vetor[13], $vetor[14], $vetor[15], $vetor[16], $vetor[1], $vetor[17]);}
			}
			//final da ordenação
			$html .= "<tbody>\n";
				$contLinhas = 0;
				$linhasPag = 83;
				$numPag = ceil(count($vetor[1])/$linhasPag);
				$contPag = 1;
				$loops = 0;
				while(($contLinhas < count($vetor[1])) and ($loops<100)) {
					$contLinhasPag = 0;
					if (count($vetor[1])>($contLinhas+$linhasPag)) $linlin = $contLinhas+$linhasPag;
					else $linlin = count($vetor[1]);
					$html .= "<tr>\n";
					$contcont = $contLinhas;
						// for ($o=1;$o<=count($vetor[0]);$o++) {
						for ($o=1;$o<=count($vetor);$o++) {
							if ($o==7 or $o==9 or $o==11 or $o==12 or $o==13 or $o==14 or $o==15 or $o==16 or $o==10) $format = " style=\"text-align: right;\"";
							else if ($o==1 or $o==8 or $o==17) $format = " style=\"text-align: center;\"";
							else $format = "";
							$html .= "<td$format>\n";
							for ($p=(0+$contcont);$p<$linlin;$p++) {
								if ($o==7) $html .= number_format($vetor[$o][$p],2,",","")."<br>\n";
								else if ($o==9 or $o==11 or $o==10) $html .= number_format($vetor[$o][$p],4,",","")."<br>\n";
								else if ($o==12 or $o==13) $html .= number_format($vetor[$o][$p],4,",","")."%<br>\n";
								else if ($o==14 or $o==15) $html .= number_format($vetor[$o][$p],4,",",".")."<br>\n";
								else if ($o==16) $html .= number_format($vetor[$o][$p],2,",","")."%<br>\n";
								else if (($o==1 or $o==17) and $vetor[$o][$p]!="-") $html .= date("d/m/y",strtotime(str_replace("/","-",$vetor[$o][$p])))."<br>\n";
								else $html .= $vetor[$o][$p]."<br>\n";
								if ($o==1) $contLinhas++;
								if ($o==1) $contLinhasPag++;
							}
							$html .= "</td>\n";
						}
					$html .= "</tr>\n";
					$loops++;
					// if($contPag<$numPag) {
					// 	$html .= '<tr>\n';
					// 		$html .= "<td colspan=\"17\" align=\"right\" style=\"font-size: 7px;\">\n";
					// 			for ($ii=$contLinhasPag;$ii<=($contLinhasPag+3);$ii++) {
					// 				$html .= "<br>\n";
					// 			}
					// 			$html .= "<b>Pág. ".$contPag." de ".$numPag."</b>\n";
					// 		$html .= "</td>";
					// 	$html .= '</tr>\n';
					// 	$contPag++;
					// }
				}
			// $html .= "</tbody>\n";
			
			// $html .= "<tfoot>\n";
			if ($contrTotal<=0 or $imprenda['percentual']<=0) {
				$html .= "<tr>\n";
					$html .= "<td colspan=\"13\" align=\"right\" style=\"font-weight: bold;\">Total:</td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\">".number_format($fatTotal,2,',','.')."</td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\">".number_format($contrTotal,2,',','.')."</td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\">".number_format(($contrTotal/$fatTotal)*100,2,',','')."%</td>\n";
				$html .= "</tr>\n";
				// if($contPag>=$numPag) {
				// 	$html .= '<tr>\n';
				// 		$html .= "<td colspan=\"17\" align=\"right\" style=\"font-size: 6.9px;\">\n";
				// 			for ($ii=($contLinhasPag+1);$ii<=($linhasPag+3);$ii++) {
				// 				$html .= "<br>\n";
				// 			}
				// 			$html .= "<b>Pág. ".$contPag." de ".$numPag."</b>\n";
				// 		$html .= "</td>";
				// 	$html .= '</tr>\n';
				// }
			} else {
				$html .= "<tr>\n";
					$html .= "<td colspan=\"13\" align=\"right\" style=\"font-weight: bold;\"></td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\">Subtotal:</td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\">".number_format($contrTotal,2,',','.')."</td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\">".number_format(($contrTotal/$fatTotal)*100,2,',','')."%</td>\n";
				$html .= "</tr>\n";
				$impostoderenda = ($contrTotal*($imprenda['percentual']/100));
				$html .= "<tr>\n";
					$html .= "<td colspan=\"13\" align=\"right\" style=\"font-weight: bold;\"></td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\">Desc.I.R.:</td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\">".number_format($impostoderenda*(-1),2,',','.')."</td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\"></td>\n";
				$html .= "</tr>\n";
				$html .= "<tr>\n";
					$html .= "<td colspan=\"13\" align=\"right\" style=\"font-weight: bold;\">Total:</td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\">".number_format($fatTotal,2,',','.')."</td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\">".number_format($contrTotal-$impostoderenda,2,',','.')."</td>\n";
					$html .= "<td align=\"right\" style=\"font-weight: bold;\">".number_format((($contrTotal-$impostoderenda)/$fatTotal)*100,2,',','')."%</td>\n";
				$html .= "</tr>\n";
				// if($contPag>=$numPag) {
				// 	$html .= '<tr>\n';
				// 		$html .= "<td colspan=\"17\" align=\"right\" style=\"font-size: 6.9px;\">\n";
				// 			for ($ii=($contLinhasPag+3);$ii<=($linhasPag+3);$ii++) {
				// 				$html .= "<br>\n";
				// 			}
				// 			$html .= "<b>Pág. ".$contPag." de ".$numPag."</b>\n";
				// 		$html .= "</td>";
				// 	$html .= '</tr>\n';
				// }
			}
			// $html .= "</tfoot>\n";
			$html .= "</tbody>\n";
			$retorno['sucess'] = 1;
		} else {
			$retorno['sucess'] = 0;
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
	
	if ($retorno['sucess']==1) {
		// echo $html;
		include("biblios/mpdf60/mpdf.php");
		// $mpdf=new mPDF('pt',array(205,295),3,'',8,8,10,10,9,9); 
		$mpdf=new mPDF('pt',array(205,295),3,'',6,6,6,6,5,5,'L'); 
		$mpdf->SetDisplayMode('fullwidth');
		$mpdf->setHeader($nomeEmpresa['nome']."||Emissão: {DATE d/m/Y}");
		$mpdf->setFooter("Pág. {PAGENO} de {nb}");
		// $css = file_get_contents("style.css");
		// $mpdf->WriteHTML($css,1);
		// $mpdf->AddPage('L');
		// $mpdf->WriteHTML($pdf);
		$mpdf->WriteHTML($html);
		$diret = "temp/";
		if (!is_dir($diret)) mkdir($diret);
		$arquivo = $diret.'relRentU'.session_id().mt_rand().'.pdf';
		$mpdf->Output($arquivo,'F');
		
		// echo "<object type=\"application/pdf\"  data=\"$arquivo?#zoom=100\" style=\"width: 100%; height: 100%;\">\n";
		// echo "<a href=\"$arquivo?#zoom=100\">Ver PDF</a> <-- Para navegadores que não suportam object -->\n";
		// echo "</object>";
		
		$retorno['arquivo'] = urlencode($arquivo);
	}
	
	echo json_encode($retorno);
?>