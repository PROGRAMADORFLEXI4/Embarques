<!DOCTYPE html>
<html>
 <head>
  <link href="css/styles.css" rel="stylesheet" />
  <title>Otras Rutas</title>
	<script src="css/jquery.js"></script>
	<script>
		$(document).on('ready', function(){
			$(".cerrar").on("click",function(){$("#osx-modal-content").slideUp(100);$("#fN").hide();});
			$(".mpS, #fN").on("click", function(){$("#dvSA").slideUp(100);$("#fN").hide();$("#detM").hide();});

			$(".tdCod,.mSol").on("click",function(){
				$.post("sm.php",{np:"td"+$(this).attr("id")},function(data,status){$("#detM").html(data);
					if(status="success"){
					  var objeto=document.getElementById('detM'); 
					  var ventana=window.open('','_blank');
					  ventana.document.write(objeto.innerHTML);
					  ventana.document.close();
					}
				 });
			});	
			$(".chkBox").on("click", function(){
				var us="#txt"+$(this).attr("id");
				$.post("sm.php",{cm:$(this).attr("id"),usu:$(us).attr("value")}, function(){$("#tr"+us.substring(4)).remove();});
 		    });
			$("#clsSM").live("click",function(){$("#detM").hide();});
		});
	</script>
	<link rel="shortcut icon" href="images/icono.ico"/>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head>
<body>
<form action="guardaOtrosRec.php" method="post">
<center>
<?php
try{
	include("conectabd.php");
	if(isset($_GET["salm"])){
		echo "<section><h2>MUESTRAS PENDIENTES POR SURTIR.</h2><article id='detM'></article>
				<table class='tSAL'>
		    	<thead class='fixedHeader'><tr><th class='thCod'>N&uacute;m. SA</th><th>Solicitante</th><th>Aprobaci&oacute;n</th><th>Vencimiento</th><th>Entregado</th></tr></thead><tbody class='scrollContent'>";
        $sql=odbc_exec($conn,"SET LANGUAGE 'spanish'; SELECT CP_NUM,CP_SOLICIT,CP_FYHAP,CP_FYHLIM,DATEDIFF(hh,CP_FYHAP,CP_FYHLIM) AS 'tt',DATEDIFF(hh,CP_FYHLIM,GETDATE()) AS 'horas' FROM SCP010 WHERE CP_TIPO=1 AND CP_QUANT-CP_QUJE>0 AND CP_STATSA<>'B' AND CP_PREREQU='' AND D_E_L_E_T_='' GROUP BY CP_NUM,CP_SOLICIT,CP_DATPRF,CP_FYHAP,CP_FYHLIM ORDER BY CONVERT(DATETIME,CP_FYHAP)")or die("ErrorSA");
        while($datos=odbc_fetch_array($sql)){
        	echo "<tr class='trPart' id='tr$datos[CP_NUM]'><td><input type='checkbox' id='$datos[CP_NUM]' class='chkBox'></td><td class='tdCod' id='$datos[CP_NUM]'><img src='images/";
	        if($datos["horas"]>=0)
            	$img="late";
            elseif(($datos["horas"]/2)>=($datos["tt"]*-1))
            	$img="medium";
            else
            	$img="good";
            echo $img.".png'>$datos[CP_NUM]</td><td class='mSol' id='$datos[CP_NUM]'>$datos[CP_SOLICIT]</td><td class='mSol' id='$datos[CP_NUM]'>".substr($datos['CP_FYHAP'],0,14)."</td><td class='mSol' id='$datos[CP_NUM]'>".substr($datos['CP_FYHLIM'],0,14)."</td><td><input type='text' value='$datos[CP_SOLICIT]' id='txt$datos[CP_NUM]' maxlength='25' width='50' /></td></tr>";
        }
        odbc_free_result($sql);
		echo "</tbody></table>
			</section><input type='button' value='Cerrar' class='mpS'>";
		
	}elseif(isset($_GET["opc"])=="e" && isset($_GET["regist"])){
		odbc_exec($conn,"DELETE FROM embRec WHERE R_E_C_N_O_=".$_GET["regist"]) or die("Error al eliminar el registro");
		odbc_close($conn);
	}elseif(isset($_GET["opc"])=="a" && isset($_GET["registro"])){
		odbc_exec($conn,"UPDATE embRec SET estatus=1 WHERE R_E_C_N_O_=".$_GET["reg"]) or die("Error al actualizar el estatus");
		odbc_close($conn);		
		echo "<script languaje='Java Script'>
			window.open('ruta.php','_self');
			</script>";
		exit;
	}elseif(isset($_GET['ped'])){
		$sql=odbc_exec($conn,"SELECT C5_OBSVTA,C5_OBSEMB FROM SC5010 WHERE C5_NUM='$_GET[ped]' AND C5_NOTA='' AND D_E_L_E_T_=''") or die("Error al obtener las observaciones");
		if(odbc_num_rows($sql)>0)
		{
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			echo "<label id='codP'>Pedido de Ventas <strong>$_GET[ped]</strong></label><textarea rows='4' cols='70' disabled>".trim($datos['C5_OBSVTA'])."</textarea><br>Observaciones: <input type='text' name='txtObsEmb' maxlength='300' size='65'  value='$datos[C5_OBSEMB]'><br>&nbsp;<input type='hidden' name='txtP' value='$_GET[ped]' />";
		}
		else
			echo "El pedido no existe o ya ha sido facturado";
		odbc_close($conn);
//		exit;		
	}
	elseif(isset($_GET['codC']))
	{
		$sql=odbc_exec($conn,"SELECT A1_NOME,A1_OBSEMB FROM SA1010 WHERE A1_COD='".$_GET['codC']."' AND D_E_L_E_T_=''")or die("Error obsEmb");
		if(odbc_num_rows($sql)>0)
		{
			$datos=odbc_fetch_array($sql);
			echo "<strong>[<input type='hidden' name='codCli' value='$_GET[codC]' />".strtoupper($_GET['codC'])."</label>] ".$datos['A1_NOME']."</strong><br>Observaciones: <input type='text' name='txtOEmb' maxlength='50' size='65'  value='$datos[A1_OBSEMB]' onClick='this.focus()'/>";
		}
		odbc_free_result($sql);
	}
	else
	{
		$sql=odbc_exec($conn,"SELECT ZZN_CODIGO,ZZN_NOMBRE FROM ZZN010 WHERE ZZN_TIPO=1 AND D_E_L_E_T_=''")
		or die("Error al obtener las observaciones del pedido de embarques");
		echo "<h2>OTRAS RUTAS</h2>";
		$chofer="<select name='cmbChof'>";
		while($datos=odbc_fetch_array($sql))
			$chofer.="<option value='".$datos['ZZN_CODIGO']."'>".$datos['ZZN_NOMBRE']."</option>";
		odbc_free_result($sql);
		echo "<table><tr><td align='left'>CHOFER:</td><td align='left'>$chofer</td></tr>
				<tr><td align='left'>DEPARTAMENTO:</td><td><input type='text' name='txtDepto' placeholder='Nombre del departamento que solicita' required size='100' maxlength='60'></td></tr>
				<tr><td align='left'>DESCRIPCIÓN:</td><td><input type='text' name='txtDescrip' placeholder='Descripción del pendiente' required size='100' maxlength='100'></td></tr></table>";
	}
	odbc_close($conn);
}catch(Exception $ex){
   	echo "Error: ".$ex->getMessage();
}

if(!isset($_GET["salm"]))
	echo "<br><input type='submit' value='Guardar'>&nbsp;&nbsp;<input type='button' value='Cancelar' onClick='window.close();' class='cerrar'>";
?>
</center>
</form>
</body>
</html>