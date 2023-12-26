<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="embStilo.css"/>
<title>Documento sin título</title>

</head>
	<body id="otro">
	<div id="test">
    	asd
    </div>
    <table>
    <tr>
    	<th>Factura</th>Fecha Factura<th>Pedido</th><th>Fecha Embarque</th><th>Guia</th><th>Fletera</th><th>Loc/Foran</th><th>$ Guia</th><th>Cliente</th><th>Destino</th><th>Valor Factura</th><th>Cajas</th><th>Costales</th><th>Exhibidores</th><th>Tinacos</th><th>Fletera x Reembarque</th><th>Costo x rembarque</th><th>Fecha recibido x Cliente</th><th>Flete x Cobrar/Pagado</th><th>Observaciones</th><th>Dias en la entrega</th><th>Vendedor</th><th>Factura pagada concentrado guias</th>
    </tr>
	<?php
		include("conectabd.php");
		$sql=odbc_exec($conn,"SELECT ZZO_FACT,ZZO_PEDIDO,ZZO_FECFAC,ZZO_FEMBAR,ZZO_GUIA,ZZO_CODFLE,CASE WHEN A1_TIPO=2 THEN 'Local' ELSE 'Foraneo' END AS TIPO,ZZO_VALORG,A1_NOME,ZZO_MONTO,ZZM_CAJAS,ZZM_COSTAL,ZZM_EXHIB,ZZM_TINACO,
ZZO_FECENT,ZZO_FPAGO,ZZO_OBSEMB,ZZO_CHOFER,F2_VEND1 FROM ZZO010 ZZO INNER JOIN SF2010 SF2 ON ZZO_FACT=F2_DOC INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD LEFT JOIN ZZM010 ZZM ON ZZO_FACT=ZZM_FATURA WHERE F2_EMISSAO BETWEEN '2013-09-01' AND '2013-09-30' AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_='' AND SF2.D_E_L_E_T_=''") or die("Error en la consulta");
		while($datos=odbc_exec($sql))
			echo "<tr><td>$datos[ZZO_FACT]</td><td>$datos[FECFAC]</td></tr>";
		odbc_free_result($sql);
		odbc_close($conn);
	?>
    </table>
	</body>
</html>
