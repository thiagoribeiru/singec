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
		$empesq = $sql->query("select id_empresa, nome, cnpj from empresas group by cnpj order by nome");
		echo "<form>";
			echo "<label for=\"empresas\"><b>Selecione a empresa: </b></label>";
			echo "<select name=\"empresas\" id=\"empresas\">";
			for ($i=1;mysqli_num_rows($empesq)>=$i;$i++) {
				$emp = mysqli_fetch_array($empesq);
				if ($emp['nome']!="Todas") {
					if ($_GET['emp']==$emp['id_empresa']) echo "<option value=\"".$emp['cnpj']."\" selected=\"selected\">".$emp['nome']." - ".$emp['cnpj']."</option>";
					else echo "<option value=\"".$emp['cnpj']."\">".$emp['nome']." - ".$emp['cnpj']."</option>";
				}
			}
			echo "</select>";
		echo "</form>";
		//formação das divs com as unidades de cada empresa
		$empesq = $sql->query("select id_empresa, nome, cnpj from empresas group by cnpj order by nome");
		for ($i=1;mysqli_num_rows($empesq)>=$i;$i++) {
			$emp = mysqli_fetch_array($empesq);
			if ($emp['nome']!="Todas") {
				if ($_GET['emp']==$emp['id_empresa']) echo "<div id=\"".$emp['cnpj']."\" style=\"display: block; width: 700px;\">";
				else if (!isset($_GET['emp']) and $i==1) echo "<div id=\"".$emp['cnpj']."\" style=\"display: block; width: 700px;\">";
				else echo "<div id=\"".$emp['cnpj']."\" style=\"display: none; width: 700px;\">";
					if ($_SERVER['REQUEST_METHOD'] == "POST" and $_GET['emp']==$emp['id_empresa']) {
						if ($_POST['nome']=="Todas") {
							echo "<script>alert('Parâmetro reservado não permitido! Por questões de segurança, a operação será cancelada.'); window.close();</script>";
						} else {
							//variaveis novas recebem o que foi postado no formulário
							//tabela empresas
							$nome = $sql->real_escape_string($_POST['nome']);
							$sinais = array(".", ",", "/", "-", " ");
							$cnpj = str_replace($sinais,"",$sql->real_escape_string($_POST['cnpj']));
							$moeda = $_POST['moeda'];
							//tabela outros_impostos
							$impostos = str_replace(",",".",$sql->real_escape_string($_POST['impostos']));
							//tabela custo_fixo
							$custofixo = str_replace(",",".",$sql->real_escape_string($_POST['custofixo']));
							//tabela margem_fixa
							$margemfixa = str_replace(",",".",$sql->real_escape_string($_POST['margemfixa']));
							//tabela imposto de renda
							$imprenda = str_replace(",",".",$sql->real_escape_string($_POST['imprenda']));
							//tabela meta lucro
							$metalucro = str_replace(",",".",$sql->real_escape_string($_POST['metalucro']));
							
							if(is_numeric($cnpj) and is_numeric($impostos) and is_numeric($imprenda) and is_numeric($metalucro) and is_numeric($custofixo) and is_numeric($margemfixa)) {
								if ((strlen($cnpj)==11) or (strlen($cnpj)==14)) {
									$antigo = mysqli_fetch_array($sql->query("select nome, cnpj, moeda from empresas where id_empresa = ".$emp['id_empresa']));
									$nomeAnt = $antigo['nome'];
									$cnpjAnt = $antigo['cnpj'];
									$moedaAnt = $antigo['moeda'];
									$antigo2 = mysqli_fetch_array($sql->query("select percentual from outros_impostos where ativo = 1 and empresa = ".$emp['id_empresa']));
									$impostosAnt = $antigo2['percentual'];
									$antigo3 = mysqli_fetch_array($sql->query("select percentual from custo_fixo where ativo = 1 and empresa = ".$emp['id_empresa']));
									$custofixoAnt = $antigo3['percentual'];
									$antigo4 = mysqli_fetch_array($sql->query("select percentual from margem_fixa where ativo = 1 and empresa = ".$emp['id_empresa']));
									$margemfixaAnt = $antigo4['percentual'];
									$antigo5 = mysqli_fetch_array($sql->query("select percentual from imposto_de_renda where ativo = 1 and empresa = ".$emp['id_empresa']));
									$imprendaAnt = $antigo5['percentual'];
									$antigo6 = mysqli_fetch_array($sql->query("select percentual from meta_lucro where ativo = 1 and empresa = ".$emp['id_empresa']));
									$metalucroAnt = $antigo6['percentual'];
									$alteracoes = 0;
									if (($nomeAnt!=$nome) or ($cnpjAnt!=$cnpj) or ($moedaAnt!=$moeda)) {
										$sql->query("UPDATE empresas SET cnpj='".$cnpj."',nome='".$nome."',moeda='".$moeda."' WHERE id_empresa = ".$emp['id_empresa']);
										if (!isset($_SESSION)) session_start();
										$moedaUser = mysqli_fetch_array($sql->query("select moeda from empresas where id_empresa = '".$emp['id_empresa']."'")) or die(mysqli_error($sql));
				  						$_SESSION['MoedaIso'] = $moedaUser['moeda'];
				  						$sinal = mysqli_fetch_array($sql->query("select moeda from moedas where iso = '".$moedaUser['moeda']."' and ativo = 1")) or die(mysqli_error($sql));
				  						$_SESSION['MoedaSinal'] = $sinal['moeda'];
				  						atualizaCookieValor();
										// echo "<script>atualizaPagMae(); alert('Alteração efetuada!'); window.close();</script>";
										$alteracoes = $alteracoes + 1;
									}
									if ($impostosAnt!=$impostos) {
										$sql->query("UPDATE outros_impostos SET ativo = 0 WHERE empresa = ".$emp['id_empresa']) or die (mysqli_error($sql));
										$sql->query("insert into outros_impostos (percentual,data,usuario,empresa,ativo) values ('$impostos',now(),'".$_SESSION['UsuarioID']."','".$emp['id_empresa']."',1)") or die (mysqli_error($sql));
										$alteracoes = $alteracoes + 1;
									}
									if ($imprendaAnt!=$imprenda) {
										$sql->query("UPDATE imposto_de_renda SET ativo = 0 WHERE empresa = ".$emp['id_empresa']) or die (mysqli_error($sql));
										$sql->query("insert into imposto_de_renda (percentual,data,usuario,empresa,ativo) values ('$imprenda',now(),'".$_SESSION['UsuarioID']."','".$emp['id_empresa']."',1)") or die (mysqli_error($sql));
										$alteracoes = $alteracoes + 1;
									}
									if ($metalucroAnt!=$metalucro) {
										$sql->query("UPDATE meta_lucro SET ativo = 0 WHERE empresa = ".$emp['id_empresa']) or die (mysqli_error($sql));
										$sql->query("insert into meta_lucro (percentual,data,usuario,empresa,ativo) values ('$metalucro',now(),'".$_SESSION['UsuarioID']."','".$emp['id_empresa']."',1)") or die (mysqli_error($sql));
										$alteracoes = $alteracoes + 1;
									}
									if ($custofixoAnt!=$custofixo) {
										$sql->query("UPDATE custo_fixo SET ativo = 0 WHERE empresa = ".$emp['id_empresa']) or die (mysqli_error($sql));
										$sql->query("insert into custo_fixo (percentual,data,usuario,empresa,ativo) values ('$custofixo',now(),'".$_SESSION['UsuarioID']."','".$emp['id_empresa']."',1)") or die (mysqli_error($sql));
										$alteracoes = $alteracoes + 1;
									}
									if ($margemfixaAnt!=$margemfixa) {
										$sql->query("UPDATE margem_fixa SET ativo = 0 WHERE empresa = ".$emp['id_empresa']) or die (mysqli_error($sql));
										$sql->query("insert into margem_fixa (percentual,data,usuario,empresa,ativo) values ('$margemfixa',now(),'".$_SESSION['UsuarioID']."','".$emp['id_empresa']."',1)") or die (mysqli_error($sql));
										$alteracoes = $alteracoes + 1;
									}
									if ($alteracoes>0) echo "<script>alert('Alteração efetuada!');</script>";
									else echo "<script>alert('Nenhuma alteração foi detectada!')</script>";
								} else echo "<script>alert('CNPJ ou CPF inválidos!'); window.location.href = \"".$_SERVER['PHP_SELF']."?emp=".$emp['id_empresa']."\";</script>";
							} else echo "<script>alert('Apenas números!')</script>";
						}
					} else {
						$empresa = mysqli_fetch_array($sql->query("SELECT id_empresa, cnpj, nome, moeda FROM empresas WHERE id_empresa = ".$emp['id_empresa']." order by nome")) or die (mysqli_error($sql));
						$id_empresa = $emp['id_empresa'];
						$cnpj = $empresa['cnpj'];
						$nome = $empresa['nome'];
						$moeda = $empresa['moeda'];
						$sqlOutrosImpostos = $sql->query("select percentual from outros_impostos where ativo = 1 and empresa = $id_empresa") or die (mysqli_error($sql));
						$outrosImpostos = mysqli_fetch_array($sqlOutrosImpostos);
						if (mysqli_num_rows($sqlOutrosImpostos)>0) $impostos = $outrosImpostos['percentual']; else $impostos = 0;
						$sqlImpRenda = $sql->query("select percentual from imposto_de_renda where ativo = 1 and empresa = $id_empresa") or die (mysqli_error($sql));
						$impRendaVar = mysqli_fetch_array($sqlImpRenda);
						if (mysqli_num_rows($sqlImpRenda)>0) $imprenda = $impRendaVar['percentual']; else $imprenda = 0;
						$sqlMetaLucro = $sql->query("select percentual from meta_lucro where ativo = 1 and empresa = $id_empresa") or die (mysqli_error($sql));
						$metaLucroVar = mysqli_fetch_array($sqlMetaLucro);
						if (mysqli_num_rows($sqlMetaLucro)>0) $metalucro = $metaLucroVar['percentual']; else $metalucro = 0;
						$sqlCusto_fixo = $sql->query("select percentual from custo_fixo where ativo = 1 and empresa = $id_empresa") or die (mysqli_error($sql));
						$custo_fixo = mysqli_fetch_array($sqlCusto_fixo);
						if (mysqli_num_rows($sqlCusto_fixo)>0) $custofixo = $custo_fixo['percentual']; else $custofixo = 0;
						$sqlMargem_fixa = $sql->query("select percentual from margem_fixa where ativo = 1 and empresa = $id_empresa") or die (mysqli_error($sql));
						$margem_fixa = mysqli_fetch_array($sqlMargem_fixa);
						if (mysqli_num_rows($sqlMargem_fixa)>0) $margemfixa = $margem_fixa['percentual']; else $margemfixa = 0;
					}
					echo "<form action=\"".$_SERVER['PHP_SELF']."?emp=".$emp['id_empresa']."\" method=\"post\">\n";
						echo "<fieldset><legend><b>Dados da Empresa</b></legend>\n";
							echo "<table>\n";
								echo "<tr>\n";
									if (strlen($cnpj)==14) echo "<td width=\"170px\"><label for=\"txnome\">- Nome Fantasia:</label></td>\n";
									if (strlen($cnpj)==11) echo "<td width=\"170px\"><label for=\"txnome\">- Nome Completo:</label></td>\n";
									echo "<td><input type=\"text\" name=\"nome\" id=\"txnome\" size=\"50\" maxlength=\"30\" value=\"$nome\"/></td>\n";
								echo "</tr><tr>\n";
									if (strlen($cnpj)==14)	{
										echo "<td><label for=\"txcnpj\">- CNPJ:</label></td>\n";
										echo "<td><input type=\"text\" name=\"cnpj\" id=\"txcnpj\" size=\"50\" maxlength=\"14\" value=\"".mask($cnpj,'##.###.###/####-##')."\"/></td>\n";
									}
									if (strlen($cnpj)==11)	{
										echo "<td><label for=\"txcnpj\">- CPF:</label></td>\n";
										echo "<td><input type=\"text\" name=\"cnpj\" id=\"txcnpj\" size=\"50\" maxlength=\"14\" value=\"".mask($cnpj,'###.###.###/##')."\"/></td>\n";
									}
								echo "</tr>\n";
							echo "</table>\n";
						echo "</fieldset><br>\n";
						echo "<fieldset><legend><b>Financeiro</b></legend>\n";
							echo "<table>\n";
								echo "<tr>\n";
									echo "<td width=\"170px\"><label for=\"txmoeda\">- Moeda Padrão:</label></td>\n";
									echo "<td><select id=\"txmoeda\" name=\"moeda\">\n";
										$moedasList = $sql->query("select iso, moeda from moedas where ativo = 1 order by iso") or die (mysqli_error($sql));
										for ($j=1;$j<=mysqli_num_rows($moedasList);$j++) {
											$moedas = mysqli_fetch_array($moedasList);
											$iso = $moedas['iso'];
											$simbolo = $moedas['moeda'];
											if ($moeda==$iso) {
												echo "<option value=\"$iso\" selected=\"selected\">($iso) $simbolo</option>\n";
											} else {
												echo "<option value=\"$iso\">($iso) $simbolo</option>\n";
											}
										}
									echo "</select></td>\n";
								echo "</tr>\n";
							echo "</table>\n";
						echo "</fieldset><br>\n";
						echo "<fieldset><legend><b>Fiscal</b></legend>\n";
							echo "<table>\n";
								echo "<tr>\n";
									echo "<td width=\"170px\"><label for=\"tximpostos\">- Outros Impostos:</label></td>\n";
									echo "<td><input type=\"text\" name=\"impostos\" id=\"tximpostos\" size=\"10\" maxlength=\"10\" value=\"".number_format($impostos,"4",",",".")."\" style=\"text-align: right;\"/> %</td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
									echo "<td width=\"170px\"><label for=\"tximprenda\">- I.R. sobre lucro:</label></td>\n";
									echo "<td><input type=\"text\" name=\"imprenda\" id=\"tximprenda\" size=\"10\" maxlength=\"10\" value=\"".number_format($imprenda,"4",",",".")."\" style=\"text-align: right;\"/> %</td>\n";
								echo "</tr>\n";
							echo "</table>\n";
						echo "</fieldset><br>\n";
						echo "<fieldset><legend><b>Custos</b></legend>\n";
							echo "<table>\n";
								echo "<tr>\n";
									echo "<td width=\"170px\"><labelfor=\"txcustofixo\">- Custo Fixo:</label></td>\n";
									echo "<td><input type=\"text\" name=\"custofixo\" id=\"txcustofixo\" size=\"10\" maxlength=\"10\" value=\"".number_format($custofixo,"4",",",".")."\" style=\"text-align: right;\"/> %</td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
									echo "<td><label for=\"txmargemfixa\">- Custo de Operação:</label></td>\n";
									echo "<td><input type=\"text\" name=\"margemfixa\" id=\"txmargemfixa\" size=\"10\" maxlength=\"10\" value=\"".number_format($margemfixa,"4",",",".")."\" style=\"text-align: right;\"/> %</td>\n";
								echo "</tr>\n";
								echo "<tr>\n";
									echo "<td><label for=\"txmetalucro\">- Meta de Lucratividade:</label></td>\n";
									echo "<td><input type=\"text\" name=\"metalucro\" id=\"txmetalucro\" size=\"10\" maxlength=\"10\" value=\"".number_format($metalucro,"4",",",".")."\" style=\"text-align: right;\"/> %</td>\n";
								echo "</tr>\n";
							echo "</table>\n";
						echo "</fieldset><br>\n";
						echo "<center><input type=\"submit\" value=\"Salvar Alterações\"/></center>";
					echo "</form>\n";
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
		//formação da div individual
		echo "<div id=\"".$emp['cnpj']."\" style=\"width: 700px;\">\n";
			if ($_SERVER['REQUEST_METHOD'] == "POST") {
				if ($_POST['nome']=="Todas") {
					echo "<script>alert('Parâmetro reservado não permitido! Por questões de segurança, a operação será cancelada.'); window.close();</script>";
				} else {
					//variaveis novas recebem o que foi postado no formulário
					//tabela empresas
					$nome = $sql->real_escape_string($_POST['nome']);
					$sinais = array(".", ",", "/", "-", " ");
					$cnpj = str_replace($sinais,"",$sql->real_escape_string($_POST['cnpj']));
					$moeda = $_POST['moeda'];
					//tabela outros_impostos
					$impostos = str_replace(",",".",$sql->real_escape_string($_POST['impostos']));
					//tabela custo_fixo
					$custofixo = str_replace(",",".",$sql->real_escape_string($_POST['custofixo']));
					//tabela margem_fixa
					$margemfixa = str_replace(",",".",$sql->real_escape_string($_POST['margemfixa']));
					//tabela imposto de renda
					$imprenda = str_replace(",",".",$sql->real_escape_string($_POST['imprenda']));
					//tabela meta de lucro
					$metalucro = str_replace(",",".",$sql->real_escape_string($_POST['metalucro']));
					
					if(is_numeric($cnpj) and is_numeric($impostos) and is_numeric($imprenda) and is_numeric($metalucro) and is_numeric($custofixo) and is_numeric($margemfixa)) {
						if ((strlen($cnpj)==11) or (strlen($cnpj)==14)) {
							$antigo = mysqli_fetch_array($sql->query("select nome, cnpj, moeda from empresas where id_empresa = ".$_SESSION['UsuarioEmpresa']));
							$nomeAnt = $antigo['nome'];
							$cnpjAnt = $antigo['cnpj'];
							$moedaAnt = $antigo['moeda'];
							$antigo2 = mysqli_fetch_array($sql->query("select percentual from outros_impostos where ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']));
							$impostosAnt = $antigo2['percentual'];
							$antigo3 = mysqli_fetch_array($sql->query("select percentual from custo_fixo where ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']));
							$custofixoAnt = $antigo3['percentual'];
							$antigo4 = mysqli_fetch_array($sql->query("select percentual from margem_fixa where ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']));
							$margemfixaAnt = $antigo4['percentual'];
							$antigo5 = mysqli_fetch_array($sql->query("select percentual from imposto_de_renda where ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']));
							$imprendaAnt = $antigo5['percentual'];
							$antigo6 = mysqli_fetch_array($sql->query("select percentual from meta_lucro where ativo = 1 and empresa = ".$_SESSION['UsuarioEmpresa']));
							$metalucroAnt = $antigo6['percentual'];
							$alteracoes = 0;
							if (($nomeAnt!=$nome) or ($cnpjAnt!=$cnpj) or ($moedaAnt!=$moeda)) {
								$sql->query("UPDATE empresas SET cnpj='".$cnpj."',nome='".$nome."',moeda='".$moeda."' WHERE id_empresa = ".$_SESSION['UsuarioEmpresa']);
								if (!isset($_SESSION)) session_start();
								$moedaUser = mysqli_fetch_array($sql->query("select moeda from empresas where id_empresa = '".$_SESSION['UsuarioEmpresa']."'")) or die(mysqli_error($sql));
		  						$_SESSION['MoedaIso'] = $moedaUser['moeda'];
		  						$sinal = mysqli_fetch_array($sql->query("select moeda from moedas where iso = '".$moedaUser['moeda']."' and ativo = 1")) or die(mysqli_error($sql));
		  						$_SESSION['MoedaSinal'] = $sinal['moeda'];
		  						atualizaCookieValor();
								// echo "<script>atualizaPagMae(); alert('Alteração efetuada!'); window.close();</script>";
								$alteracoes = $alteracoes + 1;
							}
							if ($impostosAnt!=$impostos) {
								$sql->query("UPDATE outros_impostos SET ativo = 0 WHERE empresa = ".$_SESSION['UsuarioEmpresa']) or die (mysqli_error($sql));
								$sql->query("insert into outros_impostos (percentual,data,usuario,empresa,ativo) values ('$impostos',now(),'".$_SESSION['UsuarioID']."','".$_SESSION['UsuarioEmpresa']."',1)") or die (mysqli_error($sql));
								$alteracoes = $alteracoes + 1;
							}
							if ($imprendaAnt!=$imprenda) {
								$sql->query("UPDATE imposto_de_renda SET ativo = 0 WHERE empresa = ".$_SESSION['UsuarioEmpresa']) or die (mysqli_error($sql));
								$sql->query("insert into imposto_de_renda (percentual,data,usuario,empresa,ativo) values ('$imprenda',now(),'".$_SESSION['UsuarioID']."','".$_SESSION['UsuarioEmpresa']."',1)") or die (mysqli_error($sql));
								$alteracoes = $alteracoes + 1;
							}
							if ($metalucroAnt!=$metalucro) {
								$sql->query("UPDATE meta_lucro SET ativo = 0 WHERE empresa = ".$_SESSION['UsuarioEmpresa']) or die (mysqli_error($sql));
								$sql->query("insert into meta_lucro (percentual,data,usuario,empresa,ativo) values ('$metalucro',now(),'".$_SESSION['UsuarioID']."','".$_SESSION['UsuarioEmpresa']."',1)") or die (mysqli_error($sql));
								$alteracoes = $alteracoes + 1;
							}
							if ($custofixoAnt!=$custofixo) {
								$sql->query("UPDATE custo_fixo SET ativo = 0 WHERE empresa = ".$_SESSION['UsuarioEmpresa']) or die (mysqli_error($sql));
								$sql->query("insert into custo_fixo (percentual,data,usuario,empresa,ativo) values ('$custofixo',now(),'".$_SESSION['UsuarioID']."','".$_SESSION['UsuarioEmpresa']."',1)") or die (mysqli_error($sql));
								$alteracoes = $alteracoes + 1;
							}
							if ($margemfixaAnt!=$margemfixa) {
								$sql->query("UPDATE margem_fixa SET ativo = 0 WHERE empresa = ".$_SESSION['UsuarioEmpresa']) or die (mysqli_error($sql));
								$sql->query("insert into margem_fixa (percentual,data,usuario,empresa,ativo) values ('$margemfixa',now(),'".$_SESSION['UsuarioID']."','".$_SESSION['UsuarioEmpresa']."',1)") or die (mysqli_error($sql));
								$alteracoes = $alteracoes + 1;
							}
							if ($alteracoes>0) echo "<script>alert('Alteração efetuada!');</script>";
							else echo "<script>alert('Nenhuma alteração foi detectada!')</script>";
						} else echo "<script>alert('CNPJ ou CPF inválidos!'); window.location.href = \"".$_SERVER['PHP_SELF']."\";</script>";
					} else echo "<script>alert('Apenas números!')</script>";
				}
			} else {
				$empresa = mysqli_fetch_array($sql->query("SELECT id_empresa, cnpj, nome, moeda FROM empresas WHERE id_empresa = ".$_SESSION['UsuarioEmpresa']." order by nome")) or die (mysqli_error($sql));
				$id_empresa = $empresa['id_empresa'];
				$cnpj = $empresa['cnpj'];
				$nome = $empresa['nome'];
				$moeda = $empresa['moeda'];
				$sqlOutrosImpostos = $sql->query("select percentual from outros_impostos where ativo = 1 and empresa = $id_empresa") or die (mysqli_error($sql));
				$outrosImpostos = mysqli_fetch_array($sqlOutrosImpostos);
				if (mysqli_num_rows($sqlOutrosImpostos)>0) $impostos = $outrosImpostos['percentual']; else $impostos = 0;
				$sqlImpRenda = $sql->query("select percentual from imposto_de_renda where ativo = 1 and empresa = $id_empresa") or die (mysqli_error($sql));
				$impRendaVar = mysqli_fetch_array($sqlImpRenda);
				if (mysqli_num_rows($sqlImpRenda)>0) $imprenda = $impRendaVar['percentual']; else $imprenda = 0;
				$sqlCusto_fixo = $sql->query("select percentual from custo_fixo where ativo = 1 and empresa = $id_empresa") or die (mysqli_error($sql));
				$sqlMetaLucro = $sql->query("select percentual from meta_lucro where ativo = 1 and empresa = $id_empresa") or die (mysqli_error($sql));
				$metaLucroVar = mysqli_fetch_array($sqlMetaLucro);
				if (mysqli_num_rows($sqlMetaLucro)>0) $metalucro = $metaLucroVar['percentual']; else $metalucro = 0;
				$sqlCusto_fixo = $sql->query("select percentual from custo_fixo where ativo = 1 and empresa = $id_empresa") or die (mysqli_error($sql));
				$custo_fixo = mysqli_fetch_array($sqlCusto_fixo);
				if (mysqli_num_rows($sqlCusto_fixo)>0) $custofixo = $custo_fixo['percentual']; else $custofixo = 0;
				$sqlMargem_fixa = $sql->query("select percentual from margem_fixa where ativo = 1 and empresa = $id_empresa") or die (mysqli_error($sql));
				$margem_fixa = mysqli_fetch_array($sqlMargem_fixa);
				if (mysqli_num_rows($sqlMargem_fixa)>0) $margemfixa = $margem_fixa['percentual']; else $margemfixa = 0;
			}
			echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"post\">\n";
				echo "<fieldset><legend><b>Dados da Empresa</b></legend>\n";
					echo "<table>\n";
						echo "<tr>\n";
							if (strlen($cnpj)==14) echo "<td width=\"170ox\"><label for=\"txnome\">- Nome Fantasia:</label></td>\n";
							if (strlen($cnpj)==11) echo "<td width=\"170px\"><label for=\"txnome\">- Nome Completo:</label></td>\n";
							echo "<td><input type=\"text\" name=\"nome\" id=\"txnome\" size=\"50\" maxlength=\"30\" value=\"$nome\"/></td>\n";
						echo "</tr><tr>\n";
							if (strlen($cnpj)==14)	{
								echo "<td><label for=\"txcnpj\">- CNPJ:</label></td>\n";
								echo "<td><input type=\"text\" name=\"cnpj\" id=\"txcnpj\" size=\"50\" maxlength=\"14\" value=\"".mask($cnpj,'##.###.###/####-##')."\"/></td>\n";
							}
							if (strlen($cnpj)==11)	{
								echo "<td><label for=\"txcnpj\">- CPF:</label></td>\n";
								echo "<td><input type=\"text\" name=\"cnpj\" id=\"txcnpj\" size=\"50\" maxlength=\"14\" value=\"".mask($cnpj,'###.###.###/##')."\"/></td>\n";
							}
						echo "</tr>\n";
					echo "</table>\n";
				echo "</fieldset><br>\n";
				echo "<fieldset><legend><b>Financeiro</b></legend>\n";
					echo "<table>\n";
						echo "<tr>\n";
							echo "<td width=\"170px\"><label for=\"txmoeda\">- Moeda Padrão:</label></td>\n";
							echo "<td><select id=\"txmoeda\" name=\"moeda\">\n";
								$moedasList = $sql->query("select iso, moeda from moedas where ativo = 1 order by iso") or die (mysqli_error($sql));
								for ($i=1;$i<=mysqli_num_rows($moedasList);$i++) {
									$moedas = mysqli_fetch_array($moedasList);
									$iso = $moedas['iso'];
									$simbolo = $moedas['moeda'];
									if ($moeda==$iso) {
										echo "<option value=\"$iso\" selected=\"selected\">($iso) $simbolo</option>\n";
									} else {
										echo "<option value=\"$iso\">($iso) $simbolo</option>\n";
									}
								}
							echo "</select></td>\n";
						echo "</tr>\n";
					echo "</table>\n";
				echo "</fieldset><br>\n";
				echo "<fieldset><legend><b>Fiscal</b></legend>\n";
					echo "<table>\n";
						echo "<tr>\n";
							echo "<td width=\"170px\"><label for=\"tximpostos\">- Outros Impostos:</label></td>\n";
							echo "<td><input type=\"text\" name=\"impostos\" id=\"tximpostos\" size=\"10\" maxlength=\"10\" value=\"".number_format($impostos,"4",",",".")."\" style=\"text-align: right;\"/> %</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
							echo "<td width=\"170px\"><label for=\"tximprenda\">- I.R. sobre lucro:</label></td>\n";
							echo "<td><input type=\"text\" name=\"imprenda\" id=\"tximprenda\" size=\"10\" maxlength=\"10\" value=\"".number_format($imprenda,"4",",",".")."\" style=\"text-align: right;\"/> %</td>\n";
						echo "</tr>\n";
					echo "</table>\n";
				echo "</fieldset><br>\n";
				echo "<fieldset><legend><b>Custos</b></legend>\n";
					echo "<table>\n";
						echo "<tr>\n";
							echo "<td width=\"170px\"><label for=\"txcustofixo\">- Custo Fixo:</label></td>\n";
							echo "<td><input type=\"text\" name=\"custofixo\" id=\"txcustofixo\" size=\"10\" maxlength=\"10\" value=\"".number_format($custofixo,"4",",",".")."\" style=\"text-align: right;\"/> %</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
							// echo "<td><label for=\"txmargemfixa\">- Custo de Operação:</label></td>\n";
							echo "<td><label for=\"txmargemfixa\">- Custo de Operação:</label></td>\n";
							echo "<td><input type=\"text\" name=\"margemfixa\" id=\"txmargemfixa\" size=\"10\" maxlength=\"10\" value=\"".number_format($margemfixa,"4",",",".")."\" style=\"text-align: right;\"/> %</td>\n";
						echo "</tr>\n";
						echo "<tr>\n";
							echo "<td><label for=\"txmetalucro\">- Meta de Lucratividade:</label></td>\n";
							echo "<td><input type=\"text\" name=\"metalucro\" id=\"txmetalucro\" size=\"10\" maxlength=\"10\" value=\"".number_format($metalucro,"4",",",".")."\" style=\"text-align: right;\"/> %</td>\n";
						echo "</tr>\n";
					echo "</table>\n";
				echo "</fieldset><br>\n";
				echo "<center><input type=\"submit\" value=\"Salvar Alterações\"/></center>";
			echo "</form>\n";
		echo "</div>\n";
	}

//menu opções
echo "<ul id=\"submenu\">";
		if ($nomeEmpresa[0] == "Todas")
		echo "<li><a href=\"#\" onclick=\"abrirPopup('cadEmp.php',400,250);\">Adicionar Empresas</a></li>";
echo "</ul>";

echo "</div>";
?>
</body>
</html>