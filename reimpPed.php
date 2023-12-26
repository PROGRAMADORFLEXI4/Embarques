<?PHP
	include("conectabd.php");
	if($_GET['desmarca'] == 0){
		if($_GET['opc'] == 'pxa'){
			odbc_exec($conn, "UPDATE ZZM010 SET ZZM_FECSUR='',ZZM_HORA='' WHERE ZZM_PEDIDO='".$_GET['ped']."' AND ZZM_FATURA='' AND D_E_L_E_T_=''") or die("Error al actualizar los datos en SURTIDO DE FACTURAS (ZZM)");
			$sql=odbc_exec($conn, "SELECT MAX(R_E_C_N_O_) AS reg FROM ZZS010 WHERE ZZS_PEDIDO='".$_GET['ped']."'") or die("Error al seleccionar el Registro de la salida (ZZS)");
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			odbc_exec($conn, "UPDATE ZZS010 SET ZZS_FAC2='F' WHERE R_E_C_N_O_=".$datos['reg']) or die("Error al actualizar la salida como no facturada (ZZS)");
			odbc_exec($conn,"UPDATE Z77010 SET Z77_FINSUR='', Z77_STATUS='S' WHERE Z77_ORDSUR = '".$_GET["ordsur"]."' AND Z77_STATUS = 'AU' AND D_E_L_E_T_=''")or die("Error al actualizar Z77");

			echo "<script languaje='JavaScript'>
					alert('El Pedido: ".$_GET['ped']." ha sido movido a Pedidos por auditar');
					location.href='index.php';					
				  </script>";
		}elseif($_GET['opc'] == 'pps' || $_GET['opc'] == 'pxs'){
			//Elimina la salida y sus partidas
			$sql=odbc_exec($conn, "SELECT MAX(R_E_C_N_O_) AS reg FROM ZZS010 WHERE ZZS_PEDIDO='".$_GET['ped']."'") or die("Error al seleccionar el Registro de la salida (ZZS)");
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			$sql=odbc_exec($conn, "SELECT ZZS_SALIDA FROM ZZS010 WHERE R_E_C_N_O_=".$datos['reg']) or die("Error al seleccionar la salida (ZZS) -".$datos['reg']);
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			//Elimina las partidas de la salida
			odbc_exec($conn, "DELETE FROM ZDS010 WHERE ZDS_SALIDA=".$datos['ZZS_SALIDA']) or die("Error al eliminar las partidas de la salida (ZDS)");
			//Elimina la salida
			odbc_exec($conn, "DELETE FROM ZZS010 WHERE ZZS_SALIDA=".$datos['ZZS_SALIDA']) or die("Error al eliminar la salida (ZZS)");
			if($_GET['opc']=='pps'){
				odbc_exec($conn,"UPDATE ZZM010 SET ZZM_MONTO='0',ZZM_FECFAC='',ZZM_FECSUR='',ZZM_HORA='',ZZM_CAJAS='0',ZZM_COSTAL='0',ZZM_EXHIB='0',ZZM_TINACO='0',ZZM_CODCLI='',ZZM_NOMCLI='' WHERE ZZM_FATURA='' AND ZZM_PEDIDO='".$_GET['ped']."'") or die("Error al actualizar el pedido a en proceso de surtido");
				odbc_exec($conn,"UPDATE Z77010 SET Z77_FYHSUR='', Z77_STATUS='CS' WHERE Z77_ORDSUR = '".$_GET["ordsur"]."' AND D_E_L_E_T_=''")or die("Error al actualizar Z77");

				echo "<script languaje='JavaScript'>
						alert('El Pedido: ".$_GET['ped']." ha sido movido a Pedidos en Proceso de Surtido');
						location.href='index.php';					
					  </script>";						  
			}elseif($_GET['opc']=='pxs'){
				//Elimina la partida en surtido de facturas
				odbc_exec($conn, "DELETE FROM ZZM010 WHERE ZZM_PEDIDO='".$_GET['ped']."' AND ZZM_FATURA='' AND D_E_L_E_T_=''") or die("Error al eliminar la partida en surtido de facturas (ZZM)");
				//Actualiza su estatus de impreso a Falso
				odbc_exec($conn, "UPDATE SC5010 SET C5_IMPRESO='F',C5_FYHRIMP='' WHERE C5_NUM='".$_GET['ped']."'") or die("Error al actualizar el estatus del pedido.");
				odbc_exec($conn,"UPDATE Z77010 SET Z77_FYHSUR='', Z77_STATUS='AC' WHERE Z77_ORDSUR = '".$_GET["ordsur"]."' AND D_E_L_E_T_=''")or die("Error al actualizar Z77");

				echo "<script languaje='JavaScript'>
						alert('El Pedido: ".$_GET['ped']." ha sido movido a Pedidos por Surtir');
						location.href='index.php';					
					  </script>";
			}			
		}else{
			//opc reimp
			//Reimpirmir con el boton "Impresion de pedido"
			$sql=odbc_exec($conn,"SELECT ZZN_NOMBRE FROM ZZM010 ZZM LEFT JOIN ZZN010 ON ZZM_CODALM=ZZN_CODIGO WHERE ZZM_PEDIDO='".$_GET["ped"]."' AND ZZM.D_E_L_E_T_<>'*'")or die("Error al realizar la consulta del pedido");
			if(odbc_num_rows($sql)==0){
		 		//El c�digo del almacenista no es valido
				echo "<script language='JavaScript'>
				  alert('El pedido no esta disponible');
				  window.open('index.php?','_self');
				  </script>";
			}else{
				$cons=odbc_fetch_array($sql);
				$nomAlm=$cons["ZZN_NOMBRE"];
				odbc_free_result($sql);
				//$sql=odbc_exec($conn,"SELECT C6_ITEM,C6_PRODUTO,B1_DESC,C6_QTDVEN,C6_PRUNIT,C6_VALOR,C6_PEDIM FROM SC6010 SC6 INNER JOIN SB1010 SB1 ON B1_COD=C6_PRODUTO WHERE C6_NUM='".$_GET["ped"]."' AND C6_NOTA='' AND C6_BLQ='' AND SC6.D_E_L_E_T_<>'*' AND SB1.D_E_L_E_T_<>'*' ORDER BY C6_PRODUTO")or die("Error al ejecutar la consulta de partidas del pedido");
					/* $sql=odbc_exec($conn,"
						SELECT 
							C6_TES,C6_LOCAL,C6_ITEM,C6_PRODUTO,B1_DESC,
							SUM(C6_QTDVEN-C6_QTDENT) AS PENDIENTE,
							C6_PRCVEN C6_PRUNIT,C6_PEDIM,B1_QE,B1_CANTBOL  
						FROM 
							SC6010 SC6 
							INNER JOIN SB1010 SB1 ON B1_COD=C6_PRODUTO
						WHERE 
							C6_NUM='".$_GET["ped"]."' AND C6_BLQ='' AND SC6.D_E_L_E_T_<>'*' AND 
							SB1.D_E_L_E_T_<>'*' AND C6_QTDVEN-C6_QTDENT > 0 
						GROUP BY 
							C6_TES,C6_ITEM,C6_PRODUTO,B1_DESC,C6_PRUNIT,C6_VALOR,C6_PEDIM,C6_PRCVEN,B1_QE,B1_CANTBOL,C6_LOCAL
						ORDER BY 
						C6_PRODUTO")or die("Error al ejecutar la consulta de partidas del pedido"); */
						if ($_GET["ordsur"] != "") {
							$var_ordsur =
							" LEFT JOIN (SELECT 
								Z46_CODPRO
							FROM Z46010 
							WHERE 
								Z46_PEDIDO = '" . $_GET["ped"] . "' AND  Z46_ORDSUR = '" . $_GET["ordsur"] . "' AND D_E_L_E_T_='' 
								AND Z46_VALBKO = 'F'
							GROUP BY Z46_CODPRO) Z46 ON Z46_CODPRO = C6_PRODUTO ";
						}else{
							$var_ordsur = "";
						}
						$consulta = "SELECT 
							C6_TES,C6_LOCAL,C6_ITEM,C6_PRODUTO,B1_DESC,
							SUM(C6_QTDVEN-C6_QTDENT) AS PENDIENTE,
							C6_PRCVEN C6_PRUNIT,C6_PEDIM,B1_QE,B1_CANTBOL  
						FROM 
							SC6010 SC6 
							INNER JOIN SB1010 SB1 ON B1_COD=C6_PRODUTO
							".$var_ordsur."
						WHERE 
							C6_NUM='" . $_GET["ped"] . "' AND C6_BLQ='' AND SC6.D_E_L_E_T_ = '' AND SB1.D_E_L_E_T_ = ''
							AND C6_QTDVEN-C6_QTDENT > 0 
						GROUP BY 
							C6_TES,C6_ITEM,C6_PRODUTO,B1_DESC,C6_PRUNIT,C6_VALOR,C6_PEDIM,C6_PRCVEN,B1_QE,B1_CANTBOL,C6_LOCAL
						ORDER BY C6_PRODUTO";
						$sql=odbc_exec($conn, $consulta) or die("Error al ejecutar la consulta de partidas del pedido");
				if(odbc_num_rows($sql)==0){
					echo "<script language='JavaScript'>
							alert('El pedido no cuenta con partidas disponibles por surtir.');
							console.log('".$consulta."');
							window.open('index.php','_self');
						  </script>";
				}else{
					$ped=odbc_exec($conn,"
						SELECT A1_TRANSP,A1_EST,C5_OBSVTA,C5_APFLETE,C5_CLIENTE,A1_NOME,C5_EMISSAO,A3_NOME,
							CASE WHEN A1_EST='JAL' THEN 'Local' ELSE 'Foraneo' END AS est,
							C5_FYHRVTA,C5_FYHRCYC,C5_FYHSURT,C5_FYHRIMP,C5_DIREMB,A1_END,A1_BAIRRO,A1_MUN+', '+A1_EST AS poblacion,C5_EMBCPED 
						FROM SC5010 SC5 
							INNER JOIN SA1010 SA1 ON A1_COD=C5_CLIENTE 
							LEFT JOIN SA3010 ON A3_COD=A1_VEND 
						WHERE C5_LOJAENT=A1_LOJA AND SC5.D_E_L_E_T_<>'*' AND SA1.D_E_L_E_T_='' AND C5_NUM='".$_GET["ped"]."'") or die("Error al ejecutar la consulta del pedido");
					$pedido=odbc_fetch_array($ped);
					echo "<html>
							 <head>
							  	<link rel='stylesheet' href='css/styles.css'/>
								<title>Impresi�n de Pedido</title>
							 </head>
							<body>
							<div class='page'>
								<img src='images/cancel.png' id='clsSM' title='Cerrar ventana' onClick='window.close();'/> 
									&nbsp;&nbsp;
								<img src='images/printer.png' id='prntSA' title='Imprimir' onClick='window.print();'/>
								<table>
									<tr>
										<td colspan='7'>
											<h1>Pedido de Venta Pendiente por Facturar</h1>
										</td>
									</tr>
									<tr>
										<td colspan='7'>
											<hr>
										</td>
									</tr>
									<tr>
										<th colspan='7' style='text-align:left'>Pedido: ".$_GET["ped"]." Cliente: ".$pedido["C5_CLIENTE"]." ".trim($pedido["A1_NOME"]).", Tipo: ".$pedido["est"].", Vendedor: ".$pedido["A3_NOME"]."</th>
									</tr>
									<tr>
										<td colspan='7'>
											<hr>
										</td>
									</tr>
									<tr class='trEnc'>
										<td>Item</td>
										<td>Producto</td>
										<td>Descripci&oacute;n</td>
										<td>TES</td>
										<td>Almac&eacute;n</td>
										<td>P. Unit</td>
										<td>Cantidad</td>
										<td>Cajas</td>
										<td>Valor Total</td>
									</tr>";
					$valor=0;
					while($datos=odbc_fetch_array($sql)){
						$almacen = $datos["C6_LOCAL"];
						$valor += $datos["C6_VALOR"];
						$cajas = floor($datos["PENDIENTE"] / $datos["B1_QE"]);
						$bolsas = floor(($datos["PENDIENTE"] - ($datos["B1_QE"] * $cajas)) / $datos["B1_CANTBOL"]);
						$resto = $datos["PENDIENTE"] - ($datos["B1_QE"] * $cajas) - ($datos["B1_CANTBOL"] * $bolsas);
						 //echo "<tr class='tdDatos'><td>$datos[C6_ITEM]</td><td>$datos[C6_PRODUTO]</td><td>$datos[B1_DESC]</td><td>$datos[C6_PEDIM]</td><td class='tdD'>$".number_format($datos["C6_PRUNIT"],2)."</td><td class='tdD'>".number_format($datos["C6_QTDVEN"],0)."</td><td class='tdD'>$".number_format($datos["C6_VALOR"],2)."</td></tr>";
						$sub = $datos["PENDIENTE"] * $datos["C6_PRUNIT"]; 
						echo "
						 	<tr class='tdDatos'>
						 		<td>".$datos["C6_ITEM"]."</td>
						 		<td>".$datos["C6_PRODUTO"]."</td>
						 		<td>".$datos["B1_DESC"]."</td>
						 		<td style='text-align:center;'>".$datos["C6_TES"]."</td>
						 		<td>".$datos["C6_LOCAL"]."</td>
						 		<td class='tdD'>$".number_format($datos["C6_PRUNIT"],2)."</td>
						 		<td class='tdD'>".number_format($datos["PENDIENTE"],0)."</td>
						 		<td class='tdD'>".($cajas>0?$cajas."C ":"").($bolsas>0?$bolsas."B ":"").($resto>0?$resto."R ":"")."</td>
						 		<td class='tdD'>$".number_format($sub,2)."</td>
						 	</tr>";
					}
					odbc_free_result($sql);
				 	echo "
						 		<tr>
						 			<td colspan='6'>Total del pedido:</td>
						 			<td class='tdD'><hr>$".number_format($valor,2)."</td>
						 		</tr>
						 	</table>
					 		<strong>
					 		<hr>Observaciones: ".$pedido["C5_OBSVTA"]."
					 		<br><br>
					 		<hr>&nbsp;***Enviar a: ";
					if(trim($pedido['C5_DIREMB'])=="FISCAL"){
						echo "[FISCAL] ".trim($pedido['A1_END']).", Col. ".trim($pedido['A1_BAIRRO']).", Mun. ".trim($pedido['poblacion']);
						$flet=odbc_exec($conn,"
							SELECT 
								A2_NREDUZ,Z14_NREDUZ 
							FROM 
								Z15010 Z15 
								INNER JOIN Z14010 Z14 ON Z14.D_E_L_E_T_='' AND Z14_COD=Z15_CODZON 
								INNER JOIN SA2010 A2 ON A2.D_E_L_E_T_='' AND A2_COD=Z15_CODFLE 
							WHERE 
								Z15_CODFLE='".$pedido['A1_TRANSP']."' AND Z15.D_E_L_E_T_='';") or die("Error al ejecutar la zona");
						if($fletera=odbc_fetch_array($flet)){
							$zonaflet=$fletera['Z14_NREDUZ'];
							$nomflet=$fletera['A2_NREDUZ'];
						}else{
							$zonaflet="N/A";
							$nomflet="N/A";
						}
						odbc_free_result($flet);
					}else{
						$sqlD=odbc_exec($conn,"
							SELECT * 
							FROM ZD1010 
							WHERE ZD1_CLAVE='".$pedido["C5_DIREMB"]."' AND ZD1_CLIENT='".$pedido["C5_CLIENTE"]."' AND D_E_L_E_T_=''")or die("Error DirEmb");
						$dEmb=odbc_fetch_array($sqlD);
						odbc_free_result($sqlD);
						echo trim($dEmb['ZD1_DIRECC']).", Col. ".trim($dEmb['ZD1_COLON']).", Mun. ".trim($dEmb['ZD1_POBLAC']);
						$flet=odbc_exec($conn,"
							SELECT 
								A2_NREDUZ,Z14_NREDUZ 
							FROM Z15010 Z15 
								INNER JOIN Z14010 Z14 ON Z14.D_E_L_E_T_='' AND Z14_COD=Z15_CODZON 
								INNER JOIN SA2010 A2 ON A2.D_E_L_E_T_='' AND A2_COD=Z15_CODFLE 
							WHERE Z15_CODFLE='".$dEmb['ZD1_FLETE']."' AND Z15.D_E_L_E_T_='';") or die("Error al ejecutar la zona");
						if($fletera=odbc_fetch_array($flet)){
							$zonaflet=$fletera['Z14_NREDUZ'];
							$nomflet=$fletera['A2_NREDUZ'];
						}else{
							$zonaflet="";
							$nomflet="";
						}
						odbc_free_result($flet);
					}
					
					//OBTIENE LA FECHA DE SURTIDO EN FORMATO M-D-Y
					//CALCULA LA FECHA DE SURTIDO MENOS 3HR
					$fechasurt = substr($pedido['C5_FYHSURT'],3,2)."/".substr($pedido['C5_FYHSURT'],0,2).substr($pedido['C5_FYHSURT'],5);

					$valfechasur = strtotime($fechasurt);

					if(strlen(trim($pedido['C5_FYHSURT']))==16 || strlen(trim($pedido['C5_FYHSURT']))==17){
						$iniciolab=substr($pedido['C5_FYHSURT'],3,2)."/".substr($pedido['C5_FYHSURT'],0,2).substr($pedido['C5_FYHSURT'],5,3)." 08:00:00";
					}elseif(strlen(trim($pedido['C5_FYHSURT']))==18 || strlen(trim($pedido['C5_FYHSURT']))==19){
						$iniciolab=substr($pedido['C5_FYHSURT'],3,2)."/".substr($pedido['C5_FYHSURT'],0,2).substr($pedido['C5_FYHSURT'],5,5)." 08:00:00";
					}
					$valiniciolab = strtotime($iniciolab);
					$sec1 = $valfechasur - $valiniciolab;
					if($sec1 >= 10800){
						//SI EL TIEMPO DISPONIBLE ES MAYOR A 3 HORAS LO RESTA
						//$valfechasur-=10800;
						$surtalm = date("d/m/y H:i:s",($valfechasur-10800));
					}
					else{
						$sec2 = 10800 - $sec1;
						//restar al dia anterior al cierre los minutos restantes
						$finlab = date("m/d/y",strtotime("-1 day",$valfechasur))." 18:00:00";
						$valfinlab = strtotime($finlab);
						$valfinlab -= $sec2;
						$surtalm = date("d/m/y H:i:s",$valfinlab);
					}
					//echo"<hr>Almacen No: ".trim($almacen)."<br>";
					echo"***<hr>Almacenista: ".trim($nomAlm)."<br>
						<table class='tdRes'>
							<tr>
								<td>Emisi&oacute;n</td>
								<td>Aprobaci&oacute;n Ventas</td>
								<td>Aprobaci&oacute;n CyC</td>
								<td>Limite Almac&eacute;n</td>
								<td>Limite Surtido</td>
								<td>Impresi&oacute;n</td>
							</tr>
							<tr>
								<td>".substr($pedido["C5_EMISSAO"],6)."/".substr($pedido["C5_EMISSAO"],4,2)."/".substr($pedido["C5_EMISSAO"],0,4)."</td>
								<td>".$pedido["C5_FYHRVTA"]."</td>
								<td>".$pedido["C5_FYHRCYC"]."</td>
								<td>".$surtalm."</td>
								<td>".$pedido["C5_FYHSURT"]."</td>
								<td>".date("d/m/Y H:i:s",time())."</td>
								</tr>
						</table></strong>
						<br><br>
						<center>
							<font face='cb' size='+3'>*".$_GET["ped"]."*</font><br>[".$_GET["ped"]."]
						</center>";
						if($pedido["C5_APFLETE"]=="T"){
							echo "<span class='text2'>Flete pagado autorizado por Fleximatic S.A. de C.V.</span>";
						}
						If(trim($pedido['C5_EMBCPED'])!=""){
							echo "
							<br><br>
							<span class='text2'>ESTE PEDIDO ESTA ASOCIADO CON EL PEDIDO ".trim($pedido['C5_EMBCPED']).".</span>";
						}
					echo "<br><br>En la columna Cajas:&nbsp;&nbsp;&nbsp;C=Cajas&nbsp;&nbsp;&nbsp;B=Bolsas/Inner&nbsp;&nbsp;&nbsp;R=Resto
							</div>
						</body>
					</html>";
				}
			}
		}
	}else{
		echo "
		<div class='close'>
			<button id='bC' class='cerrar'>x</button>
		</div>
		<br>";
		$sql=odbc_exec($conn,"SELECT ISNULL(SUM(ZZM_CAJAS+ZZM_COSTAL+ZZM_EXHIB+ZZM_TINACO),0) AS total FROM ZZM010 WHERE ZZM_PEDIDO='".$_GET["ped"]."' AND ZZM_FATURA='' AND D_E_L_E_T_=''")
		or die("Error al obtener cantidad a surtir del pedido");
		$datos=odbc_fetch_array($sql);
		odbc_free_result($sql);
		if($datos["total"]==0){
			odbc_exec($conn,"DELETE ZZM010 WHERE ZZM_PEDIDO='".$_GET["ped"]."' AND ZZM_FATURA='' AND D_E_L_E_T_=''")or die("Error al borrar el pedido en ZZM");
			odbc_exec($conn,"UPDATE SC5010 SET C5_FYHRIMP='',C5_IMPRESO='F',C5_URGENTE='F' WHERE C5_NUM='".$_GET["ped"]."' AND D_E_L_E_T_=''") or die("Error al actualizar el estatus al pedido");
			odbc_exec($conn,"UPDATE Z77010 SET Z77_FYHSUR='', Z77_STATUS='AC' WHERE Z77_ORDSUR = '".$_GET["ordsur"]."' AND Z77_STATUS = 'S' AND D_E_L_E_T_=''")or die("Error al actualizar Z77");
			echo "<h1>Pedido movido a pendientes por surtir</h1>";
		}else{
			echo "<h1>No es posible desmarcar el pedido en proceso de surtido ya que tiene una salida capturada</h1>";
		}
	}
	odbc_close($conn);
?>