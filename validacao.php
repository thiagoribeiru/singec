<?require_once("config.php");?>
<?
// Verifica se houve POST e se o usuário ou a senha é(são) vazio(s)
if (!empty($_POST) AND (empty($_POST['email']) OR empty($_POST['senha']))) {
  header("Location: login.php"); exit;
}

//variaveis novas recebem o que foi postado no formulário
$email = $sql->real_escape_string($_POST['email']);
$senha = $sql->real_escape_string($_POST['senha']);

// Validação do usuário/senha digitados
$sqll = "SELECT `id`, `nome`, `nivel`, `empresa`, `ativo` FROM `usuarios` WHERE (`email` = '". $email ."') AND (`senha` = '". sha1($senha) ."') AND (`ativo` = 1) LIMIT 1";
$query = $sql->query($sqll);
if (mysqli_num_rows($query) != 1) {
  // Mensagem de erro quando os dados são inválidos e/ou o usuário não foi encontrado
  echo "<meta http-equiv=\"Content-Type\" content=\"text/html\" charset=\"utf-8\">";
  echo "Login inválido! <a href=\"login.php\">Voltar para página de login.</a>";
  exit;
} else {
  // Salva os dados encontados na variável $resultado
  $resultado = mysqli_fetch_assoc($query);
  
	//criador de tabelas
	require_once("tabelas.php");
  
  $manutencao_pesq = $sql->query("select * from manutencao") or die (mysqli_error($sql));
  if ($manutencao_pesq->num_rows==0) {
	$sql->query("insert into manutencao (indice,status,mensagem,data,usuario) values (1,0,'',now(),1)") or die (mysqli_error($sql));
  }
	$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = ".$resultado['empresa']));
	$manutencao = mysqli_fetch_array($manutencao_pesq);
	if ($manutencao['status']==1 and $nomeEmpresa['nome']!='Todas') {
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html\" charset=\"utf-8\">";
		echo "Sistema em manutenção! Favor tentar novamente mais tarde! <a href=\"login.php\">Voltar para página de login.</a>";
		exit;
	}
  
   // Se a sessão não existir, inicia uma
  if (!isset($_SESSION)) session_start();
  // Salva os dados encontrados na sessão
  $_SESSION['UsuarioID'] = $resultado['id'];
  $_SESSION['UsuarioNome'] = $resultado['nome'];
  $_SESSION['UsuarioNivel'] = $resultado['nivel'];
  $_SESSION['UsuarioEmpresa'] = $resultado['empresa'];
  $_SESSION['UsuarioSitu'] = $resultado['ativo'];
  $_SESSION['UsuarioEmpresaNome'] = $nomeEmpresa['nome'];
  
  
  $moedaUser = mysqli_fetch_array($sql->query("select moeda from empresas where id_empresa = '".$_SESSION['UsuarioEmpresa']."'")) or die(mysqli_error($sql));
  $_SESSION['MoedaIso'] = $moedaUser['moeda'];
  $sinal = mysqli_fetch_array($sql->query("select moeda from moedas where iso = '".$moedaUser['moeda']."' and ativo = 1")) or die(mysqli_error($sql));
  $_SESSION['MoedaSinal'] = $sinal['moeda'];
  if (isset($_COOKIE['ultimaCota'])) setcookie("ultimaCota","");
  
  $numIds = mysqli_num_rows($sql->query("select id_user from users_logados where id_user = ".$_SESSION['UsuarioID']));
  if ($numIds==0){
	  $sql->query("insert into users_logados (id_user, id_sessao, login, validade) 
	  values ('".$_SESSION['UsuarioID']."','".session_id()."',now(),date_add(now(), interval $tempoOcioso second))") or die (mysqli_error($sql));
  }
  if ($numIds==1) {
	  $sql->query("update users_logados set id_sessao = '".session_id()."', validade = date_add(now(), interval $tempoOcioso second) where id_user = ".$_SESSION['UsuarioID']) or die (mysqli_error($sql));
  }
  if ($numIds>1) {header("Location: logout.php"); exit;}
  
  //limpa usuarios com validade vencida a mais de 30minutos
  $sql->query("delete from users_logados where now() > date_add(validade, interval 1 minute)") or die (mysqli_error($sql));
  
  //verifica arquivos temp
      //	arquivos é o diretório/pasta que terá os arquivos a serem listados.
    	$dir = "temp/";
    	 
    	$dh = opendir($dir);
    	while (false !== ($filename = readdir($dh))) {
    		$files[] = $filename;
    	}
    	sort($files);
    	foreach ($files as $links) {
    	//aqui é corrigido a questão de exibir o diretório atual e anterior 
    	  $comeco = strpos($links,".");
    		if ($comeco!=0) {
    			// $valor, será o diretório mais o link
    			$valor = "".$dir."".$links."";
    	 
    			// PARTE PARA EXIBIR E DELETAR ARQUIVOS //
    			// $arquivo será o diretório mais o nome do arquivo.
    			$arquivo = $valor;
    			 
    			// $tempo vai ser a data da última modificação do arquivo.
    		// 	$tempo = @date("d", filemtime("$arquivo"));
    		  $tempo = filemtime("$arquivo");
    			 
    			// $hoje será o dia atual - quantos dias queremos
    			// (-1 days) = 1 dia menos (arquivo modificado ontem)
    		  //$hoje = date('d', strtotime('-1 days'));
    		  //$ontem = date('d', strtotime('-1 days'));
    		  $menos24h = strtotime('-1 days');
    		  
    			// $data_arquivo é o dia que o arquivo foi criado
    			// como aparentemente, tudo na pasta a mais de 1 dia deve ser deletado
    			// somente obtemos o dia da última modificação
    			$data_arquivo = $tempo;
    			 
    			// quando o dia atual for igual ao dia da última modificação
    			// deleta o arquivo, caso contrário, exibe
    		// 	if($hoje==$data_arquivo) {
    		if($data_arquivo<=$menos24h) {
    				@unlink($arquivo);
    				// echo "$arquivo (dia $data_arquivo) deletado com sucesso.<br>";
    			} else {
    				// echo "$arquivo está OK (<font color=green>$data_arquivo</font>|<font color=orange>$hoje</font>)<br>";
    			} // fim do if de comparação de datas
    			// PARTE PARA EXIBIR E DELETAR ARQUIVOS //
    		} // fim do if de pastas
    	} // fim do foreach do arquivos
  //fim da verificação
  
  // Redireciona o visitante
	  if (isset($_COOKIE['ultimaPagUser'.$_SESSION['UsuarioID']])) {
		  header("Location: ".$_COOKIE['ultimaPagUser'.$_SESSION['UsuarioID']]); exit;
	  } else {header("Location: index.php"); exit;}
}
?>
<html>
<head>
<title> 
<?
echo $nomeSistema;
?></title>
</head>
</html>