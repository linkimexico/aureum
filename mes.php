<?php

$server = "localhost";
$user = "laura";
$pass = "c3L6ts9^";
$bd = "admin_laura";

//Creamos la conexión
$conexion = mysqli_connect($server, $user, $pass,$bd) 
or die("Ha sucedido un error inexperado en la conexion de la base de datos");
$fecha = $_GET['nombre'];
$fecha= explode(" ",$fecha);
switch ($fecha[0]) {
    case 'Enero':
        $m='01';
        break;
    case 'Febrero':
       	$m='02';
        break;
    case 'Marzo':
        $m='03';
        break;
	 case 'Abril':
        $m='04';
        break;
    case 'Mayo':
       	$m='05';
        break;
    case 'Junio':
        $m='06';
        break;
	 case 'Julio':
        $m='07';
        break;
    case 'Agosto':
       	$m='08';
        break;
    case 'Septiembre':
        $m='09';
        break;
	 case 'Octubre':
        $m='10';
        break;
    case 'Noviembre':
       	$m='11';
        break;
    case 'Diciembre':
        $m='12';
        break;
}
$mes=$fecha[1]."-".$m."-10";
//generamos la consulta

	$sql = "SELECT c,d,ubicacion FROM efemerides a inner join `simbolos` b on a.c=b.nombre where  fecha ='".$mes."'";



mysqli_set_charset($conexion, "utf8"); //formato de datos utf8

if(!$result = mysqli_query($conexion, $sql)) die();

$meses = array(); //creamos un array
$i=0;
while($row = mysqli_fetch_array($result)) 
{ 
    $nombre=$row['c'];
	$elemento=$row['d'];
	$ubicacion=$row['ubicacion'];
		
	

	
}
	$meses[] = array( 'nombre'=>$nombre, 'elemento'=> $elemento, 'ubicacion'=> $ubicacion);
		

//desconectamos la base de datos
$close = mysqli_close($conexion) 
or die("Ha sucedido un error inexperado en la desconexion de la base de datos");
  

//Creamos el JSON
//$clientes['clientes'] = $clientes;
$json_string = json_encode($meses);
echo $json_string;

//Si queremos crear un archivo json, sería de esta forma:
/*
$file = 'clientes.json';
file_put_contents($file, $json_string);
*/
	

?>