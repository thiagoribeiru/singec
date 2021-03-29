<?
require_once("config.php");

$sql->query("CREATE TABLE IF NOT EXISTS `moedas` (`cod` INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
	addCol("nome","moedas","VARCHAR(30) not null","cod");
    addCol("iso","moedas","VARCHAR(3) not null","nome");
    addCol("moeda","moedas","VARCHAR(5) not null","iso");
    addCol("compra","moedas","FLOAT","moeda");
    addCol("venda","moedas","float","compra");
    addCol("datetime","moedas","datetime","venda");
    addCol("ativo","moedas","tinyint(1) not null","datetime");
    delCol("empresa","moedas");
    if (mysqli_num_rows($sql->query("select * from moedas"))==0)
		$sql->query("INSERT INTO `moedas`(`nome`, `iso`, `moeda`, `ativo`) VALUES ('Dólar dos Estados Unidos','USD','$','1')") or die(mysqli_error($sql));
	 
$sql->query("CREATE TABLE IF NOT EXISTS `unidades` (`cod` INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("unidade","unidades","VARCHAR(5) NOT NULL","cod");
		addCol("empresa","unidades","INT NOT NULL","unidade");
	 
$sql->query("CREATE TABLE IF NOT EXISTS `mp` (`indice` INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("cod_mp","mp","INT NOT NULL","indice");
		addCol("descricao","mp","VARCHAR(70) NOT NULL","cod_mp");
		addCol("moeda","mp","VARCHAR(5) NOT NULL","descricao");
		addCol("custo","mp","FLOAT NOT NULL","moeda");
		addCol("unidade","mp","VARCHAR(5) NOT NULL","custo");
		addCol("gramatura","mp","FLOAT NOT NULL","unidade");
		addCol("largura","mp","FLOAT NOT NULL","gramatura");
		addCol("icms","mp","FLOAT NOT NULL","largura");
		addCol("piscofins","mp","FLOAT NOT NULL","icms");
		addCol("frete","mp","FLOAT NOT NULL","piscofins");
		addCol("quebra","mp","FLOAT NOT NULL","frete");
		addCol("observacoes","mp","TEXT NOT NULL","quebra");
		addCol("estado","mp","TINYINT(1) NOT NULL","observacoes","update mp set estado = '1'");
		addCol("empresa","mp","INT NOT NULL","estado");
		addCol("data","mp","DATETIME NOT NULL","empresa");
		addCol("usuario","mp","INT NOT NULL","data");
		addCol("ativo","mp","TINYINT(1) NOT NULL","usuario");
		
$sql->query("
	CREATE TABLE IF NOT EXISTS `users_logados` (
		`id_user` INT NOT NULL ,
		`id_sessao` VARCHAR(40) NOT NULL ,
		`login` DATETIME NOT NULL ,
		`validade` DATETIME NOT NULL )
	 ENGINE = InnoDB") or die(mysqli_error($sql));

$sql->query ("
	CREATE TABLE IF NOT EXISTS `processos` ( 
		`indice` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
		`cod_pr` INT NOT NULL , 
		`descricao` VARCHAR(70) NOT NULL , 
		`moeda` VARCHAR(5) NOT NULL , 
		`custo` FLOAT NOT NULL , 
		`unidade` VARCHAR(5) NOT NULL , 
		`largura` FLOAT NOT NULL , 
		`icms` FLOAT NOT NULL , 
		`piscofins` FLOAT NOT NULL , 
		`frete` FLOAT NOT NULL , 
		`quebra` FLOAT NOT NULL , 
		`setor_fornec` INT NOT NULL , 
		`observacoes` TEXT NOT NULL , 
		`empresa` INT NOT NULL , 
		`data` DATETIME NOT NULL , 
		`usuario` INT NOT NULL , 
		`ativo` TINYINT(1) NOT NULL ) 
	ENGINE = InnoDB") or die(mysqli_error($sql));

$sql->query("CREATE TABLE IF NOT EXISTS sp_dados (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("cod_sp","sp_dados","INT NOT NULL","indice");
		addCol("descricao","sp_dados","VARCHAR(125) NOT NULL","cod_sp");
		addCol("moeda","sp_dados","VARCHAR(5) NOT NULL","descricao");
		addCol("unidade","sp_dados","VARCHAR(5) NOT NULL","moeda");
		addCol("largura","sp_dados","FLOAT NOT NULL","unidade");
		addCol("gramatura","sp_dados","FLOAT NOT NULL","largura");
		addCol("grupo","sp_dados","INT NOT NULL","gramatura");
		addCol("observacoes","sp_dados","TEXT NOT NULL","grupo");
		addCol("empresa","sp_dados","INT NOT NULL","observacoes");
		addCol("data","sp_dados","DATETIME NOT NULL","empresa");
		addCol("usuario","sp_dados","INT NOT NULL","data");
		addCol("ativo","sp_dados","TINYINT(1) NOT NULL","usuario");

$sql->query("CREATE TABLE IF NOT EXISTS sp_composicao (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("mps","sp_composicao","VARCHAR(5) NOT NULL","indice");
		addCol("cod","sp_composicao","INT NOT NULL","mps");
		addCol("cod_sp","sp_composicao","INT NOT NULL","cod");
		addCol("quantidade","sp_composicao","FLOAT NOT NULL","cod_sp");
		addCol("empresa","sp_composicao","INT NOT NULL","quantidade");
		addCol("data","sp_composicao","DATETIME NOT NULL","empresa");
		addCol("usuario","sp_composicao","INT NOT NULL","data");
		addCol("ativo","sp_composicao","TINYINT(1) NOT NULL","usuario");
	
$sql->query("CREATE TABLE IF NOT EXISTS representantes (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("cod_rep","representantes","INT NOT NULL","indice");
		addCol("nome","representantes","VARCHAR(50) NOT NULL","cod_rep");
		addCol("comissao_padrao","representantes","FLOAT NOT NULL","nome");
		addCol("observacoes","representantes","TEXT NOT NULL","comissao_padrao");
		addCol("ativo_venda","representantes","TINYINT(1) NOT NULL","observacoes");
		addCol("data","representantes","DATETIME NOT NULL","ativo_venda");
		addCol("usuario","representantes","INT NOT NULL","data");
		addCol("empresa","representantes","INT NOT NULL","usuario");
		addCol("ativo","representantes","TINYINT(1) NOT NULL","empresa");
		
$sql->query("CREATE TABLE IF NOT EXISTS regioes (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("cod_reg","regioes","INT NOT NULL","indice");
		addCol("regiao","regioes","VARCHAR(20) NOT NULL","cod_reg");
		addCol("icms","regioes","FLOAT NOT NULL","regiao");
		addCol("data","regioes","DATETIME NOT NULL","icms");
		addCol("usuario","regioes","INT NOT NULL","data");
		addCol("empresa","regioes","INT NOT NULL","usuario");
		addCol("ativo","regioes","TINYINT(1) NOT NULL","empresa");
		
$sql->query("CREATE TABLE IF NOT EXISTS clientes (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("cod_cli","clientes","INT NOT NULL","indice");
		addCol("cliente","clientes","VARCHAR(40) NOT NULL","cod_cli");
		addCol("regiao","clientes","TINYINT(1) NOT NULL","cliente");
		addCol("representante","clientes","TINYINT(1) NOT NULL","regiao");
		addCol("observacoes","clientes","TEXT NOT NULL","representante");
		addCol("data","clientes","DATETIME NOT NULL","observacoes");
		addCol("usuario","clientes","INT NOT NULL","data");
		addCol("empresa","clientes","INT NOT NULL","usuario");
		addCol("ativo","clientes","TINYINT(1) NOT NULL","empresa");
		
$sql->query("CREATE TABLE IF NOT EXISTS outros_impostos (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("percentual","outros_impostos","FLOAT NOT NULL","indice");
		addCol("data","outros_impostos","DATETIME NOT NULL","percentual");
		addCol("usuario","outros_impostos","INT NOT NULL","data");
		addCol("empresa","outros_impostos","INT NOT NULL","usuario");
		addCol("ativo","outros_impostos","TINYINT(1) NOT NULL","empresa");

$sql->query("CREATE TABLE IF NOT EXISTS custo_fixo (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("percentual","custo_fixo","FLOAT NOT NULL","indice");
		addCol("data","custo_fixo","DATETIME NOT NULL","percentual");
		addCol("usuario","custo_fixo","INT NOT NULL","data");
		addCol("empresa","custo_fixo","INT NOT NULL","usuario");
		addCol("ativo","custo_fixo","TINYINT(1) NOT NULL","empresa");
		
$sql->query("CREATE TABLE IF NOT EXISTS margem_fixa (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("percentual","margem_fixa","FLOAT NOT NULL","indice");
		addCol("data","margem_fixa","DATETIME NOT NULL","percentual");
		addCol("usuario","margem_fixa","INT NOT NULL","data");
		addCol("empresa","margem_fixa","INT NOT NULL","usuario");
		addCol("ativo","margem_fixa","TINYINT(1) NOT NULL","empresa");
		
$sql->query("CREATE TABLE IF NOT EXISTS rentabilidade (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("cod_rent","rentabilidade","INT NOT NULL","indice");
		addCol("data_ped","rentabilidade","DATE NOT NULL","cod_rent");
		addCol("faturado","rentabilidade","TINYINT(1) NOT NULL","data_ped","update rentabilidade set faturado = 1");
		addCol("data_nf","rentabilidade","DATE NOT NULL","faturado");
		addCol("cliente","rentabilidade","INT NOT NULL","data_nf");
		addCol("clientevar","rentabilidade","VARCHAR(40) NOT NULL","cliente"); //somente fechamento
		addCol("regiaovar","rentabilidade","VARCHAR(20) NOT NULL","clientevar"); //somente fechamento
		addCol("representantevar","rentabilidade","VARCHAR(50) NOT NULL","regiaovar"); //somente fechamento
		addCol("cod_sp","rentabilidade","INT NOT NULL","representantevar");
		addCol("descricaovar","rentabilidade","VARCHAR(125) NOT NULL","cod_sp"); //somente fechamento
		addCol("quant","rentabilidade","FLOAT NOT NULL","descricaovar");
		addCol("undvar","rentabilidade","VARCHAR(5) NOT NULL","quant"); //somente fechamento
		addCol("vlr_padr","rentabilidade","FLOAT NOT NULL","undvar"); //somente fechamento
		addCol("vlr_nf","rentabilidade","FLOAT NOT NULL","vlr_padr");
		addCol("icms","rentabilidade","FLOAT NOT NULL","vlr_nf"); //somente fechamento
		addCol("comis","rentabilidade","FLOAT NOT NULL","icms"); //somente fechamento
		addCol("marg","rentabilidade","FLOAT NOT NULL","comis"); //somente fechamento
		addCol("data_cri","rentabilidade","DATETIME NOT NULL","marg");
		addCol("data","rentabilidade","DATETIME NOT NULL","data_cri");
		addCol("usuario","rentabilidade","INT NOT NULL","data");
		addCol("empresa","rentabilidade","INT NOT NULL","usuario");
		addCol("ativo","rentabilidade","TINYINT(1) NOT NULL","empresa");
		addCol("fechado","rentabilidade","TINYINT(1) NOT NULL","ativo");

$sql->query("CREATE TABLE IF NOT EXISTS grupos_de_produto (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("cod_grupo_prod","grupos_de_produto","INT NOT NULL","indice");
		addCol("grupo","grupos_de_produto","VARCHAR(20) NOT NULL","cod_grupo_prod");
		addCol("empresa","grupos_de_produto","INT NOT NULL","grupo");
		addCol("data","grupos_de_produto","DATETIME NOT NULL","empresa");
		addCol("usuario","grupos_de_produto","INT NOT NULL","data");
		addCol("ativo","grupos_de_produto","TINYINT(1) NOT NULL","usuario");

$sql->query("CREATE TABLE IF NOT EXISTS representantes_grupos (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("cod_rep","representantes_grupos","INT NOT NULL","indice");
		addCol("cod_grupo_prod","representantes_grupos","INT NOT NULL","cod_rep");
		addCol("comiss","representantes_grupos","FLOAT NOT NULL","cod_grupo_prod");
		addCol("empresa","representantes_grupos","INT NOT NULL","comiss");
		addCol("data","representantes_grupos","DATETIME NOT NULL","empresa");
		addCol("usuario","representantes_grupos","INT NOT NULL","data");
		addCol("ativo","representantes_grupos","TINYINT(1) NOT NULL","usuario");

$sql->query("CREATE TABLE IF NOT EXISTS equipes_de_venda (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("cod_equipe","equipes_de_venda","INT NOT NULL","indice");
		addCol("equipe","equipes_de_venda","VARCHAR(20) NOT NULL","cod_equipe");
		addCol("empresa","equipes_de_venda","INT NOT NULL","equipe");
		addCol("data","equipes_de_venda","DATETIME NOT NULL","empresa");
		addCol("usuario","equipes_de_venda","INT NOT NULL","data");
		addCol("ativo","equipes_de_venda","TINYINT(1) NOT NULL","usuario");

$sql->query("CREATE TABLE IF NOT EXISTS representantes_equipes (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("cod_rep","representantes_equipes","INT NOT NULL","indice");
		addCol("cod_equipe","representantes_equipes","INT NOT NULL","cod_rep");
		addCol("dentro","representantes_equipes","TINYINT(1) NOT NULL","cod_equipe");
		addCol("empresa","representantes_equipes","INT NOT NULL","dentro");
		addCol("data","representantes_equipes","DATETIME NOT NULL","empresa");
		addCol("usuario","representantes_equipes","INT NOT NULL","data");
		addCol("ativo","representantes_equipes","TINYINT(1) NOT NULL","usuario");
		
$sql->query("CREATE TABLE IF NOT EXISTS emails_automaticos (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("tipo","emails_automaticos","VARCHAR(20) NOT NULL","indice");
		addCol("hora","emails_automaticos","INT NOT NULL","tipo");
		addCol("minuto","emails_automaticos","INT NOT NULL","hora");
		addCol("email","emails_automaticos","VARCHAR(206) NOT NULL","minuto");
		addCol("prox_disparo","emails_automaticos","DATETIME NOT NULL","email");
		addCol("empresa","emails_automaticos","INT NOT NULL","prox_disparo");
		addCol("data","emails_automaticos","DATETIME NOT NULL","empresa");
		addCol("usuario","emails_automaticos","INT NOT NULL","data");
		addCol("ativo","emails_automaticos","TINYINT(1) NOT NULL","usuario");

$sql->query("CREATE TABLE IF NOT EXISTS manutencao (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY)") or die(mysqli_error($sql));
		addCol("status","manutencao","TINYINT(1) NOT NULL","indice");
		addCol("mensagem","manutencao","VARCHAR(50) NOT NULL","status");
		addCol("data","manutencao","DATETIME NOT NULL","mensagem");
		addCol("usuario","manutencao","INT NOT NULL","data");
		
$sql->query("CREATE TABLE IF NOT EXISTS imposto_de_renda (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("percentual","imposto_de_renda","FLOAT NOT NULL","indice");
		addCol("data","imposto_de_renda","DATETIME NOT NULL","percentual");
		addCol("usuario","imposto_de_renda","INT NOT NULL","data");
		addCol("empresa","imposto_de_renda","INT NOT NULL","usuario");
		addCol("ativo","imposto_de_renda","TINYINT(1) NOT NULL","empresa");
		
$sql->query("CREATE TABLE IF NOT EXISTS meta_lucro (indice INT NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = InnoDB") or die(mysqli_error($sql));
		addCol("percentual","meta_lucro","FLOAT NOT NULL","indice");
		addCol("data","meta_lucro","DATETIME NOT NULL","percentual");
		addCol("usuario","meta_lucro","INT NOT NULL","data");
		addCol("empresa","meta_lucro","INT NOT NULL","usuario");
		addCol("ativo","meta_lucro","TINYINT(1) NOT NULL","empresa");
?>