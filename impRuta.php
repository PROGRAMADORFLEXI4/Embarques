<html>
 <head>
	<link href="css/styles.css" rel="stylesheet" type="text/css">
 </head>
<body class="bRuta">
<div class="ruta">
<?php
	include("conectabd.php");
	date_default_timezone_set("America/Chihuahua");
	$sql=odbc_exec($conn,"SELECT ZZN_NOMBRE FROM ZZN010 WHERE ZZN_CODIGO='$_GET[chof]' AND D_E_L_E_T_=''")or die("Error en chofer");
	$datos=odbc_fetch_array($sql);
	odbc_free_result($sql);
	$nomChof=$datos['ZZN_NOMBRE'];	
	function impEncabezado($nom,$nomaux){
		echo "<table>
		<tr>
			<td rowspan='2'><img src='images/logo.png' width='80px'></td>
			<td width='90%' align='center' colspan='3'><h1>FLEXIMATIC S.A. DE C.V.</h1></td>

		</tr>
		<tr>
			<td>
					
			 <table border='1' cellspacing='0' class='tSmall' style='margin-left: 400px;'>
			 <caption>KILOMETRAJE</caption>
			  <tr>
			 	<td align='center'>KM INICIAL</td>
				<td align='center'>KM FINAL</td>
				<td align='center'>KMS RECORRIDOS</td>
				<td align='center' rowspan='2'><img src='images/gas.jpg' width='100' height='50'></td>
			 </tr>
 			 <tr height='20'>
			 	<td style='width: 120px; height: 20px;'></td>
			 	<td style='width: 120px; height: 20px;'></td>
				<td style='width: 160px; height: 20px;'></td>
			 </tr>
			</table>
			</td></tr>
		<tr>
			<td><h2>Plan y Reporte de Ruta</h2></td>
			<td><h2>Fecha y hora Inicio: ".date('d/M/y h:i')."</h2></td>
			<td><h2>Fecha y hora Finalizaci&oacute;n:</h2></td>
		</tr>
		<tr>
			<td colspan='4'>Chofer: $nom&nbsp;&nbsp;&nbsp;&nbsp;Auxiliar: $nomaux &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Unidad de reparto:</td>
		</tr>
		<tr>
		 <td colspan='4'>
			<table border='1' cellspacing='0'>
			 <tr>
			  <td colspan='3' align='center' width='44%'>Entrega</td>
			  <td rowspan='2' align='center' width='15%'>Fletera</td>
			  <td rowspan='2' width='3%'>Cajas</td>
			  <td rowspan='2' width='3%'>Costales</td>
			  <td rowspan='2' width='3%'>Exhib</td>
			  <td rowspan='2' width='3%'>Tindec</td>
			  <td rowspan='2' align='center' width='3%'>Paquetes<br>/Sobres</td>
			  <td rowspan='2' align='center' width='10%'>Firma del Cliente</td>
			  <td rowspan='2' align='center' width='6%'>Merc. da&ntilde;ada</td>
			  <td rowspan='2' align='center' width='10%'>Kilometraje</td>
			 </tr>
			 <tr>
				<td width='10%'>Llegada</td>
				<td width='10%'>Salida</td>
				<td align='center' width='20%'>Nombre del Cliente</td>
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
							<td width='10%'>H Llegada</td>
							<td width='10%'>H Salida</td>
							<td width='10%'>Departamento</td>
							<td width='60%'>Descripci&oacute;n</td>
							<td width='10%' align='center'>Kilometraje</td>
						 </tr>";
				while($datos=odbc_fetch_array($sql)){
					echo "<tr class='altofila'><td>1&nbsp;</td><td>&nbsp;</td><td>".$datos['depto']."</td><td>".$datos['descrip']."</td><td>&nbsp;</td></tr>";
				}
				odbc_free_result($sql);
				echo "</table><br>";
			}
			echo "<table border='1' cellspacing='0'>
			 <caption>EMBARQUES VARIOS</caption>
			 <tr>
			 	<td width='2%'>N&uacute;m</td>
				<td width='8%'>H Llegada</td>
				<td width='8%'>H Salida</td>
				<td width='42%'>&nbsp;</td>
				<td width='8%'>Cajas</td>
				<td width='8%'>Costales</td>
				<td width='8%'>Exhibidores</td>				
				<td width='8%'>Firma del Cliente</td>				
				<td width='8%' align='center'>Kilometraje</td>
			 </tr>
 			 <tr class='altofila'><td class='tdD'>1</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
			 <tr class='altofila'><td class='tdD'>2</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
			 <tr class='altofila'><td class='tdD'>3</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
			 <tr class='altofila'><td class='tdD'>4</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
			 <tr class='altofila'><td class='tdD'>5</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
			 
			</table>
			
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
				<td align='center'></td>				
			 </tr>
 			 <tr><td colspan='3'>&nbsp;</td></tr>
 			 <tr>
			 	<td align='center' class='tdLB'>&nbsp;</td>
				<td>&nbsp;</td>
			 	<td align='center' class='tdLB'>&nbsp;</td>
				<td>&nbsp;</td>
			 	<td align='center' ></td>
				<td>&nbsp;</td>
				</tr>
			 <tr>
				<td align='center'>Jefe de Embarques</td>
				<td>&nbsp;</td>
				<td align='center'>Chofer</td>
				<td>&nbsp;</td>
				<td align='center'></td>				
				<td>&nbsp;</td>
			 </tr>
			</table>
			<br><br>
			<style>
				p {
				  float: left;
				  width: 120px;
				  height: 20px;
				  margin: 3px;
				  padding: 3px;
				  border: gray 2px solid;
				  border-radius: 10px 10px 10px 10px;
				}

			</style>

			
				<p style='margin-left: 50px'>N. Pedidos a Entregar:</p>
				<p style=' width: 40px; height: 20px;'></p>
				<p style='margin-left: 50px'>N. Pedidos Entregados:</p></div>
				<p style=' width: 40px; height: 20px;'></p>
				<p style=' width: 160px; height: 20px; margin-left: 50px'>% de Cumplimiento de Entregas:</p>
				<p style=' width: 40px; height: 20px;'></p>";

		
	}
	$sql=odbc_exec($conn,"SELECT ZZO_AUXCHO FROM ZZO010 ZZO INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD LEFT JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD INNER JOIN ZZN010 ZZN ON ZZO_CHOFER=ZZN_CODIGO LEFT JOIN ZZM010 ZZM ON ZZO_FACT=ZZM_FATURA WHERE ZZO_CHOFER='".$_GET['chof']."' AND ZZO_FECHEN='' AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' AND ZZN.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_='' GROUP BY ZZO_AUXCHO ORDER BY ZZO_AUXCHO DESC") or die("Error al obtener el listado de auxiliares");
	if($datos2=odbc_fetch_array($sql))
	{
		$auxchof=$datos2["ZZO_AUXCHO"];
	}
	odbc_free_result($sql);
	
	$sql=odbc_exec($conn,"SELECT ZZN_NOMBRE FROM ZZN010 WHERE ZZN_CODIGO='$auxchof' AND D_E_L_E_T_=''") or die("Error al obtener el listado de auxiliares");
	if($datos2=odbc_fetch_array($sql))
	{
		$auxchof=$datos2["ZZN_NOMBRE"];
	}
	odbc_free_result($sql);
	
	
	$sql=odbc_exec($conn,"SELECT ZZN_NOMBRE,ZZO_FACT,A1_NOME,ZZO_FEMBAR,ISNULL(A2_NOME,'LOCAL') AS A2_NOME,ZZM_CAJAS,ZZM_COSTAL,ZZM_EXHIB,ZZM_TINACO FROM ZZO010 ZZO INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD LEFT JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD INNER JOIN ZZN010 ZZN ON ZZO_CHOFER=ZZN_CODIGO LEFT JOIN ZZM010 ZZM ON ZZO_FACT=ZZM_FATURA WHERE ZZO_CHOFER='".$_GET['chof']."' AND ZZO_FECHEN='' AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' AND ZZN.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_='' ORDER BY ZZO_CONSEC") or die("Error al obtener el listado de facturas por chofer");
	$chof="";
	
	impEncabezado($nomChof,$auxchof);
	while($datos=odbc_fetch_array($sql))
	{
		echo "<tr class='altofila'>
				<td></td>
				<td></td>
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