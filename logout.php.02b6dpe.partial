<?php
	require_once("config.php");
		session_start();
	
		if (!isset($_SESSION['UsuarioID'])) {header("Location: index.php"); exit;}
		//pesquisa usuario bd
		$userPesq = $sql->query("select id_user, id_sessao, validade from users_logados where id_user = ".$_SESSION['UsuarioID']);
		$userSit = mysqli_fetch_array($userPesq);
		
		//verifica se ainda tem sessao aberta
		$numIds = mysqli_num_rows($userPesq);
			if (($numIds>0) and ($userSit['id_sessao']==session_id()))
				$sql->query("delete from users_logados where id_user = ".$_SESSION['UsuarioID']) or die(mysqli_error($sql));
			
		session_destroy(); 
	header("Location: index.php"); 
	exit;
?>