<html>
 <head>
	<link href="css/styles.css" rel="stylesheet" type="text/css">
 </head>
<body class="bRuta">
<div class="ruta">
<?php
	include("conectabd.php");
	$sql=odbc_exec($conn,"SELECT ZZN_NOMBRE FROM ZZN010 WHERE ZZN_CODIGO='$_GET[chof]' AND D_E_L_E_T_=''")or die("Error en chofer");
	$datos=odbc_fetch_array($sql);
	odbc_free_result($sql);
	$nomChof=$datos['ZZN_NOMBRE'];	
	function impEncabezado($nom){
		echo "<table>
		<tr>
			<td rowspan='2'><img src='images/logo.png' width='80px'></td>
			<td width='90%' align='center' colspan='3'><h1>FLEXIMATIC S.A. DE C.V.</h1></td>
		</tr>
		<tr>
			<td><h2>Plan y Reporte de Ruta</h2></td>
			<td><h2>Fecha y hora Inicio: ".date('d/M/y h:i')."</h2></td>
			<td><h2>Fecha y hora Finalizaci&oacute;n:</h2></td>
		</tr>
		<tr>
			<td colspan='4'>Chofer: $nom</td>
		</tr>
		<tr>
		 <td colspan='4'>
			<table border='1' cellspacing='0'>
			 <tr>
			  <td colspan='2' align='center'>Entrega</td>
			  <td rowspan='2' align='center'>Fletera</td>
			  <td rowspan='2'>Cajas</td>
			  <td rowspan='2'>Costales</td>
			  <td rowspan='2'>Exhib</td>
			  <td rowspan='2'>Tindec</td>
			  <td rowspan='2' align='center'>Paquetes<br>/Sobres</td>
			  <td rowspan='2' align='center' width='90'>Firma del Cliente</td>
			  <td rowspan='2' align='center'>Merc. da&ntilde;ada</td>
			  <td rowspan='2' align='center' width='80'>Kilometraje</td>
			 </tr>
			 <tr>
				<td>Hora &nbsp;&nbsp;</td>
				<td align='center'>Nombre del Cliente</td>
			 </tr>";
	}
	
	function pieDPagina($conn)
	{
		echo "<br>";
			$sql=odbc_exec($conn,"SELECT depto,descrip FROM embRec WHERE fyHora LIKE '".date("d/m/Y",time())."%' AND chofer='".$_GET["chof"]."'") or die("Error al obtener los registros de otras rutas");
			if(odbc_num_rows($sql)>0){
				echo "<table border='1' cellspacing='0'>
						<caption>OTROS RECORRIDOS</caption>
						 <tr>
							<td width='10%'>Hora</td>
							<td width='20%'>Departamento</td>
							<td width='60%'>Descripci&oacute;n</td>
							<td width='10%' align='center'>Kilometraje</td>
						 </tr>";
				while($datos=odbc_fetch_array($sql)){
					echo "<tr height='15'><td>1&nbsp;</td><td>".$datos['depto']."</td><td>".$datos['descrip']."</td><td>&nbsp;</td></tr>";
				}
				odbc_free_result($sql);
				echo "</table><br>";
			}
			echo "<table border='1' cellspacing='0'>
			 <caption>DEVOLUCIONES DE CLIENTES</caption>
			 <tr>
			 	<td width='2%'>N&uacute;m</td>
				<td width='8%'>Hora</td>
				<td width='50%'>&nbsp;</td>
				<td width='8%'>Cajas</td>
				<td width='8%'>Costales</td>
				<td width='8%'>Exhibidores</td>				
				<td width='8%'>Firma del Cliente</td>				
				<td width='8%' align='center'>Kilometraje</td>
			 </tr>
 			 <tr class='altofila'><td class='tdD'>1</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
			 <tr class='altofila'><td class='tdD'>2</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
			 <tr class='altofila'><td class='tdD'>3</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
			 <tr class='altofila'><td class='tdD'>4</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
			 <tr class='altofila'><td class='tdD'>5</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
			 <tr class='altofila'><td class='tdD'>6</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
			 <tr class='altofila'><td class='tdD'>7</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
			</table>
			<center>
			 <table border='1' cellspacing='0' class='tSmall'>
			 <caption>KILOMETRAJE</caption>
			  <tr>
			 	<td align='center'>INICIAL</td>
				<td align='center'>FINAL</td>
				<td align='center'>KM RECORRIDOS</td>
			 </tr>
 			 <tr height='20'>
			 	<td>&nbsp;</td>
			 	<td>&nbsp;</td>
				<td>&nbsp;</td>
			 </tr>
			</table>
			</center>
			<table>
			<caption>CAUSAS DE PEDIDOS NO ENTREGADOS</caption>
			 <tr>
			 	<td align='center'>Cliente</td>
				<td align='center'>Causa</td>
			 </tr>
 			 <tr><td class='tdLB'>&nbsp;</td><td class='tdLB'>&nbsp;</td></tr>
 			 <tr><td class='tdLB'>&nbsp;</td><td class='tdLB'>&nbsp;</td></tr>
 			 <tr><td class='tdLB'>&nbsp;</td><td class='tdLB'>&nbsp;</td></tr>
			</table>
			<br>
			<table style= 'margin-left: 13px;'>
			 <tr>
			 	<td align='center'>Planea</td>
				<td>&nbsp;</td>
				<td align='center'>Recibe / Reporte</td>
				<td>&nbsp;</td>
				<td align='center'>Recibi&oacute;</td>				
			 </tr>
 			 <tr><td colspan='3'>&nbsp;</td></tr>
 			 <tr>
			 	<td align='center' class='tdLB'>&nbsp;</td>
				<td>&nbsp;</td>
			 	<td align='center' class='tdLB'>&nbsp;</td>
				<td>&nbsp;</td>
			 	<td align='center' class='tdLB'>&nbsp;</td>
				<td>&nbsp;</td>
				</tr>
			 <tr>
			 	<td align='center'>Jefe de Embarques</td>
				<td>&nbsp;</td>
				<td align='center'>Chofer</td>
				<td>&nbsp;</td>
				<td align='center'>Atenci&oacute;n al Cliente</td>				
				<td>&nbsp;</td>
			 </tr>			 
			</table>";
		
	}
	$sql=odbc_exec($conn,"SELECT ZZN_NOMBRE,ZZO_FACT,A1_NOME,ZZO_FEMBAR,ISNULL(A2_NOME,'LOCAL') AS A2_NOME,ZZM_CAJAS,ZZM_COSTAL,ZZM_EXHIB,ZZM_TINACO FROM ZZO010 ZZO INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD LEFT JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD INNER JOIN ZZN010 ZZN ON ZZO_CHOFER=ZZN_CODIGO LEFT JOIN ZZM010 ZZM ON ZZO_FACT=ZZM_FATURA WHERE ZZO_CHOFER='".$_GET['chof']."' AND ZZO_FECHEN='' AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' AND ZZN.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_='' ORDER BY ZZO_CONSEC") or die("Error al obtener el listado de facturas por chofer");
	$chof="";
	impEncabezado($nomChof);
	while($datos=odbc_fetch_array($sql))
	{
		echo "<tr height='30'>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td>".trim($datos['A1_NOME'])."&nbsp;&nbsp;[".$datos['ZZO_FACT']."]</td>
			    <td>".trim($datos['A2_NOME'])."</td>
				<td class='tdD'>".number_format($datos['ZZM_CAJAS'],0)."</td>
				<td class='tdD'>".number_format($datos['ZZM_COSTAL'],0)."</td>
				<td class='tdD'>".number_format($datos['ZZM_EXHIB'],0)."</td>
				<td class='tdD'>".number_format($datos['ZZM_TINACO'],0)."</td>
				<td>&nbsp;&nbsp;&nbsp;</td>
				<td>&nbsp;&nbsp;&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			  </tr>";
	}
	echo "</table></td>
		</tr>";
	pieDPagina($conn);
	odbc_free_result($sql);
	odbc_close($conn);
?>
</div>
</body>
</html>