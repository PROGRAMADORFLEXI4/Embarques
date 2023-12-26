<?PHP
	include("conectabd.php");
	//Guarda las observaciones de embarques
	if(isset($_GET["emb"])==1)
	{
		odbc_exec($conn,"UPDATE ZZO010 SET ZZO_OBSEMB='".$_POST["txtObs"]."' WHERE R_E_C_N_O_=".$_GET["regis"]." AND D_E_L_E_T_=''")
		or die("Error al actualizar las observaciones de embarques");
		echo "<script languaje='JavaScript'>
				window.close();
			  </script>";
	}
	elseif(isset($_GET["actPedi2"])=="T")  	//Valida si la actualización es una observación en el Pedido
	{
		odbc_exec($conn,"UPDATE SC5010 SET C5_OBSPED='".$_POST["txtObs"]."' WHERE C5_NUM='".$_POST["txtPed"]."' AND D_E_L_E_T_=''")
		or die("Error al actualizar las observaciones del Pedido");
		echo "<script languaje='JavaScript'>
				window.close();
			  </script>";
	}
	else
	{
		//Valida que el código del almacenista este registrado
		$sql=odbc_exec($conn,"SELECT ZZN_CODIGO,ZZN_NOMBRE FROM ZZN010 WHERE ZZN_CODIGO='$_POST[txtCodAlm]' AND D_E_L_E_T_<>'*'")or die("Error al realizar la consulta del almacenista");
		if(odbc_num_rows($sql)==0){
		//El código del almacenista no es valido
			echo "<script language='JavaScript'>
				  alert('El codigo del almacenista no esta registrado');
				  history.back();
				  </script>";
		}
		else
		{
			$cons=odbc_fetch_array($sql);
			$codAlm=$cons["ZZN_CODIGO"];
			$nomAlm=$cons["ZZN_NOMBRE"];
			$valor=0;
			odbc_free_result($sql);
			$sql=odbc_exec($conn,"SELECT C6_ITEM,C6_PRODUTO,B1_DESC,C6_QTDVEN-C6_QTDENT AS C6_QTDVEN,C6_PRUNIT,C6_VALOR,C6_PEDIM FROM SC6010 SC6 INNER JOIN SB1010 SB1 ON B1_COD=C6_PRODUTO INNER JOIN(SELECT C9_PRODUTO,C9_ITEM FROM SC9010 WHERE C9_PEDIDO='".$_POST["txtPed"]."' AND C9_BLCRED<>'09' AND C9_BLCRED<>'10' AND D_E_L_E_T_='' GROUP BY C9_PRODUTO,C9_ITEM) AS SC9 ON SC9.C9_PRODUTO=C6_PRODUTO AND SC9.C9_ITEM=C6_ITEM WHERE C6_NUM='".$_POST['txtPed']."' AND C6_QTDVEN-C6_QTDENT>0 AND C6_BLQ='' AND SC6.D_E_L_E_T_<>'*' AND SB1.D_E_L_E_T_<>'*' ORDER BY C6_PRODUTO")or die("Error al ejecutar la consulta de partidas del pedido");
			if(odbc_num_rows($sql)==0)
			{
				echo "<script language='JavaScript'>
						alert('El pedido no cuenta con partidas disponibles por surtir');
						history.back(-1);
					  </script>";
			}
			else
			{
				$ped=odbc_exec($conn,"SELECT C5_OBSVTA,C5_APFLETE,C5_CLIENTE,A1_NOME,C5_EMISSAO,A3_NOME,CASE WHEN A1_EST='JAL' THEN 'Local' ELSE 'Foraneo' END AS est,C5_FYHRVTA,C5_FYHRCYC,C5_FYHRIMP,C5_DIREMB,A1_END,A1_BAIRRO,A1_MUN+', '+A1_EST AS poblacion FROM SC5010 SC5 INNER JOIN SA1010 SA1 ON A1_COD=C5_CLIENTE LEFT JOIN SA3010 ON A3_COD=A1_VEND WHERE C5_LOJAENT=A1_LOJA AND SC5.D_E_L_E_T_<>'*' AND SA1.D_E_L_E_T_='' AND C5_NUM='".$_POST["txtPed"]."'")or die("Error al ejecutar la consulta del pedido");
				$pedido=odbc_fetch_array($ped);
				 echo "<html>
						 <head>
	 						<link href='css/styles.css' rel='stylesheet' type='text/css'>
						    <script src='css/jquery.js'></script>							
								<script>
									$(document).on('ready', function(){ 
										imprimir();
$.post('nuevo.php',{ped:'$_POST[txtPed]',alm:'$_POST[txtCodAlm]'},function(){
window.open('index.php');
window.close();
																		   });
										function imprimir(){
										  window.print();
										}
										
										$('#btnAcep').on('click', function(){
										   $('button').hide();
										   $.post('nuevo.php',{ped:'$_POST[txtPed]',alm:'$_POST[txtCodAlm]'},function(){location.href='index.php';});
									   });
										$('#btnCanc').on('click',function(){history.back();});
									});
								</script>
						 </head>
						<body class='bdBco'>
						<div id='fN' class='cerrar'></div>						
						<div id='dvAlm'><h1>Se imprimio correctamente el Pedido?</h1><div class='btnLinea'><center><button id='btnAcep'>Aceptar</button> <button id='btnCanc'>Cancelar</button> <button id='btnIm'>Imprimir</button></center></div></div>
						<div class='page'>
							<table><tr><td colspan='7'><h1>Pedido de Venta Pendiente por Facturar</h1></td></tr>
							<tr><th colspan='7' style='text-align:left'>Pedido: ".$_POST["txtPed"]." Cliente: ".$pedido["C5_CLIENTE"]." ".trim($pedido["A1_NOME"]).", Tipo: ".$pedido["est"].", Vendedor: ".$pedido["A3_NOME"]."</th></tr>
							<tr><td colspan='7'><hr></td></tr>
							<tr class='trEnc'><td>Item</td><td>Producto</td><td>Descripci&oacute;n</td><td>Pedimento</td><td>P. Unit</td><td>Cantidad</td><td>Valor Total</td></tr><tr><td colspan='7'><hr></td></tr>";
						 while($datos=odbc_fetch_array($sql)){
							 $valor+=$datos["C6_VALOR"];
							 $sub = $datos["C6_QTDVEN"] * $datos["C6_PRUNIT"];
							 //echo "<tr><td>".$datos["C6_ITEM"]."</td><td>".trim($datos["C6_PRODUTO"])."</td><td>".trim($datos["B1_DESC"])."</td><td>".trim($datos["C6_PEDIM"])."</td><td class='tdD'>$".number_format($datos["C6_PRUNIT"],2)."</td><td class='tdD'>".number_format($datos["C6_QTDVEN"],0)."</td><td class='tdD'>$".number_format($datos["C6_VALOR"],2)."</td></tr>";
							 echo "<tr><td>".$datos["C6_ITEM"]."</td><td>".trim($datos["C6_PRODUTO"])."</td><td>".trim($datos["B1_DESC"])."</td><td>".trim($datos["C6_PEDIM"])."</td><td class='tdD'>$".number_format($datos["C6_PRUNIT"],2)."</td><td class='tdD'>".number_format($datos["C6_QTDVEN"],0)."</td><td class='tdD'>$".number_format($sub,2)."</td></tr>";
						 }
						 odbc_free_result($sql);
						 echo "<tr><td colspan='6'>Total del pedido:</td><td class='tdD'><hr>$".number_format($valor,2)."</td></tr></table><strong>
							<hr>Observaciones: ".$pedido["C5_OBSVTA"]."<br><br><hr> &nbsp;***Enviar a: ";
							if(trim($pedido['C5_DIREMB'])=="FISCAL")
								echo "[FISCAL] ".trim($pedido['A1_END']).", Col. ".trim($pedido['A1_BAIRRO']).", Mun. ".trim($pedido['poblacion']);
							else{
								$sqlD=odbc_exec($conn,"SELECT * FROM ZD1010 WHERE ZD1_CLAVE='$pedido[C5_DIREMB]' AND ZD1_CLIENT='$pedido[C5_CLIENTE]' AND D_E_L_E_T_=''")or die("Error DirEmb");
								$dEmb=odbc_fetch_array($sqlD);
								odbc_free_result($sqlD);
								echo trim($dEmb['ZD1_DIRECC']).", Col. ".trim($dEmb['ZD1_COLON']).", Mun. ".trim($dEmb['ZD1_POBLAC']);
							}
						echo"***<hr>Almacenista: ".trim($nomAlm)."<br><table class='tdRes'><tr><td>Fecha de Emisi&oacute;n</td><td>Fecha y Hora de Aprobaci&oacute;n Ventas</td><td>Fecha y Hora de Aprobaci&oacute;n CyC</td><td>Fecha y Hora de Impresi&oacute;n</td></tr>
							<tr><td>".substr($pedido["C5_EMISSAO"],6)."/".substr($pedido["C5_EMISSAO"],4,2)."/".substr($pedido["C5_EMISSAO"],0,4)."</td><td>$pedido[C5_FYHRVTA]</td><td>$pedido[C5_FYHRCYC]</td><td>".date("d/m/Y H:i:s",time())."</td></tr></table></strong>
							<br><br><center><font face='cb' size='+3'>*".$_POST["txtPed"]."*</font><br>[".$_POST["txtPed"]."]</center>";
							if($pedido["C5_APFLETE"]=="T")
								echo "Flete pagado autorizado por Fleximatic S.A. de C.V.";
					echo "</div>
						</body>
					</html>";
			}
		}
	}
	odbc_close($conn);
?>