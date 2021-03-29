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
	$sysPesquisa = "SELECT empresa FROM usuarios WHERE id = ".$_SESSION['UsuarioID'];
	$sysResult = $sql->query($sysPesquisa) or die (mysqli_error($sql));
	$sysLista = mysqli_fetch_row($sysResult);
	$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = $sysLista[0]"));
	
	if ($nomeEmpresa[0] == "Todas") {
		$pesquisa = "SELECT id_empresa, cnpj, nome, moeda FROM empresas order by nome";
	}
	else {
		$pesquisa = "SELECT id_empresa, cnpj, nome, moeda FROM empresas WHERE id_empresa = ".$_SESSION['UsuarioEmpresa']." order by nome";
	}

	$result = $sql->query($pesquisa) or die (mysqli_error($sql));
	$linhas = mysqli_num_rows($result);
	echo "Resultados: $linhas<br>";
	
	echo "<table border=1px id=\"tabela\">\n";
	echo "<tr>\n
		<td><b>Nome</b></td>\n
		<td><b>CNPJ/CPF</b></td>\n
		<td><b>Moeda Padrão</b></td>\n
		</tr>\n";
	for ($i=$linhas;$i>0;$i--) {
		$lista = mysqli_fetch_array($result);
		echo "<tr>\n";
			echo "<td>".$lista['nome']."</td>\n";
			echo "<td>";
				if (strlen($lista['cnpj'])==14)	echo mask($lista['cnpj'],'##.###.###/####-##');
				if (strlen($lista['cnpj'])==11)	echo mask($lista['cnpj'],'###.###.###-##');
			echo "</td>\n";
			echo "<td><center>".$lista['moeda']."</center></td>\n";
			
			echo "<td><a href=\"#\" onclick=\"abrirPopup('cadEmp.php?emp=".$lista['cnpj']."',400,220);\">editar</a></td>";
		echo "</tr>\n";
	}
	echo "</table>\n";
echo "</div>";

//menu opções
echo "<ul id=\"submenu\">";
	if ($nomeEmpresa[0] == "Todas")
		echo "<li><a href=\"#\" onclick=\"abrirPopup('cadEmp.php',400,250);\">Adicionar Empresas</a></li>";
echo "</ul>";
?>
</body>
</html>