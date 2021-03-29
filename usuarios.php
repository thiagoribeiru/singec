<?require_once("session.php");?>
<html>
<head>
<title><?echo $title;?></title>
</head>
<body onunload="window.opener.location.reload();">
<?
//validação página
autorizaPagina(2);

//painel boas vindas
require_once("welcome.php");
echo "<div id=\"divmenu\">\n";
	require_once("menu.php");
echo "\n</div>";

//menu opções
	require_once("submenuconfiguracoes.php");

//tabela
echo "<div>";
	$sysPesquisa = "SELECT empresa FROM usuarios WHERE id = ".$_SESSION["UsuarioID"];
	$sysResult = $sql->query($sysPesquisa) or die (mysqli_error($sql));
	$sysLista = mysqli_fetch_row($sysResult);
	$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = $sysLista[0]"));
	
	if ($nomeEmpresa[0] == "Todas") {
		$pesquisa = "SELECT id, nome, email, empresa, ativo FROM usuarios order by empresa, nome";
	}
	else {
		$pesquisa = "SELECT id, nome, email, empresa, ativo FROM usuarios WHERE empresa = ".$_SESSION["UsuarioEmpresa"]." order by nome";
	}

	$result = $sql->query($pesquisa) or die (mysqli_error($sql));
	$linhas = mysqli_num_rows($result);
	$colunas = mysqli_num_fields($result);
	echo "Resultados: $linhas<br>";
	
	$users_online_pesq = $sql->query("select id_user from users_logados order by id_user") or die($mysqli_query($sql));
	if ($users_online_pesq->num_rows>0) {
		for ($i=1;$i<=$users_online_pesq->num_rows;$i++) {
			$users_online_result = mysqli_fetch_array($users_online_pesq);
			$users_online[$i] = $users_online_result['id_user'];
		}
	} else $users_online = array();
	echo "<div id='janela'>\n";
		echo "<table id=\"tabela_users\">\n";
		echo "<tr>\n
			<td><b>Nome</b></td>\n
			<td><b>E-mail</b></td>\n
			<td><b>Empresa</b></td>\n
			<td><b>Ativo</b></td>\n
			<td><b>On/Off</b></td>\n
			</tr>\n";
		for ($i=$linhas;$i>0;$i--) {
			$lista = mysqli_fetch_row($result);
			echo "<tr>\n";
			for ($j=1;$colunas>$j;$j++) {
				if ($j==1 or $j==2) echo "<td>".$lista[$j]."</td>";
				if ($j==3) {
					$empresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = $lista[$j]"));
					echo "<td>".$empresa['nome']."</td>";
				}
				if ($j==4) {
					if ($lista[$j]==0) echo "<td><center><a href=\"alteraSitUser.php?user=$lista[0]\"><img src=\"images/bot_off.png\" height=15px position=center></a></center></td>";
					if ($lista[$j]==1) echo "<td><center><a href=\"alteraSitUser.php?user=$lista[0]\"><img src=\"images/bot_on.png\" height=15px position=center></a></center></td>";
				}
			}
			
			$online = array_search($lista[0],$users_online,true);
			if ($online!=false)
				echo "<td style='padding:2px 0 0 1px; text-align:center'><img src='images/icon_on.png'></td>\n";
			else
				echo "<td style='padding:1px 0 0 2px; text-align:center'><img src='images/icon_off.png'></td>\n";
			
			// echo "<td><a href=\"#\" onclick=\"window.open('editUser.php?user=$lista[0]&form=usuarios', 'Edição de Usuários', 'STATUS=NO, TOOLBAR=NO, LOCATION=NO, DIRECTORIES=NO, RESISABLE=NO, SCROLLBARS=YES, WIDTH=430, HEIGHT=220');\">editar</a></td>";
			echo "<td style='border-bottom:none;'><img src='images/editar.png' onclick=\"return abrirPopup('editUser.php?user=".$lista[0]."&form=usuarios',430,220);\" style='cursor:pointer;'></td>";
			echo "</tr>\n";
		}
		echo "</table>\n";
	echo "</div>\n";
echo "</div>";

//menu opções
echo "<ul id=\"submenu\">";
	echo "<li><a href=\"#\" onclick=\"window.open('cadUser.php', 'Cadastro de Usuário', 'STATUS=NO, TOOLBAR=NO, LOCATION=NO, DIRECTORIES=NO, RESISABLE=NO, SCROLLBARS=YES, TOP=200, LEFT=200, WIDTH=430, HEIGHT=250');\">Adicionar Usuário</a></li>";
echo "</ul>";
?>
</body>
</html>