<?php
try {
    include("conectabd.php");

    $currentMonth = date("n", time());

    echo "<h1>Pedidos Surtidos en el mes de $currentMonth</h1>";

    $fechaInicioMes = date("Y-m-01");
    $fechaFinMes = date("Y-m-t");

 
    $sql_grafi = "SELECT SUM(CASE WHEN ZZM_FECSUR BETWEEN '$fechaInicioMes' AND '$fechaFinMes' THEN 1 ELSE 0 END) AS pedAnt,
                  SUM(CASE WHEN ZZM_FECSUR BETWEEN '$fechaInicioMes' AND '$fechaFinMes' THEN 1 ELSE 0 END) AS pedAct,
                  ZZN_NOMBRE 
                  FROM ZZM010 ZZM 
                  INNER JOIN ZZN010 ZZN ON ZZM_CODALM = ZZN_CODIGO 
                  WHERE ZZN_TIPO = 0 AND ZZM.D_E_L_E_T_ <> '*' AND ZZN.D_E_L_E_T_ <> '*' 
                  GROUP BY ZZN_NOMBRE 
                  ORDER BY ZZN_NOMBRE";


    $sql_execute = odbc_prepare($conn, $sql_grafi);

    if (!$sql_execute) {
        die(odbc_errormsg($conn));
    }

    $result = odbc_execute($sql_execute, array($fechaInicioMes, $fechaFinMes, $fechaInicioMes, $fechaFinMes));

    if (!$result) {
        die(odbc_errormsg($conn));
    }

    $pedidos = "?dat=";
    $leyenda = "&alm=";
    while ($datos = odbc_fetch_array($sql_execute)) {
        $pedidos .= $datos["pedAnt"] . "," . $datos["pedAct"] . ",";
        $leyenda .= $datos["ZZN_NOMBRE"] . ",";
    }

    if (strlen($pedidos) > 5) {
        $pedidos = substr($pedidos, 0, strlen($pedidos) - 1);
        $leyenda = substr($leyenda, 0, strlen($leyenda) - 1);
       

		/*cho "<center><img width='90%' src='graficas/graphbarras.php?dat=" . urlencode($pedidos) . "&alm=" . urlencode($leyenda) . "' /></center>";*/
    }
} catch (Exception $ex) {
    echo "Error: " . $ex->getMessage();
}
$sql = odbc_exec($conn, "SELECT ZPP_ADMIN FROM ZPP010 WHERE ZPP_NOMPC='" . strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR'])) . "' AND ZPP_PAGINA='Pedidos' AND ZPP_ADMIN='T' AND D_E_L_E_T_=''")
    or die("Error al validar el equipo");

if (odbc_num_rows($sql) > 0) {
    echo "<section>
            <table class='tSAL' direction='rtl'>
            <caption>MUESTRAS PENDIENTES POR SURTIR.</caption>
            <thead class='fixedHeader' direction='rtl'><tr><th class='thChec'></th><th class='thCod'>N&uacute;m. SA</th><th>Solicitante</th><th>Aprobaci&oacute;n</th><th>Vencimiento</th></tr></thead><tbody class='scrollContent'>";
    $sql = odbc_exec($conn,"SET LANGUAGE 'Spanish';");       
    $sql = odbc_exec($conn, "SELECT CP_NUM, CP_SOLICIT, CP_FYHAP, CP_FYHLIM, DATEDIFF(hh, CP_FYHAP, CP_FYHLIM) AS 'tt', DATEDIFF(hh, CP_FYHLIM, GETDATE()) AS 'horas', CP_SALIDA FROM SCP010 WHERE CP_STATUS='' AND CP_QUANT-CP_QUJE>0 AND D_E_L_E_T_='' AND CP_STATSA<>'B' AND CP_PREREQU='' AND CP_OK='' AND (CP_TIPO=0 OR CP_TIPO=2) AND (CP_SALIDA='0' or CP_SALIDA='' or CP_SALIDA='-1') GROUP BY CP_NUM, CP_SOLICIT, CP_FYHAP, CP_FYHLIM, CP_SALIDA ORDER BY CONVERT(DATETIME, CP_FYHAP)") or die("ErrorSA");
    

    while ($datos = odbc_fetch_array($sql)) {
        echo "<tr class='";
        if (trim($datos['CP_SALIDA']) <> "0") echo "trImpr";
        else echo "trPart";
        echo "' id='tr$datos[CP_NUM]'><td class='tdChec'><input type='checkbox' id='$datos[CP_NUM]' class='chkBox'></td><td class='tdPart' id='td$datos[CP_NUM]'><img src='images/";
        if ($datos["horas"] >= 0) $img = "late";
        elseif (($datos["horas"] / 2) >= ($datos["tt"] * -1)) $img = "medium";
        else $img = "good";
        echo "$img.png'>$datos[CP_NUM]&nbsp;</td><td class='mSol' id='td$datos[CP_NUM]'>$datos[CP_SOLICIT]</td><td class='mSol' id='td$datos[CP_NUM]'>" . substr($datos['CP_FYHAP'], 0, 14) . "</td><td class='mSol' id='td$datos[CP_NUM]'>" . substr($datos['CP_FYHLIM'], 0, 14) . "</td></tr>";
    }

    odbc_free_result($sql);
    odbc_close($conn);
    echo "</tbody></table></section><article id='detM'></article>";
}
?>
