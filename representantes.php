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
		//formação das divs com os representantes de cada empresa
		$empesq = $sql->query("select id_empresa, nome, cnpj from empresas group by cnpj order by nome");
		for ($i=1;mysqli_num_rows($empesq)>=$i;$i++) {
			$emp = mysqli_fetch_array($empesq);
			if ($emp['nome']!="Todas") {
				echo "<div id=\"".$emp['cnpj']."\" style=\"display: none;\">";
					echo "<table class=\"tabelas2\">";
						echo "<tr>";
							echo "<td><b><center>COD</center></b></td>";
							echo "<td><b><center>NOME</center></b></td>";
							echo "<td><b><center>OBSERVAÇÕES</center></b></td>";
						echo "</tr>";
						$unipesq = $sql->query("select cod_rep, nome, observacoes, empresa from representantes where empresa = ".$emp['id_empresa']." and ativo = 1 order by nome");
						for ($j=1;mysqli_num_rows($unipesq)>=$j;$j++) {
							$rep = mysqli_fetch_array($unipesq);
							echo "<tr class=\"destacaeditar\">";
								echo "<td width=50px>".$rep['cod_rep']."</td>";
								echo "<td><div style=\"width: 300px; height: 17px; overflow: auto;\">".$rep['nome']."</div></td>";
								echo "<td><div style=\"width: 200px; height: 17px; overflow: auto;\">".$rep['observacoes']."</div></td>";
								echo "<td style=\"border: none;\"><a href=\"#\" onclick=\"abrirPopup('cadRep.php?cod_rep=".$rep['cod_rep']."&emp=".$rep['empresa']."',650,300);\"><img src=\"images/editar.png\"></a></td>";
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
		//formação da div com as unidades da empresa
		echo "<div>";
			echo "<table class=\"tabelas2\">";
				echo "<tr>";
					echo "<td><b><center>COD</center></b></td>";
					echo "<td><b><center>NOME</center></b></td>";
					echo "<td><b><center>OBSERVAÇÕES</center></b></td>";
				echo "</tr>";
				$unipesq = $sql->query("select cod_rep, nome, observacoes from representantes where empresa = ".$_SESSION['UsuarioEmpresa']."  and ativo = 1 order by nome");
				for ($j=1;mysqli_num_rows($unipesq)>=$j;$j++) {
					$rep = mysqli_fetch_array($unipesq);
					echo "<tr class=\"destacaeditar\">";
						echo "<td width=50px>".$rep['cod_rep']."</td>";
						echo "<td><div style=\"width: 300px; height: 17px; overflow: auto;\">".$rep['nome']."</div></td>";
						echo "<td><div style=\"width: 200px; height: 17px; overflow: auto;\">".$rep['observacoes']."</div></td>";
						echo "<td style=\"border: none;\"><a href=\"#\" onclick=\"abrirPopup('cadRep.php?cod_rep=".$rep['cod_rep']."',650,300);\"><img src=\"images/editar.png\"></a></td>";
					echo "</tr>";
				}
			echo "</table>";
		echo "</div>";
	}

//menu opções
echo "<ul id=\"submenu\">";
		echo "<li><a href=\"#\" onclick=\"abrirPopup('cadRep.php',650,300);\">Cadastrar Representante</a></li>";
		// if ($nomeEmpresa[0] != "Todas") echo "<li><a href=\"#\" onclick=\"return geraRelRep();\">Gerar Relatório de Representantes</a></li>";
echo "</ul>";

echo "</div>";
?>
</body>
<script>
	function geraRelRep() {
		
	}
</script>
</html>