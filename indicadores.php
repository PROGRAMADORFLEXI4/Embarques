<!DOCTYPE html>
<html>
 <head>
	<title>Indicadores</title>
    <link rel="shortcut icon" href="images/icono.ico" />
    <link rel="stylesheet" href="indicadores.css" />
 	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta content="IE=edge" http-equiv="x-UA-Compatible">
    <script src="css/jquery.js"> </script>
    <script>
		$(document).on("ready",function(){
//			graficas();
			function graficas(){
				$.post("res.php",{opc:"grT",an:$("#anio").text()}, function(data){$("#surT").html(data);});
			}
			$("#aSig").on("click", function(){$("#anio").text(parseInt($("#anio").text())+1);});
			$("#aAnt").on("click", function(){$("#anio").text(parseInt($("#anio").text())-1);});
			$("#pedsGral").on("click", function(){
				var vista="gral";
				if($(this).text()=="Panorama de Almacén"){
					$(this).text("Panorama General");
				}else{
					$(this).text("Panorama de Almacén");
					vista="alm";
				}
				if($("#txtFec").attr("value")){
					$("#surT").text("");
					$("#surA").text("");
					$("#detalle").text("");
					$.post("res.php",{opc:"T",m:$("#txtFec").attr("value")-1,an:$("#anio").text(),vsta:vista}, function(data){$("#surT").html(data);});
					$.post("res.php",{opc:"A",m:$("#txtFec").attr("value")-1,an:$("#anio").text(),vsta:vista}, function(data){$("#surA").html(data);});
				}
			});
												
			$("a").on("click", function(){
				$(".resumen").text("");
				$("#detalle").text("");
				$("#datosP").text("");
				$(".valor").text("");
				$("#cal a").removeClass("styleA");
				$(this).addClass("styleA");
				var cont="#totP"+$(this).attr("id");
				var vista="gral";
				if($("#pedsGral").text()!="Panorama General")
					vista="alm";				
				$.post("res.php",{opc:"peds",m:$(this).attr("id"),an:$("#anio").text()}, function(data){$(cont).html(data);});
				$.post("res.php",{opc:"T",m:$(this).attr("id"),an:$("#anio").text(),vsta:vista}, function(data){$("#surT").html(data);});
				$.post("res.php",{opc:"A",m:$(this).attr("id"),an:$("#anio").text(),vsta:vista}, function(data){$("#surA").html(data);});
				$.post("res.php",{opc:"E",m:$(this).attr("id"),an:$("#anio").text()}, function(data){$("#surE").html(data);});
				$.post("res.php",{opc:"NS",m:$(this).attr("id"),an:$("#anio").text()}, function(data){$("#bko").html(data);});
				$.post("res.php",{opc:"EVTA",m:$(this).attr("id"),an:$("#anio").text()}, function(data){$("#noSur").html(data);});
			});
		});
	</script>
 </head>
<body>
<div id="fondoObsc"></div>
<div id="contenedor">
	<div id="detP">

    </div>
	<header>
        <article id="cal">
      		<h1>Indicadores de Pedidos de Almacen.</h1>
            <table class="tDatos">
                <tr><th colspan="2"><label id="aAnt">◄</label></th><th colspan="8"><label id="anio"><?php echo date("Y") ?></label></th><th colspan="2"><label id="aSig">►</label></th></tr>
                <tr>
                <?php			
                    $meses=array('Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
                    $mes=date("m");
                    for($ind=0;$ind<12;$ind++)
                        echo "<td><a id='$ind' name='$meses[$ind]'>$meses[$ind]</a><span id='totP$ind' class='valor'></span></td>";
                ?>
             </tr>
            </table>
        </article>
		<article id="resPed">
	        <div id="pedsGral">Panorama de Almac&eacute;n</div>
	        <div id="mql">MaquilaFlex</div>
	        <div class="resumen" id="surT"></div>
			<div class="resumen" id="surA"></div>
			<div class="resumen" id="surE"></div>
			<div class="resumen" id="noSur"></div>
			<div class="resumen" id="bko"></div>
        </article>
        <div class="argolla"></div>
    </header>
    <div id="detalle">
    </div>
</div>
</body>
</html>
