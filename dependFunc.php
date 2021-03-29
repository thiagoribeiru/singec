<?
$sql;//variavel mysqli global
$manuVar = 0;
$versaoSistema=0.1;
$nomeSistema='SINGEC'.' v'.$versaoSistema;
$tempoCron = 60*10;//seconds
function dominio($dominio) {
	if (count(explode($dominio,$_SERVER['SERVER_NAME']))>1) return true;
	else return false;
}

function conecta($servernameFn,$usernameFn,$passwordFn,$dbportFn,$databaseFn,$tempoOciosoFn,$nomeSistemaFn,$versaoSistemaFn,$showErrorDeprecatedFn,$displayErrorFn) {
	global $sql;
	if (!$showErrorDeprecatedFn) {
			error_reporting (E_ALL & ~ E_NOTICE & ~ E_DEPRECATED);
	}
	if (!$displayErrorFn) {
			ini_set("display_errors", 0 );
	}
	if ($dbportFn and $dbportFn!='') $dadosPort = ':'.$dbportFn;
		else $dadosPort = '';
	$sql = new mysqli($servernameFn.$dadosPort,$usernameFn,$passwordFn,$databaseFn);
	if (mysqli_connect_errno()) {
        trigger_error(mysqli_connect_error());
    } else {
		$sql->query("SET TIME_ZONE = 'America/Sao_Paulo'");
		date_default_timezone_set('America/Sao_Paulo');
    	tabelasIniciais();
		verificaManutencao();
		verificaCron();
		
    }
}

function addCol($nomeCol,$nomeTab,$tipo,$after) {
	global $sql;
	ini_set('max_execution_time', 300);
	// ($nomeCol,$nomeTab,$tipo,$after,[$stringComando])
	$numArg = func_num_args();
	$qtabs = $sql->query("show columns from $nomeTab like '$nomeCol'") or die(mysqli_error($sql));
	if ($qtabs->num_rows==0) {
		$sql->query("ALTER TABLE $nomeTab ADD COLUMN $nomeCol $tipo after $after;") or die(mysqli_error($sql));
		if ($numArg==5) {
			$stringComando = func_get_arg(4);
			$sql->query($stringComando) or die(mysqli_error($sql));
		}
	}
}

function delCol($nomeCol,$nomeTab) {
	global $sql;
	$qtabs = $sql->query("show columns from $nomeTab like '$nomeCol'") or die(mysqli_error($sql));
	if (mysqli_num_rows($qtabs)!=0) {
		$sql->query("ALTER TABLE $nomeTab DROP $nomeCol") or die(mysqli_error($sql));
	}
}

function horaSQL () {
	$horaSQL = mysqli_fetch_row($sql->query("select now()"));
	$diferenca = time()-$horaSQL[0];
	$hora = $horaSQL[0]+$diferenca;
	
	return date('Y-m-d H:i:s',$hora);
}

function tabelasIniciais() {
	global $sql;
	mysqli_query($sql,"CREATE TABLE IF NOT EXISTS empresas (id_empresa INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("cnpj","empresas","VARCHAR(14) NOT NULL UNIQUE","id_empresa");
		addCol("nome","empresas","VARCHAR(30) NOT NULL","cnpj");
		addCol("moeda","empresas","VARCHAR(3) NOT NULL DEFAULT 'USD'","nome");
	if (mysqli_num_rows(mysqli_query($sql,"select * from empresas"))==0)
		mysqli_query($sql,"INSERT INTO empresas (cnpj,nome,moeda) VALUES ('0','Todas','USD')") or die(mysqli_error($sql));
	
	$sql->query("CREATE TABLE IF NOT EXISTS usuarios (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("nome","usuarios","VARCHAR(100) NOT NULL","id");
		addCol("senha","usuarios","VARCHAR(50) NOT NULL","nome");
		addCol("email","usuarios","VARCHAR(100) NOT NULL UNIQUE","senha");
		addCol("empresa","usuarios","INT NOT NULL","email");
		addCol("nivel","usuarios","INT NOT NULL","empresa");
		addCol("ativo","usuarios","TINYINT(1) NOT NULL","nivel");
		addCol("cadastro","usuarios","DATETIME NOT NULL","ativo");
	if (mysqli_num_rows($sql->query("select * from usuarios"))==0)
		$sql->query("INSERT INTO usuarios (nome,senha,email,empresa,nivel,ativo,cadastro) VALUES ('Thiago Ribeiro','071b6f05b2d2684101ea7f75bc10179ce6eeca2b','singec@singec.com.br','1','2','1',now())") or die(mysqli_error($sql));
	
	$sql->query("CREATE TABLE IF NOT EXISTS users_logados (id_user INT NOT NULL) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("id_sessao","users_logados","VARCHAR(40) NOT NULL","id_user");
		addCol("login","users_logados","DATETIME NOT NULL","id_sessao");
		addCol("validade","users_logados","DATETIME NOT NULL","login");
	
	$sql->query("CREATE TABLE IF NOT EXISTS processoscron (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("time","processoscron","DATETIME NOT NULL","id");
}

function logMsg( $msg, $level = 'info', $file = 'logs/log.log' ) {
    $levelStr = ''; // variável que vai armazenar o nível do log (INFO, WARNING ou ERROR)
    switch ( $level ) {// verifica o nível do log
        case 'info':// nível de informação
		$levelStr = 'INFO';
		break;
        case 'warning':// nível de aviso
		$levelStr = 'WARNING';
		break;
        case 'error':// nível de erro
		$levelStr = 'ERROR';
		break;
    }
    $date = date( 'Y-m-d H:i:s' );// data atual
    // formata a mensagem do log
    // 1o: data atual
    // 2o: nível da mensagem (INFO, WARNING ou ERROR)
    // 3o: a mensagem propriamente dita
    // 4o: uma quebra de linha
    $msg = sprintf( "[%s] [%s]: %s%s", $date, $levelStr, $msg, PHP_EOL );
    // escreve o log no arquivo
    // é necessário usar FILE_APPEND para que a mensagem seja escrita no final do arquivo, preservando o conteúdo antigo do arquivo
    file_put_contents( $file, $msg, FILE_APPEND );
}

function enviaEmail($toAddress,$subject,$body,$attachment='',$arrayHeaders='') {
	require_once('PHPMailer/PHPMailerAutoload.php');
	$destino = explode(',',$toAddress);
	// headers_list
		if (isset($arrayHeaders['fromName'])) $fromName = $arrayHeaders['fromName'];
			else $fromName = 'Singec';
		if (isset($arrayHeaders['fromAddress'])) $fromAddress = $arrayHeaders['fromAddress'];
			else $fromAddress = 'singec@singec.com.br';
		if (isset($arrayHeaders['replyToName'])) $replyToName = $arrayHeaders['replyToName'];
			else $replyToName = 'naoResponder';
		if (isset($arrayHeaders['replyTo'])) $replyTo = $arrayHeaders['replyTo'];
			else $replyTo = 'singec@singec.com.br';
	// end headers_list
	$mail = new PHPMailer;
	// $mail->SMTPDebug = 3;                               // Enable verbose debug output
	$mail->Charset = 'UTF-8';
	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = 'smtp.ribeirodesenvolvimentoweb.com.br';  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = 'singec@singec.com.br';                 // SMTP username
	$mail->Password = 'thi102030';                           // SMTP password
	$mail->SMTPAutoTLS = false;
	// $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 25;                                    // TCP port to connect to
	$mail->setFrom($fromAddress, $fromName);
	for($i=0;$i<count($destino);$i++) {
		// $mail->addAddress('singec@singec.com.br', 'Joe User');     // Add a recipient
		$mail->addAddress($destino[$i]);               // Name is optional
	}
	$mail->addReplyTo($replyTo, $replyToName);
	// $mail->addCC('cc@example.com');
	$mail->addBCC($replyTo);
	// if (isset($attachment) and $attachment!="") {
		// $retorno['msg'] = $attachment;
		$mail->addAttachment($attachment);         // Add attachments
		// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	// }
	$mail->isHTML(true);                                  // Set email format to HTML
	$mail->Subject = utf8_decode($subject);
	$mail->Body    = utf8_decode($body);
	$mail->XMailer = 'Singec Mailer';
	// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
	if(!$mail->send()) {
		$retorno['sucess'] = $mail->ErrorInfo;
		logMsg("Erro no envio de email: ".$mail->ErrorInfo,"ERROR");
		$retorno['msg'] = 'A mensagem não pode ser enviada.';
		return $retorno;
		// echo 'Message could not be sent.';
		// echo 'Mailer Error: ' . $mail->ErrorInfo;
	} else {
		$retorno['sucess'] = 0;
		$retorno['msg'] = 'A mensagem foi enviada.';
		return $retorno;
		// echo 'Message has been sent';
	}
}

function verificaCron($ativaCron = false) {
	global $sql;
	$pCron = $sql->query("select * from processoscron") or die (mysqli_error($sql));
	if (mysqli_num_rows($pCron)>1) {
		$sql->query("DELETE FROM processoscron WHERE id > 0") or die (mysqli_error($sql));
	} else if (mysqli_num_rows($pCron)==0) {
		if ($ativaCron==true) {
			$sql->query("insert into processoscron (id,time) values ('1',now())") or die (mysqli_error($sql));
			// require_once('cronJobs.php');
		}
	} else if (mysqli_num_rows($pCron)==1) {
		$resultCron = mysqli_fetch_array($pCron);
		$time = $resultCron['time'];
		global $tempoCron;
		if (time()-strtotime($time)>($tempoCron*2)) {
			$sql->query("DELETE FROM processoscron WHERE id > 0") or die (mysqli_error($sql));
			enviaEmail("singec@singec.com.br","Tempo tarefa Cron excedido","As tarefas Cron foram encerradas por tempo excedido. Favor verificar.");
			logMsg("As tarefas Cron foram encerradas por tempo excedido.");
		}
	}
}
function verificaManutencao() {
	global $sql;
	global $manuVar;
	if (!isset($_SESSION)) session_start();
	if (isset($_SESSION) and $_SESSION['UsuarioEmpresa']!='' and count(explode('logout.php',$_SERVER['REQUEST_URI']))<=1) {
		$nomeEmpresa = $_SESSION['UsuarioEmpresaNome'];
		$manutencao_pesq = $sql->query("select * from manutencao") or die (mysqli_error($sql));
		$manutencao = mysqli_fetch_array($manutencao_pesq);
		if ($manutencao['status']==1 and $nomeEmpresa!='Todas') {
			echo "<meta http-equiv=\"Content-Type\" content=\"text/html\" charset=\"utf-8\">";
			echo "Sistema em manutenção! Favor tentar novamente mais tarde! <a href=\"logout.php\">Voltar para página de login.</a>";
			exit;
		} else if ($manutencao['status']==1 and $nomeEmpresa=='Todas') {
			$manuVar = 1;
		}
	}
}
?>