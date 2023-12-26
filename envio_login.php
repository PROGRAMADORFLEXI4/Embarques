<?php
//session_start();
include 'encryptor.php';
include 'api_autentica.php';


$nombre = $_POST['user'];
$pass = $_POST['contrase'];


$pass_encriptada = encriptar($pass);
$respuesta_Api = login_Api($nombre, $pass_encriptada);
$idNomina = $respuesta_Api[0]["idNomina"];
if ($respuesta_Api[0]["message"] <> "OK") {
	echo "0";
} else {
	echo $idNomina;
}
?>
