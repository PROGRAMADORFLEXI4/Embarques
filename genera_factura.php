<?php
    include("conectabd.php");

    $inhouse = isset($_POST["inhouse"])?$_POST["inhouse"]:"";
    $factura = isset($_POST["factura"])?$_POST["factura"]:"";
    $tipEntrega = isset($_POST["tipEntrega"])?$_POST["tipEntrega"]:"0";
    $destino = isset($_POST["destino"])?$_POST["destino"]:"0";
    $vehiculo = isset($_POST["vehiculo"])?$_POST["vehiculo"]:"0";
    $chofer = isset($_POST["chofer"])?$_POST["chofer"]:"";
    $auxiliar = isset($_POST["auxiliar"])?$_POST["auxiliar"]:"";
    $recnoZZO = isset($_POST["recnoZZO"])?$_POST["recnoZZO"]:"";
    $fyh_salida="";
    $fyh_llegada="";

    if(isset($_POST["fyh_salida"])){
        $fyh_salida= str_replace("T"," ",$_POST["fyh_salida"]).":00";
        $fyh_salida= substr($fyh_salida,8,2)."/".substr($fyh_salida,5,2)."/".substr($fyh_salida,2,2).substr($fyh_salida,10,strlen($fyh_salida));
    }

    if(isset($_POST["fyh_llegada"])){
        $fyh_llegada= str_replace("T"," ",$_POST["fyh_llegada"]).":00";
        $fyh_llegada= substr($fyh_llegada,8,2)."/".substr($fyh_llegada,5,2)."/".substr($fyh_llegada,2,2).substr($fyh_llegada,10,strlen($fyh_llegada));
    }

    $res="";
    /*EXEC [SP_insertaFacturaTranslado] @parametro AS VARCHAR(20), @recnoZZO AS INT, @factura AS VARCHAR(20), @tipEntrega AS INT, @recDestino AS INT, @recVehiculo AS INT, @codChofer AS VARCHAR(15), @codAuxiliar AS VARCHAR(25),@fechSalida AS VARCHAR(30),@fechLlegada AS VARCHAR(30)*/
    $file = fopen("log/cartaporte/".$factura.".txt", "a");
    fwrite($file,"-----------------------------------------------------------------------".PHP_EOL);
    fwrite($file,"----Inicio Guardando datos cartaPorte, Factura Venta: ".$factura."---".PHP_EOL);
    fwrite($file,"---------------".date('Ymd H:i:s')."-----------------".PHP_EOL);
    fwrite($file,"-----------------------------------------------------------------------".PHP_EOL);
    fwrite($file,"".PHP_EOL);

    $str_generaFactura="EXEC [SP_insertaFacturaTranslado] '".$inhouse."',".$recnoZZO.", '".$factura."', ".$tipEntrega.",".$destino.",".$vehiculo.",'".$chofer."','".$auxiliar."','".$fyh_salida."','".$fyh_llegada."';";
    fwrite($file, "Descripcion de campos: EXEC [SP_insertaFacturaTranslado] @parametro AS VARCHAR(20), @recnoZZO AS INT, @factura AS VARCHAR(20), @tipEntrega AS INT, @recDestino AS INT, @recVehiculo AS INT, @codChofer AS VARCHAR(15), @codAuxiliar AS VARCHAR(25),@fechSalida AS VARCHAR(30),@fechLlegada AS VARCHAR(30)".PHP_EOL);
    fwrite($file,"".PHP_EOL);
    fwrite($file, "Datos: ".$str_generaFactura.PHP_EOL);
    $rst_generaFactura=odbc_exec($conn, $str_generaFactura) or die("Error al insetar factura".$str_generaFactura);
    if($data=odbc_fetch_array($rst_generaFactura)){
        $res=trim($data['MENSAJE']);
    }
    odbc_free_result($rst_generaFactura);
    fwrite($file,"".PHP_EOL);
    fwrite($file,"Resultado: ".$res.PHP_EOL);
    
    fwrite($file,"----------------------------------------------------------".PHP_EOL);
    fwrite($file,"--------------Fin Guardando datos cartaPorte--------------".PHP_EOL);
    fwrite($file,"----------------------------------------------------------".PHP_EOL);

    fclose($file);

    echo $res;
?>