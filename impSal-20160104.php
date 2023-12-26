<html>
  <head>
	<title>Salida por Pedido</title>
		<script>
			function imprimeSalida(ped,alm){
				var impreso
				window.print();
				tindec=document.getElementById('tinaco').value;
				costal=document.getElementById('costal').value;
				exhibidor=document.getElementById('exhi').value;
				cajas=document.getElementById('caja').value;
				location.href="guardaSal.php?Ped="+ped+"&Alm="+alm+"&Caj="+cajas+"&Cos="+costal+"&Exh="+exhibidor+"&Tin="+tindec;
				location.href='index.php';
			}
		</script>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <style type="text/css">
<!--
body,td,th {
	font-family: Courier New, Courier, monospace;
	font-size: 8px;
}
-->
	table, th, td
	{
		border: solid 1px grey;
		border-spacing: 0;
		border-collapse: collapse;
	}
	</style></head>
    <?php
	  echo "<body onload=imprimeSalida('".$_GET["Ped"]."','".$_GET["Alm"]."')>
	  		  <form><center>";
		//Totales Exhibidor, tindec, costales, cajas
		include("conectabd.php");
		$impreso=0;
		while($impreso<2)
		{
			$lineas=0;
			$exhi=0;
			$tinaco=0;
			$costal=0;
			$caja=0;
			$codIn="";
			$cantidad=0;
			$multiBox=0;
			$partida=0;
			$sql=odbc_exec($conn,"SELECT C5_NUM,C5_EMISSAO,A1_COD,A1_NOME FROM SC5010 SC5 INNER JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD WHERE C5_NUM='".$_GET["Ped"]."' AND C5_NOTA='' AND SC5.D_E_L_E_T_='' AND SA1.D_E_L_E_T_=''")
			or die("Error al validar el pedido de Impresión en SC5");
			if(odbc_num_rows($sql)>0)
			{
				$datos=odbc_fetch_array($sql);
				echo "<table border='1'>
					<tr>
						  <td rowspan='2' align='center'><img src='images/logo.png' height='40' width='80'></td>
						  <td align='center' colspan='2'><font size='2'><strong>Fleximatic S.A. de C.V.</strong></font></td>
						  <td align='center'><font size='1'><strong>Salida: ".$_GET["Sal"]."</strong></font></td>
					</tr>
					<tr>
						<td align='center' colspan='2'><strong>SALIDA DE MERCANC&Iacute;A PEDIDO DEL ALMACEN DE PRODUCTO TERMINADO</strong></td>
						<td align='center'><input type='text' id='txtStatus' value='";
						if($impreso==0)
							echo "Original";
						else
							echo "Cliente";
						echo "' disabled style='border:none' size='4'></td>
					</tr>
					<tr></tr>
					<tr>
					  <td><strong>CLIENTE:</strong></td>
					  <td>".trim($datos["A1_NOME"])."</td>
					  <td><strong>FECHA PEDIDO:</strong></td>
					  <td>".substr($datos["C5_EMISSAO"],6)."/".substr($datos["C5_EMISSAO"],4,2)."/".substr($datos["C5_EMISSAO"],0,4)."</td>
					</tr>
					<tr>
					  <td><strong>PEDIDO:</strong></td>
					  <td>".$datos["C5_NUM"]."</td>
					  <td><strong>FECHA SALIDA:</strong></td>
					  <td>".date("d/m/Y H:i:s")."</td>
					</tr>";
					odbc_free_result($sql);
					$sql=odbc_exec($conn, "SELECT B1_CANTBOL,ZDS_QTENT,B1_COD,B1_DESC,ZDS_PEDIM,B1_TIPO,B1_CLASE,CASE WHEN ZDS_COSTAL='' THEN B1_BOLSA ELSE ZDS_COSTAL END AS ZDS_COSTAL,ZDS_QE,ZDS_CAJA,ZDS_PARTID FROM ZDS010 ZDS INNER JOIN SB1010 SB1 ON ZDS_PRODUC=B1_COD WHERE ZDS_SALIDA=".$_GET["Sal"]." AND SB1.D_E_L_E_T_='' AND ZDS.D_E_L_E_T_='' ORDER BY ZDS_CAJA,ZDS_PARTID,ZDS.R_E_C_N_O_")	or die("Error al obtener las partidas de la salida ZDS");
					if(odbc_num_rows($sql)>0)
					{
						echo "<tr>
								<td colspan='4' style='border:none'>
								 <table width='100%' style='border:none;'>
								  <tr>
									<th style='border:none;border-bottom:solid 1px grey;'>CANT.</th>
									<th style='border:none;border-bottom:solid 1px grey;'>[PRODUCTO] DESCRIPCI&Oacute;N</th>
									<th style='border:none;border-bottom:solid 1px grey;'>PEDIMENTO</th>
									<th style='border:none;border-bottom:solid 1px grey;'>TINDEC</th>
									<th style='border:none;border-bottom:solid 1px grey;'>EXHIBIDORES</th>
									<th style='border:none;border-bottom:solid 1px grey;'>COSTALES</th>
									<th style='border:none;border-bottom:solid 1px grey;'>CAJAS</th>
								  </tr>";
						  $agrupaBox="";
						  while($datos=odbc_fetch_array($sql))
						  {
							if($datos["ZDS_CAJA"]>0)
							{
								$cantidad=0;
								if($multiBox==0)
								{
									echo "<tr borderColor='#D8D8D8'><td colspan='7' align='center' style='border:none; border-bottom:solid 1px grey;'>CAJAS MULTIPLES</td></tr>";
									$lineas+=1;
									$multiBox=1;
								}
							}
							else
								$cantidad=intval($datos["ZDS_QTENT"]/$datos["ZDS_QE"]);
							if($cantidad==0 && $agrupaBox<>$datos["ZDS_CAJA"])
							{
								//echo "<tr borderColor='#FFFFFF'><td colspan='7'><hr></td></tr>";
								$lineas+=1;
							}
							if($datos["B1_CANTBOL"]>0)
								$inner=$datos["ZDS_QTENT"]/$datos["B1_CANTBOL"];
							if($inner>0 && $cantidad==0 && $datos["B1_CANTBOL"]>0)
								echo "
									<tr bgColor='#FFF' borderColor='#D8D8D8'>
									<td align='right' style='border:solid 1px #D8D8D8;'>".intval($datos["ZDS_QTENT"])."</td>
									<td style='border:solid 1px #D8D8D8;'>[".trim($datos["B1_COD"])."] ".trim($datos["B1_DESC"])."</td>
									<td style='border:solid 1px #D8D8D8;'>".trim($datos["ZDS_PEDIM"])." ".$inner." bolsas&nbsp;</td>";
							else
								echo "
									<tr bgColor='#FFFFFF' borderColor='#FFFFFF'>
									<td align='right' style='border:solid 1px #D8D8D8;'>".intval($datos["ZDS_QTENT"])."</td>
									<td style='border:solid 1px #D8D8D8;'>[".trim($datos["B1_COD"])."] ".trim($datos["B1_DESC"])."</td>
									<td style='border:solid 1px #D8D8D8;'>".trim($datos["ZDS_PEDIM"])."&nbsp;</td>";
							if($partida<>$datos["ZDS_PARTID"] || $partida==0)
							{
								if(trim($datos["B1_CLASE"])=="11")
								{
									$tinaco+=$cantidad;
								}
								elseif(trim($datos["B1_CLASE"])=="22")
								{
									$exhi+=$cantidad;
									echo "<td style='border:solid 1px #D8D8D8;'>&nbsp;</td>";
								}
								elseif(trim($datos["ZDS_COSTAL"]=="T"))
								{
									$costal+=$cantidad;
									echo "<td style='border:solid 1px #D8D8D8;'>&nbsp;</td><td style='border:solid 1px #D8D8D8;'>&nbsp;</td>";
								}
								else
								{
									$caja+=$cantidad;
									echo "<td style='border:solid 1px #D8D8D8;'>&nbsp;</td><td style='border:solid 1px #D8D8D8;'>&nbsp;</td><td style='border:solid 1px #D8D8D8;'>&nbsp;</td>";
								}
 								if($cantidad==0 && $agrupaBox<>$datos["ZDS_CAJA"])
								{
									$agrupaBox=$datos["ZDS_CAJA"];
									echo "<td align='right' style='border:solid 1px #D8D8D8;'>1</td></tr>";
									if(trim($datos["B1_CLASE"])=="11")
										$tinaco+=1;
									elseif(trim($datos["B1_CLASE"])=="22")
										$exhi+=1;
									elseif(trim($datos["ZDS_COSTAL"])=="T")
										$costal+=1;
									else
										$caja+=1;
								}
								elseif($cantidad<>0)
									echo "<td align='right' style='border:solid 1px #D8D8D8;'>".$cantidad."</td></tr>";
								else
								{
									if(trim($datos["ZDS_COSTAL"])=="T")
										echo "<td></td>";
									echo "<td></td></tr>";
								}
							}
							else
								echo "</tr>";
							$lineas+=1;
							$partida=$datos["ZDS_PARTID"];
						  }
						  odbc_free_result($sql);
						  $sql=odbc_exec($conn, "SELECT ZDS_PRODUC,ZDS_QTENT,ZDS_QE,CASE WHEN (ZDS_QTENT/ZDS_QE)<1 THEN 1 ELSE ZDS_QTENT/ZDS_QE END AS 'cajas' FROM ZDS010 WHERE ZDS_SALIDA=$_GET[Sal] AND (ZDS_PRODUC='+Kit+' OR ZDS_PRODUC='+Varilla+') AND D_E_L_E_T_=''")or die("Error al cargar las cajas de la varilla");
						  if(odbc_num_rows($sql)>0)
						  	echo "<tr><td colspan='7'><hr></td></tr>";
						  while($datos=odbc_fetch_array($sql)){
							  echo "<tr><td align='right'>".intval($datos['ZDS_QTENT'])."</td><td>[".substr($datos['ZDS_PRODUC'],1,strlen($datos['ZDS_PRODUC'])-2)."] Varilla</td><td>&nbsp;</td><td></td><td></td><td></td><td align='right'>".intval($datos['cajas'])."</td></tr>";
							  $caja+=intval($datos['cajas']);
							  $lineas++;
						  }
						  
						  echo "</table>";
						  if($lineas<19) //23 25
						  {
							  while($lineas<19){
								  echo "<br>";
								  $lineas+=1;  
							  }
						  }
				   }
					odbc_free_result($sql);
					echo "<table width='100%' style='border:solid 1px #D8D8D8;'><tr style='border:solid 1px #D8D8D8; border-bottom:solid 1px grey;' >
							<strong>
							   <td colspan='2' align='right' style='border:solid 1px #D8D8D8; border-bottom:solid 1px grey;'>TOTAL: </td><td style='border:solid 1px #D8D8D8; border-bottom:solid 1px grey;'></td>
							   <td align='right' style='border:solid 1px #D8D8D8; border-bottom:solid 1px grey;'><input type='hidden' id='tinaco' value='".$tinaco."'>".$tinaco."</td>
							   <td align='right' style='border:solid 1px #D8D8D8; border-bottom:solid 1px grey;'><input type='hidden' id='exhi' value='".$exhi."'>".$exhi."</td>
							   <td align='right' style='border:solid 1px #D8D8D8; border-bottom:solid 1px grey;'><input type='hidden' id='costal' value='".$costal."'>".$costal."</td>
							   <td align='right' style='border:solid 1px #D8D8D8; border-bottom:solid 1px grey;'><input type='hidden' id='caja' value='".$caja."'>".$caja."</td>
							 </strong>
						  </tr>";
						  /*Nombre del Almacenista que surte el pedido cuando no ha sido facturado*/
						  $sql=odbc_exec($conn,"SELECT ZZN_CODIGO,ZZN_NOMBRE FROM ZZN010 ZZN INNER JOIN ZZM010 ZZM ON ZZN_CODIGO=ZZM_CODALM WHERE ZZM_PEDIDO='".$_GET["Ped"]."' AND ZZN.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_=''")
						  or die("Error al consultar el nombre del almacenista");
						  $datos=odbc_fetch_array($sql);
						  echo  "<tr bgcolor='#D8D8D8'>
								   <td colspan='2' style='border:none;'>
									<table style='border:none;'>
										<tr style='border:none;'>
											<th colspan='2' style='border:none;'>ELABOR&Oacute;</th>
										</tr>
										<tr style='border:none;'>
											<td style='border:none;'><input type='hidden' id='txtCodAlm' value='".$datos["ZZN_CODIGO"]."'><strong>Nombre:&nbsp;&nbsp;".trim($datos["ZZN_NOMBRE"])."</strong></td>
										</tr>
										<tr style='border:none;'>
											<td>ALMACENISTA DE PRODUCTO TERMINADO</td>
										</tr>
									</table>
								   </td>
								   <td colspan='5' align='center' style='border:none;'>
									  <table style='border:none;'>
										<tr>
										  <th colspan='2' style='border:none;'>REVIS&Oacute;</th>
										</tr>
										<tr>
										  <td style='border:none;'><strong>Nombre:</strong></td>
										</tr>
										<tr>
										  <td style='border:none;'>AUDITOR DE PEDIDO</td>
										</tr>
									  </table>
								   </td>
								 </tr>
							</table>          
						  </td>
						</tr>
				</table><br><br>";
			}
			if($impreso==0)
			{
				if($lineas>24)
				{
					while(($lineas%78)<>0)
					{
						echo "<br>";
						$lineas+=1;
					}
				}
			}
			$impreso+=1;
		}
		odbc_free_result($sql);
		odbc_close($conn);
		echo "</form></center></body>";
	?>
</html>