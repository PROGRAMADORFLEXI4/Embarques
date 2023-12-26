<?php

/*session_start();
include 'conectabd.php';
include 'encryptor.php';
include 'api_autentica.php';


$nombre = $_POST['user'];
$pass = $_POST['pass'];

if(!isset($_SESSION['nom']) && !isset($_SESSION['pass'])){

	$_SESSION['nom'] = $nombre;
	$_SESSION['pass'] = $pass;

	$pass_encriptada = encriptar($_SESSION['pass']);
	$respuesta_Api = login_Api($_SESSION['nom'], $pass_encriptada);
	$_SESSION['idn'] = $respuesta_Api[0]["idNomina"];
	$_SESSION['mss'] = $respuesta_Api[0]["message"];


}


if ($_SESSION['mss'] <> "OK") {
	session_destroy();
	echo"<script>
	alert('El usuario o Password son incorrectos');
	console.log('Respuesta API ".$respuesta_Api."');
	window.location.href = 'login_embarques.php';
	</script>";
	
} else {
	$esAdmin = "F";
	$UsuarioValido = 0;
	$IdProtheusXpass = odbc_exec($conn, "SELECT ZS1_WEBEMB,ZS1_WEADM
										  FROM ZS1010 
										  WHERE ZS1_IDNOMI='".$_SESSION['idn']."' AND ZS1010.D_E_L_E_T_ = '';") or die("Problemas al actualizar la informacion");

	if ($reg = odbc_fetch_array($IdProtheusXpass)) {
		if ($reg['ZS1_WEBEMB'] == "T") {
			$UsuarioValido = 1;
			$esAdmin = $reg['ZS1_WEADM'];
			//$_SESSION["LOCAL"] = "01"; //Este se deberia de traer cmo parametro cuando se habiliten los cedis
		}
	}
	

	if ($UsuarioValido == 0) {
		//odbc_free_result($sql);
		//odbc_close($conn);
		session_destroy();
		echo "<script languaje='JavaScript'>
					alert('No cuentas con permisos para ingresar a esta pagina');
					window.location.href = 'login_embarques.php';
			</script>";
		exit;
	}
	odbc_close($conn);
}*/
//----------------------------------------------------------------------------- Codigo temporal por problemas con SSO ---------------------------------------------------------------------------------------------------------
	/*session_start();
	include("conectabd.php");
	$sql=odbc_exec($conn,"SELECT ZPP_ADMIN,ZPP_LOCAL FROM ZPP010 WHERE ZPP_NOMPC='".strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))."' AND ZPP_PAGINA='Embarques' AND D_E_L_E_T_=''") or die("Error al validar el equipo");
	if(odbc_num_rows($sql)==0){
		If(isset($_SESSION['pwd']))
		{
			$Password = $_SESSION['pwd'];
		}
		else{
			$Password="EstePassw0rdNoExist3EnP3d1d0s";
		}
		$UsuarioValido=0;
		$IdProtheusXpass=odbc_exec($conn,"SELECT ZS1_WEBEMB,ZS1_WEADM
										  FROM ZS1010 
										  WHERE ZS1_PASS='$Password' AND ZS1010.D_E_L_E_T_ = '' 
										  ;") 
										  or die("Problemas al actualizar la informacion");

		if($reg=odbc_fetch_array($IdProtheusXpass)){
			If($reg['ZS1_WEBEMB']=="T")
			{
				$UsuarioValido = 1;
				$esAdmin=$reg['ZS1_WEADM'];
				$_SESSION["LOCAL"]="01"; //Este se deberia de traer cmo parametro cuando se habiliten los cedis
			}
		}
		odbc_close($conn);
		
		if($UsuarioValido==0)
		{
			odbc_free_result($sql);
			odbc_close($conn);
			echo "<script languaje='JavaScript'>
					alert('Usuario o contrase�a no validos para ".strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))."');
					window.open('http://192.168.10.19/flexinet/','_self')
				  </script>";
			exit;
		}
	}
	else
	{
		$datos=odbc_fetch_array($sql);
		odbc_free_result($sql);
		$_SESSION["LOCAL"]=trim($datos['ZPP_LOCAL']);
		
	}*/
	//----------------------------NUEVO CODIGO DE LOG-IN------------------------------------//
	session_start();
	include("conectabd.php");

	//http://localhost/embarques/index.php
	$esAdmin = 'F';
	$sql = "SELECT * FROM 
	(select * from ZPP010 WHERE ZPP_NOMPC='" . strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR'])) . "' AND ZPP_PAGINA='Pedidos' AND D_E_L_E_T_='') as p
	inner join 
	(select * from ZS1010)as Z
	ON z.ZS1_NOMPC = p.ZPP_NOMPC";
	$sql_execute = odbc_exec($conn, $sql);

			if (odbc_num_rows($sql_execute) <> 0) {
				
				$UsuarioValido = 0;
				$usuarioEncontrado = false;

				if ($sql_execute) {
					while ($row = odbc_fetch_array($sql_execute)) {
						if ($row['ZS1_WEBEMB'] == "T") {
							// Si el usuario es web emb, marcarlo como válido y establecer la variable de sesión LOCAL en '01'
							$UsuarioValido = 1;
							$esAdmin = $row['ZS1_WEADM'];
							$_SESSION["LOCAL"] = "01"; // Este se debería de traer como parámetro cuando se habiliten los cedis
							$usuarioEncontrado = true;
							break; // Detener el bucle una vez que se encuentra un usuario válido (opcional)
						}
					}
				} else {
					die("Problemas al actualizar la información.");
				}

				odbc_close($conn);
				
				if ($UsuarioValido == 0) {
					echo "<script languaje='JavaScript'>
					alert('Usuario o contraseña no validos para ".strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))."');
					window.open('http://192.168.10.19/flexinet/','_self')
				</script>";
					exit;
				}
			} else {
				$datos = odbc_fetch_array($sql_execute);
				odbc_free_result($sql_execute);
			}


?>

<!DOCTYPE html>
<html>

<head>
	<title>Embarques</title>
	<link rel="shortcut icon" href="images/icono.ico" />
	<link rel="stylesheet" href="css/styles.css" />
	<meta http-equiv="X-UA-Compatible" content="IE=Edge" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta charset="utf-8">
	<script src="css/jquery.js"></script>
	<script>
		$(document).on("ready", function() {
			mEncabezado();
			mPie();
			mCuerpo();

			function mEncabezado() {
				//$.post("facxEmb.php", {}, function(data){$("#encabezado").html(data);});  172.16.2.239
				$.post("facxEmb.php", {
					ocultar: "0"
				}, function(data) {
					$("#encabezado").html(data);
					//tecnologia de la nasa
				}); // se cambio el estatus inicial del check ocultar a 0, antes estaba en 1, por indicacion de lucero: "me apoyas a desactivar el botón o filtro que tenemos en el sistema de embarques denominado como: Ocultar Publicidad, Aclaraciones y Cte Estándar muchas gracias" el 05/11/2020 by Luis E, Israel
			}

			function mCuerpo() {
				$.post("facRuta.php", {}, function(data) {
					$("#cuerpo").html(data);
				});
			}

			function mPie() {
				$.post("ruta.php", {}, function(data) {
					$("#pie").html(data);
				});
			}

			$(".cf").live("click", function() {
				document.getElementById("cambio").style.display = "block";
				document.getElementById("txtidfac").value = $(this).attr("id");
			});

			$("#canchof").live("click", function() {
				document.getElementById("cambio").style.display = "none";
			});

			$("#acechof").live("click", function() {
				canC = confirm("¿Realmente desea cambiar el chofer en la ruta?");
				if (canC == true) {
					registrillo = document.getElementById('txtidfac').value.substr(2);
					chofer = $("#selchof").val();
					$.get("cambchof.php", {
						regist: registrillo,
						chof: chofer
					});
					mPie();
					//alert(chofer);
				}
				document.getElementById("cambio").style.display = "none";
			});

			$(".eOR").live("click", function() {
				canC = confirm("¿Realmente desea cancelar el registro de otras rutas seleccionado?");
				if (canC == true) {
					$.get("otrosRec.php", {
						opc: "e",
						regist: $(this).attr("id")
					});
					$("#liR" + $(this).attr("id")).remove();
				}
			});

			$("#cFacxEmb").live("click", function() {
				// confirm para cancelar la factura
				var cancela = confirm("¿Realmente deseas regresar el registro a facturas por embarcar?");
				if (cancela) {
					var name = $(this).attr("name")
					$.get("impEmb.php", {
						opc: "e",
						fac2: name
					}, function(res) {
						if ($.trim(res) == 'OK') {
							$("#tr" + name).remove();
							mEncabezado();
						} else {
							alert(res);
						}
					});
				}
			});

			$("#asChof").live("click", function() {
				var controles = document.getElementById("embarques");
				var actReg = "";
				for (i = 1; i < controles.length; i++) {
					//if(controles.elements[i].value!='' && controles.elements[i].type!="button" ){
					if (controles.elements[i].value != '' && controles.elements[i].type != "button" && controles.elements[i].name.substr(0, 7) == 'cmbChof') {
						actReg += controles.elements[i].value + "," + controles.elements[i + 1].value + "," + controles.elements[i].name.substr(7) + "," + controles.elements[i].options[controles.elements[i].selectedIndex].text + "|";
						$("#tr" + controles.elements[i].name.substr(7)).remove();
						i--;
					}
				}
				if (actReg != "") {
					$.get(
						"ImpEmb.php", {
							Act: actReg
						},
						function(resultado) {
							//alert(resultado);
							mPie();
						}
					);
				}
			});

			$("#otraRuta").live("click", function() {
				window.open('otrosRec.php', '_blank', 'width=800,height=200', 'resisable=no');
				mPie();
			});




			$("a.osx").on("click", function() {
				var pedido = prompt("Número de pedido a agregar observaciones?");
				if (pedido != null && pedido != "")
					$("#osx-modal-content").slideDown(100, function() {
						$("#fN").show();
						$.get("otrosRec.php", {
							ped: pedido
						}, function(data) {
							$("#osx-modal-data").html(data);
						});
					});
			});
			$(".cerrar").on("click", function() {
				$("#osx-modal-content").slideUp(100);
				$("#fN").hide();
			});
			$("#obsCli").on("click", function() {
				var cod = prompt("¿Código de cliente?", "");
				$("#osx-modal-content").slideDown(100, function() {
					$("#fN").show();
					$.get("otrosRec.php", {
						codC: cod
					}, function(data) {
						$("#osx-modal-data").html(data);
					});
				});
			});

			$("a.fxe, #btnIm").live("click", function() {
				var comp = "";
				if ($("#txtPed").attr("value") == "" || $("#txtPed").attr("value") == "0")
					comp = $(this).attr("id");
				else
					comp = $("#txtPed").attr("value");
				$("#cmbF" + $(this).attr("id")).attr("value");
				if ($("#cmbF" + comp).attr("value").trim() == "")
					alert("No se ha especificado una fletera");
				else {
					var continuar = false;
					//ENVIAR AJAX PARA CONSULTA DE Pedido
					$.ajax({
						type: "POST",
						url: "cercanos.php",
						async: false,
						data: {
							lafactura: comp
						},
						success: function(resultado) {
							resultado = resultado.trim();
							if (resultado != "") {
								if (confirm("Los pedidos " + resultado + " vienen atras de este pedido, deseas continuar embarcando sin esperarlos?"))
									continuar = true;
								else
									continuar = false;
							} else {
								continuar = true;
							}
						}
					});
					if (continuar) {
						$("#btnCanc").hide();
						$("#btnIm").hide();
						var dat_flet_or = $("#xDefecto" + comp).attr("title");
						var flet_or = dat_flet_or.split("|");
						$.get("impEmb.php", {
							fac2: comp,
							obs: $("#txtObs" + comp).attr("value"),
							pag: $("#cmbC" + comp).attr("value"),
							flet: $("#cmbF" + comp).attr("value"),
							fletor: flet_or[0],
							suc: $("#cmbDE" + comp).attr("value"),
							client: $("#txtCli" + comp).attr("value")
						}, function(data, status) {
							$("#prntInfo").html(data);
							if (status = "success") {
								if ($("#cmbF" + comp).attr("value") != "ACLARA" && $("#cmbF" + comp).attr("value") != "EXPORT") {
									var objeto = document.getElementById('prntInfo');
									var ventana = window.open('', '_blank');
									$("#btnCanc").show();
									$("#btnIm").show();
									ventana.document.write(objeto.innerHTML);
									ventana.document.close();
									ventana.print();
									ventana.close();
								}
							}
							$("#txtPed").attr("value", comp);
							$("#dvAlm").slideDown(100, function() {
								$("#fN").show();
							});
						});
					}
				}
			});

			$("#btnCanc").on("click", function() {
				$.get("ImpEmb.php", {
					opc: "e",
					fac2: $("#txtPed").attr("value")
				}, function() {
					$("#fN").hide();
					$("#dvAlm").hide();
					$("#txtPed").attr("value", "0");
				});
			});
			$("#btnAcep").on("click", function() {
				$("#tr" + $("#txtPed").attr("value")).remove();
				$("#fN").hide();
				$("#dvAlm").hide();
				$("#txtPed").attr("value", "0");
				mCuerpo();
			});

			$("#btnImp").on("click", function() {
				var opc = "";
				opc = prompt('Introduzca el Num. de Factura del pedido a Imprimir', '');
				if (opc != null)
					location.href = "imprimePedido.php?fac=" + opc + "&emb=1";
			});

			$("#pPost").on("click", function() {
				$("#dvSA").slideDown(100, function() {
					$("#fN").show();
					$.post("pedPost.php", {}, function(data) {
						$("#dvSA").html(data);
					});
				});
			});

			$("#btnCancPP").live("click", function() {
				$("#dvSA").slideUp(100, function() {
					$("#fN").hide();
				});
			});

			$("#btnGPP").live("click", function() {
				$.post("pedPost.php", {
					ped: $("#txtPed").attr("value"),
					ordSur: $("#txtordSur").attr("value"),
					obs: $("#txtOb").attr("value"),
					obs1: $("#txtOb1").attr("value")
				}, function(data) {
					/*if(data.substr(data.length-2)==-1)
					alert("No es posible agregar el pedido ya que no ha sido facturado o ya ha sido embarcado");*/
					if (data.substr(data.length - 2) == -1) {
						alert("No es posible agregar el pedido ya que no ha sido facturado o ya ha sido embarcado");
					} else if (data.substr(data.length - 2) == -2) {
						alert("No es posible postergar este pedido pues ya fue postergado antes.!");
					}
					$("#dvSA").slideUp(100, function() {
						$("#fN").hide();
					});
				});
			});

			$("#cancPP").live("click", function() {
				$.post("pedPost.php", {
					delP: $(this).attr("name")
				}, function() {
					$("#dvSA").slideUp(100, function() {
						$("#fN").hide();
						alert("Pedido cancelado");
						mEncabezado();
					});
				});
			});

			$("#indSA").on("click", function() {
				$("#dvSA").slideDown(100, function() {
					$("#fN").show();
					$.get("otrosRec.php", {
						salm: 1
					}, function(data) {
						$("#dvSA").html(data);
					});
				});
			});

			$("#qRuta").live("click", function() {
				var registro = $(this).attr("class");
				//debugger;
				$.get("impEmb.php", {
						reg: $(this).attr("class")
					},
					function(respuesta) {
						//debugger;
						if (respuesta.trim() == "") {
							alert("No Aplica");
						} else if (respuesta.trim() == "EXIST_CP") {
							alert("Es necesario cancelar la factura para regresarlo.");
						} else {
							$("#li" + registro).remove();
							mCuerpo();
						}
					});
			});


			$(".impRuta").live("click", function() {
				$.ajax({
					url: "impRuta.php",
					data: "chof=" + $(this).attr("id"),
					type: "get",
					success: function(response) {
						$("#prntInfo").html(response);
					},
					complete: function() {
						var objeto = document.getElementById("prntInfo");
						var ventana = window.open('', '_blank');
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
	<div id='dvAlm'>
		<h1>Se imprimio correctamente la factura?</h1><input type="hidden" id='txtPed' />
		<div class='btnLinea'>
			<center><button id='btnAcep'>Aceptar</button> <button id='btnCanc'>Cancelar</button> <button id='btnIm'>Imprimir</button></center>
		</div>
	</div>

	<div id="osx-modal-content">
		<div id="osx-modal-title">OBSERVACIONES
			<div class="close"><input type="button" id="bC" class="cerrar" value="x" />
			</div>
		</div>
		<div id="osx-modal-data"></div>
	</div>

	<div class="encM">
		<a id="im" class="osx">Observaciones a Pedido</a> | <a id="obsCli" class="lblEmb">Observaciones a Cliente</a> | <a id="btnImp" class="lblEmb">Imprime Pedido</a> | <a id="pPost" class="lblEmb">Pedidos Postergados</a>
		<?php
		if ($_SESSION["LOCAL"] == "01") {
			include("conectabd.php");
			$sql = odbc_exec($conn, "SELECT COUNT(*) AS 'ctos' FROM SCP010 WHERE CP_TIPO=1 AND CP_QUANT-CP_QUJE>0 AND CP_STATSA<>'B' AND CP_PREREQU='' AND D_E_L_E_T_=''") or die("Err54");
			$regs = odbc_fetch_array($sql);
			if ($regs['ctos'] > 0)
				echo "&nbsp;&nbsp;<img id='indSA' src='images/start.png' title='Existen solicitudes por entregar '/>";
			odbc_free_result($sql);
			odbc_close($conn);
		}
		echo "&nbsp;&nbsp;<img src='images/cajas.png' title='Pedidos por embarcar' onclick='window.open(\"resumen_pedidos.php\")' style='height: 25px;'/>";
		?>
		<style>
			.exit{
				background-color: #FF7171;
				justify-content: flex-end;
				float: right;
				border-radius: 5px;
				margin-right: 30PX;
				margin-top: 10PX;


			}

		</style>
		<a href="cerrar.php"><button class="exit">Cerrar sesión</button></a>
		<table>
			<tr>
				<td class="vip">VIP</td>
				<td class="local">Locales</td>
				<td class="rc">Recoge Cliente</td>
				<!-- <td><font size="+1" color="#FF7171"><b>Express</font></b></td> -->
				<td class="foraneos">Foraneos</td>
				<td class="exportacion">Exportaci&oacute;n</td>
				<td class="especiales">Especiales</td>
				<td class="programados">Programados</td>
				<td class="cita">Cita</td>
				<td class="aclaracion">Aclaraciones</td>
				<!--  	<td class="bko">BackOrder</td>-->
				<td class="locm">Local>5000</td>
				<td class="publicidad">Publicidad</td>
				<td class="pe">PE</td>
			</tr>
		</table>
	</div>
	<div class="divs" id="encabezado" style="text-align:center;">
		<h1>Cargando ... Espere...</h1>
	</div>
	<div class="divs" id="cuerpo"></div>
	<div class="divs" id="pie"></div>
</body>

</html>
<?php

?>
