<?
$popup = 1;
require_once("session.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	//variaveis novas recebem o que foi postado no formulário
	if (!isset($_GET['cod_cli'])) {
		$pesqCod = $sql->query("select cod_cli from clientes where ativo = 1 and empresa = ".$sql->real_escape_string($_POST['empresa'])." order by cod_cli desc");
		if (mysqli_num_rows($pesqCod)==0) $cod_cli = 1;
		else {
			$ultimo_cod = mysqli_fetch_array($pesqCod);
			$cod_cli = $ultimo_cod['cod_cli'] + 1;
		}
	} else $cod_cli = $_GET['cod_cli'];
	$cliente = $sql->real_escape_string($_POST['cliente']);
	$regiao = $sql->real_escape_string($_POST['regiao']);
	$representante = $sql->real_escape_string($_POST['representante']);
	$observacoes = $sql->real_escape_string($_POST['observacoes']);
	$usuario = $_SESSION['UsuarioID'];
	$empresa = $sql->real_escape_string($_POST['empresa']);
	
			if (!empty($_POST) AND (empty($_POST['cliente']))) {
				echo "<script>alert('Preencha todos os campos!')</script>";
			}
			else {
				if (!isset($_GET['cod_cli'])) {
					//verifica se unidade já foi cadastrada
					// $pesquisa = "select unidade from unidades where empresa = '".$empresa."' and unidade = '".$unidade."'";
					// $sqll = $sql->query($pesquisa);
					// $numCadastros = mysqli_num_rows($sqll);
					// if ($numCadastros>0) {
					// 	echo "<script>alert('A unidade informada já está cadastrada no sistema.')</script>";
					// }
					// else {
					// 	// Arquiva os dados no banco de dados
						$sqll = "INSERT INTO clientes (cod_cli, cliente, regiao, representante, observacoes, data, usuario, empresa, ativo) VALUES ('$cod_cli','$cliente','$regiao','$representante','$observacoes',now(),'$usuario','$empresa',1)";
						$sql->query($sqll) or die (mysqli_error($sql));
							echo "<script>if (window.opener.location.pathname.indexOf(\"add_rent\")>-1) {window.opener.recarrega();} else {atualizaPagMae();} alert('Cadastro efetuado!'); window.close();</script>";
					// }
				}
				if (isset($_GET['cod_cli'])) {
					$antigo = mysqli_fetch_array($sql->query("select cliente, regiao, representante, observacoes from clientes where cod_cli = ".$_GET['cod_cli']." and ativo = 1 and empresa = $empresa"));
					$clienteAnt = $antigo['cliente'];
					$regiaoAnt = $antigo['regiao'];
					$representanteAnt = $antigo['representante'];
					$observacoesAnt = $antigo['observacoes'];
					if (($regiaoAnt!=$regiao) or ($clienteAnt!=$cliente) or ($representanteAnt!=$representante) or ($observacoesAnt!=$observacoes)) {
						//altera anterior para ativo = 0
						$sql->query("update clientes set ativo = 0 where cod_cli = $cod_cli and empresa = $empresa") or die (mysqli_error($sql));
						$sql->query("INSERT INTO clientes (cod_cli, cliente, regiao, representante, observacoes, data, usuario, empresa, ativo) VALUES ('$cod_cli','$cliente','$regiao','$representante','$observacoes',now(),'$usuario','$empresa',1)") or die (mysqli_error($sql));
						echo "<script>atualizaPagMae(); alert('Alteração efetuada!'); window.close();</script>";
					} else echo "<script>alert('Nenhuma alteração foi detectada!')</script>";
				}
			}
}
else if (isset($_GET['cod_cli']) and $_SERVER['REQUEST_METHOD'] != "POST") {
	if (!isset($_GET['emp']))
		$var = mysqli_fetch_array($sql->query("select cliente, regiao, representante, observacoes, empresa from clientes where ativo = 1 and cod_cli = ".$_GET['cod_cli']." and empresa = ".$_SESSION['UsuarioEmpresa']));
	else
		$var = mysqli_fetch_array($sql->query("select cliente, regiao, representante, observacoes, empresa from clientes where ativo = 1 and cod_cli = ".$_GET['cod_cli']." and empresa = ".$_GET['emp']));
	$cliente = $var['cliente'];
	$regiao = $var['regiao'];
	$representante = $var['representante'];
	$empresa = $var['empresa'];
	$observacoes = $var['observacoes'];
}
else {
	$cliente = "";
	$regiao = "";
	$representante = "";
	$empresa = "";
	$observacoes = "";
}
?>
<html>
<head>
<title><?echo $title." - Cadastro de Regiões - ICMS";?></title>
</head>
<body onLoad="document.getElementById('txCliente').focus();">
<?
//validação página
autorizaPagina(2);
teclaEsc();
?>

	<?if (isset($_GET['cod_cli'])) {
		if (!isset($_GET['emp'])) echo "<form action=\"cadCli.php?cod_cli=".$_GET['cod_cli']."\" method=\"post\">";
		else echo "<form action=\"cadCli.php?cod_cli=".$_GET['cod_cli']."&emp=".$_GET['emp']."\" method=\"post\">";
	}
	else {
		if (!isset($_GET['emp'])) echo "<form action=\"cadCli.php\" method=\"post\">";
		else echo "<form action=\"cadCli.php?emp=".$_GET['emp']."\" method=\"post\">";
	}
	?>
	<fieldset>
		<?if (isset($_GET['cod_cli'])) echo "<legend>Edição de Clientes</legend>";
		else echo "<legend>Cadastro de Clientes</legend>";?>
		<table><tr><td><table>
			<tr><td><label for="txCliente">Cliente: </label></td>
				<td><input type="text" name="cliente" id="txCliente" maxlength="50" size="50" value="<?echo $cliente;?>"/></td></tr>
				
			<tr><td><label for="txRegiao">Região: </label></td>
				<td>
				<?
					$sysLista = mysqli_fetch_row($sql->query("SELECT empresa FROM usuarios WHERE id = ".$_SESSION['UsuarioID']));
					$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = ".$sysLista[0]));
					if ($nomeEmpresa['nome']=="Todas") {
						echo "<select id=\"txRegiao\" name=\"regiao\">\n";
						$pesqReg = $sql->query("select cod_reg, regiao from regioes where ativo = 1 and empresa = ".$_GET['emp']." order by regiao");
						for ($i=1;mysqli_num_rows($pesqReg)>=$i;$i++) {
							$regList = mysqli_fetch_array($pesqReg);
							if ($regiao==$regList['cod_reg'])
								echo "<option value=\"".$regList['cod_reg']."\" selected=\"selected\">".$regList['regiao']."</option>\n";
							else
								echo "<option value=\"".$regList['cod_reg']."\">".$regList['regiao']."</option>\n";
						}
						echo "</select>\n";
					} else {
						echo "<select id=\"txRegiao\" name=\"regiao\">\n";
						$pesqReg = $sql->query("select cod_reg, regiao from regioes where ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']." order by regiao");
						for ($i=1;mysqli_num_rows($pesqReg)>=$i;$i++) {
							$regList = mysqli_fetch_array($pesqReg);
							if ($regiao==$regList['cod_reg'])
								echo "<option value=\"".$regList['cod_reg']."\" selected=\"selected\">".$regList['regiao']."</option>\n";
							else
								echo "<option value=\"".$regList['cod_reg']."\">".$regList['regiao']."</option>\n";
						}
						echo "</select>\n";
					}
				?>
			</td></tr>
			
			<tr><td><label for="txRepresentante"><?echo "<a href=\"#\" onclick=\"abrirPopup('cadRep.php',650,300);\">Representante: </a>"?></label></td>
				<td>
				<?
					$sysLista = mysqli_fetch_row($sql->query("SELECT empresa FROM usuarios WHERE id = ".$_SESSION['UsuarioID']));
					$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = ".$sysLista[0]));
					if ($nomeEmpresa['nome']=="Todas") {
						echo "<select id=\"txRepresentante\" name=\"representante\">\n";
						$pesqRep = $sql->query("select cod_rep, nome from representantes where ativo = 1 and empresa = ".$_GET['emp']." order by nome");
						for ($i=1;mysqli_num_rows($pesqRep)>=$i;$i++) {
							$repList = mysqli_fetch_array($pesqRep);
							if ($representante==$repList['cod_rep'])
								echo "<option value=\"".$repList['cod_rep']."\" selected=\"selected\">".$repList['nome']."</option>\n";
							else
								echo "<option value=\"".$repList['cod_rep']."\">".$repList['nome']."</option>\n";
						}
						echo "</select>\n";
					} else {
						echo "<select id=\"txRepresentante\" name=\"representante\">\n";
						$pesqRep = $sql->query("select cod_rep, nome from representantes where ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']." order by nome");
						for ($i=1;mysqli_num_rows($pesqRep)>=$i;$i++) {
							$repList = mysqli_fetch_array($pesqRep);
							if ($representante==$repList['cod_rep'])
								echo "<option value=\"".$repList['cod_rep']."\" selected=\"selected\">".$repList['nome']."</option>\n";
							else
								echo "<option value=\"".$repList['cod_rep']."\">".$repList['nome']."</option>\n";
						}
						echo "</select>\n";
					}
				?>
			</td></tr>
				
			<tr><td><label for="txEmpresa">Empresa: </label></td>
				<td>
				<?
					$sysLista = mysqli_fetch_row($sql->query("SELECT empresa FROM usuarios WHERE id = ".$_SESSION['UsuarioID']));
					$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = ".$sysLista[0]));
					if ($nomeEmpresa['nome']=="Todas" and !isset($_GET['emp'])) {
						echo "<select id=\"txEmpresa\" name=\"empresa\">\n";
						$pesqEmp = $sql->query("select id_empresa, nome, cnpj from empresas where nome != 'Todas'");
						for ($i=1;mysqli_num_rows($pesqEmp)>=$i;$i++) {
							$empList = mysqli_fetch_array($pesqEmp);
							if ($empresa==$empList['id_empresa'])
								echo "<option value=\"".$empList['id_empresa']."\" selected=\"selected\">".$empList['nome']." - ".$empList['cnpj']."</option>\n";
							else
								echo "<option value=\"".$empList['id_empresa']."\">".$empList['nome']." - ".$empList['cnpj']."</option>\n";
						}
						echo "</select>\n";
					} else {
						if (!isset($_GET['emp'])){
							echo $nomeEmpresa['nome']."\n";
							echo "<input type=\"hidden\" name=\"empresa\" value=\"".$_SESSION['UsuarioEmpresa']."\">\n";
						} else {
							$empNome = mysqli_fetch_array($sql->query("select nome from empresas where id_empresa = ".$_GET['emp']));
							echo $empNome['nome']."\n";
							echo "<input type=\"hidden\" name=\"empresa\" value=\"".$_GET['emp']."\">\n";
						}
					}
				?>
			</td></tr></table></td></tr>
			
			<tr><td><label for="txObservacoes">Observações: </label></td></tr>
			<tr><td><textarea name="observacoes" id="txObservacoes" cols="51" rows="4"><?echo $observacoes;?></textarea></td></tr>
				
			<?if (!isset($_GET['cod_cli'])) echo "<tr><td><center><input type=\"submit\" value=\"Salvar\"/></center></td></tr>";
			else echo "<tr><td><center><input type=\"submit\" value=\"Alterar\"/></center></td></tr>";?>
		</table>
	</fieldset>
</form>

</body>
</html>