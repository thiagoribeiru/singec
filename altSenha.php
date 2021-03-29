<?
$popup = 1;
require_once("session.php");
?>
<html>
<head>
<title><?echo $title." - Alteração de Senha";?></title>
</head>
<body>
<?
//validação página
autorizaPagina(2);
teclaEsc();

$user = $_GET['user'];
$nome = $_GET['nome'];
$email = $_GET['email'];
$numEmpresa = $_GET['numEmpresa'];
$nivel = $_GET['nivel'];
?>

<form action="altSenhaToSQL.php" method="post">
	<fieldset>
		<legend>Alteração de Senha</legend>
		<table>
			<tr><td><label for="txSenhaAtual">Senha Atual</label></td>
				<td><input type="password" name="senhaAtual" id="txSenhaAtual" maxlength="25" size="25"/></td></tr>
				
			<tr><td><label for="txNovaSenha">Nova Senha</label></td>
				<td><input type="password" name="novaSenha" id="txNovaSenha" maxlength="25" size="25"/></td></tr>
			
			<input type="hidden" name ="user" value="<?echo "$user"?>"/>
			<input type="hidden" name ="nome" value="<?echo "$nome"?>"/>
			<input type="hidden" name ="email" value="<?echo "$email"?>"/>
			<input type="hidden" name ="numEmpresa" value="<?echo "$numEmpresa"?>"/>
			<input type="hidden" name ="nivel" value="<?echo "$nivel"?>"/>
				
			<tr><td></td><td><center><input type="submit" value="Salvar"/></center></td>
		</table>
	</fieldset>
</form>

</body>
</html>