<?php
include("conectabd.php");

$factura = isset($_POST["factura"])?$_POST["factura"]:"";
$flag=false;
$res="";
$pesoTot=0;
$cVersionCFDI ="3.3";

$query=odbc_exec($conn,"SELECT COR_CFDI FROM COR010 WHERE D_E_L_E_T_='';");
if($data=odbc_fetch_array($query)){ 
    $cVersionCFDI=trim($data['COR_CFDI']);
}
odbc_free_result($query);

$rst_encabezado=odbc_exec($conn, "SELECT *,REPLACE(CONVERT(VARCHAR(10), CONVERT(DATETIME, Z82_EMISSA, 101), 103), '/', '')+Z82_HORA AS 'EMISSA',REPLACE(CONVERT(VARCHAR, CONVERT(DATETIME, Z82_FECHEN, 3),20),' ','T') AS 'FECHEN',REPLACE(CONVERT(VARCHAR, CONVERT(DATETIME, Z82_FYHRLE, 3),20),' ','T') AS 'FYHRLE' FROM Z82010 WHERE D_E_L_E_T_='' AND Z82_DOC='$factura';") or die("Error al consultar encabezadoFactura");
if($data=odbc_fetch_array($rst_encabezado)){
    $flag=true;
    $encabezado[]=array(
        "folioComprobante" => intval($data['Z82_DOC']), 
        "serieComprobante" => trim($data['Z82_SERIE']),
        "fechaHora" => trim($data['EMISSA']),
        "formaPago" => trim($data['Z82_FPAGO']),
        "metodoPago" => trim($data['Z82_MPAGO']),
        "TotalDistRec" => round($data['Z82_DISTAN'],2),
        "FechaHoraSalida" => trim($data['FECHEN']),
        "RFCDestinatario" => trim($data['Z82_RFCDES']),
        "NombreDestinatario" => trim($data['Z82_NOMDES']),
        "FechaHoraProgLlegada" => trim($data['FYHRLE']),
        "Calle" => trim($data['Z82_CALLE']),
        "cmunicipio" => trim($data['Z82_CMNPIO']),
        "Estado" => trim($data['Z82_ESTADO']),
        "CodigoPostal" => trim($data['Z82_CP']),
        "NumTotalMercancias" => round($data['Z82_NPARTI'],0),
        "PermSCT" => trim($data['Z82_CLASCT']),
        "NumPermisoSCT" => trim($data['Z82_NPERMI']),
        "NombreAseg" => trim($data['Z82_NOMASE']),
        "NumPolizaSeguro" => trim($data['Z82_POLIZA']),
        "ConfigVehicular" => trim($data['Z82_CONFVE']),
        "PlacaVM" => trim($data['Z82_PLACA']),
        "AnioModeloVM" => trim($data['Z82_MODEL']),
        "RFCOperador" => trim($data['Z82_RFCCHO']),
        "NumLicencia" => trim($data['Z82_LICENC']),
        "NombreOperador" => trim($data['Z82_NOMCHO'])
    );
}
odbc_free_result($rst_encabezado);

// number_format(round($data['Z83_QUANT']*$data['Z83_PESO'],3), 3, ".", ",") 

if($flag){
    $flag=false;
    $rst_detalle=odbc_exec($conn, "SELECT * FROM Z83010 WHERE D_E_L_E_T_='' AND Z83_DOC='$factura';") or die("Error al consultar detalleFactura");
    while($data=odbc_fetch_array($rst_detalle)){
        $flag=true;
        $detalle[]=array(
            "codigo" => trim($data['Z83_COD']), 
            "descripcion" => trim($data['Z83_DESC']),
            "unidad" => trim($data['Z83_UM']),
            "cantidad" => round($data['Z83_QUANT'],2),
            "ClaveProdServSAT" => trim($data['Z83_CODSAT']),
            "ClaveUnidadSAT" => trim($data['Z83_CLASAT']), 
            "PesoEnKg" => number_format(round($data['Z83_QUANT']*$data['Z83_PESO'],3), 3, ".", ","), 
            "ValorMercancia" => round($data['Z83_QUANT']*$data['Z83_PRCVEN'],2),
            "ObjetoImp" => "01" 
        );
        $pesoTot+=round($data['Z83_QUANT']*$data['Z83_PESO'],3);
    }
    odbc_free_result($rst_detalle);

    if($flag){
        if ($cVersionCFDI == "4.0"){
            foreach ($encabezado as $key => $value) {
                $file = fopen("txtFacturas/".$value['serieComprobante'].$value['folioComprobante'].".txt", "a");
                fwrite($file,"SFERP|6.0|".PHP_EOL);
                fwrite($file,"Comprobante|".$value['folioComprobante']."|".$value['serieComprobante']."|1||||".PHP_EOL);
                fwrite($file,"Generales|".$value['fechaHora']."||||601|||45645|".PHP_EOL);
                fwrite($file,"Divisa||XXX||".PHP_EOL);
                fwrite($file,"Receptor|FLEXIMATIC|FLE980113E95|||||S01|601|".PHP_EOL);
                fwrite($file,"DireccionFiscal|Mexico|Jalisco|Tlajomulco de Zúñiga|Tlajomulco de Zúñiga|Camino Real de Colima|901|14|Santa Anita|45645|".PHP_EOL);
                foreach ($detalle as $key2 => $value2) {
                    fwrite($file,"Concepto|".$value2['codigo']."|".$value2['descripcion']."|".$value2['unidad']."|".$value2['cantidad']."|0||0||||".$value2['ClaveProdServSAT']."|".$value2['ClaveUnidadSAT']."|".$value2['ObjetoImp']."|".PHP_EOL);
                }
                fwrite($file,"Totales|0||||0|".PHP_EOL);
                If (!empty($value['NumPermisoSCT'])){ //Si permiso tiene información genera el complemento CartaPorte
                    fwrite($file,"Complemento|CartaPorte20|".PHP_EOL);
                    fwrite($file,"CartaPorte|No||||".$value['TotalDistRec']."|".PHP_EOL);
                    fwrite($file,"Ubicacion|Origen||FLE980113E95|FLEXIMATIC||||||".$value['FechaHoraSalida']."|||".PHP_EOL);
                    fwrite($file,"DomicilioUbicacion|Camino Real de Colima||||||097|JAL|MEX|45645|".PHP_EOL);
                    fwrite($file,"Ubicacion|Destino||".$value['RFCDestinatario']."|".$value['NombreDestinatario']."||||||".$value['FechaHoraProgLlegada']."||".$value['TotalDistRec']."|".PHP_EOL);         
                    fwrite($file,"DomicilioUbicacion|".$value['Calle']."||||||".$value['cmunicipio']."|".$value['Estado']."|MEX|".$value['CodigoPostal'].PHP_EOL);
                    fwrite($file,"Mercancias|".$pesoTot."|KGM||".$value['NumTotalMercancias']."||".PHP_EOL);
                    foreach ($detalle as $key2 => $value2) {
                        fwrite($file,"Mercancia|".$value2['ClaveProdServSAT']."||".$value2['descripcion']."|".$value2['cantidad']."|".$value2['ClaveUnidadSAT']."|".$value2['unidad']."||||||".$value2['PesoEnKg']."|0|MXN|||".PHP_EOL);
                    }
                    fwrite($file,"Autotransporte|".$value['PermSCT']."|".$value['NumPermisoSCT']."|".PHP_EOL);
                    fwrite($file,"IdentificacionVehicular|".$value['ConfigVehicular']."|".$value['PlacaVM']."|".$value['AnioModeloVM']."|".PHP_EOL);
                    fwrite($file,"Seguros|".$value['NombreAseg']."|".$value['NumPolizaSeguro']."||||||".PHP_EOL);
                    fwrite($file,"TiposFigura|01|".$value['RFCOperador']."|".$value['NumLicencia']."|".$value['NombreOperador']."|||".PHP_EOL);
                }
                fclose($file);
            }
        }elseif($cVersionCFDI == "3.3"){
            foreach ($encabezado as $key => $value) {
                $file = fopen("txtFacturas/".$value['serieComprobante'].$value['folioComprobante'].".txt", "a");
                fwrite($file,"SFERP|6.0|".PHP_EOL);
                fwrite($file,"Comprobante|".$value['folioComprobante']."|".$value['serieComprobante']."|1|||".PHP_EOL);
                fwrite($file,"Generales|".$value['fechaHora']."||||601|||45645|".PHP_EOL);
                fwrite($file,"Divisa||XXX||".PHP_EOL);
                fwrite($file,"Receptor|FLEXIMATIC|FLE980113E95|||||P01|".PHP_EOL);
                foreach ($detalle as $key2 => $value2) {
                    fwrite($file,"Concepto|".$value2['codigo']."|".$value2['descripcion']."|".$value2['unidad']."|".$value2['cantidad']."|0||0||||".$value2['ClaveProdServSAT']."|".$value2['ClaveUnidadSAT']."|".PHP_EOL);
                }
                fwrite($file,"Totales|0||||0|".PHP_EOL);
                If (!empty($value['NumPermisoSCT'])){ //Si permiso tiene información genera el complemento CartaPorte
                    fwrite($file,"Complemento|CartaPorte20|".PHP_EOL);
                    fwrite($file,"CartaPorte|No||||".$value['TotalDistRec']."|".PHP_EOL);
                    fwrite($file,"Ubicacion|Origen||FLE980113E95|FLEXIMATIC||||||".$value['FechaHoraSalida']."|||".PHP_EOL);
                    fwrite($file,"DomicilioUbicacion|Camino Real de Colima||||||097|JAL|MEX|45645|".PHP_EOL);
                    fwrite($file,"Ubicacion|Destino||".$value['RFCDestinatario']."|".$value['NombreDestinatario']."||||||".$value['FechaHoraProgLlegada']."||".$value['TotalDistRec']."|".PHP_EOL);         
                    fwrite($file,"DomicilioUbicacion|".$value['Calle']."||||||".$value['cmunicipio']."|".$value['Estado']."|MEX|".$value['CodigoPostal'].PHP_EOL);
                    fwrite($file,"Mercancias|".$pesoTot."|KGM||".$value['NumTotalMercancias']."||".PHP_EOL);
                    foreach ($detalle as $key2 => $value2) {
                        fwrite($file,"Mercancia|".$value2['ClaveProdServSAT']."||".$value2['descripcion']."|".$value2['cantidad']."|".$value2['ClaveUnidadSAT']."|".$value2['unidad']."||||||".$value2['PesoEnKg']."|0|MXN|||".PHP_EOL);
                    }
                    fwrite($file,"Autotransporte|".$value['PermSCT']."|".$value['NumPermisoSCT']."|".PHP_EOL);
                    fwrite($file,"IdentificacionVehicular|".$value['ConfigVehicular']."|".$value['PlacaVM']."|".$value['AnioModeloVM']."|".PHP_EOL);
                    fwrite($file,"Seguros|".$value['NombreAseg']."|".$value['NumPolizaSeguro']."||||||".PHP_EOL);
                    fwrite($file,"TiposFigura|01|".$value['RFCOperador']."|".$value['NumLicencia']."|".$value['NombreOperador']."|||".PHP_EOL);
                }
                fclose($file);
            }
        }

        $res="CORRECTO";
    }else{
        $res="ERROR_DETA";
    }
}else{
    $res="ERROR_ENCA";
}


echo $res;


?>