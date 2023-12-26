<?php
	session_start();
	include("conectabd.php");
	//echo strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']));
	$sql=odbc_exec($conn,"SELECT ZPP_ADMIN FROM ZPP010 WHERE ZPP_NOMPC='".strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))."' AND ZPP_PAGINA='Pedidos' AND D_E_L_E_T_=''")or die("Error al validar el equipo");
	$esAdmin="F";
	if(odbc_num_rows($sql)==0)
	{
		If(isset($_SESSION['pwd']))
		{
			$Password = $_SESSION['pwd'];
		}
		else{
			$Password="EstePassw0rdNoExist3EnP3d1d0s";
		}
		$UsuarioValido=0;
		$IdProtheusXpass=odbc_exec($conn,"SELECT ZS1_WEBALM,ZS1_WAADM
										  FROM ZS1010 
										  WHERE ZS1_PASS='$Password' AND ZS1010.D_E_L_E_T_ = '' 
										  ;") 
										  or die("Problemas al actualizar la informacion");

		if($reg=odbc_fetch_array($IdProtheusXpass)){
			If($reg['ZS1_WEBALM']=="T")
			{
				$UsuarioValido = 1;
				$esAdmin=$reg['ZS1_WAADM'];
			}
		}
		odbc_close($conn);
		
		if($UsuarioValido==0)
		{
			odbc_free_result($sql);
			echo "<script languaje='JavaScript'>
					alert('Usuario o contraseña no validos ".strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))."');
					window.open('http://gdl-net/flexinet/','_self');
			</script>";
			exit;
		}
		
	}
	else{
		$datos=odbc_fetch_array($sql);
		odbc_free_result($sql);
		$esAdmin=$datos['ZPP_ADMIN'];
	}
?>
<!DOCTYPE html>
<html>
 <head>
	<link rel="shortcut icon" href="images/icono.ico"/>
 	<link rel="stylesheet" href="css/styles.css"/>
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />    
    <script src="css/jquery.js"></script>
    <script>
		timerID=setTimeout("tempo()", 360000);
		function tempo(){
			clearTimeout(timerID);
			location.reload();
		}

		$(document).on("ready", function(){
			cGraf();
			function cGraf(){
				$.post("muestras.php",{}, function(data){$("main").html(data);});
			}

			$("#prntMenu").on("click",function(){$("#menus").slideToggle();});
			
			$(".tdPart,.mSol").live("click", function(e){
				$(".trPart").removeClass("trSel");
				$('#tr'+$(this).attr('id')).addClass("trSel");
				
				//alert("sm.php np "+$(this).attr("id"));
				$.post("sm.php",{np:$(this).attr("id")},function(data,status){
					 $("#detM").html(data);
					 if(status=="success"){
						 if(data=="  No hay direccion de entrega")
						 {
							 alert("No se puede procesar porque la solicitud tiene un cliente de entrega pero no hay direccion de entrega.");
						 }
						 else
						 {
					  	var objeto=document.getElementById('detM'); 
						  var ventana=window.open('','_blank');
						  ventana.document.write(objeto.innerHTML);
						  ventana.document.close();
						 }
					 }
					 else
					 {alert(data);}
				});
				$.post("sm2.php",{np:$(this).attr("id")},function(data,status){
					 $("#detM").html(data);
					 if(status="success"){
					  	var objeto=document.getElementById('detM'); 
						  var ventana=window.open('','_blank');
						  ventana.document.write(objeto.innerHTML);
						  ventana.document.close();
					 }
				});
				e.preventDefault();
			});
			
			$("#pPost").on("click",function(){
				$("#dvSA").slideDown(100, function(){
					$("#fN").show();
					$.post("pedPost.php",{}, function(data){$("#dvSA").html(data);});
				});											 											
			});
			$("#btnCancPP").live("click",function(){$("#dvSA").slideUp(100, function(){$("#fN").hide();});});
			$("#btnGPP").live("click",function(){$.post("pedPost.php",{ped:$("#txtPed").attr("value"),obs:$("#txtOb").attr("value"),alm:"s"},function(data){
				if(data.substr(data.length-2)==-1)
					alert("No es posible agregar el pedido ya que no ha sido facturado o ya ha sido embarcado");
				  $("#dvSA").slideUp(100, function(){$("#fN").hide();});
			  });});
			$("#cancPP").live("click", function(){$.post("pedPost.php",{delP:$(this).attr("name")},function(){$("#dvSA").slideUp(100, function(){$("#fN").hide(); alert("Pedido cancelado");});});});
			
			$("a").on("click",function(){
				var vEst=$(this).attr("name");
				var vPed=$(this).attr("id");
				var vUrg=vPed.substr(vPed.length-1,1);
				vPed=vPed.substr(0,vPed.length-1);
				if(vUrg=="x")
					$.get("reimpPed.php",{ped:vPed,desmarca:1},function(){location.href="index.php";});
				else{
					if(vEst==0 && vUrg==0)
						alert("No es posible surtir el pedido ya que existen pedidos urgentes");
					else{
						$("#dvAlm").slideDown(100, function(){
							$.post("validaAlm.php",{est:vEst,urg:vUrg,Ped:vPed}, function(data){$("#dvAlm").html(data);});
							$("#fN").show();
						});
					}
				}
			});
			
			$(".cerrar").live("click",function(){$("#dvAlm").slideUp(100);$("#dvAlm").html();$("#fN").hide();});
			
			$(".chkBox").live("click",function(){
				opc=prompt('Introduzca el Num. de salida','');
				if(opc!=null && opc!=''){
					$.post("sm.php",{sm:$(this).attr("id"),salida:opc}, function(data){$("#detM").html(data);});
					$('#tr'+$(this).attr('id')).remove();
				}
				else
				{
					this.checked=0;
					alert("El número de salida no debe estar vacío")
				}
					
			});
			
			$("#clsSM").live("click", function(){
				$(".trPart").removeClass("trSel");
				$("#detM").hide();
		   });
						
			$("#btnApP").on("click", function(){
				var pedido="";
				pedido=prompt('Introduzca el Num. de pedido a aprobar','');
				if(pedido!=null)
					location.href="imprimePedido.php?apP="+pedido;
			});
			
			$("#btnIPC,#btnIP").on("click", function(){
				var opc="";
				opc=prompt('Introduzca el Num. de Pedido a Imprimir','');
				if(opc!=null){
					if($(this).attr('id')=='btnIPC')
						window.open("imprimePedido.php?ped="+opc);
					else
						window.open("reimpPed.php?ped="+opc+"&desmarca=0&opc=reimp");
				}
						 
		 	});
			
			$("#btnSal").on("click", function(){
				var opc="";
				opc=prompt('Introduzca el Num. de pedido para imprimir la salida','');
				if(opc!="")
					location.href="reimpSalida.php?Ped="+opc;
			});
			
			$("#btnSalC").on("click", function(){
				var opc="";
				opc=prompt('Introduzca el Num. de pedido para imprimir la salida','');
				box=prompt('Introduzca el Num. de cajas totales','');
				if(opc!="" && box!="")
					location.href="reimpSalidaCaja.php?Ped="+opc+"&Box="+box;
			});
			
			$("#btnSolAlm").on("click", function(){
				var opc="";
				opc=prompt('Introduzca el Num. de solicitud para re-imprimir','');
				if(opc!="" && opc!=null)
				{
					$.post("sm3.php",{np:"td"+opc},function(data,status){
						 $("#detM").html(data);
						 if(status="success"){
							var objeto=document.getElementById('detM'); 
							  var ventana=window.open('','_blank');
							  ventana.document.write(objeto.innerHTML);
							  ventana.document.close();
						 }
					});
					$.post("sm4.php",{np:"td"+opc},function(data,status){
						 $("#detM").html(data);
						 if(status="success"){
							var objeto=document.getElementById('detM'); 
							  var ventana=window.open('','_blank');
							  ventana.document.write(objeto.innerHTML);
							  ventana.document.close();
						 }
					});
					e.preventDefault();
				}
			});
			
			$("#btnEmb").on("click", function(){
				window.open('embarques.php','_top');
			});

			$("#obsCli").on("click",function(){
				var cod=prompt("¿Codigo de cliente?","");
				$("#osx-modal-content").slideDown(100, function(){
					$("#fN").show();
					$.get("otrosRec.php",{codC:cod}, function(data){$("#osx-modal-data").html(data);});
				});
			});
			
		})
	</script>
 </head>
 <body class="body">
 
 <div id="fN" class="cerrar"></div>
 <div id="dvAlm"></div>
 <div id="dvSA"></div>
 <form name="frmPedi2">
	<header>
        <img class="logo" src="images/logo.png" />
    </header>
    <main>
    	<div id="osx-modal-content"><div id="osx-modal-title">OBSERVACIONES<div class="close"><input type="button" id="bC" class="cerrar" value="x"/></div></div>
        	<div id="osx-modal-data"></div>
    	</div>
	</main>
 </form>    
 </body>
</html>