<?php

	include("conectabd.php");
	//para GDL-NET
	require_once("class.phpmailer.php"); 
	require_once("class.smtp.php");
	
	include("send_mail_365.php");

    $fletePedido="";
    $fleteNueva="";
	$result ="";
	
	$cvincul =str_replace("!","'", $_POST['pedidosVin']); 

    $rstPedid = odbc_exec($conn,"select A2_NOME from sa2010 where d_e_l_e_t_='' and a2_cod='".$_POST['flePedi']."'; ") or die("Error en consulta de provedores");
	if($pedi=odbc_fetch_array($rstPedid)){
        $fletePedido=trim($pedi['A2_NOME']);
    }

    $rstNueva = odbc_exec($conn,"select A2_NOME from sa2010 where d_e_l_e_t_='' and a2_cod='".$_POST['fletera']."'; ") or die("Error en consulta de provedores");
	if($nuev=odbc_fetch_array($rstNueva)){
        $fleteNueva=trim($nuev['A2_NOME']);
    }


    $rst_actualiza ="update sc5010 set c5_transp='".$_POST['fletera']."', c5_costfl='".$_POST['costo']."', c5_porfle='".$_POST['porcentaje']."' where d_e_l_e_t_='' and c5_num in (".$cvincul.") ";

    if (odbc_exec($conn, $rst_actualiza)) {

        $complemento="
        Embarques cambio la fletera recomendada por sistema del pedido: <strong>".$_POST['pedido']."</strong>, el cliente: <strong>".$_POST['cliente']."</strong> tenia la fletera: <strong>".$fletePedido."</strong> y se cambio por <strong>".$fleteNueva."</strong>.<br><br>

        Tenga presente que esta cuenta no es monitoreada, así que no responda a este correo.";

          
        if (enviar_correo(
            trim('noreplay@fleximatic.com.mx'),
            trim('Flexinet'),
            $complemento,
            "Cambio de fletera en el pedido: ".$_POST['pedido'],
            explode(';', 'gerentecomercial@fleximatic.com.mx'),
            '', '', '', '', '', '', '')	) {

            $res = "CORRECTO";
            
        }else{

            $res = "ERRORCORREO";
            
        } 

 
    }else{
        $res = "ERROR";
    }


    echo $res;

?>