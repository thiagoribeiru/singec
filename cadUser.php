<?
$popup = 1;
require_once("session.php");
?>
<html>
<head>
<title><?echo $title." - Cadastro de Usuários";?></title>
</head>
<body>
<?
//validação página
autorizaPagina(2);
teclaEsc();
?>

<form action="cadUserToSQL.php" method="post">
	<fieldset>
		<legend>Cadastro de Usuários</legend>
		<table>
			<tr><td><label for="txNome">Nome</label></td>
				<td><input type="text" name="nome" id="txNome" maxlength="25" size="40"/></td></tr>
				
			<tr><td><label for="txEmpresa">Empresa</label></td>
				<td>
				<?
					$sysPesquisa = "SELECT empresa FROM usuarios WHERE id = ".$_SESSION['UsuarioID'];
					$sysResult = $sql->query($sysPesquisa) or die (mysqli_error($sql));
					$sysLista = mysqli_fetch_row($sysResult);
					$sysNomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = ".$sysLista[0]));
					
					if ($sysNomeEmpresa[0] == "Todas") {
						$pesquisa = "SELECT id_empresa, nome FROM empresas";
					}
					else {
						$pesquisa = "SELECT id_empresa, nome FROM empresas WHERE id_empresa = ".$_SESSION['UsuarioEmpresa'];
					}
					$result = $sql->query($pesquisa) or die (mysqli_error($sql));
					$linhas = mysqli_num_rows($result);
					if ($linhas>1) {
						echo "<select name=\"empresa\" id=\"txEmpresa\">";
						for ($i=0;$linhas>$i;$i++){
							$lista = mysqli_fetch_row($result);
							echo "<option value=\"".$lista[0]."\">".$lista[1]."</option>";
						}
						echo "</select>";
					}
					else {
						$lista = mysqli_fetch_row($result);
						echo "$lista[1]";
						echo "<input type=\"hidden\" name=\"empresa\" value=\"".$lista[0]."\">";
					}
				?>
				</td>
			</tr>
			
			<tr><td><label for="txNivel">Nível</label></td>
				<td><input type="radio" name="nivel" id="txNivel" value="1" checked>Usuário</input>
					<input type="radio" name="nivel" id="txNivel" value="2">Administrador</input></td></tr>
			
			<tr><td><label for="txEmail">Email</label></td>
				<td><input type="email" name="email" id="txEmail" maxlength="40" size="40"/></td></tr>
				
			<tr><td><label for="txSenha">Senha</label></td>
				<td><input type="password" name="senha" id="txSenha" maxlength="25" size="40"/></td></tr>
				
			<tr><td><label for="txSenha2">Confirme</label></td>
				<td><input type="password" name="senha2" id="txSenha2" maxlength="25" size="40"/></td></tr>
				
			<tr><td></td><td><center><input type="submit" value="Salvar"/></center></td>
		</table>
	</fieldset>
</form>

</body>
</html>