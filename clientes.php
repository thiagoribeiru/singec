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
	require_once("submenucomercial.php");
	
	//######### INICIO Paginação
	$numreg = 25;
	// Quantos registros por página vai ser mostrado
	if (!isset($_GET['pg'])) $pg = 0; else $pg=$_GET['pg'];
	$inicial = $pg * $numreg;
	//######### FIM dados Paginação

//tabela
echo "<div class=\"tabelacorpo\">";
	$sysPesquisa = "SELECT empresa FROM usuarios WHERE id = ".$_SESSION['UsuarioID'];
	$sysResult = $sql->query($sysPesquisa) or die (mysqli_error($sql));
	$sysLista = mysqli_fetch_row($sysResult);
	$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = ".$sysLista[0]));
	
	if ($nomeEmpresa[0] == "Todas") {
		//formação do select com empresas cadastradas
		$empesq = $sql->query("select nome, cnpj from empresas group by cnpj order by nome");
		echo "<form>";
			echo "<label for=\"empresas\"><b>Selecione a empresa: </b></label>";
			echo "<select name=\"empresas\" id=\"empresas\">";
			for ($i=1;mysqli_num_rows($empesq)>=$i;$i++) {
				$emp = mysqli_fetch_array($empesq);
				if ($emp['nome']!="Todas") {
					echo "<option value=\"".$emp['cnpj']."\">".$emp['nome']." - ".$emp['cnpj']."</option>";
				}
			}
			echo "</select>";
		echo "</form>";
		//formação das divs com os clientes de cada empresa
		$empesq = $sql->query("select id_empresa, nome, cnpj from empresas group by cnpj order by nome");
		for ($i=1;mysqli_num_rows($empesq)>=$i;$i++) {
			$emp = mysqli_fetch_array($empesq);
			if ($emp['nome']!="Todas") {
				echo "<div id=\"".$emp['cnpj']."\" style=\"display: none;\">";
					echo "<table class=\"tabelas2\">";
						echo "<tr>";
							echo "<td><b><center>COD</center></b></td>";
							echo "<td><b><center>CLIENTE</center></b></td>";
							echo "<td><b><center>REGIÃO</center></b></td>";
							echo "<td><b><center>REPRESENTANTE</center></b></td>";
							echo "<td><b><center>OBSERVACÕES</center></b></td>";
						echo "</tr>";
						$stringPesquisa = "select cod_cli, cliente, regiao, representante, observacoes, empresa from clientes where empresa = ".$emp['id_empresa']." and ativo = 1 order by cliente";
						//conta os registros para paginação
						$sql_conta = $sql->query($stringPesquisa);
						$quantreg = mysqli_num_rows($sql_conta);
						//lista
						$unipesq = $sql->query($stringPesquisa." limit $inicial, $numreg");
						$linhasPesq = $numreg;
						for ($j=1;$linhasPesq>=$j;$j++) {
							$cli = mysqli_fetch_array($unipesq);
							echo "<tr class=\"destacaeditar\">";
								echo "<td width=50px>".$cli['cod_cli']."</td>";
								echo "<td><div style=\"width: 200px; height: 17px; overflow: auto;\">".$cli['cliente']."</div></td>";
								$reg = mysqli_fetch_array($sql->query("select regiao from regioes where cod_reg = ".$cli['regiao']." and ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']));
								echo "<td><div style=\"width: 150px; height: 17px; overflow: auto;\">".$reg['regiao']."</div></td>";
								$rep = mysqli_fetch_array($sql->query("select nome from representantes where cod_rep = ".$cli['representante']." and ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']));
								echo "<td><div style=\"width: 300px; height: 17px; overflow: auto;\">".$rep['nome']."</div></td>";
								echo "<td><div style=\"width: 400px; height: 17px; overflow: auto;\">".$cli['observacoes']."</div></td>";
								if ($cli['cod_cli']!="") {
									$link = "<a href=\"#\" onclick=\"abrirPopup('cadCli.php?cod_cli=".$cli['cod_cli']."&emp=".$cli['empresa']."',400,300);\"><img src=\"images/editar.png\"></a>";
								} else {
									$link = "";
								}
								echo "<td style=\"border: none;\">$link</td>";
							echo "</tr>";
						}
						echo "<tr><td colspan=\"5\">";
							echo "<center>";
								if ($quantreg!=0) include("paginacao.php");
							echo "</center>";
						echo "</td></tr>";
					echo "</table>";
					
				//menu opções
				echo "<ul id=\"submenu\">";
						echo "<li><a href=\"#\" onclick=\"abrirPopup('cadCli.php?emp=".$emp['id_empresa']."',400,300);\">Cadastrar Cliente</a></li>";
				echo "</ul>";
				
				echo "</div>";
			}
		}
		//formação do script de exibição das divs
		$empesq = $sql->query("select cnpj from empresas group by cnpj order by nome");
		echo "<script type=\"text/javascript\">\n";
		echo "window.onload = function(){\n";
			echo "id('empresas').onchange = function(){\n";
				for ($i=1;mysqli_num_rows($empesq)>=$i;$i++) {
					$emp = mysqli_fetch_array($empesq);
					echo "if( this.value==".$emp['cnpj']." )\n";
						echo "id('".$emp['cnpj']."').style.display = 'block';\n";
					echo "if( this.value!=".$emp['cnpj']." )\n";
						echo "id('".$emp['cnpj']."').style.display = 'none';\n";
				}
			echo "}\n";
		echo "}\n";
		echo "function id( el ){\n";
		    echo "return document.getElementById( el );\n";
		echo "}\n";
		echo "</script>\n";
	}
	else {
		//formação da div com as unidades da empresa
		echo "<div>";
			echo "<table class=\"tabelas2\">";
				echo "<tr>";
					echo "<td><b><center>COD</center></b></td>";
					echo "<td><b><center>CLIENTE</center></b></td>";
					echo "<td><b><center>REGIÃO</center></b></td>";
					echo "<td><b><center>REPRESENTANTE</center></b></td>";
					echo "<td><b><center>OBSERVACÕES</center></b></td>";
				echo "</tr>";
				$stringPesquisa = "select cod_cli, cliente, regiao, representante, observacoes from clientes where empresa = ".$_SESSION['UsuarioEmpresa']."  and ativo = 1 order by cliente";
				//conta os registros para paginação
				$sql_conta = $sql->query($stringPesquisa);
				$quantreg = mysqli_num_rows($sql_conta);
				//lista
				$unipesq = $sql->query($stringPesquisa." limit $inicial, $numreg");
				$linhasPesq = $numreg;
				for ($j=1;$linhasPesq>=$j;$j++) {
					$cli = mysqli_fetch_array($unipesq);
					echo "<tr class=\"destacaeditar\">";
						echo "<td width=50px>".$cli['cod_cli']."</td>";
						echo "<td><div style=\"width: 200px; height: 17px; overflow: auto;\">".$cli['cliente']."</div></td>";
						$reg = mysqli_fetch_array($sql->query("select regiao from regioes where cod_reg = ".$cli['regiao']." and ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']));
						echo "<td><div style=\"width: 150px; height: 17px; overflow: auto;\">".$reg['regiao']."</div></td>";
						$rep = mysqli_fetch_array($sql->query("select nome from representantes where cod_rep = ".$cli['representante']." and ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']));
						echo "<td><div style=\"width: 300px; height: 17px; overflow: auto;\">".$rep['nome']."</div></td>";
						echo "<td><div style=\"width: 400px; height: 17px; overflow: auto;\">".$cli['observacoes']."</div></td>";
						if ($cli['cod_cli']!="") {
							$link = "<a href=\"#\" onclick=\"abrirPopup('cadCli.php?cod_cli=".$cli['cod_cli']."',400,300);\"><img src=\"images/editar.png\"></a>";
						} else {
							$link = "";
						}
						echo "<td style=\"border: none;\">$link</td>";
					echo "</tr>";
				}
				echo "<tr><td colspan=\"5\">";
					echo "<center>";
						if ($quantreg!=0) include("paginacao.php");
					echo "</center>";
				echo "</td></tr>";
			echo "</table>";
		echo "</div>";
	
	//menu opções
	echo "<ul id=\"submenu\">";
			echo "<li><a href=\"#\" onclick=\"abrirPopup('cadCli.php',400,300);\">Cadastrar Cliente</a></li>";
	echo "</ul>";
	}

echo "</div>";
?>
</body>
</html>