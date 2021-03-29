<?
$versaoSistema=0.1;
$nomeSistema='SINGEC'.' v'.$versaoSistema;
$servidor='localhost';
$usuariosql='root';
$senhasql='focus';
$banco='basetecidos';

// Tenta se conectar ao servidor MySQL
mysql_connect($servidor,$usuariosql,$senhasql) or trigger_error(mysqli_error($sql));
// Tenta se conectar a um banco de dados MySQL
mysql_select_db($banco) or trigger_error(mysqli_error($sql));

$var1 = "SELECT id, nivel, nome FROM usuarios order by nome";
$var2 = $sql->query($var1);
$linhas = mysqli_num_rows($var2);
for ($i=0;$i<$linhas;$i++) {
	$colunas = mysqli_num_fields($var2);
	$var3 = mysqli_fetch_row($var2);
	for ($j=0;$j<$colunas;$j++) {
		echo $var3[$j];
		if ($j<$colunas-1) echo " - ";
	}
	echo "<br>";
	//insere na tabela acess
	$id_user = $var3[0];
	if ($var3[1]==1) {$usuarios = 0; $compras = 0;}
	if ($var3[1]==2) {$usuarios = 1; $compras = 1;}
	$var4 = "select id_user from acess where id_user = $var3[0]";
	if (mysqli_num_rows($sql->query($var4))==0) {
		$var5 = "insert into acess (id_user, usuarios, compras) values ($id_user, $usuarios, $compras)";
		$sql->query($var5) or die(mysqli_error($sql));
	}
}
?>