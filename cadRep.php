<?
$popup = 1;
require_once("session.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
	//variaveis novas recebem o que foi postado no formulário
	if (!isset($_GET['cod_rep'])) {
		$pesqCod = $sql->query("select cod_rep from representantes where ativo = 1 and empresa = ".$sql->real_escape_string($_POST['empresa'])." order by cod_rep desc");
		if (mysqli_num_rows($pesqCod)==0) $cod_rep = 1;
		else {
			$ultimo_cod = mysqli_fetch_array($pesqCod);
			$cod_rep = $ultimo_cod['cod_rep'] + 1;
		}
	} else $cod_rep = $_GET['cod_rep'];
	$nome = $sql->real_escape_string($_POST['nome']);
	$comissao = $sql->real_escape_string(str_replace(",",".",$_POST['comissao']));
	if ($comissao!=0) {
		if (!is_numeric($comissao)) {echo "<script>alert('Comissão não permite símbolos especiais!'); window.close();</script>"; exit;}
	}
	$observacoes = $sql->real_escape_string($_POST['observacoes']);
	if ($_POST['ativo_venda']==1) $ativo_venda = 1; else $ativo_venda = 0;
	$usuario = $_SESSION['UsuarioID'];
	$empresa = $sql->real_escape_string($_POST['empresa']);
	$equipeVec = $_POST['equipe'];
	
			if (!empty($_POST) AND (empty($_POST['nome']))) {
				echo "<script>alert('Preencha todos os campos!')</script>";
			}
			else {
				if (!isset($_GET['cod_rep'])) {
					//verifica se unidade já foi cadastrada
					// $pesquisa = "select unidade from unidades where empresa = '".$empresa."' and unidade = '".$unidade."'";
					// $sqll = $sql->query($pesquisa);
					// $numCadastros = mysqli_num_rows($sqll);
					// if ($numCadastros>0) {
					// 	echo "<script>alert('A unidade informada já está cadastrada no sistema.')</script>";
					// }
					// else {
					// 	// Arquiva os dados no banco de dados
						$sqll = "INSERT INTO representantes (cod_rep, nome, comissao_padrao, observacoes, ativo_venda, data, usuario, empresa, ativo) VALUES ('$cod_rep','$nome','$comissao','$observacoes','$ativo_venda',now(),'$usuario','$empresa',1)";
						$sql->query($sqll) or die (mysqli_error($sql));
							// echo "<script>atualizaPagMae(); alert('Cadastro efetuado!'); window.close();</script>";
							echo "<script>atualizaPagMae(); alert('Cadastro efetuado!'); document.location.href = 'cadRep.php?cod_rep=$cod_rep';</script>";
							exit;
					// }
				}
				if (isset($_GET['cod_rep'])) {
					$alteracao = 0;
					$antigo = mysqli_fetch_array($sql->query("select nome, comissao_padrao, ativo_venda, observacoes from representantes where cod_rep = ".$_GET['cod_rep']." and ativo = 1 and empresa = $empresa"));
					$nomeAnt = $antigo['nome'];
					$comissaoAnt = $antigo['comissao_padrao'];
					$ativo_vendaAnt = $antigo['ativo_venda'];
					$observacoesAnt = $antigo['observacoes'];
					$pesqEquipeSqlAnt = $sql->query("select cod_equipe, dentro from representantes_equipes where ativo = 1 and empresa = $empresa and cod_rep = ".$_GET['cod_rep']) or die (mysqli_error($sql));
						if (mysqli_num_rows($pesqEquipeSqlAnt)>0) {
							for ($cr=0;$cr<mysqli_num_rows($pesqEquipeSqlAnt);$cr++) {
								$pesqEquipeAnt = mysqli_fetch_array($pesqEquipeSqlAnt);
								$posicaoAnt = $pesqEquipeAnt['cod_equipe'];
								if ($pesqEquipeAnt['dentro']!=0)$equipeVecAnt[$posicaoAnt] = $pesqEquipeAnt['dentro'];
							}
						}
						// if ($equipeVec!=$equipeVecAnt) echo "<script>alert('Diferente!');</script>";
					if (($nomeAnt!=$nome) or ($comissaoAnt!=$comissao) or ($ativo_vendaAnt!=$ativo_venda)  or ($observacoesAnt!=$observacoes)) {
						//altera anterior para ativo = 0
						$sql->query("update representantes set ativo = 0 where cod_rep = $cod_rep and empresa = $empresa") or die (mysqli_error($sql));
						$sql->query("INSERT INTO representantes (cod_rep, nome, comissao_padrao, observacoes, ativo_venda, data, usuario, empresa, ativo) VALUES ('$cod_rep','$nome','$comissao','$observacoes','$ativo_venda',now(),'$usuario','$empresa',1)") or die (mysqli_error($sql));
						$alteracao = $alteracao + 1;
					}
					if ($equipeVec!=$equipeVecAnt) {
						//altera anterior para ativo = 0
						$quantEquipesPesq = $sql->query("select cod_equipe from equipes_de_venda where empresa = $empresa order by cod_equipe desc") or die (mysqli_error($sql));
						if (mysqli_num_rows($quantEquipesPesq)>0) {
							$quantEquipes = mysqli_fetch_array($quantEquipesPesq);
							for ($i=1;$i<=$quantEquipes['cod_equipe'];$i++){
								if ($equipeVecAnt[$i]!=$equipeVec[$i]) {
									// echo "<script>alert('".$equipeVecAnt[$i]."-".$equipeVec[$i]."')</script>";
									if ($equipeVec[$i]=="" or $equipeVec[$i]==0) $dentro = 0; else $dentro = 1;
									$sql->query("update representantes_equipes set ativo = 0 where ativo = 1 and empresa = $empresa and cod_equipe = $i and cod_rep = ".$_GET['cod_rep']) or die ($sql->query());
									$sql->query("insert into representantes_equipes (cod_rep, cod_equipe, dentro, empresa, data, usuario, ativo) values ('".$_GET['cod_rep']."','$i','$dentro','$empresa',now(),'$usuario','1')") or die ($sql->query());
								}
							}
						}
						$alteracao = $alteracao + 1;
					}
					if ($alteracao>0) {
						if (isset($_GET['emp'])) echo "<script>atualizaPagMae(); alert('Alteração efetuada!'); location.href= 'cadRep.php?cod_rep=".$_GET['cod_rep']."&emp=$empresa';</script>";
						else echo "<script>atualizaPagMae(); alert('Alteração efetuada!'); location.href= 'cadRep.php?cod_rep=".$_GET['cod_rep']."';</script>";
						exit;
					} else echo "<script>alert('Nenhuma alteração foi detectada!')</script>";
				}
			}
}
else if (isset($_GET['cod_rep']) and $_SERVER['REQUEST_METHOD'] != "POST") {
	if (!isset($_GET['emp']))
		$var = mysqli_fetch_array($sql->query("select nome, comissao_padrao, ativo_venda, empresa, observacoes from representantes where ativo = 1 and cod_rep = ".$_GET['cod_rep']." and empresa = ".$_SESSION['UsuarioEmpresa']));
	else
		$var = mysqli_fetch_array($sql->query("select nome, comissao_padrao, ativo_venda, empresa, observacoes from representantes where ativo = 1 and cod_rep = ".$_GET['cod_rep']." and empresa = ".$_GET['emp']));
	$nome = $var['nome'];
	$comissao = $var['comissao_padrao'];
	$ativo_venda = $var['ativo_venda'];
	$empresa = $var['empresa'];
	$observacoes = $var['observacoes'];
	$pesqEquipeSql = $sql->query("select cod_equipe, dentro from representantes_equipes where ativo = 1 and empresa = $empresa and cod_rep = ".$_GET['cod_rep']) or die (mysqli_error($sql));
	if (mysqli_num_rows($pesqEquipeSql)>0) {
		for ($cr=0;$cr<mysqli_num_rows($pesqEquipeSql);$cr++) {
			$pesqEquipe = mysqli_fetch_array($pesqEquipeSql);
			$posicao = $pesqEquipe['cod_equipe'];
			$equipeVec[$posicao] = $pesqEquipe['dentro'];
		}
	} else $equipeVec = "";
}
else {
	$nome = "";
	$comissao = "";
	$ativo_venda = 1;
	$empresa = "";
	$equipeVec = "";
}
?>
<html>
<head>
<title><?echo $title." - Cadastro de Representantes";?></title>
</head>
<body onLoad="document.getElementById('txNome').focus();">
<?
//validação página
autorizaPagina(2);
teclaEsc();
?>

	<?if (isset($_GET['cod_rep'])) {
		if (!isset($_GET['emp'])) echo "<form action=\"cadRep.php?cod_rep=".$_GET['cod_rep']."\" method=\"post\">";
		else echo "<form action=\"cadRep.php?cod_rep=".$_GET['cod_rep']."&emp=".$_GET['emp']."\" method=\"post\">";
	}
	else echo "<form action=\"cadRep.php\" method=\"post\">";?>
	<fieldset>
		<?if (isset($_GET['cod_rep'])) echo "<legend>Edição de Representantes</legend>";
		else echo "<legend>Cadastro de Representantes</legend>";?>
		<div style="position: relative; float: left; width: 325px;">
		<table><tr><td><table>
			<tr><td><label for="txNome">Nome: </label></td>
				<td><input type="text" name="nome" id="txNome" maxlength="40" size="26" value="<?echo $nome;?>"/></td></tr>
				
			<tr><td><label for="txComissao">Comissão Padrão: </label></td>
				<td><input type="text" name="comissao" id="txComissao" maxlength="5" size="5" value="<?echo $comissao;?>"/> %
					<label for="txAtivo"> | Ativo: </label>
					<input type="checkbox" name="ativo_venda" id="txAtivo" value="1" <?if ($ativo_venda==1) echo "checked";?>/></td></tr>
				
			<tr><td><label for="txEmpresa">Empresa: </label></td>
				<td>
				<?
					$sysLista = mysqli_fetch_row($sql->query("SELECT empresa FROM usuarios WHERE id = ".$_SESSION['UsuarioID']));
					$nomeEmpresa = mysqli_fetch_array($sql->query("SELECT nome FROM empresas WHERE id_empresa = ".$sysLista[0]));
					if ($nomeEmpresa['nome']=="Todas" and !isset($_GET['emp'])) {
						echo "<select id=\"txEmpresa\" name=\"empresa\" style=\"max-width: 185px;\">\n";
						$pesqEmp = $sql->query("select id_empresa, nome, cnpj from empresas where nome != 'Todas'");
						for ($i=1;mysqli_num_rows($pesqEmp)>=$i;$i++) {
							$empList = mysqli_fetch_array($pesqEmp);
							if ($empresa==$empList['id_empresa'])
								echo "<option value=\"".$empList['id_empresa']."\" selected=\"selected\">".$empList['nome']." - ".$empList['cnpj']."</option>\n";
							else
								echo "<option value=\"".$empList['id_empresa']."\">".$empList['nome']." - ".$empList['cnpj']."</option>\n";
						}
						echo "</select>\n";
					} else {
						if (!isset($_GET['emp'])){
							echo $nomeEmpresa['nome']."\n";
							echo "<input type=\"hidden\" name=\"empresa\" value=\"".$_SESSION['UsuarioEmpresa']."\">\n";
						} else {
							$empNome = mysqli_fetch_array($sql->query("select nome from empresas where id_empresa = ".$_GET['emp']));
							echo $empNome['nome']."\n";
							echo "<input type=\"hidden\" name=\"empresa\" value=\"".$_GET['emp']."\">\n";
						}
					}
				?>
				</td></tr></table></td></tr>
			
			<tr><td>
				<label for="txComissGrupo">Comissões por Grupos de Produtos: </label>
				<div style="width: 305px; height: 55px; overflow-y: scroll;" class="divquantidade" id="txComissGrupo"><table class="tablecomposicao" style="font-size: 11px;" border="0px" cellspacing="0px" bgcolor="#fffff">
					<?
					$grupoPesq = $sql->query("select cod_grupo_prod, grupo from grupos_de_produto where ativo = 1 and empresa = '$empresa'");
					for ($i=0;$i<mysqli_num_rows($grupoPesq);$i++) {
						$grupo = mysqli_fetch_array($grupoPesq);
						$cod_grupo = $grupo['cod_grupo_prod'];
						if ($nomeEmpresa['nome']=="Todas" and isset($_GET['emp']))
							echo "<tr class=\"linhaLink\" onclick=\"abrirPopup('cadComissPorGrupo.php?cod_rep=".$_GET['cod_rep']."&emp=$empresa&cod_grupo=$cod_grupo',270,100);\">";
						else echo "<tr class=\"linhaLink\" onclick=\"abrirPopup('cadComissPorGrupo.php?cod_rep=".$_GET['cod_rep']."&cod_grupo=$cod_grupo',270,100);\">";
						echo "<td><div style=\"width: 220px; overflow: auto;\">".$grupo['grupo']."</div></td>";
						$comissPesq = $sql->query("select comiss from representantes_grupos where cod_rep = '".$_GET['cod_rep']."' and ativo = 1 and empresa = '$empresa' and cod_grupo_prod = '$cod_grupo'") or die (mysqli_error($sql));
						if (mysqli_num_rows($comissPesq)>0) {
							$comissGrupo = mysqli_fetch_array($comissPesq);
							$resultado = number_format($comissGrupo['comiss'],4,",","")."%";
						} else $resultado = "";
						echo "<td><div align=\"right\" style=\"width: 60px; overflow: auto;\">".$resultado."</div></td>";
						echo "</tr>";
					}
					?>
				</table></div>
			</td></tr>
			<tr><td><label for="txObservacoes">Observações: </label></td></tr>
			<tr><td><textarea name="observacoes" id="txObservacoes" cols="36" rows="1"><?echo $observacoes;?></textarea></td></tr>
			
			<?if (!isset($_GET['cod_rep'])) echo "<tr><td><center><input type=\"submit\" value=\"Salvar\"/></center></td></tr>";
			else echo "<tr><td><center><input type=\"submit\" value=\"Alterar\"/></center></td></tr>";?>
		</table>
	</div>
	<div style="position: relative; float: left; width: 10px;">
		<img src="images/separacao.png" height="250" width="4">
	</div>
	<div style="position: relative; float: left; width: 305px;">
		<div style="float: left;"><label for="txEquipes">Equipes de Venda: </label></div><div style="float: right;"><img src="images/ico_add.png" onclick="abrirPopup('cadEquipe.php',400,130);" style="cursor: pointer;"></div>
		<div style="width: 302px; height: 90px; overflow-y: scroll;" class="divquantidade" id="txEquipes">
			<table class="tablecomposicao" style="font-size: 11px;" border="0px" cellspacing="0px" bgcolor="#fffff">
				<?
				$equipePesq = $sql->query("select cod_equipe, equipe from equipes_de_venda where ativo = 1 and empresa = '$empresa' order by equipe");
				for ($i=0;$i<mysqli_num_rows($equipePesq);$i++) {
					$equipe = mysqli_fetch_array($equipePesq);
					$cod_equipe = $equipe['cod_equipe'];
					if ($nomeEmpresa['nome']=="Todas" and isset($_GET['emp']))
						$linkEquipe = " onclick=\"abrirPopup('cadEquipe.php?emp=$empresa&cod_equipe=$cod_equipe',400,130);\"";
					else $linkEquipe = " onclick=\"abrirPopup('cadEquipe.php?cod_equipe=$cod_equipe',400,130);\"";
					echo "<tr class=\"linhaLink\">";
						if ($equipeVec[$equipe['cod_equipe']]==1) $equipeCheck = " checked"; else $equipeCheck = "";
						// echo "<td><input type=\"checkbox\" name=\"equipe[".$equipe['cod_equipe']."]\" id=\"txEquipes\" value=\"".$equipe['cod_equipe']."\" style=\"margin: 0px;\"$equipeCheck/></td>";
						echo "<td><input type=\"checkbox\" name=\"equipe[".$equipe['cod_equipe']."]\" id=\"txEquipes\" value=\"1\" style=\"margin: 0px;\"$equipeCheck/></td>";
						echo "<td$linkEquipe><div style=\"width: 264px; overflow: auto;\">".$equipe['equipe']."</div></td>";
					echo "</tr>";
				}
				?>
			</table>
		</div>
		<label for="txClientes">Clientes Vinculados: </label>
		<div style="width: 302px; height: 100px; overflow-y: scroll;" class="divquantidade" id="txClientes">
			<table class="tablecomposicao" style="font-size: 11px;" border="0px" cellspacing="0px" bgcolor="#fffff">
				<?
				$clientePesq = $sql->query("select cod_cli, cliente from clientes where ativo = 1 and empresa = '$empresa' and representante = '".$_GET['cod_rep']."' order by cliente");
				for ($i=0;$i<mysqli_num_rows($clientePesq);$i++) {
					$cliente = mysqli_fetch_array($clientePesq);
					$cod_cli = $cliente['cod_cli'];
					if ($nomeEmpresa['nome']=="Todas" and isset($_GET['emp']))
						echo "<tr class=\"linhaLink\" onclick=\"abrirPopup('cadCli.php?emp=$empresa&cod_cli=$cod_cli',400,300);\">";
					else echo "<tr class=\"linhaLink\" onclick=\"abrirPopup('cadCli.php?cod_cli=$cod_cli',400,300);\">";
					echo "<td><div style=\"width: 280px; overflow: auto;\">".$cliente['cliente']."</div></td>";
					echo "</tr>";
				}
				?>
			</table>
		</div>
	</div>
	</fieldset>
</form>

</body>
</html>