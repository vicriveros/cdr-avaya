<?php
//conexion a base de datos 
$bd_host = "localhost";
$bd_base= "avaya";
$bd_usuario= "user";
$bd_password= "pass";
$conn_string= "host=".$bd_host." port=5432 dbname=".$bd_base." user=".$bd_usuario." password=".$bd_password;
$con = pg_connect($conn_string);

//ip donde la central va a enviar los registros (se debe configurar previamente en la central)
$host = "192.168.1.2"; 
//puerto del socket por donde va a capturar los registros
$port = 9001;

echo "--------------------------------------------------------";
echo "\n";
echo "CDR AVAYA - ".date('d-m-Y \a\t H:i:s');
echo "\n";
echo "--------------------------------------------------------";
echo "\n";

set_time_limit(0);
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("error al crear el
socket\n");
$result = socket_bind($socket, $host, $port) or die("error al crear el
socket\n");
$result = socket_listen($socket, 3) or die("error al levantar el socket de escucha\n");
while (true) {
$spawn = socket_accept($socket) or die("conexion no establecida\n");

$input = socket_read($spawn,1024,PHP_BINARY_READ) or die("error al leer el input\n");
echo $input;

list($fecha_hora, $duracion, $rings, $extension, $direccion, $numero, $dial, $cuenta, $int_ext, $idllamada, $continuacion, $eq1, $nom1, $eq2, $nom2, ) = explode(",", $input);
	if (!empty($fecha_hora)) {
		$fecha=substr($fecha_hora, 0,10);
		$hora=substr($fecha_hora, 11);
		$sql="INSERT INTO llamadas (fecha, hora, duracion, extension, direccion, nro, int_ex, device1, name1, device2, name2) VALUES ('".$fecha."','".$hora."', '".$duracion."', '".$extension."', '".$direccion."', '".$numero."', '".$int_ext."', '".$eq1."', '".$nom1."', '".$eq2."', '".$nom2."')";
		pg_query ($con, $sql) or die ("Problemas en insert:".pg_last_error ());
	}

socket_close($spawn);
}
socket_close($socket);
?>