<?php
  function login_Api($nombre, $contra){
//Consultar los datos del producto
        $url = 'http://192.168.10.139:9192/Api/LoginInternalUsers/v1/Login-Users-AD';// [CONECTABD][CONECTA_SQL][BD][BASE DE DATOS][WEB SERVICE]
        $data = '{
            "nombre":"'.$nombre.'",
            "password":"'.$contra.'"
        }';
        
        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json;charset=utf-8\r\n",
                'method'  => 'POST',
                'content' => $data,
                //'timeout' => 1200,  //1200 Seconds is 20 Minutes
            )
        );
        $context  = stream_context_create($options);
        $res = @file_get_contents($url, false, $context);
        //echo "<script type='text/javascript'>console.log(`Res: ".$res."`)</script>";//DEBUGGER;
        //return $res;

       $someArray = json_decode($res, true);
       return $someArray;
    }
?>