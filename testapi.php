<?php
include 'encryptor.php';
include 'api_autentica.php';


//$nombre = $_POST['user'];
//$pass = $_POST['contrase'];

$nombre = 'programador3';
$pass = 'Dev1905Vb$';


$pass_encriptada = encriptar($pass);
$respuesta_Api = login_Api($nombre, $pass_encriptada);

echo '|'.$respuesta_Api.'|'.$pass.' '.$nombre.' '.$pass_encriptada; 
print_r($respuesta_Api);
if($respuesta_Api[0]["message"] <>"OK"){
    echo $respuesta_Api[0]["message"];
}else{
    echo $respuesta_Api[0]["idNomina"];
}
//echo $respuesta_Api[0]["idNomina"];
//print_r($respuesta_Api);


?>