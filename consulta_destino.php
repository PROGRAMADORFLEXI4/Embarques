<?php
include("conectabd.php");

    $valorSelect = isset($_POST["valorSelect"])?$_POST["valorSelect"]:"";
    $codCliente = isset($_POST["codCliente"])?$_POST["codCliente"]:"";
    $diremb = isset($_POST["diremb"])?$_POST["diremb"]:"";
    $codFletera = isset($_POST["codFletera"])?$_POST["codFletera"]:"";
    $str_destino ="";
    $res="<option value='-1'>Selecciona una opción</option>";
    switch ($valorSelect) {
        case 1:
            $str_destino ="SELECT '[SUCURSAL] - '+Z79_DESC AS 'CLAVE',Z79_CP AS 'CP',Z79_CODFLE AS 'FLETERA',R_E_C_N_O_ FROM Z79010 WHERE D_E_L_E_T_='' AND Z79_CODFLE='".$codFletera."';";
            break;
        case 0:
            $res="";
            if($diremb=="FISCAL"){
                $str_destino ="SELECT '[$diremb] - '+A1_END AS 'CLAVE',A1_CEP AS 'CP',R_E_C_N_O_ FROM SA1010 WHERE D_E_L_E_T_='' AND A1_COD='".$codCliente."';";
            }else{
                $str_destino ="SELECT '['+RTRIM(LTRIM(ZD1_CLAVE)) +'] - '+ ZD1_DIRECC AS 'CLAVE',ZD1_CP AS 'CP',ZD1_FLETE AS 'FLETERA',R_E_C_N_O_ FROM ZD1010 WHERE D_E_L_E_T_='' AND ZD1_CLIENT='".$codCliente."' AND ZD1_CLAVE='".$diremb."';";
            }
            break;
        
        default:
            # code...
            break;
    }

    $sql_destino=odbc_exec($conn, $str_destino) or die("Error al obtener el destino");
    while($data=odbc_fetch_array($sql_destino)){
        $res .="    <option value='".trim($data['R_E_C_N_O_'])."'>".trim($data['CLAVE'])."</option>";
    }
    odbc_free_result($sql_destino);

    echo $res;
?>