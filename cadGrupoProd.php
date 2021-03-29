<?
$popup = 1;
require_once("session.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	//variaveis novas recebem o que foi postado no formulário
	if (!isset($_GET['cod_grupo'])) {
		$pesqCod = $sql->query("select cod_grupo_prod from grupos_de_produto where ativo = 1 and empresa = ".$sql->real_escape_string($_POST['empresa'])." order by cod_grupo_prod desc");
		if (mysqli_num_rows($pesqCod)==0) $cod_grupo = 1;
		else {
			$ultimo_cod = mysqli_fetch_array($pesqCod);
			$cod_grupo = $ultimo_cod['cod_grupo_prod'] + 1;
		}
	} else $cod_grupo = $_GET['cod_grupo'];
	$grupo = $sql->real_escape_string($_POST['grupo']);
	$usuario = $_SESSION['UsuarioID'];
	$empresa = $sql->real_escape_string($_POST['empresa']);
	
			if (!empty($_POST) AND (empty($_POST['grupo']))) {
				echo "<script>alert('Preencha todos os campos!')</script>";
			}
			else {
				if (!isset($_GET['cod_grupo'])) {
					//verifica se grupo já foi cadastrado
					$pesquisa = "select grupo from grupos_de_produto where empresa = '".$empresa."' and grupo = '".$grupo."'";
					$sqll = $sql->query($pesquisa);
					$numCadastros = mysqli_num_rows($sqll);
					if ($numCadastros>0) {
						echo "<script>alert('O grupo informado já está cadastrado no sistema.')</script>";
					}
					else {
						// Arquiva os dados no banco de dados
						$sqll = "INSERT INTO grupos_de_produto (cod_grupo_prod, grupo, data, usuario, empresa, ativo) VALUES ('$cod_grupo','$grupo',now(),'$usuario','$empresa',1)";
						$sql->query($sqll) or die (mysqli_error($sql));
							echo "<script>atualizaPagMae(); alert('Cadastro efetuado!'); window.close();</script>";
					}
				}
				if (isset($_GET['cod_grupo'])) {
					$antigo = mysqli_fetch_array($sql->query("select grupo from grupos_de_produto where cod_grupo_prod = ".$_GET['cod_grupo']." and ativo = 1 and empresa = $empresa"));
					$grupoAnt = $antigo['grupo'];
					if ($grupoAnt!=$grupo) {
						//altera anterior para ativo = 0
						$sql->query("update grupos_de_produto set ativo = 0 where cod_grupo_prod = $cod_grupo and empresa = $empresa") or die (mysqli_error($sql));
						$sql->query("INSERT INTO grupos_de_produto (cod_grupo_prod, grupo, data, usuario, empresa, ativo) VALUES ('$cod_grupo','$grupo',now(),'$usuario','$empresa',1)") or die (mysqli_error($sql));
						echo "<script>atualizaPagMae(); alert('Alteração efetuada!'); window.close();</script>";
					} else echo "<script>alert('Nenhuma alteração foi detectada!')</script>";
				}
			}
}
else if (isset($_GET['cod_grupo']) and $_SERVER['REQUEST_METHOD'] != "POST") {
	if (!isset($_GET['emp']))
		$var = mysqli_fetch_array($sql->query("select grupo, empresa from grupos_de_produto where ativo = 1 and cod_grupo_prod = ".$_GET['cod_grupo']." and empresa = ".$_SESSION['UsuarioEmpresa']));
	else
		$var = mysqli_fetch_array($sql->query("select grupo, empresa from grupos_de_produto where ativo = 1 and cod_grupo_prod = ".$_GET['cod_grupo']." and empresa = ".$_GET['emp']));
	$grupo = $var['grupo'];
	$empresa = $var['empresa'];
}
else {
	$grupo = "";
	$empresa = "";
}
?>
<html>
<head>
<title><?echo $title." - Cadastro de Grupo de Produtos";?></title>
</head>
<body onLoad="document.getElementById('txGrupo').focus();">
<?
//validação página
autorizaPagina(2);
teclaEsc();
?>

	<?if (isset($_GET['cod_grupo'])) {
		if (!isset($_GET['emp'])) echo "<form action=\"cadGrupoProd.php?cod_grupo=".$_GET['cod_grupo']."\" method=\"post\">";
		else echo "<form action=\"cadGrupoProd.php?cod_grupo=".$_GET['cod_grupo']."&emp=".$_GET['emp']."\" method=\"post\">";
	}
	else echo "<form action=\"cadGrupoProd.php\" method=\"post\">";?>
	<fieldset>
		<?if (isset($_GET['cod_prod'])) echo "<legend>Edição de Grupo de Produtos</legend>";
		else echo "<legend>Cadastro de Grupo de Produtos</legend>";?>
		<table>
			<tr><td><label for="txGrupo">Grupo: </label></td>
				<td><input type="text" name="grupo" id="txGrupo" maxlength="20" size="55" value="<?echo $grupo;?>"/></td></tr>
				
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
				
			<?if (!isset($_GET['cod_grupo'])) echo "<input type=\"submit\" value=\"Salvar\"/></td></tr>";
			else echo "<input type=\"submit\" value=\"Alterar\"/></td></tr>";?>
		</table>
	</fieldset>
</form>

</body>
</html>