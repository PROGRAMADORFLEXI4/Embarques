<!DOCTYPE html>
<html>
	<head>
		<script type='text/javascript' src='js/jquery.min.js'></script>
	  	<script language="javascript">

			function actualizaQE(campo,qe){
				var nCampo;
				if(campo.id.substr(0,5) == "txtQE"){
					if(campo.id.substr(campo.id.length-2) == "-F"){
						nCampo=campo.id.substr(5,campo.id.length-7);
					}else{
						nCampo=campo.id.substr(5);
					}
				}else{
					nCampo=campo.id.substr(3)
				}
				location.href="guardaTempo.php?Ped="+btoa(document.getElementById("txtPedi2").value)+"&ordsur="+btoa(document.getElementById("txtOrdSur").value)+"&cant="+campo.value+"&cod="+nCampo+"&qe="+qe;
			}
			
			function agrupa(campo){
				var control = document.getElementById("txtEmp"+campo.id.substr(3)+"-F");
				if(campo.value==0){
					control.disabled=false;
					control.value=1;
				}
				else{
					control.disabled=true;
					control.value=0;
				}
			}
			
			function guardaPed(){
				var controles=document.getElementById("SalxPedi2");
				var codigos=""
				for(i=0;i<controles.length;i++)
				{
					if(controles.elements[i].id.substr(0,7)=="txtSurS")
					{
						if(controles.elements[i].id.indexOf("-F")>0)
						{
							if(parseInt(controles.elements[i+3].value)>0)
							{
								if(controles.elements[i+2].value!=0)
									controles.elements[i+8].value=0;
								codigos+="[|"+controles.elements[i].id.substr(7,controles.elements[i].id.length-9)+" | "+controles.elements[i].value+" | "+controles.elements[i+3].value+" | "+controles.elements[i+7].value+" | "+controles.elements[i+8].value+" | "+controles.elements[i+6].value+" | "+							controles.elements[i+4].checked+" | "+controles.elements[i+2].value+"|]";
							}
						}
						else
						{
							if(controles.elements[i+2].id=="tindec" && controles.elements[i+2].value>0)
							{
								if(controles.elements[i+5].id.substr(0,3)=="txt")
									codigos+="[|"+controles.elements[i].id.substr(7)+" | "+controles.elements[i].value+" | "+controles.elements[i+1].value+" | "+controles.elements[i+5].value+" | 0 | "+controles.elements[i+4].value+" |  | "+controles.elements[i+2].value+"|]";
								else
									codigos+="[|"+controles.elements[i].id.substr(7)+" | "+controles.elements[i].value+" | "+controles.elements[i+1].value+" | "+controles.elements[i+7].value+" | 0 | "+controles.elements[i+6].value+" |  | "+controles.elements[i+2].value+"|]";
							}
							else
							{
								if(controles.elements[i+4].id.substr(0,3)=="txt")
								{
									if(controles.elements[i+2].id=="tindec")
										codigos+="[|"+controles.elements[i].id.substr(7)+" | "+controles.elements[i].value+" | "+controles.elements[i+1].value+" | "+controles.elements[i+5].value+" | 0 | "+controles.elements[i+4].value+" |  | "+controles.elements[i-1].value+"|]";
									else
									{
										if(controles.elements[i+2].id.indexOf("-T")>0)
											codigos+="[|"+controles.elements[i].id.substr(7,controles.elements[i].id.length-9)+" | "+controles.elements[i].value+" | "+controles.elements[i+1].value+" | "+controles.elements[i+4].value+" | 0 | "+controles.elements[i+3].value+" |  | "+controles.elements[i-1].value+"|]";
										else
											codigos+="[|"+controles.elements[i].id.substr(7)+" | "+controles.elements[i].value+" | "+controles.elements[i+1].value+" | "+controles.elements[i+4].value+" | 0 | "+controles.elements[i+3].value+" |  | "+controles.elements[i-1].value+"|]";
									}
								}
								else
								{
									if(controles.elements[i+6].id.substr(0,8)=="txtPedim")
										codigos+="[|"+controles.elements[i].id.substr(7)+" | "+controles.elements[i].value+" | "+controles.elements[i+1].value+" | "+controles.elements[i+6].value+" | 0 | "+controles.elements[i+5].value+" | "+controles.elements[i+3].checked+" | "+controles.elements[i-1].value+"|]";
									else
										codigos+="[|"+controles.elements[i].id.substr(7)+" | "+controles.elements[i].value+" | "+controles.elements[i+1].value+" | "+controles.elements[i+7].value+" | 0 | "+controles.elements[i+6].value+" | "+controles.elements[i+4].checked+" | "+controles.elements[i-1].value+"|]";
								}
							}
						}
					}
				}

				if(codigos!=""){
					controles.elements[i-1].value;
				}

				$.ajax({
					type: "POST",
					url: "codigos.php",
					data: {loscodigos: codigos},
					success: function(resultado){
						if(resultado!=""){
							//alert(resultado);
							location.href = "guardaSalida.php?Ped="+document.getElementById("txtPedi2").value+"&ordsur="+btoa(document.getElementById("txtOrdSur").value)+"&Alm="+document.getElementById("txtCodAlm").value+"&Cli="+document.getElementById("txtCliente").value+"&Codigos=sesion";
						}
					}
				});
					//location.href="guardaSalida.php?Ped="+document.getElementById("txtPedi2").value+"&Alm="+document.getElementById("txtCodAlm").value+"&Cli="+document.getElementById("txtCliente").value+"&Codigos="+codigos;
			}
			
			function soloNumeros(campo){
				var key=window.event.keyCode;
				if(key==13){
					var Campo = document.getElementById("txtEmp"+campo.id.substr(7));
					if(Campo.disabled == false){
						Campo.focus();
					}
				}else{
					if(key<47 || key>57){
						window.event.keyCode=0;
					}
				}
			}
			
			function mayorSolicitado(campo,pedido){
				var solicita2=document.getElementById("txtSurS"+campo.id.substr(7));
				var modifica2=document.getElementById("txtMod"+campo.id.substr(7));
				var tempo;
				if(isNaN(parseInt(campo.value))){
					alert("El valor no es correcto");
					campo.value=solicita2.value;
					campo.focus();
				}else{
					if(parseInt(campo.value)>parseInt(solicita2.value)){
						alert("La cantidad de la salida es mayor a la cantidad solicitada");
						campo.value=solicita2.value;
						campo.focus();
					}else{
						if(parseInt(campo.value)!=parseInt(modifica2.value)){
							if(campo.id.indexOf("-F")>1){
								tempo=(campo.id.substr(7,campo.id.indexOf("-F")-7));
							}else{
								if(campo.id.indexOf("-T")>1){
									tempo=(campo.id.substr(7,campo.id.indexOf("-T")-7));
								}else{
									tempo=campo.id.substr(7);
								}
							}
							if(campo.id.indexOf("-F")>0 || campo.id.indexOf("-T")>0){
								location.href="guardaTempo.php?Ped="+btoa(pedido)+"&ordsur="+btoa(document.getElementById("txtOrdSur").value)+"&opc=U&cod="+tempo+"&cant="+parseInt(campo.value)+"&sol="+parseInt(solicita2.value);
							}else{
								location.href="guardaTempo.php?Ped="+btoa(pedido)+"&ordsur="+btoa(document.getElementById("txtOrdSur").value)+"&opc=O&cod="+tempo+"&cant="+parseInt(campo.value)+"&sol="+parseInt(solicita2.value);
							}
						}
					}
				}
			}

		</script>
		<title>Salida por Pedido</title>
	  	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<style type="text/css">
			/*body {
				background-image: url(images/fondo.png);
			}
			body,td,th {
				font-family: Times New Roman, Times, serif;
			}*/
		</style>
	</head>
<body>
	<form id='SalxPedi2' method='post'>
	<center>
	<?php
		//Totales Exhibidor, tindec, costales, cajas
		$exhi=0;
		$tinaco=0;
		$tinacos=0;
		$costal=0;
		$caja=0;
		$empaknTindec="";
		$codIn="";
		$cantidad=0;
		$registros=0;
		$bgColor="#CCCCCC";
		$bgOColor="#CCCCCC";
		include("conectabd.php");
		$sql=odbc_exec($conn,"
			SELECT C5_NUM,C5_EMISSAO,A1_COD,A1_NOME 
			FROM SC5010 SC5 
			INNER JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD 
			WHERE C5_NUM='".base64_decode($_GET["Ped"])."' AND C5_NOTA='' AND SC5.D_E_L_E_T_='' AND SA1.D_E_L_E_T_=''") or die("Error al validar el pedido en SC5");

		if(odbc_num_rows($sql)>0){
			$datos=odbc_fetch_array($sql);
			echo "
				<table border='1'>
					<tr>
						<strong>
							<td rowspan='2'><img src='images/logo.png' height='60' width='100'></td>
							<td align='center' colspan='2'>Fleximatic S.A. de C.V.</td>
							<td align='center'><input type='button' name='bntGuardar' value='Guardar' onclick='this.disabled=true;guardaPed()'></td>
						</strong>
					</tr>
					<tr>
						<td align='center' colspan='2'><strong>SALIDA DE MERCANC&Iacute;A PEDIDO DEL ALMACEN DE PRODUCTO TERMINADO</strong></td>
						<td align='center'><input type='button' name='btnCancelar' value='Cancelar' onclick=location.href='index.php'></td>
					</tr>
					<tr></tr>
					<tr>
						<td><strong>CLIENTE:</strong></td>
						<td><input type='hidden' id='txtCliente' value='".$datos["A1_COD"]."'>".trim($datos["A1_NOME"])."</td>
						<td><strong>FECHA PEDIDO:</strong></td>
						<td>".substr($datos["C5_EMISSAO"],6)."/".substr($datos["C5_EMISSAO"],4,2)."/".substr($datos["C5_EMISSAO"],0,4)."</td>
					</tr>
					<tr>
						<td><strong>PEDIDO:</strong></td>
						<td><input type='hidden' id='txtPedi2' value='".$datos["C5_NUM"]."'>".$datos["C5_NUM"]."</td>
						<input type='hidden' id='txtOrdSur' value='".base64_decode($_GET['ordsur'])."'>
						<td><strong>FECHA SALIDA:</strong></td>
						<td>".date("d/m/Y")."</td>
					</tr>";
			odbc_free_result($sql);
			/*Si hay partidas sin facturar realiza la busqueda*/
			$sql = odbc_exec($conn,"
				SELECT 
					Id,Producto AS C6_PRODUTO,Pedimento AS C6_PEDIM,B1_DESC,B1_TIPO,B1_CLASE,Empaque,Qe AS costal,
					CASE WHEN Qe=0 THEN 1 ELSE Qe END AS B1_QE,Cant AS cantR,SUM(C6_QTDVEN-C6_QTDENT) AS Solicitado 
				FROM tempo 
					LEFT JOIN SC6010 SC6 ON producto=C6_PRODUTO 
					INNER JOIN SB1010 SB1 ON B1_COD=Producto 
					LEFT JOIN SC9010 SC9 ON C6_NUM=C9_PEDIDO 
				WHERE 
					Pedido='".base64_decode($_GET["Ped"])."' AND C6_NUM='".base64_decode($_GET["Ped"])."' AND 
					C6_PRODUTO=C9_PRODUTO AND C6_ITEM=C9_ITEM AND C9_BLCRED<>'09' AND
					SB1.D_E_L_E_T_<>'*' AND SC6.D_E_L_E_T_='' AND SC9.D_E_L_E_T_='' 
				GROUP BY 
					Id,Producto,Pedimento,B1_DESC,B1_TIPO,B1_CLASE,Empaque,Qe,Cant 
				ORDER BY B1_QE,Producto")or die("Error al obtener las partidas del pedido SC6");

			if(odbc_num_rows($sql)>0){
				echo "<tr>
				<td colspan='4'>
				<table width='100%' border='0' cellspacing='1'>
				<tr>
					<th>CANT.</th>
					<th>[PRODUCTO] DESCRIPCI&Oacute;N</th>
					<th>PEDIMENTO</th>
					<th>TINDEC</th>
					<th>EXHIBIDORES</th>
					<th>COSTALES</th>
					<th>CAJAS</th>
				</tr>";
			while($datos=odbc_fetch_array($sql)){
				//Si hay tinacos en el pedido, habilitar a las cajas completas para que puedan ir dentro del tinaco seleccionado
				if($tinaco>0 && $datos["B1_CLASE"]<>"11"){
					$empaknTindec="<select id='tindec'><option value='0' selected>0</option>";
					for($ind=1;$ind<=$tinaco;$ind++){
						$empaknTindec.="<option value='".$ind."'>".$ind."</option>";
					}
					$empaknTindec.="</select>";
				}
				/*Si no se acompleta la caja manda a piezas sueltas*/
				if($datos["B1_QE"]>$datos["cantR"] || $datos["costal"]=="0"){
					$codIn.="<tr bgcolor=".$bgOColor."><td align='center'><input type='hidden' id='txtSurS".$datos["Id"]."-F' value='".round($datos["Solicitado"])."'><input type='hidden' id='txtMod".$datos["Id"]."-F' value='".round($datos["cantR"])."'><select id='cmb".trim($datos['C6_PRODUTO'])."'></select><input type='text' id='txtSurR".trim($datos["Id"])."-F' value='".round($datos["cantR"])."' size='3' maxlength='5' style='text-align:right' onkeypress='soloNumeros(this)' onblur=mayorSolicitado(this,'".base64_decode($_GET["Ped"])."')>";

					if($datos["Empaque"]=="T"){
						$codIn.=" Costal<input type='radio' value='T' name='ml$datos[Id]' id='rdb".$datos["Id"]."-F' checked> Caja<input type='radio' value='F' name='ml$datos[Id]' id='rdb".$datos["Id"]."-F'>";
					}else{
						$codIn.=" Costal<input type='radio' name='mul$datos[Id]' value='T' id='rdb".$datos["Id"]."-F'> Caja<input type='radio' name='mul$datos[Id]' value='F' id='rdb".$datos["Id"]."-F' checked>";
					}
								
					if($datos["B1_TIPO"]=="04"){
						$codIn.="[PxE]:<input type='text'";
					}else{
						$codIn.="<input type='hidden'";
					}

					$codIn.=" id='txtQE".$datos["Id"]."-F' value='".intval($datos["B1_QE"])."' size='3' maxlength='5' onkeypress='soloNumeros(this)' style='text-align:right' style='background-color:#CCC' onchange='actualizaQE(this,1)'></td><td><font size='-1'>[".trim($datos["C6_PRODUTO"])."] ".trim($datos["B1_DESC"])."</font></td><td><input type='hidden' value='".trim($datos["C6_PEDIM"])."' id='txtPedim".$datos["Id"]."-F'><font size='-1'>".trim($datos["C6_PEDIM"])."</font></td><td></td><td></td><td></td><td align='center'><input type='text' value='1' style='text-align:right' style='background-color:".$bgOColor."' maxlength='5' size='2' id='txtEmp".$datos["Id"]."-F' onkeypress='soloNumeros(this)'></td></tr>";
					if($bgOColor=="#FFFFFF"){
						$bgOColor="#CCCCCC";
					}else{
						$bgOColor="#FFFFFF";
					}
				}else{
					if($bgColor=="#FFFFFF"){
						$bgColor="#CCCCCC";
					}else{
						$bgColor="#FFFFFF";
					}
					/*Cierra las cajas y si hay residuo las manda a piezas sueltas "Si no se surte nada del codigo poner cantidad en 0
					  al resto no se le agrega las opciones si es comercializado*/
					if(intval($datos["cantR"]/$datos["B1_QE"])<>ceil($datos["Solicitado"]/$datos["B1_QE"])){
						$registros+=1;
						$cantidad=intval($datos["cantR"]/$datos["B1_QE"]);
						echo "<tr bgColor='".$bgColor."'>
								<td align='center'><input type='hidden' id='txtReg".$datos["Id"]."' value='".$registros."'><input type='hidden' id='txtSurS".$datos["Id"]."' value='".intval($cantidad*$datos["B1_QE"])."'><input type='hidden' id='txtMod".$datos["Id"]."' value='".intval($cantidad*$datos["B1_QE"])."'>[".$registros."] ";
						/*Si hay tinacos, agrega el combo*/
						if(trim($empaknTindec)<>""){
							echo $empaknTindec;
						}
						echo "<input type='text' id='txtSurR".$datos["Id"]."' value='".intval($cantidad*$datos["B1_QE"])."' size='3' maxlength='5' style='text-align:right' onkeypress='soloNumeros(this)' onblur=mayorSolicitado(this,'".base64_decode($_GET["Ped"])."')>";

						if($datos["B1_TIPO"]=="04"){
							if($datos["Empaque"]=="T"){
								echo " Costal<input type='radio' value='T' id='rdb".$datos["Id"]."' checked onclick='actualizaQE(this,2)'> Caja<input type='radio' value='F' id='rdb".$datos["Id"]."' onclick='actualizaQE(this,2)'>";
							}else{
								echo " Costal<input type='radio' value='T' id='rdb".$datos["Id"]."' onclick='actualizaQE(this,2)'> Caja<input type='radio' value='F' id='rdb".$datos["Id"]."' checked onclick='actualizaQE(this,2)'>";
							}
							echo " [PxE]:<input type='text'";									
						}else{
							echo "<input type='hidden' id='txtQE".$datos["Id"]."' value='".intval($datos["B1_QE"])."' size='3' maxlength='5' onkeypress='soloNumeros(this)' style='text-align:right' style='background-color:#CCC' onchange='actualizaQE(this,1)'></td><td><font size='-1'>[".trim($datos["C6_PRODUTO"])."] ".trim($datos["B1_DESC"])."</font></td><td><input type='hidden' value='".trim($datos["C6_PEDIM"])."' id='txtPedim".$datos["Id"]."'><font size='-1'>".trim($datos["C6_PEDIM"])."</font></td>";
						}
						if(trim($datos["B1_CLASE"])=="11"){
							$tinaco+=$cantidad;
						}elseif(trim($datos["B1_CLASE"])=="22"){
							$exhi+=$cantidad;
							echo "<td></td>";
						}elseif(trim($datos["Empaque"])=="T"){
							$costal+=$cantidad;
							echo "<td></td><td></td>";
						}else{
							$caja+=$cantidad;
							echo "<td></td><td></td><td></td>";
						}
						echo "<td align='center'><input type='text' disabled value='".$cantidad."' style='text-align:right' style='background-color:".$bgColor."' size='2' id='txtEmp".$datos["Id"]."'></td></tr>";
						if(intval($datos["cantR"]-($cantidad*$datos["B1_QE"]))>0){
							$codIn.="<tr bgcolor='".$bgOColor."'><td align='center'><input type='hidden' id='txtSurS".$datos["Id"]."-F' value='".intval($datos["cantR"]-($cantidad*$datos["B1_QE"]))."'><input type='hidden' id='txtMod".$datos["Id"]."-F' value='".intval($datos["cantR"]-($cantidad*$datos["B1_QE"]))."'><select id='cmb".trim($datos['C6_PRODUTO'])."'></select><input type='text' id='txtSurR".$datos["Id"]."-F' value='".intval($datos["cantR"]-($cantidad*$datos["B1_QE"]))."' size='3' maxlength='5' style='text-align:right' onkeypress='soloNumeros(this)' onblur=mayorSolicitado(this,'".base64_decode($_GET["Ped"])."')>";
							if($datos["Empaque"]=="T"){
								$codIn.=" Costal<input type='radio' value='T' name='mlo$datos[Id]' id='rdb".$datos["Id"]."-F' checked> Caja<input type='radio' value='F' name='mlo$datos[Id]' id='rdb".$datos["Id"]."-F'>";
							}else{
								$codIn.=" Costal<input type='radio' value='T' id='rdb".$datos["Id"]."-F' name='mls$datos[Id]'> Caja<input type='radio' value='F' id='rdb".$datos["Id"]."-F' name='mls$datos[Id]' checked>";								
							}
							$codIn.=" <input type='hidden' id='txtQE".$datos["Id"]."-F' value='".round($datos["B1_QE"])."'></td><td><font size='-1'>[".trim($datos["C6_PRODUTO"])."] ".trim($datos["B1_DESC"])."</font></td><td><input type='hidden' value='".trim($datos["C6_PEDIM"])."' id='txtPedim".$datos["Id"]."-F'><font size='-1'>".trim($datos["C6_PEDIM"])."</font></font></td><td></td><td></td><td></td><td align='center'><input type='text' value='1' style='text-align:right' style='background-color:".$bgOColor."' maxlength='5' size='2' id='txtEmp".$datos["Id"]."-F' onkeypress='soloNumeros(this)'></td></tr>";
						}
						if($bgOColor=="#FFFFFF"){
							$bgOColor="#CCCCCC";
						}else{
							$bgOColor="#FFFFFF";
						}
					}else{
						$registros+=1;
						if($datos["B1_TIPO"]==01 && $datos["B1_CLASE"]==11){
							$cuantos=$datos["Solicitado"];
							while($cuantos>0){
								if($bgColor=="#FFFFFF"){
									$bgColor="#CCCCCC";
								}else{
									$bgColor="#FFFFFF";
								}
								echo " <tr bgColor='".$bgColor."'><td align='center'><input type='hidden' id='txtReg".$datos["Id"]."-T' value='".$registros."'><input type='hidden' id='txtSurS".$datos["Id"]."-T' value='".$datos['Solicitado']."'><input type='hidden' id='txtMod".$datos["Id"]."-T' value='1'>[".$registros."]<input type='text' id='txtSurR".$datos["Id"]."-T' value='1' size='3' maxlength='5' style='text-align:right' onkeypress='soloNumeros(this)' onblur=mayorSolicitado(this,'".base64_decode($_GET["Ped"])."')><input type='hidden' id='txtQE".$datos["Id"]."-T' value='".intval($datos["B1_QE"])."' size='3' maxlength='5' onkeypress='soloNumeros(this)' style='text-align:right' style='background-color:#CCC' onchange='actualizaQE(this,1)'></td><td><font size='-1'>[".trim($datos["C6_PRODUTO"])."] ".trim($datos["B1_DESC"])."</font></td><td><input type='hidden' value='".trim($datos["C6_PEDIM"])."' id='txtPedim".$datos["Id"]."-T'><font size='-1'>".trim($datos["C6_PEDIM"])."</font></td><td align='center'><input type='text' disabled style='text-align:right' style='background-color:".$bgColor."' value='1' size='2' id='txtEmp".$datos["Id"]."-T'></td></tr>";		
								if($cuantos>1){
									$registros++;
								}
								$cuantos--;
							}
							$tinaco+=ceil($datos["Solicitado"]/$datos["B1_QE"]);
						}else{
							echo "<tr bgColor='".$bgColor."'><td align='center'><input type='hidden' id='txtReg".$datos["Id"]."' value='".$registros."'><input type='hidden' id='txtSurS".$datos["Id"]."' value='".round($datos["Solicitado"])."'><input type='hidden' id='txtMod".$datos["Id"]."' value='".round($datos["cantR"])."'>[".$registros."] ";
								/*Si hay tinacos, agrega el combo*/
							if(trim($empaknTindec)<>""){
								echo $empaknTindec;									
							}
							echo "<input type='text' id='txtSurR".$datos["Id"]."' value='".round($datos["cantR"])."' size='3' maxlength='5' style='text-align:right' onkeypress='soloNumeros(this)' onblur=mayorSolicitado(this,'".base64_decode($_GET["Ped"])."')>";
							if($datos["B1_TIPO"]=="04"){
								if($datos["Empaque"]=="T"){
									echo " Costal<input type='radio' value='T' id='rdb".$datos["Id"]."' checked onclick='actualizaQE(this,2)'> Caja<input type='radio' value='F' id='rdb".$datos["Id"]."' onclick='actualizaQE(this,2)'>";
								}else{
									echo " Costal<input type='radio' value='T' id='rdb".$datos["Id"]."' onclick='actualizaQE(this,2)'> Caja<input type='radio' value='F' id='rdb".$datos["Id"]."' checked onclick='actualizaQE(this,2)'>";
								}
								echo " [PxE]:<input type='text'";
							}else{
								echo "<input type='hidden'";
							}
							echo " id='txtQE".$datos["Id"]."' value='".intval($datos["B1_QE"])."' size='3' maxlength='5' onkeypress='soloNumeros(this)' style='text-align:right' style='background-color:#CCC' onchange='actualizaQE(this,1)'></td><td><font size='-1'>[".trim($datos["C6_PRODUTO"])."] ".trim($datos["B1_DESC"])."</font></td><td><input type='hidden' value='".trim($datos["C6_PEDIM"])."' id='txtPedim".$datos["Id"]."'><font size='-1'>".trim($datos["C6_PEDIM"])."</font></td>";
							if(trim($datos["B1_CLASE"])=="11"){
								$tinaco+=ceil($datos["Solicitado"]/$datos["B1_QE"]);
							}elseif(trim($datos["B1_CLASE"])=="22"){
								$exhi+=ceil($datos["Solicitado"]/$datos["B1_QE"]);
								echo "<td></td>";
							}elseif(trim($datos["Empaque"])=="T"){
								$costal+=ceil($datos["Solicitado"]/$datos["B1_QE"]);
								echo "<td></td><td></td>";
							}else{
								$caja+=ceil($datos["Solicitado"]/$datos["B1_QE"]);
								echo "<td></td><td></td><td></td>";
							}
							echo "<td align='center'><input type='text' disabled style='text-align:right' style='background-color:".$bgColor."' value='".ceil($datos["cantR"]/$datos["B1_QE"])."' size='2' id='txtEmp".$datos["Id"]."'></td></tr>";
						}
					}
				}
			}
			odbc_free_result($sql);
			echo "
			<tr>
				<strong>
					<td colspan='2' align='right'>TOTAL COMPLETOS: </td>
					<td></td>
					<td><input type='text' id='txtTotTin' value='".$tinaco."' readonly style='text-align:right' style='background-color:#CCCCCC' size='3'></td>
					<td><input type='text' id='txtTotEx' value='".$exhi."' readonly style='text-align:right' style='background-color:#CCCCCC' size='3'></td>
					<td><input type='text' id='txtTotCos' value='".$costal."' readonly style='text-align:right' style='background-color:#CCCCCC' size='3'></td>
					<td><input type='text' id='txtTotCaj' value='".$caja."' readonly style='text-align:right' style='background-color:#CCCCCC' size='3'></td>
				</strong>
			</tr>
			<tr>
				<td colspan='7' align='center'>Piezas Sueltas (Cajas Multiples)</td>
			</tr>";
					$opc="<option value='0' selected>0</option>";
					for($num=1;$num<=$registros;$num++)
						$opc.="<option value='".$num."'>".$num."</option>";
			echo str_replace("</select>",$opc."</select>", $codIn);
			/*Cajas con varillas de los tinacos y del código 2449*/
			$sql=odbc_exec($conn,"SELECT Id,Producto,Cant,Qe,CASE WHEN (Cant/Qe)=0 THEN 1 ELSE Cant/Qe END AS 'cajas' FROM tempo WHERE Pedido='".base64_decode($_GET["Ped"])."' AND (Producto='+Kit+' OR Producto='+Varilla+')")or die("Error en las varillas");
			if(odbc_num_rows($sql)>0){
				echo "<tr><td colspan='7'><hr></td></tr>";
			}
			while($datos=odbc_fetch_array($sql)){
				echo "<tr><td><input type='hidden' id='txtSurS$datos[Id]-F' value='$datos[Cant]'><input type='hidden' id='txtMod$datos[Id]-F' value='$datos[Cant]'><select style='visibility:hidden' id='cmb$datos[Producto]'>$opc</select><input type='text' id='txtSurR$datos[Id]-F' value='$datos[Cant]' size='3' maxlength='5' style='text-align:right' onkeypress='soloNumeros(this)' onblur=mayorSolicitado(this,'".base64_decode($_GET["Ped"])."')> <input type='radio' name='mul$datos[Id]' value='T' id='rdb$datos[Id]-F' style='visibility:hidden'> <input type='radio' name='mul$datos[Id]' value='F' id='rdb$datos[Id]-F' checked style='visibility:hidden'><input type='hidden' id='txtQE$datos[Id]-F' value='$datos[Qe]' size='3' maxlength='5' style='text-align:right' style='background-color:#CCC'></td><td>".substr($datos['Producto'],1,strlen($datos['Producto'])-2)."</td><td><input type='hidden' value='' id='txtPedim$datos[Id]-F'></td><td></td><td></td><td></td><td align='center'><input type='text' id='txtEmp$datos[Id]-F' readonly size='2' maxlength='5' style='text-align:right' disabled value='$datos[cajas]'/></td></tr>";
			}
			odbc_free_result($sql);
			/*Nombre del Almacenista que surte el pedido cuando no ha sido facturado*/
			/*$sql=odbc_exec($conn,"SELECT ZZN_CODIGO,ZZN_NOMBRE FROM ZZN010 ZZN INNER JOIN ZZM010 ZZM ON ZZN_CODIGO=ZZM_CODALM WHERE ZZM_PEDIDO='".base64_decode($_GET["Ped
				"])."' AND ZZM_FATURA='' AND ZZN.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_=''") or die("Error al consultar el nombre del almacenista");*/
			$sql=odbc_exec($conn,"
				SELECT 
					ZZN_CODIGO,
					ZZN_NOMBRE 
				FROM 
					ZZN010 ZZN 
					INNER JOIN ZZM010 ZZM ON ZZN_CODIGO=ZZM_CODALM 
				WHERE ZZM_ORDSUR='".base64_decode($_GET["ordsur"])."' AND ZZM_FATURA='' AND ZZN.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_=''") or die("Error al consultar el nombre del almacenista");
			$datos=odbc_fetch_array($sql);
			echo  "<tr bgcolor='#CCCCCC'>
		              <td colspan='2'>
		                <table>
		                    <tr>
		                    	<th colspan='2'>ELABOR&Oacute;</th>
		                    </tr>
		                    <tr>
		                    	<td><input type='hidden' id='txtCodAlm' value='".trim($datos["ZZN_CODIGO"])."'><strong>Nombre:&nbsp;&nbsp;".trim($datos["ZZN_NOMBRE"])."</strong></td>
		                    </tr>
		                    <tr>
		                    	<td>ALMACENISTA DE PRODUCTO TERMINADO</td>
		                    </tr>
		                </table>
		              </td>
		              <td colspan='5' align='center'>
		                  <table bgcolor='#CCCCCC'>
		                    <tr>
		                      <th colspan='2'>REVIS&Oacute;</th>
		                    </tr>
		                    <tr>
		                      <td><strong>Nombre:</strong></td>
		                    </tr>
		                    <tr>
		                      <td>AUDITOR DE PEDIDO</td>
		                    </tr>
		                  </table>
		                 </td>
		              </tr>
				    </table>          
		          </td>
		        </tr>";
		}
		echo "</table>";
	}
		odbc_free_result($sql);
		odbc_close($conn);
	?>
		</center>
	</form>
</body>
</html>