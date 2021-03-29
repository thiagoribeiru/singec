<?
$popup = 1;
require_once("session.php");?>
<html>
<head>
<title><?echo $title;?></title>
<style>
	#tb1 tbody tr td div {
		height: 14px;
		overflow: auto;
	}
	table thead tr th {
		border-right: solid 1px;	
		border-bottom: solid 1px;
		border-top: solid 1px;
	}
	table tbody tr td {
		border-right: solid 1px;	
		border-bottom: solid 1px;
	}
	.primeiracoluna {
		border-top: none;
		border-bottom: none;
		padding: 0px;
	}
</style>
<script>
	function verificaStatus(nome){
		if(nome.form.tudo.checked == 1)
			{
				nome.form.tudo.checked = 1;
				marcarTodos(nome);
			}
		else
			{
				nome.form.tudo.checked = 0;
				desmarcarTodos(nome);
			}
	}
	 
	function marcarTodos(nome){
	   for (var i=0;i<nome.form.elements.length;i++)
		  if(nome.form.elements[i].type == "checkbox")
			 nome.form.elements[i].checked=1
	}
	 
	function desmarcarTodos(nome){
	   for (var i=0;i<nome.form.elements.length;i++)
		  if(nome.form.elements[i].type == "checkbox")
			 nome.form.elements[i].checked=0
	}
</script>
</head>
<body>
<div style="width: 100%; height: 100%; overflow: auto;">
<?
//validação página
autorizaPagina(2);
teclaEsc();

	if ($_SERVER['REQUEST_METHOD']=="POST" and isset($_POST['fechar'])) {
		$cod = $_POST['fechar'];
		$impostos = mysqli_fetch_array($sql->query("select percentual from outros_impostos where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1"));
		$custo_fixo = mysqli_fetch_array($sql->query("select percentual from custo_fixo where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1"));
		$margem = mysqli_fetch_array($sql->query("select percentual from margem_fixa where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1"));
		for ($i=0;$i<count($cod);$i++) {
			$item = mysqli_fetch_array($sql->query("select cod_rent, data_nf, cliente, (select cliente from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente) as clientevar, (select regiao from regioes where ativo = 1 and empresa = rentabilidade.empresa and cod_reg = (select regiao from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente)) as regiaovar, (select nome from representantes where ativo = 1 and empresa = rentabilidade.empresa and cod_rep = (select representante from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente)) as representantevar, (select representante from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente) as cod_repvar, (select grupo from sp_dados where ativo = 1 and empresa = rentabilidade.empresa and cod_sp = rentabilidade.cod_sp) as grupovar, cod_sp, (select descricao from sp_dados where ativo = 1 and empresa = rentabilidade.empresa and cod_sp = rentabilidade.cod_sp) as descricaovar, quant as quant, (select unidade from sp_dados where ativo = 1 and empresa = rentabilidade.empresa and cod_sp = rentabilidade.cod_sp) as undvar, (select icms from regioes where ativo = 1 and empresa = rentabilidade.empresa and cod_reg = (select regiao from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente)) as icms, (select comissao_padrao from representantes where ativo = 1 and empresa = rentabilidade.empresa and cod_rep = (select representante from clientes where ativo = 1 and empresa = rentabilidade.empresa and cod_cli = rentabilidade.cliente)) as comissao_padrao, vlr_nf, fechado, data_cri, data_ped, faturado from rentabilidade where empresa = ".$_SESSION['UsuarioEmpresa']." and ativo = 1 and cod_rent = ".$cod[$i])) or die (mysqli_error($sql));
			$fechado = 1;
			$cod_rent = $sql->real_escape_string($item['cod_rent']);
			$data_nf = $sql->real_escape_string($item['data_nf']);
			$cliente = $sql->real_escape_string($item['cliente']);
			$clientevar = $sql->real_escape_string($item['clientevar']);
			$regiaovar = $sql->real_escape_string($item['regiaovar']);
			$representantevar = $sql->real_escape_string($item['representantevar']);
			$cod_sp = $sql->real_escape_string($item['cod_sp']);
			$descricaovar = $sql->real_escape_string($item['descricaovar']);
			$quant = $sql->real_escape_string($item['quant']);
			$undvar = $sql->real_escape_string($item['undvar']);
			$comissPesq = $sql->query("select comiss from representantes_grupos where cod_rep = '".$item['cod_repvar']."' and ativo = 1 and empresa = '".$_SESSION['UsuarioEmpresa']."' and cod_grupo_prod = '".$item['grupovar']."'");
			if (mysqli_num_rows($comissPesq)>0) {
				$comissGrupo = mysqli_fetch_array($comissPesq);
				$comissao = $comissGrupo['comiss'];
			} else {
				$comissao = $item['comissao_padrao'];
			}
			$comis = $sql->real_escape_string($comissao);
			$vlrpadraovar = somaSp($item['cod_sp'])/((100-($item['icms']+$impostos['percentual']+$comis+$custo_fixo['percentual']+$margem['percentual']))/100);
			$vlr_padr = $vlrpadraovar;
			$vlr_nf = $sql->real_escape_string($item['vlr_nf']);
			$icms = $sql->real_escape_string($item['icms']);
			$marg = ((($item['vlr_nf']*(100-($item['icms']+$impostos['percentual']+$comis)))-($vlrpadraovar*(100-($item['icms']+$impostos['percentual']+$comis+$margem['percentual']))))/$item['vlr_nf'])-$margem['percentual'];
			$data_cri = $sql->real_escape_string($item['data_cri']);
			$data_ped = $sql->real_escape_string($item['data_ped']);
			$faturado = $sql->real_escape_string($item['faturado']);
			$sql->query("update rentabilidade set ativo = 0 where cod_rent = $cod_rent and ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']) or die (mysqli_error($sql));
			$sql->query("insert into rentabilidade (cod_rent, data_ped, faturado, data_nf, cliente, clientevar, regiaovar, representantevar, cod_sp, descricaovar, quant, undvar, vlr_padr, vlr_nf, icms, comis, marg, data_cri, data, usuario, empresa, ativo, fechado) values ('$cod_rent', '$data_ped', '$faturado', '$data_nf', '$cliente', '$clientevar', '$regiaovar', '$representantevar', '$cod_sp', '$descricaovar', '$quant', '$undvar', '$vlr_padr', '$vlr_nf', '$icms', '$comis', '$marg', '$data_cri', now(), '".$_SESSION['UsuarioID']."', '".$_SESSION['UsuarioEmpresa']."', 1, $fechado)") or die (mysqli_error($sql));
		}
		echo "<script>atualizaPagMae(); alert('Fechamento dos itens selecionados concluido!'); location.href=\"".$_SERVER['PHP_SELF']."\";</script>";
		exit;
	}

	echo "<div style=\"width: 1328px;\">";
	echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\" id=\"form_fechamento\">";
	echo "<table id=\"tb1\" cellpadding=2px cellspacing=0px style=\"font-size: 11px;\">\n";
		echo "<thead><tr height=20px>\n";
			echo "<th width=10px class=\"primeiracoluna\"><input type=\"checkbox\" name=\"tudo\" onclick=\"verificaStatus(this)\" /></th>\n";
			echo "<th width=50px><center>"."DT.PED"."</center></th>\n";
			echo "<th width=180px><center>"."CLIENTE"."</center></th>\n";
			echo "<th width=50px><center>"."REGIÃO"."</center></th>\n";
			echo "<th width=140px><center>"."REPRESENTANTE"."</center></th>\n";
			echo "<th width=50px><center>"."PROD."."</center></th>\n";
			echo "<th width=210px><center>"."DESCRIÇÃO"."</center></th>\n";
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
			if ($fechado[$i]==0 and $faturado[$i]==1) {
				echo "<tr height=20px>\n";
					echo "<td class=\"primeiracoluna\"><input type=\"checkbox\" name=\"fechar[]\" value=\"".$cod_rent[$i]."\"/></td>";
					if ($data_ped_array[$i]!="" and $data_ped_array[$i]!="-") $data_ped = date('d/m/y',strtotime($data_ped_array[$i]));
					else if ($data_ped_array[$i]=="-") $data_ped = "-";
					else $data_ped = "";
					echo "<td><center>".$data_ped."</td>";
					echo "<td $fundocli><div style=\"width: 140px; float: left;\" id=\"cliente$i\">".$cliente[$i]."</div>";
					echo "<div style=\"width: 18px; height: 14px; float: right;\" id=\"clienteseta$i\"></div></td>";
					echo "<td><div style=\"width: 30px; float: left;\" id=\"regiao$i\">".$regiao[$i]."</div>";
					echo "<div style=\"width: 18px; height: 14px; float: right;\" id=\"regiaoseta$i\"></div></td>";
					echo "<td $fundorep><div style=\"width: 120px; float: left;\" id=\"representante$i\">".$representante[$i]."</div>";
					echo "<div style=\"width: 18px; height: 14px; float: right;\" id=\"representanteseta$i\"></div></td>";
					echo "<td>".$prod[$i]."</td>";
					echo "<td><div style=\"width: 190px; float: left;\" id=\"descricao$i\">".$descricao[$i]."</div>";
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
					if ($data_nf_array[$i]!="") $data_nf = date('d/m/y',strtotime($data_nf_array[$i])); else $data_nf = "";
					echo "<td><center>".$data_nf."</td>";
				echo "</tr>\n";
			}
		}
	echo "</tbody></table>\n";
	echo "<center><input type=\"submit\" value=\"Fechar Itens Selecionados\"/>";
	echo "</form>";
	echo "</div>";
	
	// echo "<div id=\"pageNav\" align=\"center\">";
	// echo "</div>";
	// echo "<div style=\"float: left; width: 300px; padding: 5px;\" align=\"right\"><table style=\"border: 1px solid; margin-right: 46px; text-align: right; font-size:12px\">";
	// 	//TOTALIZADORES
	// 	for ($t=0;$t<$quantreg;$t++) {
	// 		// $total = mysqli_fetch_array($sql_conta);
	// 		$totalFat = $totalFat + $total_nf[$t];
	// 		// $vlrpadraoTot = somaSp($total['prod'])/((100-($total['icms']+$impostos['percentual']+$total['comissao_padrao']+$custo_fixo['percentual']+$margem['percentual']))/100);
	// 		$totalCont = $totalCont + /*(($total['quant']*$total['vlr_nf'])-($total['quant']*$vlrpadraoTot))*/$contribuicao[$t];
	// 	}
	// 	echo "<tr><td><b>SubTotal:</b></td><td width=\"82px\"><b>".number_format($totalFat,2,",",".")."</b></td><td width=\"82px\"><b>".number_format($totalCont,2,",",".")."</b></td></tr>";
	// 	echo "</table>";
	// echo "</div>";
?>
</div>
</body>
</html>