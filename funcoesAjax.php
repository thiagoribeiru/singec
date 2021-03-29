<?
require_once("config.php");
$retorno['error'] = 1;
$retorno['mensagem'] = 'Não foi detectado nenhum envio. Favor verificar com o desenvolvedor do sistema.';

if (isset($_POST)) {
	if (isset($_POST['funcao'])) {
		//aqui comecam as funcoes...
		// termina-las sempre retornando erro 0
		if ($_POST['funcao']=='preencheEmailsAutomaticos') {
			$cnpj = $_POST['cnpj'];
			$idEmpresa = mysqli_fetch_array($sql->query("SELECT id_empresa as id FROM empresas where cnpj = '".$cnpj."'")) or die (mysqli_error($sql));
			$tabelaSql = $sql->query("SELECT * FROM emails_automaticos where ativo = '1' and empresa = '".$idEmpresa['id']."'") or die (mysqli_error($sql));
			if (mysqli_num_rows($tabelaSql)==1) {
				$tabela = mysqli_fetch_array($tabelaSql) or die (mysqli_error($sql));
				$retorno['retorno'] = true;
				$retorno['array'] = json_encode($tabela);
			} else {
				$retorno['retorno'] = false;
			}
			$retorno['error'] = 0;
		}
		if ($_POST['funcao']=='pegaIdEmpresa') {
			$cnpj = $_POST['cnpj'];
			$idEmpresa = mysqli_fetch_array($sql->query("SELECT id_empresa as id FROM empresas where cnpj = '".$cnpj."'")) or die (mysqli_error($sql));
			$retorno['id_empresa'] = $idEmpresa['id'];
			$retorno['error'] = 0;
		}
		if ($_POST['funcao']=='verificaProdAfect') {
			if ($_POST['mps'] == "mp") $colunaCod = "cod_mp";
			$produto = mysqli_fetch_array($sql->query("select * from ".$_POST['mps']." where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and $colunaCod = '".$_POST['prod']."'")) or die (mysqli_error($sql));
			$estado = $produto['estado'];
			if ($estado=='1') {
				$afetadosPesq = $sql->query("select cod_sp from sp_composicao where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and mps = '".$_POST['mps']."' and cod = '".$_POST['prod']."' group by cod_sp order by cod_sp") or die (mysqli_error($sql));
				if ($afetadosPesq->num_rows > 0) {
					$itensAfetados = "";
					for ($i=1;$i<=$afetadosPesq->num_rows;$i++) {
						$afetados = mysqli_fetch_array($afetadosPesq);
						$itensAfetados .= $afetados['cod_sp'];
						if ($i!=$afetadosPesq->num_rows) $itensAfetados .= ",";
					}
					$retorno['existe'] = true;
					$retorno['itensAfetados'] = urlencode($itensAfetados);
				} else {
					$retorno['existe'] = false;
				}
			}
			$retorno['error'] = 0;
		}
		if ($_POST['funcao']=='on_off') {
			if ($_POST['mps'] == "mp") $colunaCod = "cod_mp";
			$produto = mysqli_fetch_array($sql->query("select * from ".$_POST['mps']." where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and $colunaCod = '".$_POST['prod']."'")) or die (mysqli_error($sql));
			$indice = $produto['indice'];
			$estado = $produto['estado'];
			$sql->query("update ".$_POST['mps']." set ativo = '0' where indice = '$indice'") or die (mysqli_error($sql));
			if ($estado=='1') {
				$sql->query("insert into ".$_POST['mps']." ($colunaCod,descricao,moeda,custo,unidade,gramatura,largura,icms,piscofins,frete,quebra,observacoes,estado,empresa,data,usuario,ativo) values ('".$produto['cod_mp']."','".$produto['descricao']."','".$produto['moeda']."','".$produto['custo']."','".$produto['unidade']."','".$produto['gramatura']."','".$produto['largura']."','".$produto['icms']."','".$produto['piscofins']."','".$produto['frete']."','".$produto['quebra']."','".$produto['observacoes']."','0','".$produto['empresa']."',now(),'".$_SESSION['UsuarioID']."','1')") or die(mysqli_error($sql));
				$retorno['estado'] = '0';
			} else {
				$sql->query("insert into ".$_POST['mps']." ($colunaCod,descricao,moeda,custo,unidade,gramatura,largura,icms,piscofins,frete,quebra,observacoes,estado,empresa,data,usuario,ativo) values ('".$produto['cod_mp']."','".$produto['descricao']."','".$produto['moeda']."','".$produto['custo']."','".$produto['unidade']."','".$produto['gramatura']."','".$produto['largura']."','".$produto['icms']."','".$produto['piscofins']."','".$produto['frete']."','".$produto['quebra']."','".$produto['observacoes']."','1','".$produto['empresa']."',now(),'".$_SESSION['UsuarioID']."','1')") or die(mysqli_error($sql));
				$retorno['estado'] = '1';
			}
			$retorno['error'] = 0;
		}
		if ($_POST['funcao']=='alteraLotes') {
			$prod = $_POST['prod'];
			$item = explode(",",urldecode($_POST['itens']));
			$alteracao = $_POST['alteracao'];
			$mps = $_POST['mps'];
			$mpsDest = strtoupper($_POST['mpsDest']);
			$mpDest = $_POST['mpDest'];
			$prDest = $_POST['prDest'];
			$spDest = $_POST['spDest'];
			$quantidade = $_POST['quantidade'];
			if ($alteracao=="excluir") {
				$query1 = "update sp_composicao set ativo = '0' where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and mps = 'MP' and cod = '$prod' and cod_sp in (";
				for ($i=0;$i<count($item);$i++) {
					$query1 .= "'".$item[$i]."'";
					if ($i!=(count($item)-1)) $query1 .= ",";
				}
				$query1 .= ")";
				$sql->query($query1) or die (mysqli_error($sql));
			} else if ($alteracao=='substituir') {
				$query1 = "update sp_composicao set ativo = '0' where ativo = '1' and empresa = '".$_SESSION['UsuarioEmpresa']."' and mps = 'MP' and cod = '$prod' and cod_sp in (";
				for ($i=0;$i<count($item);$i++) {
					$query1 .= "'".$item[$i]."'";
					if ($i!=(count($item)-1)) $query1 .= ",";
				}
				$query1 .= ")";
				$sql->query($query1) or die (mysqli_error($sql));
				if ($mpsDest=="MP") $mpsCod = $mpDest;
				if ($mpsDest=="PR") $mpsCod = $prDest;
				if ($mpsDest=="SP") $mpsCod = $spDest;
				for ($j=0;$j<count($item);$j++) {
					$query2 = "insert into sp_composicao (mps,cod,cod_sp,quantidade,empresa,data,usuario,ativo) values ('$mpsDest','$mpsCod','".$item[$j]."','$quantidade','".$_SESSION['UsuarioEmpresa']."',now(),'".$_SESSION['UsuarioID']."','1')";
					$sql->query($query2) or die (mysqli_error($sql));
				}
			}
			$retorno['error'] = 0;
		}
		if ($_POST['funcao']=='manutencao') {
			$manut = mysqli_fetch_array($sql->query("select status from manutencao")) or die (mysqli_error($sql));
			if ($manut['status']==0) {
				$sql->query("update manutencao set status = 1, data = now(), usuario = ".$_SESSION['UsuarioID']." where indice = 1") or die (mysqli_error($sql));
			} else {
				$sql->query("update manutencao set status = 0, data = now(), usuario = ".$_SESSION['UsuarioID']." where indice = 1") or die (mysqli_error($sql));
			}
			$retorno['error'] = 0;
		}
	} else {
		$retorno['mensagem'] = 'Função não definida.';
	}
	echo json_encode($retorno);
	exit;
}

echo json_encode($retorno);
?>