<?php
	include("conectabd.php");
	$sql=odbc_exec($conn,"SELECT ZPP_ADMIN FROM ZPP010 WHERE ZPP_NOMPC='".strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))."' AND ZPP_PAGINA='Embarques' AND D_E_L_E_T_=''") or die("Error al validar el equipo");
	if(odbc_num_rows($sql)==0){
		odbc_free_result($sql);
		odbc_close($conn);
		echo "<script languaje='JavaScript'>
				alert('Usuario o contraseña no validos para ".strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))."');
				window.open('http://gdl-net/flexinet/','_self')
			  </script>";
		exit;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Embarques</title>
    <link rel="shortcut icon" href="images/icono.ico" />
	<link rel="stylesheet" href="css/styles.css"/>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<script src="css/jquery.js"></script>
	<script>
		$(document).on("ready",function(){
			mEncabezado();
			mPie();
			mCuerpo();
			
			function mEncabezado(){
				//$.post("facxEmb.php", {}, function(data){$("#encabezado").html(data);});
				$.post("facxEmb.php", {ocultar:"1"}, function(data){$("#encabezado").html(data);});
			}
			
			function mCuerpo(){
				$.post("facRuta.php", {}, function(data){$("#cuerpo").html(data);});
			}
			function mPie(){
				$.post("ruta.php", {}, function(data){$("#pie").html(data);});
			}
			
			$(".cf").live("click", function(){
				document.getElementById("cambio").style.display="block";
				document.getElementById("txtidfac").value=$(this).attr("id");
			 });
			 
			 $("#canchof").live("click", function(){
				document.getElementById("cambio").style.display="none";
			 });
			 
			  $("#acechof").live("click", function(){
				canC=confirm("¿Realmente desea cambiar el chofer en la ruta?");
				if(canC==true){
					registrillo=document.getElementById('txtidfac').value.substr(2);
					chofer=$("#selchof").val();
					$.get("cambchof.php",{regist:registrillo,chof:chofer});
					mPie();
					//alert(chofer);
				}
				document.getElementById("cambio").style.display="none";
			 });
			 
			$(".eOR").live("click", function(){
				canC=confirm("¿Realmente desea cancelar el registro de otras rutas seleccionado?");
				if(canC==true){
					$.get("otrosRec.php",{opc:"e",regist:$(this).attr("id")});
 				    $("#liR"+$(this).attr("id")).remove();
				}
			 });
			
			$("#cFacxEmb").live("click", function(){
				$("#tr"+$(this).attr("name")).remove();
				mEncabezado();
				$.get("impEmb.php",{opc:"e",fac2:$(this).attr("name")});
			});
			
			$("#asChof").live("click", function(){
				var controles=document.getElementById("embarques");
				var actReg="";
				for(i=1;i<controles.length;i++)
				{
					//if(controles.elements[i].value!='' && controles.elements[i].type!="button" ){
					if(controles.elements[i].value!='' && controles.elements[i].type!="button" && controles.elements[i].name.substr(0,7)=='cmbChof'){
						actReg+=controles.elements[i].value+","+controles.elements[i+1].value+","+controles.elements[i].name.substr(7)+","+controles.elements[i].options[controles.elements[i].selectedIndex].text+"|";
						$("#tr"+controles.elements[i].name.substr(7)).remove();
						i--;
					}
				}
				if(actReg!="")
				{
					$.get
					(
						"ImpEmb.php",
						{Act:actReg},
						function(resultado)
						{
							//alert(resultado);
							mPie(); 
						}
					);
				}
			});
			
			$("#otraRuta").live("click",function(){
				window.open('otrosRec.php','_blank','width=800,height=200','resisable=no');
				mPie();
		    });
			
			$("a.osx").on("click",function(){
				var pedido=prompt("¿Número de pedido a agregar observaciones?");
				if(pedido!=null && pedido!="")
					$("#osx-modal-content").slideDown(100, function(){$("#fN").show(); $.get("otrosRec.php", {ped:pedido},function(data){$("#osx-modal-data").html(data);});});
			});
			$(".cerrar").on("click",function(){$("#osx-modal-content").slideUp(100);$("#fN").hide();});
			$("#obsCli").on("click",function(){
				var cod=prompt("¿Codigo de cliente?","");
				$("#osx-modal-content").slideDown(100, function(){
					$("#fN").show();
					$.get("otrosRec.php",{codC:cod}, function(data){$("#osx-modal-data").html(data);});
				});
			});

			$("a.fxe, #btnIm").live("click", function(){
			    var comp="";
				if($("#txtPed").attr("value")=="" || $("#txtPed").attr("value")=="0")
					comp=$(this).attr("id");
				else
					comp=$("#txtPed").attr("value");
				$("#cmbF"+$(this).attr("id")).attr("value");
				if($("#cmbF"+comp).attr("value")=="")
					alert("No se ha especificado una fletera");
				else{
					$("#btnCanc").hide();
				    $("#btnIm").hide();
					var dat_flet_or=$("#xDefecto"+comp).attr("title");
					var flet_or=dat_flet_or.split("|");
					$.get("impEmb.php",{fac2:comp,obs:$("#txtObs"+comp).attr("value"),pag:$("#cmbC"+comp).attr("value"),flet:$("#cmbF"+comp).attr("value"),fletor:flet_or[0],suc:$("#cmbDE"+comp).attr("value"),client:$("#txtCli"+comp).attr("value")},function(data,status){$("#prntInfo").html(data);
						if(status="success"){
							if($("#cmbF"+comp).attr("value")!="ACLARA" && $("#cmbF"+comp).attr("value")!="EXPORT"){
							  var objeto=document.getElementById('prntInfo'); 
							  var ventana=window.open('','_blank'); 
							  $("#btnCanc").show();
							  $("#btnIm").show();					  
							  ventana.document.write(objeto.innerHTML);
							  ventana.document.close();
							  ventana.print();
							  ventana.close();
							}
						}
					  $("#txtPed").attr("value",comp);
					  $("#dvAlm").slideDown(100, function(){$("#fN").show();});
					});
				}
			});
			
			$("#btnCanc").on("click", function(){$.get("ImpEmb.php",{opc:"e",fac2:$("#txtPed").attr("value")},function(){$("#fN").hide(); $("#dvAlm").hide();$("#txtPed").attr("value","0");});});
			$("#btnAcep").on("click",function(){$("#tr"+$("#txtPed").attr("value")).remove(); $("#fN").hide(); $("#dvAlm").hide(); $("#txtPed").attr("value","0"); mCuerpo();});

			$("#btnImp").on("click", function(){
				var opc="";
				opc=prompt('Introduzca el Num. de Pedido a Imprimir','');
				if(opc!=null)
					location.href="imprimePedido.php?ped="+opc+"&emb=1";										  
			});
			
			$("#pPost").on("click",function(){
				$("#dvSA").slideDown(100, function(){
					$("#fN").show();
					$.post("pedPost.php",{}, function(data){$("#dvSA").html(data);});
				});											 											
			});
			
			$("#btnCancPP").live("click",function(){$("#dvSA").slideUp(100, function(){$("#fN").hide();});});
			$("#btnGPP").live("click",function(){$.post("pedPost.php",{ped:$("#txtPed").attr("value"),obs:$("#txtOb").attr("value")},function(data){
				if(data.substr(data.length-2)==-1)
					alert("No es posible agregar el pedido ya que no ha sido facturado o ya ha sido embarcado");
				  $("#dvSA").slideUp(100, function(){$("#fN").hide();});
			  });});
			
			$("#cancPP").live("click", function(){$.post("pedPost.php",{delP:$(this).attr("name")},function(){$("#dvSA").slideUp(100, function(){$("#fN").hide(); alert("Pedido cancelado"); mEncabezado();});});});
			
			$("#indSA").on("click", function(){
				$("#dvSA").slideDown(100, function(){
					$("#fN").show();
					$.get("otrosRec.php",{salm:1}, function(data){$("#dvSA").html(data);});
				});
			 });
			
			$("#qRuta").live("click", function()
			{
				var registro=$(this).attr("class");
				$.get("impEmb.php",{reg:$(this).attr("class")},
				function(respuesta)
				{
					if(respuesta.trim()=="")
					{
						alert("No Aplica");
					}
					else
					{
						$("#li"+registro).remove(); 
						mCuerpo();
					}
				}); 
			});
			

			$(".impRuta").live("click", function(){
				$.ajax({
					url: "impRuta.php",
					data: "chof="+$(this).attr("id"),
					type: "get",
					success: function(response) {
						$("#prntInfo").html(response);
					},
					complete: function() {
						  var objeto=document.getElementById("prntInfo");
						  var ventana=window.open('','_blank');
						  ventana.document.write(objeto.innerHTML);
						  ventana.document.close();
						  ventana.print();
						  ventana.close();
					}
				});
			 });
			
		});
	</script>    
</head>
<body class="body">
	
	<div id="fN" class="cerrar"></div>
    <div id="dvSA"></div>
    <div id="prntInfo"></div>
	<div id='dvAlm'><h1>Se imprimio correctamente la factura?</h1><input type="hidden" id='txtPed' /> <div class='btnLinea'><center><button id='btnAcep'>Aceptar</button> <button id='btnCanc'>Cancelar</button> <button id='btnIm'>Imprimir</button></center></div></div>
    
   	<div id="osx-modal-content"><div id="osx-modal-title">OBSERVACIONES<div class="close"><input type="button" id="bC" class="cerrar" value="x"/></div></div>
        <div id="osx-modal-data"></div>
    </div>

	<div class="encM">
		<a id="im" class="osx">Observaciones a Pedido</a> | <a id="obsCli" class="lblEmb">Observaciones a Cliente</a> | <a id="btnImp" class="lblEmb">Imprime Pedido</a> | <a id="pPost" class="lblEmb">Pedidos Postergados</a>
	<?php
		include("conectabd.php");
		$sql=odbc_exec($conn,"SELECT COUNT(*) AS 'ctos' FROM SCP010 WHERE CP_TIPO=1 AND CP_QUANT-CP_QUJE>0 AND CP_STATSA<>'B' AND CP_PREREQU='' AND D_E_L_E_T_=''")or die("Err54");
		$regs=odbc_fetch_array($sql);
		if($regs['ctos']>0)
			echo "&nbsp;&nbsp;<img id='indSA' src='images/start.png' title='Existen solicitudes por entregar'/>";
		odbc_free_result($sql);
		odbc_close($conn);
	?>
        <table>
            <tr>
                <td class="vip">VIP</td>
                <td class="local">Locales</td>
                <td class="rc">Recoge Cliente</td>
                <!-- <td><font size="+1" color="#FF7171"><b>Express</font></b></td> -->
                <td class="foraneos">Foraneos</td>
                <td class="exportacion">Exportaci&oacute;n</td><td class="especiales">Especiales</td><td class="programados">Programados</td>
               	<td class="cita">Cita</td><td class="aclaracion">Aclaraciones</td><td class="bko">BackOrder</td><td class="publicidad">Publicidad</td><td class="pe">PE</td>
            </tr>
        </table>
    </div>    
	<div class="divs" id="encabezado" style="text-align:center;"><h1>Cargando ... Espere...</h1></div>
	<div class="divs" id="cuerpo"></div>
	<div class="divs" id="pie"></div>
</body>
</html>