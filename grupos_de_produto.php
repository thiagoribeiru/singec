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
echo "<div class=\"tabelacorpo\">";
	$sysPesquisa = "SELECT empresa FROM usuarios WHERE id = ".$_SESSION['UsuarioID'];
	$sysResult = $sql->query($sysPesquisa) or die (mysqli_error($sql));
	$sysLista = mysqli_fetch_row($sysResult);
	$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = $sysLista[0]"));
	
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
		//formação das divs com os grupos de cada empresa
		$empesq = $sql->query("select id_empresa, nome, cnpj from empresas group by cnpj order by nome");
		for ($i=1;mysqli_num_rows($empesq)>=$i;$i++) {
			$emp = mysqli_fetch_array($empesq);
			if ($emp['nome']!="Todas") {
				echo "<div id=\"".$emp['cnpj']."\" style=\"display: none;\">";
					echo "<table border=1px cellpadding=2px cellspacing=0px>";
						echo "<tr>";
							echo "<td><b><center>COD</center></b></td>";
							echo "<td><b><center>GRUPO</center></b></td>";
						echo "</tr>";
						$unipesq = $sql->query("select cod_grupo_prod, grupo, empresa from grupos_de_produto where empresa = ".$emp['id_empresa']." and ativo = 1 order by grupo");
						for ($j=1;mysqli_num_rows($unipesq)>=$j;$j++) {
							$reg = mysqli_fetch_array($unipesq);
							echo "<tr>";
								echo "<td>".$reg['cod_grupo_prod']."</td>";
								echo "<td>".$reg['grupo']."</td>";
								echo "<td><a href=\"#\" onclick=\"abrirPopup('cadGrupoProd.php?cod_grupo=".$reg['cod_grupo_prod']."&emp=".$reg['empresa']."',400,130);\">editar</a></td>";
							echo "</tr>";
						}
					echo "</table>";
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
		//formação da div com os grupos da empresa
		echo "<div>";
			echo "<table border=1px cellpadding=2px cellspacing=0px>";
				echo "<tr>";
					echo "<td><b><center>COD</center></b></td>";
					echo "<td><b><center>GRUPO</center></b></td>";
				echo "</tr>";
				$unipesq = $sql->query("select cod_grupo_prod, grupo from grupos_de_produto where empresa = ".$_SESSION['UsuarioEmpresa']."  and ativo = 1 order by grupo");
				for ($j=1;mysqli_num_rows($unipesq)>=$j;$j++) {
					$reg = mysqli_fetch_array($unipesq);
					echo "<tr>";
						echo "<td>".$reg['cod_grupo_prod']."</td>";
						echo "<td>".$reg['grupo']."</td>";
						echo "<td><a href=\"#\" onclick=\"abrirPopup('cadGrupoProd.php?cod_grupo=".$reg['cod_grupo_prod']."',400,130);\">editar</a></td>";
					echo "</tr>";
				}
			echo "</table>";
		echo "</div>";
	}

//menu opções
echo "<ul id=\"submenu\">";
		echo "<li><a href=\"#\" onclick=\"abrirPopup('cadGrupoProd.php',400,130);\">Cadastrar Novo Grupo de Produtos</a></li>";
echo "</ul>";

echo "</div>";
?>
</body>
</html>