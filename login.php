<? require_once("config.php");
if (isset($_GET['email'])) {
	$remetente = $_POST['email'];
	$nome = $_POST['nome'];
	$assunto = $_POST['assunto'];
	$texto = $_POST['texto'];
	// $to      = 'singec@singec.com.br';
	$to      = 'singec@singec.com.br';
	// $headers = 'From: '.$nome.' <'.$remetente.'>'."\r\n".'Reply-To: '.$remetente."\r\n".'X-Mailer: PHP/'.phpversion();
	// mail($to, $assunto, $texto, $headers);
	$headers = array(
		'fromName' => $nome,
		'fromAddress' => $remetente,
		'replyTo' => $remetente,
		'replyToName' => $nome,
	);
	enviaEmail($to,$assunto,$texto,'',$headers);
}
?>
<html>
<head>
<title> 
<?php
echo $nomeSistema;
?></title>
	<link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico"/>
	<link type="text/css" href="login.css" rel="stylesheet">
	<meta http-equiv="Content-Type" content="text/html" charset="utf-8">
</head>
<body class="bodyhome">
<div class="logo"><img src="images/logo_login.png"></div>	
<div class="rain">
	<div class="border start">
		
		<form action="validacao.php" method="post">
		<!--<fieldset>
		<legend>Dados de Login</legend>-->
		  <label for="txEmail">E-mail</label>
		  <input type="mail" name="email" id="txEmail" maxlength="40" />
		  <label for="txSenha">Senha</label>
		  <input type="password" name="senha" id="txSenha" />

		  <input type="submit" value="ENTRAR"/>
		<!--</fieldset>-->
		</form>
	</div>
</div>
<div class="submit"></div>
<div class="contato"><a href="javascript: abrirContato();" title="Entrar em contato" class="linkcontato">Entrar em contato</a>
 - Desenvolvido por <a href="http://www.facebook.com/thiagoribeiru" target="_blank" class="linkcontato">Thiago Ribeiro</a>
 <br>Copyright © 2016 Singec Brasil - 2016 | Todos direitos reservados
</div>
<div id="popup" class="popup">
	<a href="javascript: fecharContato();" title="Fechar" style="background:#000000; padding:08px; border-radius:50%; color:red; text-decoration:none; margin:-24px 0 0 0; float:right;"> [ x ] </a>
	<div class="popupInner">
		<form action="login.php?email=ok" method="post">
			<p class="titulo">Fale Conosco</p>
			<p class="inform">Responderemos o seu e-mail o mais rápido possivel!</p>
			<label for="email">Seu E-mail</label>
			<input type="mail" name="email" id="email" maxlength="30" />
			<label for="nome">Nome</label>
			<input type="text" name="nome" id="nome" maxlength="30" />
			<label for="assunto">Assunto</label>
			<input type="mail" name="assunto" id="assunto" maxlength="30" />
			<label for="texto">Texto</label>
			<textarea name="texto" id="texto" cols="35" rows="6"></textarea>
			<input type="submit" value="Enviar"/>
		</form>
	</div>
</div>

</body>
<script>
	$(function(){
	  var $form_inputs =   $('form input');
	  var $rainbow_and_border = $('.rain, .border');
	  /* Used to provide loping animations in fallback mode */
	  $form_inputs.bind('focus', function(){
		$rainbow_and_border.addClass('end').removeClass('unfocus start');
	  });
	  $form_inputs.bind('blur', function(){
		$rainbow_and_border.addClass('unfocus start').removeClass('end');
	  });
	  $form_inputs.first().delay(800).queue(function() {
		$(this).focus();
	  });
	});
	function fecharContato(){
		document.getElementById('popup').style.display = 'none';
	}
	function abrirContato(){
		document.getElementById('popup').style.display = 'block';
		/* setTimeout ("fecharContato()", 454502); */
	}
</script>
</html>