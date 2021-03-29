<?require_once("config.php");?>
<html>
<head>
<title> 
<?
echo $nomeSistema;
?></title>
<script type="text/javascript">
	function atualizaUsuarios() {
		opener.location.reload();
	}
</script>
</head>
<body onunload="atualizaUsuarios();">
<?
// Verifica se houve POST e se o usuário ou a senha ou nome é(são) vazio(s)
if (!empty($_POST) AND (empty($_POST['email']) OR empty($_POST['senha'])  OR empty($_POST['senha2'])OR empty($_POST['nome']))) {
  header("Location: cadUser.php"); exit;
}

//variaveis novas recebem o que foi postado no formulário
$nome = $sql->real_escape_string($_POST['nome']);
$senha = $sql->real_escape_string($_POST['senha']);
$senha2 = $sql->real_escape_string($_POST['senha2']);
$email = $sql->real_escape_string($_POST['email']);
$empresa = $sql->real_escape_string($_POST['empresa']);
$nivel = $sql->real_escape_string($_POST['nivel']);
$ativo = 1;

//verifica se as senhas são iguais
if ($senha!=$senha2){
	echo "As senhas não correspondem.<br>";
	echo "<a href=\"cadUser.php\">Voltar</a>";
	exit;
}

//verifica se email já foi cadastrado
$pesquisa = "select email from usuarios where email = '".$email."'";
$sqll = $sql->query($pesquisa);
$numCadastros = mysqli_num_rows($sqll);
if ($numCadastros>0) {
	echo "O e-mail informado já está cadastrado no sistema.<br>";
	echo "<a href=\"cadUser.php\">Voltar</a>";
	echo exit;
}

// Arquiva os dados no banco de dados
$sqll = "INSERT INTO usuarios (nome, senha, email, empresa, nivel, ativo, cadastro)
		VALUES ('$nome','".sha1($senha)."','$email','$empresa','$nivel','$ativo',now())";
$sql->query($sqll) or die (mysqli_error($sql));
	echo "Cadastro efetuado!";
	
	//header("Location: index.php"); exit;
?>
</body>
</html>