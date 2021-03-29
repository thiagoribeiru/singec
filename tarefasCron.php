<?
date_default_timezone_set('America/Sao_Paulo');

$name = 'temp/testeCron.txt';
$q = chr(13).chr(10);
$text = 'Tarefa executada em '.date('H:i:s d/m/Y').$q;
$file = fopen($name, 'a');
fwrite($file, $text);
fclose($file);
?>