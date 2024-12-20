<? // somente funcoes sem echo
function vlrLiquido($cod,$mps,$point = false) {
	global $sql;
	if ($point) $empresa = $point;
	else $empresa = $_SESSION['UsuarioEmpresa'];
	if ($mps=="MP") {
		$linha = mysqli_fetch_array($sql->query("select custo, icms, piscofins, frete, quebra from mp where empresa = ".$empresa." and ativo = 1 and cod_mp = $cod"));
		return /*number_format(*/($linha['custo']-($linha['custo']*($linha['icms']/100))+($linha['custo']*($linha['frete']/100))-($linha['custo']*($linha['piscofins']/100))+($linha['custo']*($linha['quebra']/100)))/*,4,",",".")*/;
	}
	if ($mps=="PR") {
		$linha = mysqli_fetch_array($sql->query("select custo, icms, piscofins, frete, quebra from processos where empresa = ".$empresa." and ativo = 1 and cod_pr = $cod"));
		return /*number_format(*/($linha['custo']-($linha['custo']*($linha['icms']/100))+($linha['custo']*($linha['frete']/100))-($linha['custo']*($linha['piscofins']/100))+($linha['custo']*($linha['quebra']/100)))/*,4,",",".")*/;
	}
}

//funções para somar os subprodutos com subprodutos...
function somaSP($ref_sp,$point = false) {
	global $sql;
	if ($ref_sp!="") {
		if ($point) $usuarioEmpresa = $point;
		else $usuarioEmpresa = $_SESSION['UsuarioEmpresa'];
		$resultado = 0;
		$moeda_sp = mysqli_fetch_array($sql->query("select moeda from sp_dados where ativo = 1 and empresa = $usuarioEmpresa and cod_sp = $ref_sp"));
		$pesq_sp = $sql->query("select mps, cod, quantidade from sp_composicao where ativo = 1 and empresa = $usuarioEmpresa and cod_sp = $ref_sp");
		for ($i=mysqli_num_rows($pesq_sp);$i>0;$i--) {
			$linha = mysqli_fetch_array($pesq_sp);
			if ($linha['mps']==MP) {
				$linhaMP = mysqli_fetch_array($sql->query("select custo, moeda from mp where ativo = 1 and empresa = $usuarioEmpresa and cod_mp = ".$linha['cod']));
				$resultado = $resultado + ($linha['quantidade']*cotaToIsoSession($linhaMP['moeda'],"compra",vlrLiquido($linha['cod'],$linha['mps'],$point),$moeda_sp['moeda'],$point));
			}
			if ($linha['mps']==PR) {
				$linhaPR = mysqli_fetch_array($sql->query("select custo, moeda from processos where ativo = 1 and empresa = $usuarioEmpresa and cod_pr = ".$linha['cod']));
				$resultado = $resultado + ($linha['quantidade']*cotaToIsoSession($linhaPR['moeda'],"compra",vlrLiquido($linha['cod'],$linha['mps'],$point),$moeda_sp['moeda'],$point));
			}
			if ($linha['mps']==SP) {
				$linhaSP = mysqli_fetch_array($sql->query("select cod_sp, moeda from sp_dados where ativo = 1 and empresa = $usuarioEmpresa and cod_sp = ".$linha['cod']));
				$resultado = $resultado + ($linha['quantidade']*cotaToIsoSession($linhaSP['moeda'],"compra",somaSP($linhaSP['cod_sp'],$point),$moeda_sp['moeda'],$point));
			}
		} return $resultado;
	} else return 0;
}

function somaLarg($ref_sp) {
	global $sql;
	if ($ref_sp!="") {
		$usuarioEmpresa = $_SESSION['UsuarioEmpresa'];
		$larguraPadrao = mysqli_fetch_row($sql->query("select largura from sp_dados where ativo = 1 and empresa = $usuarioEmpresa and cod_sp = $ref_sp"));
		if ($larguraPadrao[0]==0) {
			$resultado = 0;
			$pesq_sp = $sql->query("select mps, cod from sp_composicao where ativo = 1 and empresa = $usuarioEmpresa and cod_sp = $ref_sp");
			for ($i=mysqli_num_rows($pesq_sp);$i>0;$i--) {
				$linha = mysqli_fetch_array($pesq_sp);
				if ($linha['mps']==MP) {
					$linhaMP = mysqli_fetch_array($sql->query("select largura from mp where ativo = 1 and empresa = $usuarioEmpresa and cod_mp = ".$linha['cod']));
					if (mysqli_num_rows($pesq_sp)==$i) $resultado = $linhaMP['largura'];
					else if ($resultado > $linhaMP['largura'] and $linhaMP['largura']!=0) $resultado = $linhaMP['largura'];
				}
				if ($linha['mps']==PR) {
					$linhaPR = mysqli_fetch_array($sql->query("select largura from processos where ativo = 1 and empresa = $usuarioEmpresa and cod_pr = ".$linha['cod']));
					if (mysqli_num_rows($pesq_sp)==$i) $resultado = $linhaPR['largura'];
					else if ($resultado > $linhaPR['largura'] and $linhaPR['largura']!=0) $resultado = $linhaPR['largura'];
				}
				if ($linha['mps']==SP) {
					$linhaSP = mysqli_fetch_array($sql->query("select cod_sp, largura from sp_dados where ativo = 1 and empresa = $usuarioEmpresa and cod_sp = ".$linha['cod']));
					$largura = somaLarg($linhaSP['cod_sp']);
					if (mysqli_num_rows($pesq_sp)==$i) $resultado = $largura;
					else if ($resultado > $largura) $resultado = $largura;
				}
			} return $resultado;
		} else return $larguraPadrao[0];
	} else return 0;
}

function somaGR($ref_sp) {
	global $sql;
	if ($ref_sp!="") {
		$usuarioEmpresa = $_SESSION['UsuarioEmpresa'];
		$gramaturaPadrao = mysqli_fetch_row($sql->query("select gramatura from sp_dados where ativo = 1 and empresa = $usuarioEmpresa and cod_sp = $ref_sp"));
		if ($gramaturaPadrao[0]==0) {
			$resultado = 0;
			$pesq_sp = $sql->query("select mps, cod, quantidade from sp_composicao where ativo = 1 and empresa = $usuarioEmpresa and cod_sp = $ref_sp");
			for ($i=mysqli_num_rows($pesq_sp);$i>0;$i--) {
				$linha = mysqli_fetch_array($pesq_sp);
				if ($linha['mps']==MP) {
					$linhaMP = mysqli_fetch_array($sql->query("select gramatura from mp where ativo = 1 and empresa = $usuarioEmpresa and cod_mp = ".$linha['cod']));
					$resultado = $resultado + ($linha['quantidade']*$linhaMP['gramatura']);
				}
				if ($linha['mps']==SP) {
					$linhaSP = mysqli_fetch_array($sql->query("select cod_sp from sp_dados where ativo = 1 and empresa = $usuarioEmpresa and cod_sp = ".$linha['cod']));
					$resultado = $resultado + ($linha['quantidade']*somaGR($linhaSP['cod_sp']));
				}
			} return $resultado;
		} return $gramaturaPadrao[0];
	} else return 0;
}
//***FIM > funções para somar os subprodutos com subprodutos...

function mask($val, $mask) {
	 $maskared = '';
	 $k = 0;
	 for($i = 0; $i<=strlen($mask)-1; $i++) {
	 	if($mask[$i] == '#') {
	 		if(isset($val[$k])) $maskared .= $val[$k++];
	 	}
	 	else {
	 		if(isset($mask[$i])) $maskared .= $mask[$i];
	 	}
	 }
	 return $maskared;
}

//função para pegar cotação de moedas
// function cota($moeda) { // Inicia a funcao para pegar a cotacao de determinada moeda ($moeda)
// 	$link = "http://download.finance.yahoo.com/d/quotes.csv?s=".$moeda."USD=X&f=sl1d1t1ba&e=.csv"; //link para pegar a cotacao no formato CSv
// 	if (@fopen($link,"r")) { // abre o arquivo CSV
// 		$arq = file($link);
// 	}
// 	if (is_array($arq)) { // Se o arquivo retornar um array continua
// 	   for ($x=0;$x<count($arq);$x++) { // Passa por todas as chaves do array
// 		  $linha = explode(",",$arq[$x]); // Separa os valores do arquivo CSV
		  
// 		  $result['cotacao']  = $linha[1]; // Pega o valor que o Yahoo usa para fazer a conversao
// 		  $data = date("Y-m-d",strtotime(str_replace('"','',$linha[2]))); // Retira as aspas da data
// 		  $hora = date("H:i:s",strtotime(str_replace('"','',$linha[3]))); // Retira as aspas do horario da cotacao
// 		  $result['datetime'] = $data." ".$hora;
// 		  $result['compra']  = $linha[4]; // Pega o valor de compra da moeda
// 		  $result['venda']  = $linha[5]; // Pega o valor de venda da moeda
// 		}
// 	}
// 	else{ // Se o arquivo nao retornar nenhum array
// 		$result['cotacao'] = "N/A"; // Define not avaiable para os campos
// 		// $result['data'] = "N/A";
// 		// $result['hora'] = "N/A";
// 		$result['datetime'] = "N/A";
// 		$result['compra']  = "N/A";
// 		$result['venda']  = "N/A";
// 	}
	
// 	return $result; // retorna o array com os valores a serem usados
// }
function cota($moeda) {
    // URL do feed do ECB
    $url = "https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml";

    // Faz a requisição ao feed XML
    $xml = @simplexml_load_file($url);

    if ($xml === false) {
        // Retorna erro caso o feed não esteja acessível
        return [
            'cotacao' => 'N/A',
            'datetime' => 'N/A',
            'compra' => 'N/A',
            'venda' => 'N/A',
        ];
    }

    // Obtém a data do feed
    $data = (string)$xml->Cube->Cube['time'];

    // Procura a taxa de câmbio da moeda desejada
    $cotacao = null;
    foreach ($xml->Cube->Cube->Cube as $rate) {
        if ((string)$rate['currency'] === strtoupper($moeda)) {
            $cotacao = (float)$rate['rate'];
            break;
        }
    }

    // Verifica se a moeda foi encontrada
    if ($cotacao === null) {
        return [
            'cotacao' => 'N/A',
            'datetime' => 'N/A',
            'compra' => 'N/A',
            'venda' => 'N/A',
        ];
    }

    // Calcula a cotação com base no USD se necessário
    // Taxa do USD em relação ao EUR
    $usdToEur = null;
    foreach ($xml->Cube->Cube->Cube as $rate) {
        if ((string)$rate['currency'] === "USD") {
            $usdToEur = (float)$rate['rate'];
            break;
        }
    }

    if ($usdToEur === null) {
        return [
            'cotacao' => 'N/A',
            'datetime' => 'N/A',
            'compra' => 'N/A',
            'venda' => 'N/A',
        ];
    }

    // Converte a cotação para USD
    $cotacaoEmUsd = $cotacao / $usdToEur;

    return [
        'cotacao' => round($cotacaoEmUsd, 4),
        'datetime' => $data,
        'compra' => round($cotacaoEmUsd, 4),
        'venda' => round($cotacaoEmUsd, 4),
    ];
}

// Exemplo de uso
// $moeda = "BRL"; // Substitua pela moeda desejada
// $resultado = cota($moeda);
// print_r($resultado);

//ultima cotação
function forcaCotaServ(){
	global $sql;
	$moedaPesq = $sql->query("select * from moedas where ativo = 1");
	$moedaLinhas = mysqli_num_rows($moedaPesq);
	if ($moedaLinhas>0) {
		for ($i=1;$moedaLinhas>=$i;$i++) {
			$moeda = mysqli_fetch_array($moedaPesq);
			$cotacao = cota($moeda['iso']);
			$compra = $cotacao['compra'];
			$venda = $cotacao['venda'];
			$hora = $cotacao['datetime'];
			if (($cotacao!="N/A") and ($venda!="N/A") and ($hora!="N/A")) {
				$sql->query("update moedas set ativo = '0' where iso = '".$moeda['iso']."'") or die(mysqli_error($sql));
					$sql->query("INSERT INTO `moedas`(`nome`, `iso`, `moeda`, `compra`, `venda`, `datetime`, `ativo`) VALUES ('".$moeda['nome']."','".$moeda['iso']."','".$moeda['moeda']."','$compra','$venda',now(),'1')") or die(mysqli_error($sql));
			}
		}
	}
}
function cotaServ() {
	global $sql;
	$pesqUSD = $sql->query("select datetime from moedas where iso = 'USD' and ativo = 1");
	$usd = mysqli_fetch_array($pesqUSD);
	if ((strtotime($usd['datetime'])+7200)<time()) {
		forcaCotaServ();
	}
}
function cotaToIsoSession() {
	global $sql;
	// ($isoOrig,$tipo,[$vlr],[$isoDest],[$point])
	$numArg = func_num_args();
	if ($numArg==5) {$point = func_get_arg(4);}
	else $point = false;
	if ($point) {
		$moedaUser = mysqli_fetch_array($sql->query("select moeda from empresas where id_empresa = '".$point."'")) or die(mysqli_error($sql));
		$valor = mysqli_fetch_array($sql->query("select compra, venda from moedas where iso = '".$moedaUser['moeda']."' and ativo = 1")) or die(mysqli_error($sql));
		$cookieCompra = $valor['compra'];
		$cookieVenda = $valor['venda'];
	} else {
		$cookieCompra = $_COOKIE['vlrCompra'];
		$cookieVenda = $_COOKIE['vlrVenda'];
	}
	$isoOrig = func_get_arg(0);
	$tipo = func_get_arg(1);
	if ($numArg==2) {$vlr = 1; $vlrDest['compra'] = 1; $vlrDest['venda'] = 1;}
	if ($numArg==3) {$vlr = func_get_arg(2); $vlrDest['compra'] = 1; $vlrDest['venda'] = 1;}
	$vlrOrig = mysqli_fetch_array($sql->query("select compra, venda from moedas where iso = '$isoOrig' and ativo = 1")) or die(mysqli_error($sql));
	if ($numArg>=4) {
		$vlr = func_get_arg(2);
		$isoDest = func_get_arg(3);
		$vlrDest = mysqli_fetch_array($sql->query("select compra, venda from moedas where iso = '$isoDest' and ativo = 1")) or die(mysqli_error($sql));
		$vlrDest['compra'] = ($vlrDest['compra']/$cookieCompra);
		$vlrDest['venda'] = ($vlrDest['venda']/$cookieVenda);
	}
	if ($tipo=="compra") return (($vlrOrig['compra']/$cookieCompra)*$vlr)/$vlrDest['compra'];
	else if ($tipo=="venda") return (($vlrOrig['venda']/$cookieVenda)*$vlr)/$vlrDest['venda'];
	else return "ERRO_TIPO";
}
function limitarTexto($texto, $limite){
	$contador = strlen($texto);
	if ( $contador >= $limite ) {      
		$texto = substr($texto, 0, strrpos(substr($texto, 0, $limite), ' ')) . '...';
		return $texto;
	}
	else{
		return $texto;
	}
}
function somaMP($ref_sp,$point = false) {
	global $sql;
	if ($ref_sp!="") {
		if ($point) $usuarioEmpresa = $point;
		else $usuarioEmpresa = $_SESSION['UsuarioEmpresa'];
		$resultado = 0;
		$moeda_sp = mysqli_fetch_array($sql->query("select moeda from sp_dados where ativo = 1 and empresa = $usuarioEmpresa and cod_sp = $ref_sp"));
		$pesq_sp = $sql->query("select mps, cod, quantidade from sp_composicao where ativo = 1 and empresa = $usuarioEmpresa and cod_sp = $ref_sp");
		for ($i=mysqli_num_rows($pesq_sp);$i>0;$i--) {
			$linha = mysqli_fetch_array($pesq_sp);
			if ($linha['mps']==MP) {
				$linhaMP = mysqli_fetch_array($sql->query("select custo, moeda from mp where ativo = 1 and empresa = $usuarioEmpresa and cod_mp = ".$linha['cod']));
				$resultado = $resultado + ($linha['quantidade']*cotaToIsoSession($linhaMP['moeda'],"compra",$linhaMP['custo'],$moeda_sp['moeda'],$point));
			}
			if ($linha['mps']==PR) {
				$linhaPR = mysqli_fetch_array($sql->query("select custo, moeda, setor_fornec from processos where ativo = 1 and empresa = $usuarioEmpresa and cod_pr = ".$linha['cod']));
				if ($linhaPR['setor_fornec']=='2') {
					$resultado = $resultado + ($linha['quantidade']*cotaToIsoSession($linhaPR['moeda'],"compra",$linhaPR['custo'],$moeda_sp['moeda'],$point));
				}
			}
			if ($linha['mps']==SP) {
				$linhaSP = mysqli_fetch_array($sql->query("select cod_sp, moeda from sp_dados where ativo = 1 and empresa = $usuarioEmpresa and cod_sp = ".$linha['cod']));
				$resultado = $resultado + ($linha['quantidade']*cotaToIsoSession($linhaSP['moeda'],"compra",somaMP($linhaSP['cod_sp'],$point),$moeda_sp['moeda'],$point));
			}
		} return $resultado;
	} else return 0;
} 
?>