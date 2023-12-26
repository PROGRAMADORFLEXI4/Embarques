<?php
	include("conectabd.php");
	//echo strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']));
	$sql=odbc_exec($conn,"SELECT ZPP_ADMIN FROM ZPP010 WHERE ZPP_NOMPC='".strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))."' AND ZPP_PAGINA='Pedidos' AND D_E_L_E_T_=''")or die("Error al validar el equipo");
	$esAdmin="F";
	if(odbc_num_rows($sql)==0){
		echo "<script languaje='JavaScript'>
				alert('Usuario o contrase�a no validos ".strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))."');
				window.open('http://gdl-web/flexinet/','_self');
		</script>";
		odbc_free_result($sql);
		exit;
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
				$.post("graficas.php",{}, function(data){$("aside").html(data);});
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
				$.post("sm2.php", { np: $(this).attr("id") }, function(data, status) {
				$("#detM").html(data);
				if (status === "success") {
					var objeto = document.getElementById('detM');
					var ventana = window.open('', '_blank');
					ventana.document.write(objeto.innerHTML);
					ventana.document.close();
				}
			});

			e.preventDefault(); // Dependiendo del contexto, asegúrate de que esto se encuentre dentro de una función de manejo de eventos.
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
					alert("El n�mero de salida no debe estar vac�o")
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
				var cod=prompt("�Codigo de cliente?","");
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
        <ul type="square" class="clasificaciones">
            <li class="vip">VIP</li>
            <li class="local">Locales &nbsp;<label class="rc">Recoge Cliente</label></li>
            <li class="foraneos">Foraneos</li>
        </ul>    
        <ul type="square" class="clasificaciones">
            <li class="exportacion">Exportaci&oacute;n</li>
            <li class="especiales">Especiales</li>
            <li class="programados">Programados</li>
        </ul>
        <ul type="square" class="clasificaciones">
        	<li class="cita">Cita/Contado/Tinacos/Tramite normal</li>
            <li class="aclaracion">Aclaraciones</li>
            <!--<li class="bko">BackOrder &nbsp;<label class="publicidad">Publicidad</label></li>-->
			<li class="bko">BackOrder</li>
        </ul>
		<ul type="square" class="clasificaciones">
			<li class="publicidad">Publicidad</li>
			<li class="pe">PE</li>
			<li class="audit">Auditado</li>
		</ul>
		<!--<ul type="square" class="clasificaciones">
			<li class='contado'>Contado</li>
		</ul>-->
        <?php
            if($esAdmin=="T")
				echo "<label id='pPost' class='lblEmb'>Pedidos Postergados</label>  | <label id='obsCli' class='lblEmb'>Observaciones a Cliente</label> 
                		<div id='prntMenu'><img src='images/printer.png'/><div id='menus'>
						<label class='btnMenu' id='btnApP'>Aprobar Pedido</label>
                        <label class='btnMenu' id='btnSal'>Reimpresi&oacute;n de Salida</label>
                        <label class='btnMenu' id='btnIPC'>Imp. Pedido completo</label>
                        <label class='btnMenu' id='btnIP'>Impresi�n de Pedido</label>
						<label class='btnMenu' id='btnSolAlm'>Reimprimir Solicitud</label>
						<label class='btnMenu' id='btnSalC'>Reimpresi&oacute;n Walmart</label>
						<!-- <label class='btnMenu' id='btnEmb'>Embarques</label> -->
                        </div></div>";
        ?>
        <table class="tablaH" style='widht:93%;'>
        	<tr>	
        		<th class="thPed">Num. Pedido</th>
        		<th class="thCliente">Nombre del Cliente</th>
        		<th class="thPed">Fecha Ap. CYC</th>
        		<th class="thPed">Fecha de Surtido</th>
        		<th class="thPed">Fecha de Salida</th>
        		<th class="thPed">Fecha de Auditado</th>
        		<th class="thAlm">Almacenista</th>
        		<th class="thPed">Fecha L&iacute;mite</th>
        		<th class="thRes">Fletera</th>
        		<th class="thInd">Ind</th>
        	</tr>
        </table>
    </header>
    <main>
    	<div id="osx-modal-content"><div id="osx-modal-title">OBSERVACIONES<div class="close"><input type="button" id="bC" class="cerrar" value="x"/></div></div>
        	<div id="osx-modal-data"></div>
    	</div>
     <table cellpadding="0" cellspacing="0">
        <tr>
         <td>
          <table cellpadding="0" cellspacing="0" id="pxs">
          <caption>PEDIDOS EN ESPERA DE SURTIR</caption>
            <?php
            odbc_exec($conn,"SET LANGUAGE 'Spanish'");
            /*$exce=odbc_exec($conn,"SELECT C5_CONDPAG,CASE WHEN C5_USER='Atencion a Cliente' THEN 'aclaracion' ELSE CASE WHEN C5_FPROG>0 THEN 'programados' ELSE CASE WHEN A1_TIPO=1 THEN 'vip' ELSE CASE WHEN A1_TIPO=2 THEN CASE WHEN C5_RECCLIE='SI' THEN 'rc' ELSE 'local' END ELSE CASE WHEN A1_TIPO=3 THEN 'foraneos' ELSE CASE WHEN A1_TIPO=4 THEN 'exportacion' ELSE CASE WHEN A1_TIPO=6 THEN 'cita' ELSE 'especiales' END END END END END END END AS 'tipo',C5_EMBCPED,ISNULL(ZVA_NUM,'') AS 'postergado',ISNULL(ZVA_OBSERV,'') AS 'desZVA',ISNULL(C5_OBSPED,'') AS 'obsPed',C5_NUM,SUBSTRING(A1_NOME,1,30) AS 'A1_NOME',C5_FYHRCYC,ISNULL(ZZM_FYHSUR,'') AS ZZM_FYHSUR,ISNULL(ZZM_CAJAS,0) AS ZZM_CAJAS,ISNULL(ZZM_FECSUR,'') AS ZZM_FECSUR,ISNULL(ZZM_FATURA,'') AS ZZM_FATURA,C5_URGENTE,ZZN_NOMBRE,ISNULL(ZZS_FYHSAL,'') AS ZZS_FYHSAL,DATEDIFF(mi,C5_FYHRCYC, C5_FYHSURT) AS 'horas',DATEDIFF(mi,C5_FYHSURT, GETDATE()) AS 'horasT',C5_FYHSURT FROM SC5010 SC5 INNER JOIN SC6010 SC6 ON C5_NUM=C6_NUM LEFT JOIN (SELECT ZZM_PEDIDO,ZZN_NOMBRE,ZZM_FYHSUR,(ZZM_CAJAS+ZZM_COSTAL+ZZM_EXHIB+ZZM_TINACO) AS ZZM_CAJAS,ZZM_FECSUR,ZZM_FATURA FROM ZZM010 ZZM LEFT JOIN ZZN010 ON ZZM_CODALM=ZZN_CODIGO WHERE ZZM.D_E_L_E_T_<>'*') AS ZZM ON C5_NUM=ZZM.ZZM_PEDIDO INNER JOIN SA1010 SA1 ON A1_COD=C5_CLIENTE LEFT JOIN ZZS010 ON C5_NUM=ZZS_PEDIDO LEFT JOIN SC9010 SC9 ON C5_NUM=C9_PEDIDO LEFT JOIN (SELECT ZVA_NUM,ZVA_OBSERV FROM ZVA010 WHERE ZVA_STATUS=1 AND D_E_L_E_T_='') AS ZVA ON C5_NUM=ZVA.ZVA_NUM WHERE C5_LOJAENT=A1_LOJA AND C5_FYHRCYC<>'' AND C5_NOTA='' AND (C6_TES='501' OR C6_TES='502') AND (C6_QTDVEN-C6_QTDENT)>0 AND C6_BLQ='' AND C9_BLCRED<>'09' AND C9_BLCRED<>'01' AND SC5.D_E_L_E_T_<>'*' AND SC9.D_E_L_E_T_<>'*' AND SC6.D_E_L_E_T_<>'*' AND SA1.D_E_L_E_T_<>'*' GROUP BY C5_NUM,C5_OBSPED,C5_CONDPAG,A1_NOME,C5_FYHRCYC,ZZM_FYHSUR,ZZM_CAJAS,ZZM_FECSUR,ZZM_FATURA,C5_URGENTE,ZZN_NOMBRE,ZZS_FYHSAL,C5_FYHSURT,C5_RECCLIE,C5_EMBCPED,ZVA_NUM,ZVA_OBSERV,C5_USER,A1_TIPO,C5_FPROG ORDER BY CONVERT(DATETIME,C5_FYHRCYC),ZZM_FATURA ASC")or die("Error al ejecutar la consulta");*/
			//case when C5_CLIENTE = '' then 'pe' else 
			$exce=odbc_exec($conn,"SELECT C5_CLIENTE,C5_CONDPAG,case when C5_CLIENTE = 'D00123' then 'pe' else  CASE WHEN C5_USERVTA='Merkadotecnia' THEN 'publicidad' ELSE CASE WHEN C5_USER='Atencion a Cliente' THEN 'aclaracion' ELSE CASE WHEN C5_FPROG>0 THEN 'programados' ELSE CASE WHEN A1_TIPO=1 THEN 'vip' ELSE CASE WHEN A1_TIPO=2 THEN CASE WHEN C5_RECCLIE='SI' THEN 'rc' ELSE 'local' END ELSE CASE WHEN A1_TIPO=3 THEN 'foraneos' ELSE CASE WHEN A1_TIPO=4 THEN 'exportacion' ELSE CASE WHEN A1_TIPO=6 THEN 'cita' ELSE CASE WHEN A1_TIPO=8 THEN 'audit' ELSE 'especiales' END END END END END END END END END end AS 'tipo',C5_EMBCPED,ISNULL(ZVA_NUM,'') AS 'postergado',ISNULL(ZVA_OBSERV,'') AS 'desZVA',ISNULL(C5_OBSPED,'') AS 'obsPed',C5_NUM,SUBSTRING(A1_NOME,1,30) AS 'A1_NOME',C5_FYHRCYC,ISNULL(ZZM_FYHSUR,'') AS ZZM_FYHSUR,ISNULL(ZZM_CAJAS,0) AS ZZM_CAJAS,ISNULL(ZZM_FECSUR,'') AS ZZM_FECSUR,ISNULL(ZZM_FATURA,'') AS ZZM_FATURA,C5_URGENTE,ZZN_NOMBRE,ISNULL(ZZS_FYHSAL,'') AS ZZS_FYHSAL,DATEDIFF(mi,C5_FYHRCYC, C5_FYHSURT) AS 'horas',DATEDIFF(mi,C5_FYHSURT, GETDATE()) AS 'horasT',C5_FYHSURT FROM SC5010 SC5 INNER JOIN SC6010 SC6 ON C5_NUM=C6_NUM LEFT JOIN (SELECT ZZM_PEDIDO,ZZN_NOMBRE,ZZM_FYHSUR,(ZZM_CAJAS+ZZM_COSTAL+ZZM_EXHIB+ZZM_TINACO) AS ZZM_CAJAS,ZZM_FECSUR,ZZM_FATURA FROM ZZM010 ZZM LEFT JOIN ZZN010 ON ZZM_CODALM=ZZN_CODIGO WHERE ZZM.D_E_L_E_T_<>'*') AS ZZM ON C5_NUM=ZZM.ZZM_PEDIDO INNER JOIN SA1010 SA1 ON A1_COD=C5_CLIENTE LEFT JOIN ZZS010 ON C5_NUM=ZZS_PEDIDO LEFT JOIN SC9010 SC9 ON C5_NUM=C9_PEDIDO LEFT JOIN (SELECT ZVA_NUM,ZVA_OBSERV FROM ZVA010 WHERE ZVA_STATUS=1 AND D_E_L_E_T_='') AS ZVA ON C5_NUM=ZVA.ZVA_NUM WHERE C5_LOJAENT=A1_LOJA AND C5_FYHRCYC<>'' AND C5_NOTA='' AND (C6_TES='501' OR C6_TES='502' OR C6_TES='522' OR C6_TES='523') AND (C6_QTDVEN-C6_QTDENT)>0 AND C6_BLQ='' AND C9_BLCRED<>'09' AND C9_BLCRED<>'01' AND SC5.D_E_L_E_T_<>'*' AND SC9.D_E_L_E_T_<>'*' AND SC6.D_E_L_E_T_<>'*' AND SA1.D_E_L_E_T_<>'*' GROUP BY C5_NUM,c5_cliente,C5_OBSPED,C5_CONDPAG,A1_NOME,C5_FYHRCYC,ZZM_FYHSUR,ZZM_CAJAS,ZZM_FECSUR,ZZM_FATURA,C5_URGENTE,ZZN_NOMBRE,ZZS_FYHSAL,C5_FYHSURT,C5_RECCLIE,C5_EMBCPED,ZVA_NUM,ZVA_OBSERV,C5_USER,C5_USERVTA,A1_TIPO,C5_FPROG ORDER BY CONVERT(DATETIME,C5_FYHRCYC),ZZM_FATURA ASC")or die("Error al ejecutar la consulta");
            $pedido="";	
            $pxs="";
            $ppa="";
            $pxf="";
            $bkO="";
            $primero=0;
            $img="";
            while($datos=odbc_fetch_array($exce))
            { 
             $nmf=$datos["C5_NUM"];

            	$nomfle=odbc_exec($conn,"SELECT A2_NREDUZ,C5_NUM FROM
				(SELECT * FROM SC5010 where D_E_L_E_T_='' ) C5 
				LEFT JOIN 
				(SELECT * FROM SA2010 where D_E_L_E_T_='' ) A2 ON C5_TRANSP = A2_COD WHERE C5_NUM = '".$datos["C5_NUM"]."';")or die("Error al ejecutar la consulta");
            	$res=odbc_fetch_array($nomfle);

            	
                if($pedido!=$datos["C5_NUM"])
                {
					$fecha_resto=substr($datos["C5_FYHSURT"],6,2)."-".substr($datos["C5_FYHSURT"],3,2)."-".substr($datos["C5_FYHSURT"],0,2).substr($datos["C5_FYHSURT"],8);
					$dias=intval((strtotime($fecha_resto)-strtotime(date("Y-m-d H:i:s")))/60/60/24);
					$horas=intval(((strtotime($fecha_resto)-strtotime(date("Y-m-d H:i:s")))/60/60)-($dias*24));
					$minutos=intval((strtotime($fecha_resto)-strtotime(date("Y-m-d H:i:s")))/60)-($dias*24)-($horas*60);
					//echo ($dias>0?$dias.'D':'').($horas>0?$horas.'H':'').($minutos>0?$minutos.'M':'');
                    $clrFuente=$datos['tipo'];
                    $img="<img src='images/";
					if($datos["postergado"]==""){
						if($datos["horasT"]>=0)
							$img.="late";
						elseif(($datos["horas"]/2)>=($datos["horasT"]*-1))
							$img.="medium";
						else
							$img.="good";
						$img.=".png'>";
					}
					else
						$img.="cal.png' title='$datos[desZVA]'>";

                    if(trim($datos["obsPed"])<>"")
                        $img.="&nbsp;<img src='images/note.png' title='".$datos["obsPed"]."' />";
						
					if(trim($datos['C5_EMBCPED'])<>"")
					{
						if(trim($datos['C5_CLIENTE'])=="D00123")
							$img.="&nbsp;<img src='images/link4.png' title='Surtir junto con pedido $datos[C5_EMBCPED]' />";
						else
							$img.="&nbsp;<img src='images/reload.png' title='Surtir junto con pedido $datos[C5_EMBCPED]' />";
					}
					if($datos["C5_CONDPAG"]=="000" && $datos["tipo"]<>"Atencion a Cliente" && trim(strtoupper($datos["A1_NOME"]))<>"VENTAS AL PUBLICO EN GENERAL" && $datos["tipo"]<>"exportacion")
					{
						//$clrFuente='contado';
						$img.="&nbsp;<img src='images/money.ico' title='Cliente de contado'/>";
					}
                    /*Si es BackOrder lo marca*/
                    if(trim($datos["ZZM_FATURA"])<>"")
                        $clrFuente='bko';
                    /*Valida si los pedidos tienen cajas surtidas significa que esta en espera de facturaci�n o cuenta con bakorder*/
                    if($datos["ZZM_CAJAS"]>0 && trim($datos["ZZM_FATURA"])<>"")
                    {
                        $bkO.="
								<tr class='$clrFuente'>
									<td class='thPed'>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a class='$clrFuente' id='$datos[C5_NUM]0' name='2' title='".$datos["C5_NUM"]."'>".$datos["C5_NUM"]."</a>
									</td>
									<td class='thCliente'>".$datos["A1_NOME"]."</td>
									<td class='thPed'>".substr($datos["C5_FYHRCYC"],0,15)."</td>
									<td class='thPed'>".substr($datos["ZZS_FYHSAL"],0,15)."</td>
									<td class='thPed'>".substr($datos["ZZM_FYHSUR"],0,16)."</td>
									<td class='thPed'>".substr($datos["ZZM_FECSUR"],0,15)."</td>
									<td class='thAlm'>".trim($datos["ZZN_NOMBRE"])."</td>
									<td class='thPed'>".$datos["C5_FYHSURT"]."</td>
									<td class='thRes'>".$res['A2_NREDUZ']."</td>
									<td class='thInd'>&nbsp;$img</td>
                                </tr>";
                    }
                    elseif(trim($datos["ZZM_FYHSUR"])=="")
                    {
                        $primero=$primero+1;
                        echo "<tr class='$clrFuente'><td class='thPed'><font size='3'>[".number_format($primero,0)."]</font>";
                        if($datos["C5_URGENTE"]=="T")
                            echo "<img src='images/flag.png'/> ";
                        if($esAdmin=="T")
                            //Marca como pedido para surtir
                            echo "<img src='images/edit.png' title='Agregar Observaciones al pedido' onclick=javascript:window.open('guardaObsP.php?Ped=".$datos["C5_NUM"]."','_blank','resisable=no')>&nbsp;&nbsp;<a class='$clrFuente' id='$datos[C5_NUM]1' name='0' title='Marcar / Desmarcar como urgente'>".$datos["C5_NUM"]."</a>";
                        else
                        {
                            if($primero==1 || $datos["C5_URGENTE"]=="T")
                                echo "<a class='$clrFuente' id='$datos[C5_NUM]0' name='1'>".$datos["C5_NUM"]."</a>";
                            else
                                echo "<a class='$clrFuente' id='$datos[C5_NUM]0' name='0'>".$datos["C5_NUM"]."</a>";
                        }
                        echo "	</td>
                        		<td class='thCliente'>".$datos["A1_NOME"]."</td>";
                        echo "	<td class='thPed'>".substr($datos["C5_FYHRCYC"],0,15)."</td>	
                        		<td class='thPed'>&nbsp;</td>
                        		<td class='thPed'>&nbsp;</td>
                        		<td class='thPed'>&nbsp;</td>
                        		<td class='thAlm'>&nbsp;</td>
                        		<td class='thPed'>".$datos["C5_FYHSURT"]."</td>
                        		<td class='thRes'>".$res['A2_NREDUZ']."</td>
                        		<td class='thInd'>&nbsp;".$img."</td></tr>";
                    }
                    else
                    {
                        if($datos["ZZM_CAJAS"]==0)
                        {
							if(trim($datos["ZZS_FYHSAL"])!="")
								$clrFuente='bko';
                            //No se encuentra capturada una salida
                            $pxs.="<tr class='$clrFuente'>
                            <td class='thPed'>";
                            //Si el pedido tiene capturado la salida, mostrarlo como solo lectura?
                            if(trim($datos["ZZM_FECSUR"])<>"")
                            {
                                $pxs.=$datos["C5_NUM"];
                            }
                            else
                            {
								if($esAdmin=="T")
								{
									//Re imprime el pedido si es administrador
									$pxs.="<img src='images/edit.png' title='Agregar Observaciones al pedido' onclick=javascript:window.open('guardaObsP.php?Ped=".$datos["C5_NUM"]."','_blank','resisable=no')>&nbsp;&nbsp;<a class='$clrFuente' id='$datos[C5_NUM]x' name='x' title='Cancela Surtido del Pedido: ".$datos["C5_NUM"]."'>".$datos["C5_NUM"]."</a>";
                                }
                                else
                                {
                                    //Captura la salida
                                    $pxs.="<a class='$clrFuente' href='guardaTempo.php?Ped=".base64_encode($datos["C5_NUM"])."&opc=N' target='_top' title='Captura Salida del Pedido: ".$datos["C5_NUM"]."'>".$datos["C5_NUM"]."</a>";
                                }
                            }
                            $pxs.="</td>
                            	<td class='thCliente'>".$datos["A1_NOME"]."</td>
                            	<td class='thPed'>".substr($datos["C5_FYHRCYC"],0,15)."</td>
                            	<td class='thPed'>".substr($datos["ZZM_FYHSUR"],0,16)."</td>
                            	<td class='thPed'>&nbsp;</td><td class='thPed'>&nbsp;</td>
                            	<td class='thAlm'>".trim($datos["ZZN_NOMBRE"])."</td>
                            	<td class='thPed'>".$datos["C5_FYHSURT"]."</td>
                            	<td class='thRes'>".$res['A2_NREDUZ']."</td>
                            	<td class='thInd'>&nbsp;".$img."</td></tr>";
                        }
                        else
                        {
							$pfac2=odbc_exec($conn,"SELECT C6_NOTA FROM SC6010 WHERE C6_NUM='$datos[C5_NUM]' AND C6_NOTA<>'' AND D_E_L_E_T_='' GROUP BY C6_NOTA")or die("Error s6");
							if(odbc_num_rows($pfac2)>0)
								$clrFuente='bko';
                            //Pedidos por Auditar
                            if($esAdmin=="T")
                            {
                                //Re imprime el pedido si es administrador
                                if($datos["ZZM_FECSUR"]=="")
                                    $ppa.="<tr class='$clrFuente'><td class='thPed'>
											<img src='images/edit.png' title='Agregar Observaciones al pedido' onclick=javascript:window.open('guardaObsP.php?Ped=".$datos["C5_NUM"]."','_blank','resisable=no')>&nbsp;&nbsp;
											<a class='$clrFuente' id='$datos[C5_NUM]2' name='2' title='Cancela / Re imprime el Pedido: ".$datos["C5_NUM"]."'>".$datos["C5_NUM"]."</a>";
                                else
                                    $pxf.="<tr class='$clrFuente'><td class='thPed'>
											<img src='images/edit.png' title='Agregar Observaciones al pedido' onclick=javascript:window.open('guardaObsP.php?Ped=".$datos["C5_NUM"]."','_blank','resisable=no')>&nbsp;&nbsp;
											<a class='$clrFuente' id='$datos[C5_NUM]2' name='2' title='Cancela / Re imprime el Pedido: ".$datos["C5_NUM"]."'>".$datos["C5_NUM"]."</a>";
                            }
                            else
                            {
                                if($datos["ZZM_FECSUR"]=="")
                                    $ppa.="<tr class='$clrFuente'><td class='thPed'><a class='$clrFuente' href='guardaTempo.php?Ped=".base64_encode($datos["C5_NUM"])."&opc=N' target='_top' title='Captura Salida del Pedido: ".$datos["C5_NUM"]."'>".$datos["C5_NUM"]."</a>";
                                else
                                    $pxf.="<tr class='$clrFuente'><td class='thPed'>".$datos["C5_NUM"];
                            }
                            if($datos["ZZM_FECSUR"]=="")
                            {
                                $ppa.="</td>
                                <td class='thCliente'>".$datos["A1_NOME"]."</td>
                                <td class='thPed'>".substr($datos["C5_FYHRCYC"],0,15)."</td>
                                <td class='thPed'>".substr($datos["ZZM_FYHSUR"],0,16)."</td>
                                <td class='thPed'>".trim(substr($datos["ZZS_FYHSAL"],0,16))."</td>
                                <td class='thPed'>&nbsp;</td>
                                <td class='thAlm'>".trim($datos["ZZN_NOMBRE"])."</td>
                                <td class='thPed'>".$datos["C5_FYHSURT"]."</td>
                                <td class='thRes'>".$res['A2_NREDUZ']."</td>
                                <td class='thInd'>&nbsp;".$img."</td></tr>";
                            }
                            else
                            {
                                $pxf.="</td>
                                <td class='thCliente'>".$datos["A1_NOME"]."</td>
                                <td class='thPed'>".substr($datos["C5_FYHRCYC"],0,15)."</td>
                                <td class='thPed'>".substr($datos["ZZM_FYHSUR"],0,16)."</td>
                                <td class='thPed'>".trim(substr($datos["ZZS_FYHSAL"],0,16))."</td>
                                <td class='thPed'>".$datos["ZZM_FECSUR"]."</td>
                                <td class='thAlm'>".trim($datos["ZZN_NOMBRE"])."</td>
                                <td class='thPed'>".$datos["C5_FYHSURT"]."</td>
                                <td class='thRes'>".$res['A2_NREDUZ']."</td>
                                <td class='thInd'>&nbsp;".$img."</td></tr>";
                            }
                        }
                    }
                }
                $pedido=$datos["C5_NUM"];
            }
          ?>
          </table>
         </td>
        </tr>
        <tr>
         <td>
         	<hr>
            <table cellpadding="0" cellspacing="0">
	            <caption>PEDIDOS EN PROCESO DE SURTIDO</caption>
            <?php
                echo $pxs;
            ?>
            </table>
         </td>
        </tr>
        <tr>
         <td>
         	<hr>
            <table cellpadding="0" cellspacing="0">
            	<caption>PEDIDOS POR AUDITAR</caption>
                <?php
                    echo $ppa;
                ?>
           </table>
         </td>
        </tr>
          <td>
          	<hr>
            <table cellpadding="0" cellspacing="0">
            	<caption>PEDIDOS POR FACTURAR</caption>
                <?php
                    echo $pxf;
					if($esAdmin=="T")
					{
						$sql=odbc_exec($conn,"SELECT C5_NUM,A1_NOME,C5_FYHRCYC FROM SC5010 SC5 INNER JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD INNER JOIN SC6010 SC6 ON C5_NUM=C6_NUM INNER JOIN SC9010 SC9 ON C6_NUM=C9_PEDIDO INNER JOIN ZZM010 ZZM ON ZZM_PEDIDO=C5_NUM AND ZZM.D_E_L_E_T_='' WHERE C5_LOJAENT=A1_LOJA AND C5_FYHRCYC<>'' AND C5_NOTA='' AND C6_TES='522' AND C6_BLQ='' AND C9_BLCRED<>'09' AND C9_BLCRED<>'01' AND SC5.D_E_L_E_T_='' AND SC6.D_E_L_E_T_='' AND SC9.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' GROUP BY C5_NUM,A1_NOME,C5_FYHRCYC,ZZM_FYHSUR ORDER BY C5_NUM")or die("Error en pedidos publicidad");
						while($datos=odbc_fetch_array($sql))
							echo "<tr class='publicidad'><td>".$datos["C5_NUM"]."</td><td>".$datos["A1_NOME"]."</td><td>".substr($datos["C5_FYHRCYC"],0,15)."</td><td colspan='6'>&nbsp;</td></tr>";
						odbc_free_result($sql);					
					}

					
        //Pedidos por embarcar
		/*
        $sql=odbc_exec($conn,"SELECT ZPP_ADMIN FROM ZPP010 WHERE ZPP_NOMPC='".strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))."' AND ZPP_PAGINA='Embarques' AND D_E_L_E_T_=''")
        or die("Error al validar el equipo para embarques");
        if(odbc_num_rows($sql)>0)
        {
            odbc_free_result($sql);
            odbc_exec($conn,"SET LANGUAGE 'espa�ol'");
            $sql=odbc_exec($conn,"SELECT C5_NUM,CASE WHEN C5_USER='atencion a cliente' THEN 0 ELSE CASE WHEN C5_FPROG>0 THEN 6 ELSE A1_TIPO END END AS 'tipo',SUBSTRING(A1_NOME,1,30) AS 'A1_NOME',C5_HORA,C5_FYHRCYC,C5_FYHSURT,ZZO_FECFAC,ZZO_FACT,ZZO_OBSEMB FROM ZZO010 ZZO INNER JOIN SC5010 SC5 ON ZZO_PEDIDO=C5_NUM LEFT JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD WHERE ZZO_FACT IN(SELECT C6_NOTA FROM SC6010 WHERE C6_TES BETWEEN 501 AND 502 GROUP BY C6_NOTA) AND ZZO_FEMBAR='' AND ZZO.D_E_L_E_T_='' AND SC5.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' ORDER BY ZZO_OBSEMB,CONVERT(DATETIME,C5_FYHRCYC)") or die("Error al obtener los datos de embarque");
            echo "</table>
            </tr>
            </tr><tr><td colspan='6' align='center'><hr></td></tr>
                    <tr>
						
                        <table cellpadding='0' cellspacing='0'>
							<caption>PEDIDOS POR EMBARCAR</caption>";
                            while($datos=odbc_fetch_array($sql))
                            {
                                if($datos['tipo']==0)
                                    $clrFuente='aclaracion';
                                elseif($datos['tipo']==6)
                                    $clrFuente='programados';
                                elseif($datos['tipo']==1)
                                    $clrFuente='vip';
                                elseif($datos['tipo']==2)
                                    $clrFuente='local';
                                elseif($datos['tipo']==3)
                                    $clrFuente='foraneos';
                                elseif($datos['tipo']==4)
                                    $clrFuente='exportacion';
                                elseif($datos['tipo']==5)
                                    $clrFuente='especiales';								
                                if(trim($datos['ZZO_OBSEMB'])<>"")
                                    echo "<tr bgcolor='#0033CC'>";
                                else
                                    echo "<tr class='$clrFuente'>";
                                echo "<td class='thPed'>".$datos['C5_NUM']."</td>
                                        <td class='thCliente'>$datos[A1_NOME]</td>
                                        <td class='thPed'>".substr($datos['C5_FYHRCYC'],0,15)."</td>
                                        <td class='thPed'>".$datos['ZZO_FECFAC']."</td>
                                        <td class='thCliente'>".$datos['ZZO_FACT']."</td><td class='thPed'>".$datos["C5_FYHSURT"]."</td><td class='thInd'>";
                                        if(trim($datos['ZZO_OBSEMB'])<>"")
                                            echo "&nbsp;&nbsp;<center><img src='images/note.png' title='".$datos['ZZO_OBSEMB']."' /></center>";
                                        echo "</td>
                                      </tr>";
                            }
            echo"       </table>
                    </tr>
                 </tr>";
        }
        else{
            odbc_free_result($sql);
            odbc_close($conn);
        }
		*/
        ?>
            </table>
          </td>
        </tr>    
        <tr>
            <td colspan="8" align="center"><hr></td>
        </tr>
        <tr>
            <table cellpadding="0" cellspacing="0">
            	<caption>PEDIDOS EN BACKORDER</caption>
                <?php
                    echo $bkO;
                ?>
           </table>
        </tr>
     </table>
     <br/>
         <footer>
            Fleximatic S.A. de C.V.<br> Departamento de sistemas Mayo 2011<br>  Desarrollado por: Ing. Fernando Ju&aacute;rez, Lae. Ernesto Gonz&aacute;lez. <br> Ultima actualizacion por: Ing. Carlos Ochoa.   
        </footer>
     <br/><br/>
    
    </main>
    <aside>
    </aside>
 </form>    
 </body>
</html>