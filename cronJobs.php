<?
require_once('config.php');
ignore_user_abort(true);

// logMsg('Verificação CronJobs iniciada.');
// $cont = 0;
// $timePesq = $sql->query("select * from processoscron") or die (mysqli_error($sql));
// if (mysqli_num_rows($timePesq)>0) {
// 	$primeiraLinha = mysqli_fetch_array($timePesq);
// 	if (time()-strtotime($primeiraLinha['time'])<=($tempoCron*2)) {
// 		$sql->query("update processoscron set time = now() where id > 0") or die(mysqli_error($sql));
// 		$cont = 1;
// 	} else $cont = 0;
// } else $cont = 0;
// while($cont==1) {
// 	$timePesq = $sql->query("select * from processoscron") or die (mysqli_error($sql));
// 	if (mysqli_num_rows($timePesq)>0) {
// 		$primeiraLinha = mysqli_fetch_array($timePesq);
// 		if (time()-strtotime($primeiraLinha['time'])<=($tempoCron*2)) {
// 			$sql->query("update processoscron set time = now() where id > 0") or die(mysqli_error($sql));
// 		} else break;
// 	} else break;
// 	$tempoLimit = $tempoCron*2;
// 	set_time_limit($tempoLimit);
	$now = date('Y-m-d H:i:s');
	$pesqCron = $sql->query("select * from emails_automaticos where ativo = '1' and prox_disparo < '$now'") or die(mysqli_error($sql));
	if (mysqli_num_rows($pesqCron)>0) {
		for($i=0;$i<mysqli_num_rows($pesqCron);$i++) {
			$result = mysqli_fetch_array($pesqCron);
			$tipo = $result['tipo'];
			if ($tipo=='entradas_ult_24h')
				entradas_ult_24h($result['prox_disparo'],$result['empresa'],$result['email'],$result['indice']);
		}
	}
// 	sleep($tempoCron);
// 	// $cont++;//comente para desativar o loop
// }
// logMsg('Verificação CronJobs finalizada.');

function entradas_ult_24h($tempo,$empresa,$email,$indice) {
	global $sql;	
	$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = '".$empresa."'"));
	$dataPedDe = date('Y-m-d',strtotime('-24 hours',strtotime($tempo)));
	$dataPedAte = date('Y-m-d',strtotime('-1 second',strtotime($tempo)));
	$de = date('Y-m-d H:i:s',strtotime('-24 hours',strtotime($tempo)));
	$ate = date('Y-m-d H:i:s',strtotime('-1 second',strtotime($tempo)));
	$point = $empresa;
	
	$filtro1 = urlencode(serialize("and (faturado = '1' or faturado = '0') and data_ped between '$dataPedDe' and '$dataPedAte' and data_cri between '$de' and '$ate' and `data` between '$de' and '$ate'"));
	$filtro2 = "&order=&sent=asc&point=$point";
	
	$shemma = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
	// $url = $shemma.$_SERVER['HTTP_HOST']."/relRent3.php?filtro=".$filtro1.$filtro2;
	$url = explode(basename($_SERVER['PHP_SELF']),($shemma.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']))[0]."relRent3.php?filtro=".$filtro1.$filtro2;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = curl_exec($ch);
	curl_close($ch);
	$data = json_decode($data);
	
	if ($data->sucess==1) {
		$anexo = ''.urldecode($data->arquivo);
		// echo "<a href=\"".$anexo."\">link</a><br>";
		// $assunto = 'Entradas de '.date('d/m/Y H:i',strtotime('-24 hours',strtotime($tempo))).' ate '.date('d/m/Y H:i',strtotime('-1 second',strtotime($tempo)));
		$assunto = 'Relatório de entradas nas últimas 24hs - '.$nomeEmpresa['nome'];
		// echo $assunto;
		$corpo = 'Entradas de '.date('d/m/Y H:i',strtotime('-24 hours',strtotime($tempo))).' ate '.date('d/m/Y H:i',strtotime('-1 second',strtotime($tempo))).'<br><br>*Este é um e-mail automático, não responda.';
		
		$retorno = enviaEmail($email,$assunto,$corpo,$anexo);
	
		if ($retorno['sucess']==0 and isset($retorno)) {
			logMsg($retorno['msg'].' Destino(s): '.$email.'. Período: de '.date('d/m/Y',strtotime('-24 hours',strtotime($tempo))).' ate '.date('d/m/Y',strtotime('-1 second',strtotime($tempo))).'. entradas_ult_24h');
			if (!$sql->query("update emails_automaticos set prox_disparo = '".date('Y-m-d H:i:s',strtotime('+24 hours',strtotime($tempo)))."' where indice = '".$indice."'")) {
				logMsg(mysqli_error($sql),'error');
				$erroNro = mysql_errno();
				$erroMsg = mysqli_error($sql);
				$ret = enviaEmail('singec@singec.com.br','Erro Sql','Erro: '.$erroNro.' - '.$erroMsg);
				if ($ret['sucess']!=0) logMsg('Falha no envio de informações de erros.','error');
			}
		} else if(!isset($retorno)) {
			logMsg('Funcao sem resposta. Destino(s): '.$email.'. Período: de '.date('d/m/Y',strtotime('-24 hours',strtotime($tempo))).' ate '.date('d/m/Y',strtotime('-1 second',strtotime($tempo))).'. entradas_ult_24h','error');
		} else {
			logMsg('Erro: '.$retorno['sucess'].' - '.$retorno['msg'].' Destino(s): '.$email.'. Período: de '.date('d/m/Y',strtotime('-24 hours',strtotime($tempo))).' ate '.date('d/m/Y',strtotime('-1 second',strtotime($tempo))).'. entradas_ult_24h','error');
		}
	} else {
		if (!$sql->query("update emails_automaticos set prox_disparo = '".date('Y-m-d H:i:s',strtotime('+24 hours',strtotime($tempo)))."' where indice = '".$indice."'")) {
			logMsg(mysqli_error($sql),'error');
			$erroNro = mysql_errno();
			$erroMsg = mysqli_error($sql);
			$ret = enviaEmail('singec@singec.com.br','Erro Sql','Erro: '.$erroNro.' - '.$erroMsg);
			if ($ret['sucess']!=0) logMsg('Falha no envio de informações de erros.','error');
		}
	}
}
?>