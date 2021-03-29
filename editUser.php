<?$popup = 1;
require_once("session.php");?>
<html>
<head>
<title><?echo $title." - Edição de Usuários";?></title>
</head>
<body>
<?
//validação página
autorizaPagina(2);
teclaEsc();

$user = $_GET['user'];
if($_GET['form']=="usuarios") {
	$query = $sql->query("SELECT nome, email, empresa, nivel FROM usuarios WHERE id = $user");
	$resultado = mysqli_fetch_array($query);
	
	$nome = $resultado['nome'];
	$email = $resultado['email'];
	$numEmpresa = $resultado['empresa'];
	$nivel = $resultado['nivel'];
} else if ($_GET['form']=="altSenhaToSQL") {
	$nome = $_GET['nome'];
	$email = $_GET['email'];
	$numEmpresa = $_GET['numEmpresa'];
	$nivel = $_GET['nivel'];
}
?>

<form action="editUserToSQL.php" method="post">
	<fieldset>
		<legend>Edição de Usuários</legend>
		<table>
			<tr><td><label for="txNome">Nome</label></td>
				<td><input type="text" name="nome" id="txNome" maxlength="25" size="40" value="<?echo "$nome";?>"/></td></tr>
				
			<tr><td><label for="txEmpresa">Empresa</label></td>
				<td>
				<?
					$sysPesquisa = "SELECT empresa FROM usuarios WHERE id = ".$_SESSION["UsuarioID"];
					$sysResult = $sql->query($sysPesquisa) or die (mysqli_error($sql));
					$sysLista = mysqli_fetch_row($sysResult);
					$sysNomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = $sysLista[0]"));
					
					if ($sysNomeEmpresa[0] == "Todas") {
						$pesquisa = "SELECT id_empresa, nome FROM empresas";
					}
					else {
						$pesquisa = "SELECT id_empresa, nome FROM empresas WHERE id_empresa = ".$_SESSION["UsuarioEmpresa"];
					}
					$result = $sql->query($pesquisa) or die (mysqli_error($sql));
					$linhas = mysqli_num_rows($result);
					if ($linhas>1) {
						echo "<select name=\"empresa\" id=\"txEmpresa\">";
						for ($i=0;$linhas>$i;$i++){
							$lista = mysqli_fetch_row($result);
							if ($lista[0]==$numEmpresa) $opcao="selected=\"selected\"";
							else $opcao="";
							echo "<option value=\"$lista[0]\" $opcao>$lista[1]</option>";
						}
						echo "</select>";
					}
					else {
						$lista = mysqli_fetch_row($result);
						echo "$lista[1]";
						echo "<input type=\"hidden\" name=\"empresa\" value=\"$lista[0]\">";
					}
				?>
				</td>
			</tr>
			
			<tr><td><label for="txNivel">Nível</label></td>
				<td><input type="radio" name="nivel" id="txNivel" value="1"<?if ($nivel==1) echo " checked";?>>Usuário</input>
					<input type="radio" name="nivel" id="txNivel" value="2"<?if ($nivel==2) echo " checked";?>>Administrador</input></td></tr>
			
			<tr><td><label for="txEmail">Email</label></td>
				<td><input type="email" name="email" id="txEmail" maxlength="40" size="40" value="<?echo "$email";?>"/></td></tr>
				
			<input type="hidden" name="user" value="<?echo "$user";?>">
			
			<tr><td></td><td><center><a href="altSenha.php?<?echo "user=$user&nome=$nome&email=$email&numEmpresa=$numEmpresa&nivel=$nivel";?>">Alterar Senha</a></center></td></tr>
				
			<tr><td></td><td><center><input type="submit" value="Salvar"/></center></td>
		</table>
	</fieldset>
</form>

</body>
</html>