<!DOCTYPE html>
<html>
 <head>

 <style>
    #customers {
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #customers td, #customers th {
        border: 1px solid #ddd;
        padding: 6px;
    }

    #customers th {
        padding-top: 9px;
        padding-bottom: 9px;
        text-align: center;
        background-color: #A4A4A4;
        color: white;
    }

</style>


  <link href="css/styles.css" rel="stylesheet" />
  <title>Otras Rutas</title>
	<script src="css/jquery.js"></script>
	<link rel="shortcut icon" href="images/icono.ico"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body>
<center>
<?php

    include("conectabd.php");
    
	
     //usados
    $cDiremb="";
    $cCodFletera =""; 
    $cPediFletera =""; 
    $cFletCliente =""; 
 	$cFletera ="";  
    $cDescCobro=""; 
 	$cCp ="";  
 	$cTipoCobro ="";  
 	$nValPedido =0;  
 	$nFactornoVariable =0;  
 	$nIVA =16;  
 	$nPorcAceptado=0;
    $lPorcAceptado=false;


    $cEstado=""; 
    $cMunicipio=""; 
    $nCobroDestino =0; 
    $nCajaCobroDestinoMultiple=0;  
    $nDiasEntrega =0;   
    $nEADFleteraPorcentaje =0;  

    $nMtrs3Xclasificacion =0;  
    $nPesoVolXclasificacion =0;  
    $nCodigosVendidos =0;  
    $nTotalpesoVolumenCobrar =0;  //Este es el Total de metros cubicos
    $nCostoFlete =0;  
    
    $nTotCajas =0; 
    $nTotTinacos =0; 
    $nTotExhibidores =0;  
    $nTotLavaderos =0;  
    $nTotCostales =0; 
    $nCajaPesoVolumen =0;
    $nTinacoPesoVolumen =0;
    $nExhibidorPesoVolumen =0;
    $nLavaderoPesoVolumen =0;
    $nCostalPesoVolumen =0;


    $nCodigosTotales =0;
    $nPesototalPedido =0;
    $nEADCostofletera =0;
    $nEECostocliente = 0;
    $nIvaFlete = 0;
    $nTotalCostoFlete = 0;
    $nIVATotalCostoFlete = 0;
    $nPorcPedido = 0;
    

	
//-----Titulos------------

	$cVNumPed="";  
	$cVDirEmb="";     

	$nVcajas=0;    
	$nVTinacos=0;   
	$nVExhibidores=0;  
	$nVLavaderos=0;  
	$nVCostales=0;   

    $cVEstado=""; 
    $cVMunicipio="";   
    $nVPesoTot = 0;
    $nVButosTotal = 0;

    $conta=0;

    //NUEVAS VARIABLES
    $lExiste = True; //verifica si existe el codigo postal en sistema
    $cQueryCli ="";
    $lBloqFl = "";
    $cClasificacion ="";
    $lCheckCostal = "";
    $nPorCombustible = 0;
    $nCostoCombustible=0;
    $nDato = 0;

    //Ultimas variables
    $nValor = 0;
    $nentero = 0;
    $ndecimal= 0;
    $ncajaMultiple=0;
    $csqlcajaMulti="";
    $nsumaCaja=0;
    $lExistCFlexi=False;
    $lContado=false;

    $nCostPista=0;
    $nPorcManiobras=0;
    $nTotalMtrs3=0;
    $nExManiobras=0;
    $nExRecoleccion=0;
    $npesoMultiple=0;

    $nKilosVolumetricos=0;
    $nCostServicio=0;
    $nCostEntreSegura=0;


    $num_ped = trim(base64_decode($_GET['Ped']));
    //$ord_sur = trim(base64_decode($_GET['ordsur']));


    
    $cvincul=""; 
    $lvincul=False;
    $cNumPed="";


    $nTotCerradaMtrs3=0;
    $nTotCajasCerradas=0;
    $nTotPesoCerradas=0;
    $nMultipleMtrs3=0;
    $nTotMultipleMtrs3=0;
    $nTotPesoMultiple=0;
    $nM3CajaMultiple=0;
    $cDescCajMultiple="";
    $ctipo_cli="";

    $lTarifario=false;
    $nKgVol=0;
    $nCosMin=0;
   

    
	//-----------------PARAMETROS DE FLETERA--------------------------------------------------------------------
    /*
	$sql_consulta_parametros = "SELECT COR_MINFLE,COR_CFMLOC,COR_PMLOC,COR_CFMFOR FROM COR010 WHERE D_E_L_E_T_='';";

    $ver_parametros= odbc_exec($conn,$sql_consulta_parametros);

    if ($paramet = odbc_fetch_array($ver_parametros)) {

        $nPorcAceptado=floatval($paramet['COR_MINFLE']);

    }
    odbc_free_result($ver_parametros);
   */

   if ($num_ped <> "") {


        /**CONSULTA PARA OBTENER EL PEDIDO VINCULADO */
        $qPedido = odbc_exec($conn, "SELECT C5_NUM,C5_EMBCPED from sc5010 where d_e_l_e_t_='' and c5_num='".$num_ped."' ") or die("Error al ejecutar la consulta:qPedido");
        if (odbc_num_rows($qPedido) > 0) { 
                $pedid = odbc_fetch_array($qPedido);

                $cNumPed = trim($pedid['C5_NUM']); 

                $cVNumPed = trim($pedid['C5_EMBCPED']); 

        }
        odbc_free_result($qPedido);

    
        /*ESTAS VALIDACIONES OBTIENE TODOS LOS PEDIDOS VINCULADOS */
        if ($cVNumPed <> "") {  


            $qVinculPedido = odbc_exec($conn, "SELECT C5_NUM,C5_EMBCPED from sc5010 where d_e_l_e_t_='' and c5_num='".$cVNumPed."' ") or die("Error al ejecutar la consulta:qVinculPedido");
            if (odbc_num_rows($qVinculPedido) > 0) { 

                $vinc = odbc_fetch_array($qVinculPedido);
            
                $c1NumPed = trim($vinc['C5_NUM']); 
            
                $cV1NumPed = trim($vinc['C5_EMBCPED']);        
            
            
                if ($cNumPed == $cV1NumPed) {
                
                
                    $cvincul .= "'".$cNumPed."', '".$cVNumPed."', '";    
                    

                    $q1VinculPedido = odbc_exec($conn, "SELECT C5_NUM,C5_EMBCPED from sc5010 where d_e_l_e_t_='' and C5_EMBCPED='".$cVNumPed."' ") or die("Error al ejecutar la consulta:q1VinculPedido");
                    if (odbc_num_rows($q1VinculPedido) > 0) {
                
                        while ($vinc1 = odbc_fetch_array($q1VinculPedido)) {
                        
                        
                            if ($cNumPed <> trim($vinc1['C5_NUM']) AND $cVNumPed <> trim($vinc1['C5_NUM'])  ){   
                            
                                    $cvincul .= trim($vinc1['C5_NUM'])."', '";  
                                    
                                    $lvincul=True;
                                
                            }
                        
                        }
                    
                        if ($lvincul== false){
                    
                            $q2VinculPedido = odbc_exec($conn, "SELECT C5_NUM,C5_EMBCPED from sc5010 where d_e_l_e_t_='' and C5_EMBCPED='".$cNumPed."' ") or die("Error al ejecutar la consulta:q2VinculPedido");
                            if (odbc_num_rows($q2VinculPedido) > 0) {
                        
                                while ($vinc2 = odbc_fetch_array($q2VinculPedido)) {
                                
                                
                                    if ($cNumPed <> trim($vinc2['C5_NUM']) AND $cVNumPed <> trim($vinc2['C5_NUM'])){    
                                    
                                        $cvincul .= trim($vinc2['C5_NUM'])."', '";      
                                        
                                    }
                                
                                } 
                            }
                            odbc_free_result($q2VinculPedido);
                        }
                    

                    }
                    odbc_free_result($q1VinculPedido);
 
                }else{
                
                    $q1VinculPedido = odbc_exec($conn, "SELECT C5_DIREMB,C5_NUM,C5_EMBCPED from sc5010 where d_e_l_e_t_='' and c5_num='".$cV1NumPed."' ") or die("Error al ejecutar la consulta:q1VinculPedido");
                    if (odbc_num_rows($q1VinculPedido) > 0) { 
            
                        $vinc1 = odbc_fetch_array($q1VinculPedido);
                    
                        $cV2NumPed = trim($vinc1['C5_EMBCPED']);
                    
                        if ($c1NumPed == $cV2NumPed ){         
  
                            $cvincul .= "'".$c1NumPed."', '".$cV1NumPed."', '";    
                            
                            $q2VinculPedido = odbc_exec($conn, "SELECT C5_NUM,C5_EMBCPED from sc5010 where d_e_l_e_t_='' and C5_EMBCPED='".$cV1NumPed."' ") or die("Error al ejecutar la consulta:q2VinculPedido");
                            if (odbc_num_rows($q2VinculPedido) > 0) {
                        
                                while ($vinc2 = odbc_fetch_array($q2VinculPedido)) {
                                
                                
                                    if ($c1NumPed <> trim($vinc2['C5_NUM']) AND $cV1NumPed <> trim($vinc2['C5_NUM'])){    
                                    
                                            $cvincul = trim($vinc2['C5_NUM'])."', '";  
                                            
                                            $lvincul=True;    
                                        
                                    }
                                
                                }
                            
                                if ($lvincul== False){
                            
                                    $q3VinculPedido = odbc_exec($conn, "SELECT C5_NUM,C5_EMBCPED from sc5010 where d_e_l_e_t_='' and C5_EMBCPED='".$c1NumPed."' ") or die("Error al ejecutar la consulta:q3VinculPedido");
                                    if (odbc_num_rows($q3VinculPedido) > 0) {
                                
                                        while ($vinc3 = odbc_fetch_array($q3VinculPedido)) {
                                        
                                        
                                            if ($c1NumPed <> trim($vinc3['C5_NUM'])  and $cV1NumPed <> trim($vinc3['C5_NUM'])) { 
                                            
                                                $cvincul .= trim($vinc3['C5_NUM'])."', '";      
                                                
                                            }
                                        
                                        }
                                    }
                                    odbc_free_result($q3VinculPedido);
                                }
                            
            
                            }
                            odbc_free_result($q2VinculPedido);

                        }
                    
                    }
                    odbc_free_result($q1VinculPedido);
                
                } 
                
            }
            odbc_free_result($qVinculPedido);
            

                                        
            $cvincul = substr($cvincul,0,-3 );

            $cMenViculado="
            <div class='container' style='width:1000px; margin:0px auto;overflow:hidden'>
                <h2>NOTA: El pedido tiene los siguientes pedidos vinculados: ".str_replace("'","", $cvincul)."</h2>
            </div>
            ";
                        
        }else{
            $cvincul="'".$num_ped."'";

            $cMenViculado="";
        }

        /*FIN: ESTAS VALIDACIONES OBTIENE TODOS LOS PEDIDOS VINCULADOS */


        $res ="<h1>NUEVAS FLETERAS</h1>";

        $strGeneral="
        SELECT SUM(C6VALMERC) AS 'C6_VALMERCPUBL', CLASIFICACION, DESCCLASIFICACION, LARGO, ALTO, ANCHO, ROUND((LARGO  * ALTO * ANCHO ),5) AS METROSCUBICOSxCAJA, round((LARGO  * ALTO * ANCHO ) * SUM(CajasCerradas),5) AS 'TOTM3xCERRADAS'  ,round(sum(M3xMultiple),5) as 'TOTM3xMULTIPLE',SUM(CajasCerradas) as 'CAJASCERRADAS', round(sum(CajasMultiples),4)  as 'CAJASMULTIPLES',
        ROUND(SUM(PESOCERRADAS),4) AS PESOCERRADAS, ROUND(SUM(PESOMULTIPLE),4) AS PESOMULTIPLE, COSTAL, C5_CLIENTE, C5_TRANSP,C5_DIREMB,A1_BLOQFL, A1_TRANSP,A1_CEP, ZD1_CP,A1_TIPO
        FROM (
        SELECT  C6_ITEM, 
                C5_NUM, 
               CASE WHEN F2_VALBRUT IS NULL THEN 0 ELSE F2_VALBRUT END F2_VALBRUT,
               C5_VALMERC,
               C6_PRUNIT,
               (C6_QTDVEN*C6_PRUNIT) AS 'C6VALMERC',
               C6_PRODUTO, 
               B1_DESC,
               B1_BOLSA AS COSTAL,
               Z52_TIPO AS CLASIFICACION, -- 1=CAJA; 2=TINACO; 3=EXHIBIDOR; 4=LAVADERO;5=BULTO
               Z52_DESCRI AS DESCCLASIFICACION, 
               ROUND(Z52_LARGO,3) AS LARGO,
               ROUND(Z52_ANCHO,3) AS ALTO,
               ROUND(Z52_ALTO,3) AS ANCHO,
               ( ROUND(Z52_LARGO,3)  * ROUND(Z52_ALTO,3) * ROUND(Z52_ANCHO,3) ) AS METROSCUBICOS,
               B1_TIPO,
               C5_CLIENTE,
               C5_DIREMB,
               C5_TRANSP,
               A1_TIPO,
               A1_BLOQFL,
               A1_TRANSP,
               A1_CEP,
               ZD1_CP,
               ROUND(B1_PESONET,4) AS B1_PESONET,
               ROUND(Z52_PESO,4) AS KGCAJASOLA,
               ROUND(B1_PNCAJA,4) AS KGPESONETOCAJA,
               ROUND(B1_PBCAJA,4) AS KGPESOBRUTOCAJA,
               C6_QTDVEN AS CANTVENDIDA,
               ROUND(B1_QE,4) AS CANTXCAJA, 
               ROUND(B1_CANTBOL,4) AS CANTXBOLSA, 
               ROUND(C6_QTDVEN / CASE 
                             WHEN B1_QE = 0 THEN 1 
                             ELSE ROUND(B1_QE,4) 
                           END,4)    AS CODIGOSVENDIDOS,
              floor(       ROUND(C6_QTDVEN / CASE 
                             WHEN B1_QE = 0 THEN 1 
                             ELSE ROUND(B1_QE,4) 
                           END,4)) as 'CajasCerradas',
              ROUND(C6_QTDVEN / CASE 
                             WHEN B1_QE = 0 THEN 1 
                             ELSE ROUND(B1_QE,4) 
                           END,4) - floor(       ROUND(C6_QTDVEN / CASE 
                             WHEN B1_QE = 0 THEN 1 
                             ELSE ROUND(B1_QE,4) 
                           END,4)) as  'CajasMultiples',
              ROUND( ( (ROUND(Z52_LARGO,3)  * ROUND(Z52_ALTO,3) * ROUND(Z52_ANCHO,3) )/ROUND(CASE WHEN B1_QE = 0 THEN 1 ELSE ROUND(B1_QE,4) END,4) ) * (CAST(C6_QTDVEN AS INT)%CAST(ROUND(CASE WHEN B1_QE = 0 THEN 1 ELSE ROUND(B1_QE,4) END,4) AS INT)),5) AS 'M3xMultiple',
              ROUND((B1_PESONET * B1_QE) * 		floor(       ROUND(C6_QTDVEN / CASE 
                             WHEN B1_QE = 0 THEN 1 
                             ELSE ROUND(B1_QE,4) 
                           END,4)) ,4) AS 'PESOCERRADAS',
              ROUND((B1_PESONET * B1_QE) * 	  (ROUND(C6_QTDVEN / CASE 
                             WHEN B1_QE = 0 THEN 1 
                             ELSE ROUND(B1_QE,4) 
                           END,4) - floor(       ROUND(C6_QTDVEN / CASE 
                             WHEN B1_QE = 0 THEN 1 
                             ELSE ROUND(B1_QE,4) 
                           END,4))) ,4) AS 'PESOMULTIPLE'
        FROM   SC5010 C5 --PEDIDOS DE VENTA
               LEFT JOIN SF2010 F2 --FACTURA
                       ON C5_NOTA=F2_DOC AND F2.D_E_L_E_T_=''
               INNER JOIN SC6010 C6 --ITEMS DE LOS PEDIDOS DE VENTA
                       ON C5_NUM = C6_NUM 
                          AND C6.D_E_L_E_T_ = '' 
               INNER JOIN SB1010 B1  --DESCRIPCION DE LOS PRODUCTOS
                       ON C6_PRODUTO = B1_COD 
                          AND B1.D_E_L_E_T_ = '' 
               INNER JOIN SA1010 A1 --CLIENTES
                      ON C5_CLIENTE = A1_COD 
                         AND A1.D_E_L_E_T_ = '' 
               LEFT JOIN ZD1010 ZD1 --DIRECCION EMBARQUES
                      ON C5_DIREMB = ZD1_CLAVE
                         AND C5_CLIENTE=ZD1_CLIENT
                         AND ZD1.D_E_L_E_T_ = ''
               LEFT JOIN Z52010 Z52  --CLASIFICACION PRODUCTO
                      ON B1_DESCLAS = Z52_DESCRI AND Z52.D_E_L_E_T_=''
        WHERE  C5_NUM in (".$cvincul.") 
               AND C5.D_E_L_E_T_ = '') F
        GROUP BY DESCCLASIFICACION,CLASIFICACION,LARGO, ALTO, ANCHO, COSTAL, C5_CLIENTE, C5_TRANSP,C5_DIREMB,A1_TRANSP,A1_BLOQFL,A1_CEP,ZD1_CP,A1_TIPO;";

        $rstGeneral = odbc_exec($conn, $strGeneral) or die("Error al ejecutar la consulta:rstGeneral");
        if (odbc_num_rows($rstGeneral) > 0) {

            //obtiene datos de la caja multiple seleccionada en sistema
            $csqlcajaMulti = odbc_exec($conn, "SELECT * from Z52010 where d_e_l_e_t_='' and z52_multip='T'") or die("Error al ejecutar la consultacajaMultiple");
            if($cajMulti=odbc_fetch_array($csqlcajaMulti)){  

                $nM3CajaMultiple = $cajMulti['Z52_LARGO'] * $cajMulti['Z52_ANCHO'] * $cajMulti['Z52_ALTO'];
                $cDescCajMultiple= trim($cajMulti['Z52_DESCRI']);

            }
            odbc_free_result($csqlcajaMulti);
    
            while ($dato = odbc_fetch_array($rstGeneral)) {

                $ctipo_cli=trim($dato['A1_TIPO']);

                $cCliente=trim($dato['C5_CLIENTE']); //CLIENTE
                $cFletCliente=trim($dato['A1_TRANSP']); //Codigo de fletera del cliente
                $cDiremb = trim($dato['C5_DIREMB']);  //Dirección de embarque
                $nValPedido += trim($dato['C6_VALMERCPUBL']);  //Valor del Pedido
                $cPediFletera =  trim($dato['C5_TRANSP']);
                $lBloqFl = trim($dato['A1_BLOQFL']);

                $nMultipleMtrs3+=$dato['TOTM3xMULTIPLE'];
                $nTotPesoMultiple+=$dato['PESOMULTIPLE'];

                $nTotCerradaMtrs3+=$dato['TOTM3xCERRADAS'];
                $nTotCajasCerradas+=$dato['CAJASCERRADAS'];
                $nTotPesoCerradas+=$dato['PESOCERRADAS'];


                //marca si se encontro la caja multiple en el pedido
                if (trim($dato['DESCCLASIFICACION'])==$cDescCajMultiple) {
                    $lExistCFlexi=True;
                }
                                
                if ($dato['CAJASCERRADAS']>0){

                    $arreglo[]=array("clasificacion" => $dato['CLASIFICACION'], "costal" => trim($dato['COSTAL']),"mtr3Cerrada" => $dato['TOTM3xCERRADAS'],"cajasCerradas" => $dato['CAJASCERRADAS'],"pesoCerradas" => $dato['PESOCERRADAS'],"descClasificacion" => trim($dato['DESCCLASIFICACION']));

                }else{
                    //se usa este arreglo unicamente si en el pedido no hay ninguna caja cerrada.
                    $arreglo1[]=array("clasificacion" => $dato['CLASIFICACION'], "costal" => trim($dato['COSTAL']),"mtr3Cerrada" => $dato['TOTM3xCERRADAS'],"cajasCerradas" => $dato['CAJASCERRADAS'],"pesoCerradas" => $dato['PESOCERRADAS'],"descClasificacion" => trim($dato['DESCCLASIFICACION']));

                }

                if ($cDiremb == 'FISCAL'){
                    $cCp = trim($dato['A1_CEP']); //codigo postal del cliente
                }else{
                    $cCp = trim($dato['ZD1_CP']); //codigo postal de la direccion de embarques
                }


            }

            //$res .= "Entero: ".$Tentero;

            // $res .= " CajaMultiple: ".($nMultipleMtrs3);

            $strconsultFiscal = "
            SELECT DDF_TDCLAS,DDF_COD,A2_NREDUZ,A2_TCOBRO,A2_FANOVAR, Z51_CODIGO,Z51_ESTADO,Z51_MNPIO,DDF_DIAS, DDF_COSTO, DDF_EAD as PORCENTEADFLE,DDF_BLOQFL, DDF_PORGAS,A2_PISTAS,A2_PORMANI,A2_COSTSER,A2_COSTOES,A2_TARIFAR,Z51_PORFLE,Z51_MONTPE FROM  DDF010 DDF 
            INNER JOIN  
                (SELECT Z51_CODIGO,Z51_ESTADO,Z51_MNPIO,Z51_PORFLE,Z51_MONTPE FROM Z51010 WHERE D_E_L_E_T_='' group by Z51_ESTADO,Z51_MNPIO,Z51_CODIGO,Z51_PORFLE,Z51_MONTPE) Z51
                    ON DDF_CP= Z51_CODIGO
            INNER JOIN
                SA2010 SA2 
                    ON DDF_COD=A2_COD AND SA2.D_E_L_E_T_=''
            WHERE  DDF_CP = '" . $cCp . "' AND A2_TCOBRO<>'' AND DDF_BLOQFL='F' ".$cQueryCli. "
                AND DDF.D_E_L_E_T_='';";
            $rstconsultFiscal = odbc_exec($conn, $strconsultFiscal) or die("Error al ejecutar la consulta2");

            //Se obtenen los datos de la tabla de destinos fleteras para este caso.
            if (odbc_num_rows($rstconsultFiscal) > 0) {

                while ($dato2 = odbc_fetch_array($rstconsultFiscal)) {

                    $cCodFletera=trim($dato2['DDF_COD']); //codigo de fletera
                    $cFletera = trim($dato2['A2_NREDUZ']); //nombre de fletera 
                    $cTipoCobro = trim($dato2['A2_TCOBRO']);  //Tipo de cobro
                    $nFactornoVariable = trim($dato2['A2_FANOVAR']);  //Factor no variable
                    $cEstado= trim($dato2['Z51_ESTADO']); //Estado
                    $cMunicipio= trim($dato2['Z51_MNPIO']);  //Municipio

                    $nEADFleteraPorcentaje = trim($dato2['PORCENTEADFLE']);  //Porcentaje EAD fletera
                    $nDiasEntrega = trim($dato2['DDF_DIAS']); //Dias de entrega
                    $nPorCombustible = $dato2['DDF_PORGAS'];  //Porcentaje gasto combustible
                    $nCostPista=$dato2['A2_PISTAS'];
                    $nPorcManiobras=$dato2['A2_PORMANI'];
                    $nCostServicio=$dato2['A2_COSTSER'];
                    $nCostEntreSegura=$dato2['A2_COSTOES'];

                    $nCostoFlete=0;
                    $nTotCajas =0; 
                    $nTotTinacos =0; 
                    $nTotExhibidores =0;  
                    $nTotLavaderos =0;  
                    $nTotCostales =0; 
                    $nCajaPesoVolumen =0;
                    $nTinacoPesoVolumen =0;
                    $nExhibidorPesoVolumen =0;
                    $nLavaderoPesoVolumen =0;
                    $nCostalPesoVolumen =0;
                    $nEECostocliente =0;
                    $nCobroDestino=0;

                    $nTotalCostoFlete = 0;
                    $nPorcPedido = 0;
                    $nIvaFlete = 0;
                    $nIVATotalCostoFlete = 0;
                    $nPorcPedido = 0;
                    $lTarifario=false;
                    $nKgVol=0;
                    $nCosMin=0;

                    $lContado=false;//contada caja multiple

                    if ($nTotCajasCerradas==0){
                        $arrfinal = $arreglo1; 
                    }else{
                        $arrfinal =  $arreglo; 
                    }

                    
                    ///////////////////////////////INICIO: PARAMETROS DE FLETERA////////////////////////////////////////////////////////
                    if(!$lPorcAceptado) {
                        $nPorcAceptado = floatval($dato2['Z51_PORFLE']);
                        $lPorcAceptado = true;
                    }
                    ///////////////////////////////FIN: PARAMETROS DE FLETERA////////////////////////////////////////////////////////


                    //busca el costo especial a entrega del cliente
                    $strEEcliente = "SELECT * FROM Z55010 where d_e_l_e_t_='' and z55_cod='".$cCliente."' and Z55_CODFLE='".$cCodFletera."' AND Z55_CP='" . $cCp . "' ; ";
                    $rstEEcliente = odbc_exec($conn, $strEEcliente) or die("Error en consulta de EECLIENTE");
                    
                    if($CEE=odbc_fetch_array($rstEEcliente)){
                
                        $nEECostocliente = $CEE['Z55_COBRO'];
    
                    }
                    odbc_free_result($rstEEcliente);
    

                    foreach ($arrfinal as $clave => $fila) {

                        $cClasificacion = $fila['clasificacion'];
                        $lCheckCostal = $fila['costal'];
                        $nMtrs3Xclasificacion = $fila['mtr3Cerrada'];

                        //valida el costo por clasificación

                        if (trim($dato2['A2_TARIFAR'])=='T'){ //En caso que obtenga el costo del tarifario

                            $lTarifario=true;
                        
                        }elseif(trim($dato2['DDF_TDCLAS'])=='T'){ //En caso que incluya el mismo costo para todas las clasificaciones asigna el costo general

                            $nCobroDestino = round($dato2['DDF_COSTO'],2);
        
                        }else{
                            
                            //Obtiene un costo diferente por clasificacion, obtiene cada costo
                            $strcostoClas = "SELECT * FROM Z53010 where d_e_l_e_t_='' AND Z53_CLASIF='".$fila['descClasificacion']."' AND Z53_CODFLE='".$cCodFletera."' AND Z53_CP='" . $cCp . "'; ";
                            $rstcostoClas = odbc_exec($conn, $strcostoClas) or die("Error en consulta de Costo Clasif");
                            
                            if($clasi=odbc_fetch_array($rstcostoClas)){
                        
                                $nCobroDestino = $clasi['Z53_COSTO'];
        
                            }
                            odbc_free_result($rstcostoClas);
                        }
                         
                        //----------------validacion cajas multiples--------------------------
                        $nsumaCaja=0;


                        //obtiene los metros cubicos multiples de los restos + m3 de 1 caja multiple
                        $nTotMultipleMtrs3 = $nMultipleMtrs3 + $nM3CajaMultiple;


                        //obtiene la cantidad de cajas multiples del pedido y redondea hacia arriba
                        $ncajaMultiple=ceil($nTotMultipleMtrs3/$nM3CajaMultiple);

                        //obtiene los metros cubicos reales con cajas multiples cerradas
                        $nTotMultipleMtrs3 = $ncajaMultiple * $nM3CajaMultiple;


                        if(trim($fila['descClasificacion'])==$cDescCajMultiple){ //en caso de existir la descripcion de caja multiple en el pedido, suma las cajas multiples, entra una unica vez.

                            $nsumaCaja=$ncajaMultiple; 

                        }else if ($lExistCFlexi==false && $lContado==false) {  //en caso de no existir en el pedido la descripcion de la caja Multiple, busca y obtiene la información de la caja multiple, entra solo una vez.
                            
                            /*------------BUSCA EL VALOR DE ALGUNA CAJA Multiple */

                            if ($dato2['DDF_TDCLAS']=='T'){ //si es un mismo costo para cualqueir clasificación

                                $nCajaCobroDestinoMultiple= $nCobroDestino;

                            }else{
                                    //busca el costo de la caja multiple,se ejecuta la consulta porque al entrar aqui no se sabe el costo de la caja multiple
                                    $strcostoMult = "SELECT Z52_LARGO,Z52_ANCHO,Z52_ALTO,Z52_DESCRI,Z53_CODFLE,Z53_CP,Z53_COSTO FROM
                                        z52010 z52
                                    inner join 
                                        z53010 z53 on z52.z52_descri = z53.z53_clasif and z53.d_e_l_e_t_=''
                                    where z52.d_e_l_e_t_='' and z52_multip='T' AND Z53_CODFLE='".$cCodFletera."' AND Z53_CP='" . $cCp . "'; ";
                                    $rstcostoMult = odbc_exec($conn, $strcostoMult) or die("Error en consulta de de Costo Clasif2");
                                    
                                    if($cMult=odbc_fetch_array($rstcostoMult)){
                                
                                        $nCajaCobroDestinoMultiple = $cMult['Z53_COSTO'];

                                    }
                                    odbc_free_result($rstcostoMult);
                            }

                            
                            if($cTipoCobro == "1"){
                                    
                                $nTotCajas += $ncajaMultiple; //suma las cajas multiples 
                                
                            }elseif ($cTipoCobro == "2"){

                                $nCodigosVendidos = $ncajaMultiple;
                                $nCostoFlete += $nCodigosVendidos * $nCajaCobroDestinoMultiple;
                                $nTotCajas += $nCodigosVendidos;  
                            
                            }

                            $lContado=true;
                            
                        }
                            
                        //-----fin validacion cajas multiples--------

                        if($cTipoCobro == "1"){
                            $cDescCobro="Volumen/Peso"; 

                            if($cClasificacion == "1"){ //Cajas

                                $nCodigosVendidos = $fila['cajasCerradas']  + $nsumaCaja;
                                $nTotCajas += $nCodigosVendidos; 
                                $nCostalPesoVolumen += $fila['pesoCerradas'];

                            }Elseif ($cClasificacion == "2"){ //Tinacos

                                $nCodigosVendidos = $fila['cajasCerradas'];
                                $nTotTinacos += $nCodigosVendidos; 
                                $nCostalPesoVolumen += $fila['pesoCerradas'];

                            }Elseif ($cClasificacion == "3"){ //Exhibidor

                                $nCodigosVendidos = $fila['cajasCerradas'];
                                $nTotExhibidores += $nCodigosVendidos; 
                                $nCostalPesoVolumen += $fila['pesoCerradas'];

                            }Elseif ($cClasificacion == "4"){ //Lavadero

                                $nCodigosVendidos = $fila['cajasCerradas'];
                                $nTotLavaderos += $nCodigosVendidos; 
                                $nCostalPesoVolumen += $fila['pesoCerradas'];
                                    
                            }Elseif ($cClasificacion == "5" ){ //Bulto

                                $nCodigosVendidos = $fila['cajasCerradas'];
                                $nTotCostales += $nCodigosVendidos; 
                                $nCostalPesoVolumen += $fila['pesoCerradas'];
                            } 

                            
                        }Elseif ($cTipoCobro == "2"){

                            $cDescCobro="Bulto/Caja";

                            if( $cClasificacion== "1"){ //Cajas

                                $nCodigosVendidos = ($fila['cajasCerradas']  + $nsumaCaja);
                                $nCostoFlete += $nCodigosVendidos * $nCobroDestino;
                                $nTotCajas += $nCodigosVendidos;  
                                $nCajaPesoVolumen +=$fila['pesoCerradas'];

                            }Elseif ($cClasificacion == "2"){ //Tinacos

                                $nCodigosVendidos = $fila['cajasCerradas'];
                                $nCostoFlete += $nCodigosVendidos * $nCobroDestino;
                                $nTotTinacos += $nCodigosVendidos;  
                                $nTinacoPesoVolumen += $fila['pesoCerradas'];

                            }Elseif ($cClasificacion == "3"){ //Exhibidor

                                $nCodigosVendidos = $fila['cajasCerradas'];
                                $nCostoFlete += $nCodigosVendidos * $nCobroDestino;
                                $nTotExhibidores += $nCodigosVendidos;  
                                $nExhibidorPesoVolumen += $fila['pesoCerradas'];

                            }Elseif ($cClasificacion == "4"){ //Lavadero

                                $nCodigosVendidos = $fila['cajasCerradas'];
                                $nCostoFlete += $nCodigosVendidos * $nCobroDestino;
                                $nTotLavaderos += $nCodigosVendidos;  
                                $nLavaderoPesoVolumen += $fila['pesoCerradas'];

                            }Elseif ($cClasificacion == "5"){ //Bulto

                                $nCodigosVendidos = $fila['cajasCerradas']; 
                                $nCostoFlete += $nCodigosVendidos * $nCobroDestino;
                                $nTotCostales += $nCodigosVendidos;  
                                $nCostalPesoVolumen += $fila['pesoCerradas'];    

                            }
                            
                        }
                    }


                    $nCodigosTotales = $nTotCajas +  $nTotTinacos + $nTotExhibidores + $nTotLavaderos + $nTotCostales;

                    $nPesototalPedido = $nCajaPesoVolumen + $nTinacoPesoVolumen + $nExhibidorPesoVolumen + $nLavaderoPesoVolumen + $nCostalPesoVolumen +$nTotPesoMultiple; 

                    $nTotalMtrs3 = round($nTotCerradaMtrs3 + $nTotMultipleMtrs3,2);
                                    
                    if ($cTipoCobro=="1"){ //Volumen-Peso

                        $nCostoManiobras= $nPorcManiobras; //no se obtiene porcentaje, se toma como costo de maniobras

                        $nIVA = $ctipo_cli<>"4"?$nIVA:0; //sin iva para exportacion
    
                        if($nFactornoVariable>0){
    
                            if($lTarifario){ //En caso que incluya el tarifario
    
                                $nKgVol= $nTotalMtrs3 * $nFactornoVariable; //obtiene los kilos volumetricos para obtener el costo
    
                                $strTarif = "SELECT * FROM Z73010 WHERE D_E_L_E_T_='' AND Z73_CODFLE='".$cCodFletera."' AND Z73_CP='".$cCp."' AND ('".$nKgVol."'>=Z73_RANGOA AND '".$nKgVol."'<=Z73_RANGOB); ";
                                $rstTarif = odbc_exec($conn, $strTarif) or die("Error en consulta de strTarif");
                                
                                if($datarif=odbc_fetch_array($rstTarif)){
                            
                                    $nCobroDestino = round($datarif['Z73_COSTO'],3);
                                    $nCosMin= round($datarif['Z73_COSMIN'],3);
    
                                }
                                odbc_free_result($rstTarif);
    
                                $nCostoFlete = $nKgVol * $nCobroDestino;
    
                            }else{
                                $nCostoFlete= $nCobroDestino * ($nTotalMtrs3*$nFactornoVariable);
                            }			
    
                        }else{
                            $nCostoFlete= $nCobroDestino * $nTotalMtrs3;
                        }
    
                        $nEADCostofletera = $nCostoFlete  * ($nEADFleteraPorcentaje/100);
    
                        $nCostoCombustible = $nCostoFlete * ($nPorCombustible/100);
    
                        $nTotalCostoFlete = $nCostoFlete + $nCostoManiobras + $nCostPista + $nEADCostofletera + $nCostoCombustible + $nCostServicio + $nCostEntreSegura + $nEECostocliente;
    
                        //Si aplica tarifario y no llega al costo minimo, colocar el costo minimo.
                        $nTotalCostoFlete = $lTarifario==true && $nTotalCostoFlete<=$nCosMin? $nCosMin: $nTotalCostoFlete;
    
                    }elseif ($cTipoCobro=="2"){ //Bulto o Caja

                        $nCostoManiobras = $nCostoFlete * ($nPorcManiobras/100);

                        $nIVA = $ctipo_cli<>"4"?$nIVA:0; //sin iva para exportacion

                        $nEADCostofletera = $nCostoFlete  * ($nEADFleteraPorcentaje/100);

                        $nCostoCombustible = $nCostoFlete * ($nPorCombustible/100);

                        if ($cCodFletera == "000058" || $cCodFletera == "002147" ){ //GDL MERIDA - FLETES DE REGRESO
                            //En la formula se indico que si el cliente cuenta con Entrega especial no debe sumar el costo de EAD
                            $nEADCostofletera= $nEECostocliente>0? 0:$nEADCostofletera;
                        }

                        $nTotalCostoFlete = $nCostoFlete + $nEECostocliente + $nEADCostofletera + $nCostoCombustible + $nCostPista + $nCostEntreSegura + $nCostoManiobras + $nCostServicio;
                    }

                    //$res .= "°°°°|||||°°°° ".$cCodFletera."cobro destino: ".$nCobroDestino." CajaMultiple: ".($nCajaCobroDestinoMultiple);

                    $nIvaFlete = $nTotalCostoFlete * ($nIVA/100);

                    $nIVATotalCostoFlete =  $nTotalCostoFlete + $nIvaFlete; //COSTO + IVA

                    $nPorcPedido = Round(($nTotalCostoFlete/$nValPedido) * 100,2);

                    
                    if ($cFletCliente==$cCodFletera){
                        $iconClient="C";
                    }else{
                        $iconClient="";	
                    }

                    if($nPorcPedido>$nPorcAceptado){
                        $icono="
                        <span class='badge' style='background-color:#dd0017; color:white;'>&nbsp;&nbsp;".$iconClient."&nbsp;&nbsp;</span>";
            
                    }else{
                        $icono="
                        <span class='badge' style='background-color:#dd0017; color:white;'>&nbsp;&nbsp;".$iconClient."&nbsp;&nbsp;</span>";
                    }
                    
                    $data[]=array("nomFlete" => $cFletera,"CodiFlete"=>$cCodFletera, "descCobro"=>$cDescCobro, "diasEntrega"=>intval($nDiasEntrega), "porcPedido"=>number_format($nPorcPedido,2),"totCostoFlete"=>number_format($nTotalCostoFlete,2),"ivaFlete"=>number_format($nIvaFlete,2),"totCostIVAFle"=>number_format($nIVATotalCostoFlete,2),"icono"=>$icono,"EntregaEspecial"=>$nEECostocliente);
                }
                
            }else{
                $lExiste = False;
            }
            odbc_free_result($rstconsultFiscal);
        }else{
            $lExiste = False;
        }
        odbc_free_result($rstGeneral);
        
        if ($lExiste==true){

            //-----------------------------------/ DATOS DEL PEDIDO /----------------------------------------------------------

            $res .="
            <!------------------------- GRUPO DE ELEMENTOS 1 ----------------------------------->		
            <div class='container' style='width:1000px; margin:20px auto;overflow:hidden; border-style:ridge;' >
                <h2>DATOS DEL PEDIDO</h2>

                ".$cMenViculado."

                <div class='container' style='width:1000px; margin:20px auto;overflow:hidden'>
                    <div class='row'>		

                        <div style='width:210px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>Pedido: </strong>
                            <input style='width:80px;background-color: #eee;border: 0' type='text' id='Pedido' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Pedido(s)'  value='".$num_ped."' disabled>
                        </div>

                        <div style='width:250px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>Dir Embarque:</strong>
                            <input style='width:120px' type='text' id='DirEmbarque' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Dir Embarque' value='" . $cDiremb . "' disabled>
                        </div>

                        <div style='width:250px; margin:0 auto;overflow:hidden; float: left;' >
                            <strong>CP:</strong>
                            <input style='width:80px' type='text' id='CP' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='CP' value='" . $cCp . "' disabled>
                        </div>

                        <div style='width:250px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>Costo Pedido sin IVA:</strong>
                            <input style='width:100px' type='text' id='CostoPedido' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Costo Pedido' value='$" . number_format($nValPedido,2) . "' disabled>
                        </div>

                    </div>
                </div>

                <!------------------------- GRUPO DE ELEMENTOS 2 ----------------------------------->		
                <div class='container' style='width:1000px; margin:20px auto;overflow:hidden'>
                    <div class='row'>		

                        <div style='width:180px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>N° Cajas: </strong>
                            <input style='width:80px' type='text' id='cantCajas' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Cajas' value='" . $nTotCajas . "' disabled>
                        </div>

                        <div style='width:200px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>N° Tinacos:</strong>
                            <input style='width:80px' type='text' id='cantTinacos' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Tinacos' value='" . $nTotTinacos . "' disabled>
                        </div>

                        <div style='width:200px; margin:0 auto;overflow:hidden; float: left;' >
                            <strong>N° Exhibidores:</strong>
                            <input style='width:80px' type='text' id='cantExhibidores' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Exhibidores' value='" . $nTotExhibidores . "' disabled>
                        </div>

                        <div style='width:200px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>N° Lavaderos:</strong>
                            <input style='width:80px' type='text' id='cantLavaderos' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Exhibidores' value='" . $nTotLavaderos . "' disabled>
                        </div>

                        <div style='width:200px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>N° Costales: </strong>
                            <input style='width:80px' type='text' id='cantCostales' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Costales' value='" . $nTotCostales . "' disabled>
                        </div>
                    </div>
                </div>
            
                
                <!------------------------- GRUPO DE ELEMENTOS 3 ----------------------------------->	
                <div class='container' style='width:1000px; margin:20px auto;overflow:hidden'>
                    <div class='row'>	

                        <div style='width:230px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>Estado: </strong>
                            <input style='width:150px' type='text' id='estado' class='form-control' pattern='[a-zA-Z0-9]' title='Estado' value='" . $cEstado . "' disabled>
                        </div>

                        <div style='width:280px; margin:0 auto;overflow:hidden; float: left;' >
                            <strong>Municipio:</strong>
                            <input style='width:200px' type='text' id='Municipio' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Municipio' value='" . $cMunicipio . "' disabled>
                        </div>

                        <div style='width:180px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>Peso Total (kg): </strong>
                            <input style='width:80px' type='text' id='pesoTotal' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Peso Total' value='" . number_format(round($nPesototalPedido, 2), 2) . "' disabled>
                        </div>

                    <div style='width:150px; margin:0 auto;overflow:hidden; float: left;'>
                        <strong>Metros Cubicos:</strong>
                        <input style='width:50px' type='text' id='bulTotal' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Metros cubicos' value='" . round($nTotalMtrs3,2) . "' disabled>
                    </div>

                        <div style='width:150px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>Bultos Totales:</strong>
                            <input style='width:50px' type='text' id='bulTotal' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Bultos totales' value='" . $nCodigosTotales . "' disabled>
                        </div>

                    </div>
                </div>
            </div>";

            //-----------------------------------/ DATOS DE LA FACTURA /----------------------------------------------------------
            $nFactura="";
            $subFactura=0;
            $totFactura=0;
            $ncajasFact=0;
            $ntinacosFact=0;
            $nexhibiFact=0;
            $ncostalesFact=0;
            $nbultTot=0;

            /*$rstFactura=odbc_exec($conn,"SELECT ZZO_FACT,ZZO_MONTO,ZZO_SUBGUI,ZZO_VALORG,ISNULL(ZZM_CAJAS,0) ZZM_CAJAS,ISNULL(ZZM_TINACO,0) ZZM_TINACO,ISNULL(ZZM_EXHIB,0) ZZM_EXHIB,ISNULL(ZZM_COSTAL,0) ZZM_COSTAL FROM 
                ZZO010 ZZO
                LEFT JOIN
                (SELECT ZZM_PEDIDO,ZZM_CAJAS,ZZM_TINACO,ZZM_EXHIB,ZZM_COSTAL FROM ZZM010 WHERE D_E_L_E_T_='' ) ZZM ON ZZM_PEDIDO=ZZO_PEDIDO
                WHERE ZZO.D_E_L_E_T_='' AND ZZO_PEDIDO='".$num_ped."'");*/
            // cambio para backorders CHT
            /* $rstFactura = odbc_exec($conn, "SELECT ZZO_FACT,ZZO_MONTO,ZZO_SUBGUI,ZZO_VALORG,ISNULL(ZZM_CAJAS,0) ZZM_CAJAS,ISNULL(ZZM_TINACO,0) ZZM_TINACO,ISNULL(ZZM_EXHIB,0) ZZM_EXHIB,ISNULL(ZZM_COSTAL,0) ZZM_COSTAL FROM 
            ZZO010 ZZO
            LEFT JOIN
            (SELECT ZZM_PEDIDO,ZZM_CAJAS,ZZM_TINACO,ZZM_EXHIB,ZZM_COSTAL FROM ZZM010 WHERE D_E_L_E_T_='' ) ZZM ON ZZM_PEDIDO=ZZO_PEDIDO
            WHERE ZZO.D_E_L_E_T_='' AND ZZO_PEDIDO='" . $num_ped . "' AND ZZO_ORDSUR='" . $ord_sur . "' "); */

            $rstFactura=odbc_exec($conn,"SELECT ZZO_FACT,ZZO_MONTO,ZZO_SUBGUI,ZZO_VALORG,ISNULL(ZZM_CAJAS,0) ZZM_CAJAS,ISNULL(ZZM_TINACO,0) ZZM_TINACO,ISNULL(ZZM_EXHIB,0) ZZM_EXHIB,ISNULL(ZZM_COSTAL,0) ZZM_COSTAL FROM 
            ZZO010 ZZO
            LEFT JOIN
            (SELECT ZZM_PEDIDO,ZZM_CAJAS,ZZM_TINACO,ZZM_EXHIB,ZZM_COSTAL FROM ZZM010 WHERE D_E_L_E_T_='' ) ZZM ON ZZM_PEDIDO=ZZO_PEDIDO
            WHERE ZZO.D_E_L_E_T_='' AND ZZO_PEDIDO='".$num_ped."'");

            if($dataFac= odbc_fetch_array($rstFactura)){
                $nFactura=trim($dataFac['ZZO_FACT']);
                $subFactura=number_format(($dataFac['ZZO_MONTO']/1.16),2) ;
                $totFactura=number_format($dataFac['ZZO_MONTO'],2) ;
                $ncajasFact=number_format($dataFac['ZZM_CAJAS'],0);
                $ntinacosFact=number_format($dataFac['ZZM_TINACO'],0);
                $nexhibiFact=number_format($dataFac['ZZM_EXHIB'],0);
                $ncostalesFact=number_format($dataFac['ZZM_COSTAL'],0);
                $nbultTot=$ncajasFact+$ntinacosFact+ $nexhibiFact+$ncostalesFact;
            }
            odbc_free_result($rstFactura);

            $res .="
            <!------------------------- GRUPO DE ELEMENTOS 1 ----------------------------------->		
            <div class='container' style='width:1000px; margin:20px auto;overflow:hidden; border-style:ridge;' >
                <h2>DATOS DE LA FACTURA</h2>

                <div class='container' style='width:1000px; margin:20px auto;overflow:hidden'>
                    <div class='row'>		

                        <div style='width:410px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>Factura: </strong>
                            <input style='width:160px;background-color: #eee;border: 0' type='text' id='Factura' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Facturas(s)'  value='".$nFactura."' disabled>
                        </div>

                        <div style='width:250px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>SubTotal:</strong>
                            <input style='width:100px' type='text' id='Subtotal' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Subtotal' value='$" . $subFactura . "' disabled>
                        </div>

                        <div style='width:250px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>Total:</strong>
                            <input style='width:100px' type='text' id='total' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='total' value='$" . $totFactura . "' disabled>
                        </div>

                    </div>
                </div>

                <!------------------------- GRUPO DE ELEMENTOS 2 ----------------------------------->		
                <div class='container' style='width:1000px; margin:20px auto;overflow:hidden'>
                    <div class='row'>		

                        <div style='width:180px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>N° Cajas: </strong>
                            <input style='width:80px' type='text' id='cantCajas' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Cajas' value='" . $ncajasFact . "' disabled>
                        </div>

                        <div style='width:200px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>N° Tinacos:</strong>
                            <input style='width:80px' type='text' id='cantTinacos' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Tinacos' value='" . $ntinacosFact . "' disabled>
                        </div>

                        <div style='width:200px; margin:0 auto;overflow:hidden; float: left;' >
                            <strong>N° Exhibidores:</strong>
                            <input style='width:80px' type='text' id='cantExhibidores' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Exhibidores' value='" . $nexhibiFact . "' disabled>
                        </div>

                        <div style='width:200px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>N° Costales: </strong>
                            <input style='width:80px' type='text' id='cantCostales' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Costales' value='" . $ncostalesFact . "' disabled>
                        </div>

                        <div style='width:150px; margin:0 auto;overflow:hidden; float: left;'>
                            <strong>Bultos Totales:</strong>
                            <input style='width:50px' type='text' id='bulTotalFac' class='form-control' pattern='[a-zA-Z0-9]' maxlength='45' title='Bultos totalesFac' value='" . $nbultTot . "' disabled>
                        </div>
                    </div>
                </div>
            </div>";

        
            $res .="
                <div class='modal-body' style='width:1000; margin:20px auto;overflow:hidden;'>
                    <table id='customers' >
                    <thead>
                        <tr>
                            <th ></th>
                            <th >Fletera</th>
                            <th >Cobro</th>
                            <th >Dias entrega</th>
                            <th >% Cotizado</th>
                            <th >Costo EE</th>
                            <th >Sub Total</th>
                            <th >IVA (16%)</th>
                            <th >Total</th>
                            <th >Status</th>
                        </tr>
                    </thead>
                    <tbody>";
            
            $UnicoRegistro=true;
            $opPrimera=true;
            $opSegunda=true;
            $opTercera=true;
            

            foreach($data as $clave => $fila){
                $orden1[$clave] = floatval(str_replace(',', '', $fila['porcPedido']));
                $orden2[$clave] = $fila['diasEntrega'];
            }

            array_multisort($orden1, SORT_ASC, $orden2, SORT_ASC, $data);

            foreach ($data as $clave => $fila) {


                if ($cFletCliente==trim($fila['CodiFlete']) ){
                    $iconClient="C";
                }else{
                    $iconClient="";	
                }

                if ($cPediFletera==$fila['CodiFlete']){
                    $stcheck="checked";
                }else{
                    $stcheck="";   
                }


                $res .= "				
                        <tr >
                            <td><input type='radio' name='selFleteras' id='mycheck$conta' value='".$fila['CodiFlete']."|".$fila['porcPedido']."|".$fila['totCostoFlete']."' class='radioB' ></td>
                            <td>".$fila['nomFlete'] . "</td>
                            <td>".$fila['descCobro']."</td>
                            <td>" . $fila['diasEntrega'] . " dias</td>
                            <td>" . $fila['porcPedido']. "%</td>
                            <td>$" . $fila['EntregaEspecial'] . "</td>
                            <td>$" . $fila['totCostoFlete'] . "</td>
                            <td>$" . $fila['ivaFlete'] . "</td>
                            <td>$" . $fila['totCostIVAFle'] . "</td>
                            <td style='text-align: center;'>";

                if($opPrimera==true and floatval(str_replace(',', '', $fila['porcPedido']))<=$nPorcAceptado){

                    $res .= "					
                        <span style='background-color:#008f39; color:white;'>&nbsp;&nbsp;".$iconClient."&nbsp;&nbsp;</span>";
                    $opPrimera=false;


                }elseif($opSegunda==true and floatval(str_replace(',', '', $fila['porcPedido']))<=$nPorcAceptado){
                    $res .= "
                        <span style='background-color:#f8f32b; color:white;'>&nbsp;&nbsp;".$iconClient."&nbsp;&nbsp;</span>";
                    $opSegunda=false;
                }else{
                    $res .=  $fila['icono'];
                }

                $res .="   </td>
                        </tr>";
                $conta++;
            }
            


            $res .="    </tbody>
                    </table>
                </div>
            <!------------------------- NOMENCLATURA----------------------------------->	
        

            <div class='container' style='width:1000px; margin:20px auto;overflow:hidden;'>
                <div class='row'>

                    <div style='width:195px;margin:0 auto;overflow:hidden; float: left;'>
                        <h4 style='text-align:center;'>OPCIÓN RECOMENDADA<br><span class='badge' style='background-color:#008f39; color:white;'>&nbsp;&nbsp;&nbsp;&nbsp;</span></h4>
                    </div>

                    <div style='width:195px; margin:0 auto;overflow:hidden; float: left;'>
                        <h4 style='text-align:center;'>SEGUNDA OPCIÓN<br><span class='badge' style='background-color:#f8f32b; color:white;'>&nbsp;&nbsp;&nbsp;&nbsp;</span></h4>
                    </div>

                    <div style='width:195px; margin:0 auto;overflow:hidden; float: left;'>
                        <h4 style='text-align:center;'>MENOR A ".$nPorcAceptado."% <br><span class='badge' style='background-color:#ff8000; color:white;'>&nbsp;&nbsp;&nbsp;&nbsp;</span></h4>
                    </div>

                    <div style='width:195px; margin:0 auto;overflow:hidden; float: left;'>
                        <h4 style='text-align:center;'>EXCEDE EL ".$nPorcAceptado."%<br><span class='badge' style='background-color:#dd0017; color:white;'>&nbsp;&nbsp;&nbsp;&nbsp;</span></h4>
                    </div>

                    <div style='width:195px; margin:0 auto;overflow:hidden; float: left;'>
                        <h4 style='text-align:center;'>FLETERA CLIENTE<br><span class='badge' style='color:white;'>&nbsp;&nbsp;C&nbsp;&nbsp;</span></h4>
                    </div>

                </div>
            </div>";
        }else{

            $res .="
            <div class='container' style='width:1000; margin:0 auto;overflow:hidden; border-style:ridge;' >

                <h2>NO EXISTE INFORMACIÓN DEL PEDIDO ".$cvincul."</h2>

            </div>";
                
        }  
        
        $cvincul =str_replace("'","!", $cvincul); //se modifica' x ! para pasar los pedidos a otro php, en el otro se regresa a la normalidad

        $res .="  
        <!------------------------- BOTONES----------------------------------->	

        <div style='width:800px; margin:0 auto;overflow:hidden;'>
            <br><input type='submit' value='Guardar' onClick=Guardar() id='Guardar' >&nbsp;&nbsp;<input type='button' value='Cancelar' onClick=cerrar() class='cerrar'>
        </div>

        
        <script>

            var nCheckeds = $conta;
            var lVacio = Boolean(true);



            if (nCheckeds>0){
            
                for(var i=0; i<nCheckeds; i++){

                    var x = $('#mycheck'+i).val();
                    var Fleteras = x.split('|');

                    if ('$cPediFletera'==Fleteras[0]){
                        $('#mycheck'+i).attr('checked', true);
                        lVacio=Boolean(false);
                    }
                }
            }


            
            $('input[name=selFleteras]').change(function () {	
                var valor = $(this).val().split('|'); 

                var FleteraS = valor[0];

                if ('$cPediFletera' != FleteraS && lVacio==false){
                    alert('ADVERTENCIA!!, se cambiará la fletera recomendada.');
                }

                
            });             
            

            function Guardar(){

                if( $('input[name=selFleteras]:radio').is(':checked') ) {  


                    var Seleccionado = $('input:radio[name=selFleteras]:checked').val();

                    var valor = Seleccionado.split('|');

                    var FleteraS = valor[0];
                    var PorcentajeS = parseFloat(valor[1].replace(',',''));
                    var CostoS = parseFloat(valor[2].replace(',','')) ;   


                    if ('$cPediFletera' != FleteraS){

                        if (lVacio==true){
                            var r = confirm('¿Deseas guardar la información?');
                        }else{
                            var r = confirm('Se cambio la fletera por defecto, ¿Deseas guardar la información?');  
                        }
                        
                        if(r == true){

                            var maxLength = 80;
                            var msj = -1;

                            while (msj == -1 || msj.length > maxLength) {
                                msj = prompt('Introduzca una observación menor a ' + maxLength + ' caracteres');
                            }

                            if(msj == null || msj.trim() == ''){
                                alert('Es necesaro ingresar una observacion.');
                                r=false;
                            }
                        }

                        if (r == true) {

                            $.ajax({
                                url: 'Inserta_flete_recomendada.php',
                                type: 'POST',
                                data: ({
                                    pedido:'$num_ped',
                                    valPed: '$nValPedido',
                                    cp: '$cCp',
                                    fletera:FleteraS, 
                                    costo: CostoS,
                                    porcentaje: PorcentajeS, 
                                    totBult: '$nCodigosTotales',
                                    totPeso: '$nPesototalPedido',
                                    mcubic: '$nTotalMtrs3',
                                    cliente: '$cCliente', 
                                    pedidosVin:'$cvincul', 
                                    fleCli: '$cFletCliente',
                                    flePedi:'$cPediFletera',
                                    msj: msj

                                }),
                                success: function (data){
                                    
                                    if(data.trim() == 'CORRECTO'){
                                        alert('Se actualizo correctamente');
                                        location.href='Embarques.php';
                                    }else if(data.trim() == 'ERRORCORREO'){
                                        alert('Error al mandar el correo.');
                                    
                                    }else{
                                        console.log(data);
                                        alert('Error al actualizar la información');
                                    }

                                },error: function () {
                                    alert('Error desconocido, contactar a sistema');

                                }
                            }); 
                        } 
                    }else{
                        alert('No se realizo ningún cambio');
                        location.href='Embarques.php';
                    }
                        
                }else{
                    alert('No hay información para actualizar');
                }

            }

            function cerrar(){
                location.href='Embarques.php';
            }
            
        </script>";
        
    }else{
        $res ="
        <div class='container' style='width:1000; margin:0 auto;overflow:hidden; border-style:ridge;' >

            <h2>ERROR EN EL PEDIDO</h2>

        </div>";
    }

    echo $res;
?>
   
</center>
</form>
</body>
</html>