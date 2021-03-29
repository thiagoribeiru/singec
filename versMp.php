<?$popup = 1;
require_once("session.php");?>
<html>
<head>
<title><?echo $title;?></title>
</head>
<body>
<?
teclaEsc();
$cod_mp = $_GET['cod_mp'];
echo "<table><tr><td align=\"right\">\n";
	
echo "</td></tr>\n"	;
echo "<tr><td>\n";
echo "<table border=1px>\n";
	echo "<tr>\n";
		echo "<td><center>"."<b>DESCRIÇÃO</b>"."</td>\n";
		echo "<td><center>"."<b>$</b>"."</td>\n";
		echo "<td><center>"."<b>CUSTO</b>"."</td>\n";
		echo "<td><center>"."<b>UNI</b>"."</td>\n";
		echo "<td><center>"."<b>LARG.</b>"."</td>\n";
		echo "<td><center>"."<b>G/M²</b>"."</td>\n";
		echo "<td><center>"."<b>ICMS</b>"."</td>\n";
		echo "<td><center>"."<b>PIS/COF</b>"."</td>\n";
		echo "<td><center>"."<b>FRETE</b>"."</td>\n";
		echo "<td><center>"."<b>QUEB</b>"."</td>\n";
		echo "<td><center>"."<b>OBSERVAÇÕES</b>"."</td>\n";
		echo "<td><center>"."<b>SITUAÇÃO</b>"."</td>\n";
		echo "<td><center>"."<b>DATA</b>"."</td>\n";
		echo "<td><center>"."<b>USUÁRIO</b>"."</td>\n";
	echo "</tr>\n";
	$listVersoes = $sql->query("select descricao, moeda, custo, unidade, largura, gramatura, icms, piscofins, frete, quebra,".
				" observacoes, data, usuario, ativo, estado from mp where empresa = ".$_SESSION['UsuarioEmpresa']." and cod_mp = $cod_mp order by data asc");
	$linhasMp = mysqli_num_rows($listVersoes);
		for ($i=0;$i<$linhasMp;$i++) {
			$linha = mysqli_fetch_array($listVersoes);
				if ($linha['ativo']) echo "<tr bgcolor=\"#E6E6E6\">\n";
				else echo "<tr>\n";
					echo "<td>".$linha['descricao']."</td>\n";
					echo "<td>".$linha['moeda']."</td>\n";
					echo "<td>".number_format($linha['custo'],4,",",".")."</td>\n";
					echo "<td><center>".$linha['unidade']."</center></td>\n";
					echo "<td>".number_format($linha['largura'],2,",","")." m</td>\n";
					if ($linha['gramatura']==0) echo "<td><center>-</center></td>\n";
						else echo "<td>".number_format($linha['gramatura'],0,"",".")." g/m²</td>\n";
					if ($linha['icms']==0) echo "<td><center>-</center></td>\n";
						else echo "<td>".number_format($linha['icms'],2,",","")." %</td>\n";
					if ($linha['piscofins']==0) echo "<td><center>-</center></td>\n";
						else echo "<td>".number_format($linha['piscofins'],2,",","")." %</td>\n";
					if ($linha['frete']==0) echo "<td><center>-</center></td>\n";
						else echo "<td>".number_format($linha['frete'],2,",","")." %</td>\n";
					if ($linha['quebra']==0) echo "<td><center>-</center></td>\n";
						else echo "<td>".number_format($linha['quebra'],2,",","")." %</td>\n";
					echo "<td>".$linha['observacoes']."</td>\n";
					if ($linha['estado'] == 0) echo "<td>INATIVO</td>\n";
					else echo "<td>ATIVO</td>\n";
					echo "<td>".date('d/m/Y-H\h:i\m:s\s',strtotime($linha['data']))."</td>\n";
					$nomeUser = mysqli_fetch_array($sql->query("select nome, email from usuarios where id=".$linha['usuario']));
					echo "<td>".$nomeUser['nome']."</td>\n";
				echo "</tr>\n";
		}
	echo "</table>\n</td></tr></table>\n";

?>
</body>
</html>