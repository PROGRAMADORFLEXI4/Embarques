<?php
  function encriptar($valor){
//Consultar los datos del producto
        $url = 'http://gdl-test:8082/dashboard/encryptor_new.php?id='.$valor;// [CONECTABD][CONECTA_SQL][BD][BASE DE DATOS][WEB SERVICE]
        
        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: false\r\n",
                'method'  => 'POST',
                //'timeout' => 1200,  //1200 Seconds is 20 Minutes
            )
        );
        $context  = stream_context_create($options);
        $res = @file_get_contents($url, false, $context);
        //echo "<script type='text/javascript'>console.log(`Res: ".$res."`)</script>";//DEBUGGER;
        //return $res;

    //   $someArray = json_decode($res, true);
       return $res;
    }
?>