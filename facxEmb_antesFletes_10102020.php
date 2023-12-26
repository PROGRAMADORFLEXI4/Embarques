<script>
	$(".fletera").live("change", function(){
		if($(this).attr('title')=='T')
		{
			var desicion;
			desicion=confirm("La fletera predeterminada esta bloqueada, aun as\u00ED deseas cambiarla?")
			if (!desicion)
			{
				document.getElementById($(this).attr('id')).selectedIndex=0;
			}
		}
	});
	
	$("#ocultartrash").on("click", function()
			{	
				if(document.getElementById('ocultartrash').value==1)
				{
					document.getElementById('ocultartrash').value=0;
					document.getElementById("encabezado").innerHTML="<h1>Cargando ... Espere...</h1>";
					//$("#encabezado").load("facxEmb.php", {ocultar:"0"}, function(){});
					
					$.post("facxEmb.php", {ocultar:"0"}, function(data){$("#encabezado").html(data);});
				}
				else
				{
					document.getElementById('ocultartrash').value=1;
					
					document.getElementById("encabezado").innerHTML="<h1>Cargando ... Espere...</h1>";
					//$("#encabezado").load("facxEmb.php", {ocultar:"1"}, function(){});
					$.post("facxEmb.php", {ocultar:"1"}, function(data){$("#encabezado").html(data);});
				}
			});
</script>
<?php 
	$ocultar=$_POST['ocultar'];
	$eti="";
	$cons="";
	if($ocultar=="1")
	{
		$eti="checked";
		$cons=" AND C5_USERVTA<>'Merkadotecnia' AND C5_USER<>'atencion a cliente' AND C5_CLIENTE<>'V00018' ";
	}
	else
	{
		$eti="";
		$cons="";
	}
?>
<form name="facxEmb">
        <h1><u>Facturas Por Embarcar</u><br>
		<input type="checkbox" name="ocultartrash" value="<?php echo $ocultar;?>" id="ocultartrash" <?php echo $eti;?>> Ocultar Publicidad, Aclaraciones y Cte Estandar</h1>		
        <table>
          <tr>
            <th>Pedido</th>
            <th>Cliente</th>
            <th>Fecha Factura</th>
            <th>Lim. Surtido</th>
            <th>Factura</th>
            <th>Estado</th>
            <th>Observaciones</th>
            <th>Pago</th>
            <th>Fletera</th>
			<th>Fletera x Defecto</th>
            <th>Dir Embarque</th>
          </tr>
<?php
	//Fletereas
	include("conectabd.php");
	odbc_exec($conn,"SET LANGUAGE 'español';");
	//$sql=odbc_exec($conn,"SELECT A2_COD,A2_NREDUZ,A2_NOME FROM SA2010 WHERE A2_TPPROV='EMBA1' OR A2_TPPROV='EMBAR' AND D_E_L_E_T_='' ORDER BY A2_NOME") or die("Error al obtener el catalogo de las fleteras");
	$sql=odbc_exec($conn,"SELECT A2_COD,A2_NREDUZ,A2_NOME FROM SA2010 WHERE A2_TPPROV='EMBA1' AND D_E_L_E_T_='' ORDER BY A2_NOME") or die("Error al obtener el catalogo de las fleteras");
	$fleteras="<option value='LOCAL'>LOCAL</option><option value='EXPORT'>EXPORTACION</option><option value='ACLARA'>ACLARACION</option>";
	while($datos=odbc_fetch_array($sql))
		$fleteras.="<option value='".$datos['A2_COD']."' title='".trim($datos['A2_NOME'])."'>".substr(trim($datos['A2_NREDUZ']),0,15)."</option>";
	odbc_free_result($sql);
	/*$sql=odbc_exec($conn,"SELECT valor,ISNULL(ZVA_NUM,'') AS 'postergado',ISNULL(ZVA_OBSERV,'') AS 'desZVA',ISNULL(ZVA_STATUS,'') AS 'statusZVA',C5_EMBCPED,C5_CONDPAG,C5_OBSPED,CASE WHEN DATEDIFF(hh,C5_FYHSURT,GETDATE())>0 THEN 1 ELSE 0 END AS 'pxV',A1_OBSEMB,C5_APFLETE,C5_OBSEMB,ZZO.R_E_C_N_O_ AS 'registro',C5_CLIENTE,C5_APALM,C5_RECCLIE,C5_NUM,C5_FYHSURT,C5_HORA,CASE WHEN C5_USER='atencion a cliente' THEN 0 ELSE CASE WHEN C5_FPROG>0 THEN 7 ELSE A1_TIPO END END AS 'tipo',CASE WHEN C5_DIREMB='' THEN 'FISCAL' ELSE C5_DIREMB END AS 'C5_DIREMB',RTRIM(A1_END)+' | '+RTRIM(A1_MUN)+','+A1_EST AS 'dir',ZZO_FECFAC,ZZO_FACT,ZZO_OBSEMB,A1_COD,A1_NOME,A1_EST,A2_COD,A2_NOME,A2_NREDUZ FROM ZZO010 ZZO LEFT JOIN SC5010 SC5 ON ZZO_PEDIDO=C5_NUM LEFT JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD LEFT JOIN SA2010 SA2 ON C5_TRANSP=A2_COD LEFT JOIN(SELECT ZVA_NUM,ZVA_OBSERV,ZVA_STATUS FROM ZVA010 WHERE D_E_L_E_T_='') AS ZVA ON C5_NUM=ZVA.ZVA_NUM INNER JOIN (SELECT SUM(C6_VALOR) AS 'valor',C6_NUM FROM SC6010 WHERE C6_TES BETWEEN '501' AND '502' AND C6_BLQ='' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM WHERE ZZO_FEMBAR='' AND ZZO.D_E_L_E_T_='' AND SC5.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' ORDER BY ZZO_OBSEMB,CONVERT(DATETIME,C5_FYHRCYC),C5_EMISSAO,A1_EST,C5_TRANSP") or die("Error al obtener los datos de embarques");*/
	//$sql=odbc_exec($conn,"SELECT valor,ISNULL(ZVA_NUM,'') AS 'postergado',ISNULL(ZVA_OBSERV,'') AS 'desZVA',ISNULL(ZVA_STATUS,'') AS 'statusZVA',C5_EMBCPED,C5_CONDPAG,C5_OBSPED,CASE WHEN DATEDIFF(hh,C5_FYHSURT,GETDATE())>0 THEN 1 ELSE 0 END AS 'pxV',A1_OBSEMB,C5_APFLETE,C5_OBSEMB,ZZO.R_E_C_N_O_ AS 'registro',C5_CLIENTE,C5_APALM,C5_RECCLIE,C5_NUM,C5_FYHSURT,C5_HORA,CASE WHEN C5_USERVTA='Merkadotecnia' THEN 8 ELSE CASE WHEN C5_USER='atencion a cliente' THEN 0 ELSE CASE WHEN C5_FPROG>0 THEN 7 ELSE A1_TIPO END END END AS 'tipo',CASE WHEN C5_DIREMB='' THEN 'FISCAL' ELSE C5_DIREMB END AS 'C5_DIREMB',RTRIM(A1_END)+' | '+RTRIM(A1_MUN)+','+A1_EST AS 'dir',ZZO_FECFAC,ZZO_FACT,ZZO_OBSEMB,A1_COD,A1_NOME,A1_EST,A2_COD,A2_NOME,A2_NREDUZ FROM ZZO010 ZZO LEFT JOIN SC5010 SC5 ON ZZO_PEDIDO=C5_NUM LEFT JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD LEFT JOIN SA2010 SA2 ON C5_TRANSP=A2_COD LEFT JOIN(SELECT ZVA_NUM,ZVA_OBSERV,ZVA_STATUS FROM ZVA010 WHERE D_E_L_E_T_='') AS ZVA ON C5_NUM=ZVA.ZVA_NUM INNER JOIN (SELECT SUM(C6_VALOR) AS 'valor',C6_NUM FROM SC6010 WHERE (C6_TES BETWEEN '501' AND '502' OR C6_TES='522') AND C6_BLQ='' AND D_E_L_E_T_='' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM WHERE ZZO_FEMBAR='' AND ZZO.D_E_L_E_T_='' AND SC5.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' ORDER BY ZZO_OBSEMB,CONVERT(DATETIME,C5_FYHRCYC),C5_EMISSAO,A1_EST,C5_TRANSP") or die("Error al obtener los datos de embarques");
	$sql=odbc_exec($conn,"SELECT 
						ZZO_OBSERV,ZZO_FEMBAR,valor,ISNULL(ZVA_NUM,'') AS 'postergado',ISNULL(ZVA_OBSERV,'') AS 'desZVA',ISNULL(ZVA_STATUS,'') AS 'statusZVA',
						C5_EMBCPED,C5_CONDPAG,C5_OBSPED,
						CASE WHEN DATEDIFF(hh,C5_FYHSURT,GETDATE())>0 
						THEN 1 
						ELSE 0 
						END AS 'pxV',A1_OBSEMB,C5_APFLETE,C5_OBSEMB,
						ZZO.R_E_C_N_O_ AS 'registro',C5_CLIENTE,C5_APALM,C5_RECCLIE,C5_NUM,C5_FYHSURT,C5_HORA,C5_VALMERC,C5_LOF,
						case when C5_CLIENTE = 'D00123' 
						then 9 
						else  
							CASE WHEN C5_USERVTA='Merkadotecnia' 
							THEN 8 
							ELSE 
								CASE WHEN C5_USER='atencion a cliente' 
								THEN 0 
								ELSE 
									CASE WHEN C5_FPROG>0 
									THEN 7 
									ELSE A1_TIPO 
									END 
								END 
							END 
						end AS 'tipo',
						CASE WHEN C5_DIREMB='' 
						THEN 'FISCAL' 
						ELSE C5_DIREMB 
						END AS 'C5_DIREMB',RTRIM(A1_END)+' | '+RTRIM(A1_MUN)+','+A1_EST AS 'dir',ZZO_FECFAC,ZZO_FACT,ZZO_OBSEMB,A1_COD,A1_NOME,A1_EST,A2_COD,A2_NOME,A2_NREDUZ,
						A1_BLOQFL 
					FROM ZZO010 ZZO 
						LEFT JOIN SC5010 SC5 ON ZZO_PEDIDO=C5_NUM 
						LEFT JOIN SA1010 SA1 ON C5_CLIENTE=A1_COD 
						LEFT JOIN SA2010 SA2 ON C5_TRANSP=A2_COD 
						LEFT JOIN(SELECT ZVA_NUM,ZVA_OBSERV,ZVA_STATUS FROM ZVA010 WHERE D_E_L_E_T_='') AS ZVA ON C5_NUM=ZVA.ZVA_NUM 
						INNER JOIN (SELECT SUM(C6_PRCVEN*C6_QTDENT) AS valor,C6_NUM FROM SC6010 WHERE (C6_TES BETWEEN '501' AND '502' OR C6_TES='522') AND D_E_L_E_T_='' AND C6_LOCAL='01' GROUP BY C6_NUM) AS SC6 ON C5_NUM=SC6.C6_NUM 
						WHERE ZZO_CODFLE='' AND ZZO.D_E_L_E_T_='' AND SC5.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' ".$cons." 
						ORDER BY ZZO_OBSEMB,CONVERT(DATETIME,C5_FYHRCYC),C5_EMISSAO,A1_EST,C5_TRANSP") or die("Error al obtener los datos de embarques");
										//SELECT SUM(C6_VALOR) AS 'valor',C6_NUM FROM SC6010 WHERE (C6_TES BETWEEN '501' AND '502' OR C6_TES='522') AND C6_BLQ='' AND D_E_L_E_T_='' GROUP BY C6_NUM
	while($datos=odbc_fetch_array($sql))
	{
		If(trim($datos['ZZO_FEMBAR'])=="")
		{
			$obsEmb="";
			if($datos["statusZVA"]==0){
				if($datos['pxV']==1)
					$obsEmb="<img src='images/cancel.png' />";
			}
			else
				$obsEmb="<img src='images/cal.png' title='Ped Postergado $datos[desZVA]'/>";
			$dirEmb="<select id='cmbDE".$datos['registro']."'>";
			$cont=odbc_exec($conn, "SELECT COUNT(*) AS 'cuantos' FROM ZZM010 WHERE ZZM_PEDIDO='".$datos['C5_NUM']."' AND D_E_L_E_T_=''") or die("Error al obtener los pedidos en backOrder");
			//$reg=odbc_fetch_array($cont);
			//if($reg['cuantos']>1)            //el 
			//	$color='bko';
			if($datos['tipo']==0)
				$color='aclaracion';
			elseif($datos['tipo']==7)
				$color='programados';
			elseif($datos['tipo']==1)
				$color='vip';
			elseif($datos['tipo']==2)
			{
				if($datos['C5_RECCLIE']=="SI")
					$color="rc";
				elseif($datos['C5_VALMERC']<="5000")
					$color='locm';
				else
					$color='local';
			}
			elseif($datos['tipo']==3)
				$color='foraneos';
			elseif($datos['tipo']==4)
				$color='exportacion';
			elseif($datos['tipo']==5)
				$color='especiales';
			elseif($datos['tipo']==6)
				$color='cita';
			elseif($datos['tipo']==8)
				$color='publicidad';
			elseif($datos['tipo']==9)
				$color='pe';
			
			odbc_free_result($cont);
	/*		if($datos['C5_APFLETE']=="T")
				$obsEmb.="&nbsp;&nbsp;<img src='images/good.png' title='Aprobado. Flete pagado'/>";*/
			if(trim($datos['A1_OBSEMB'])!='')
				$obsEmb.="&nbsp;&nbsp;<img src='images/bell.png' title='$datos[A1_OBSEMB]'/>";
			if(trim($datos['C5_OBSPED'])!='')
				$obsEmb.="&nbsp;&nbsp;<img src='images/gohome.png' title='$datos[C5_OBSPED]'/>";
				
			if(trim($datos['C5_EMBCPED'])<>"")
			{
				if(trim($datos['A1_COD'])=="D00123")
					$obsEmb.="&nbsp;<img src='images/LINK4.png' title='Surtir junto con pedido $datos[C5_EMBCPED]' />";
				else
					$obsEmb.="&nbsp;<img src='images/reload.png' title='Surtir junto con pedido $datos[C5_EMBCPED]' />";
			}
			if($datos["C5_CONDPAG"]=="000" && $datos["tipo"]<>"0" && trim(strtoupper($datos["A1_NOME"]))<>"VENTAS AL PUBLICO EN GENERAL" && $datos["tipo"]<>"4")
				$obsEmb.="&nbsp;<img src='images/money.ico' title='Cliente de contado'/>'";
			
			echo "<tr id='tr$datos[registro]' class=$color";
			if($datos['statusZVA']=="0" && $datos['postergado']<>"")
				echo " bgcolor='#D2D2D2'";
			elseif(trim($datos['ZZO_OBSEMB'])<>"" || trim($datos['C5_OBSEMB']))
				echo " bgcolor='#0066CC'";
			if(trim($datos['ZZO_OBSEMB'])<>"" || trim($datos['C5_OBSEMB']))
				$obsEmb.="&nbsp;&nbsp;<img src='images/note.png' title='Obs Ped: ".trim($datos['C5_OBSEMB'])."\n Obs fact: ".trim($datos['ZZO_OBSEMB'])."'>";
			//REVISA SI SE PUEDE CAMBIAR LA FLETERA Y CUAL ES LA FLETERA POR Defecto
			if(trim($datos['C5_DIREMB'])=="FISCAL")
			{
				$fletera_cod=$datos['A2_COD'];
				$fletera_nom=$datos['A2_NOME'];
				$fletera_nomr=$datos['A2_NREDUZ'];
				$fletera_blq=$datos['A1_BLOQFL'];
			}
			else{
				$conflet=odbc_exec($conn,"SELECT ZD1_FLETE,ZD1_BLOQFL,CASE WHEN A2_NOME IS NULL THEN '' ELSE A2_NOME END A2_NOME,CASE WHEN A2_NREDUZ IS NULL THEN '' ELSE A2_NREDUZ END A2_NREDUZ FROM ZD1010 ZD1 LEFT JOIN SA2010 A2 ON ZD1_FLETE=A2_COD AND A2.D_E_L_E_T_='' WHERE ZD1_CLAVE='$datos[C5_DIREMB]' AND ZD1_CLIENT='$datos[C5_CLIENTE]' AND ZD1.D_E_L_E_T_=''");			
				$flet=odbc_fetch_array($conflet);
				$fletera_cod=$flet['ZD1_FLETE'];
				$fletera_nom=$flet['A2_NOME'];
				$fletera_nomr=$flet['A2_NREDUZ'];
				$fletera_blq=$flet['ZD1_BLOQFL'];
				odbc_free_result($conflet);
			}
			//IF (TRIM($fletera_cod)!="" && TRIM($fletera_cod)!= NULL)
			//{
				//$fletera_cod="panfilo";
				//$fletera_nom="panfilo";
				//$fletera_nomr=$datos['registro'];
				//$fletera_blq=2;
				//odbc_exec($conn,"UPDATE ZZO010 SET ZZO_CODFLE='".$fletera_cod."' WHERE R_E_C_N_O_=".$datos['registro'].";")or die("Error Actualiza ZZO");
			//}
			$dat=odbc_exec($conn,"SELECT ZD1_CLAVE,LTRIM(ZD1_DIRECC)+' | '+RTRIM(ZD1_POBLAC) AS 'ZD1_DIRECC' FROM ZD1010 WHERE ZD1_CLIENT='".$datos['C5_CLIENTE']."' AND ZD1_CLAVE<>'".$datos['C5_DIREMB']."' AND D_E_L_E_T_='' ORDER BY ZD1_CLAVE")
			or die("Error al obtener las direcciones de embarque");
			while($dEmb=odbc_fetch_array($dat))
				$dirEmb.="<option value='".$dEmb['ZD1_CLAVE']."' title='".$dEmb['ZD1_DIRECC']."'>".$dEmb['ZD1_CLAVE']."</option>";
			odbc_free_result($dat);
			//------------------------------------------------------------------------------------------------------------------
			//---------------------------------- SI ES PEDIDO ESPECIAL D00123 ------------------------------------------------
			// CAMBIA EL ESTADO POR EL ESTADO DE LA DIRECCION DE ENTREGA
			$estado_impresion=$datos['A1_EST'];
			if(trim($datos['A1_COD'])=="D00123")
			{
				$separa=explode(",",$datos['C5_DIREMB']);
				//echo "<script>alert('"."SELECT ZD1_EDOCVE FROM ZD1010 WHERE ZD1_CLIENT='D00123' AND ZD1_CLAVE='".$datos['C5_DIREMB']."' AND D_E_L_E_T_='';"."'])</script>";
				$dato=odbc_exec($conn,"SELECT ZD1_EDOCVE FROM ZD1010 WHERE ZD1_CLIENT='D00123' AND ZD1_CLAVE='".$datos['C5_DIREMB']."' AND D_E_L_E_T_='';")
				or die("Error al obtener el estado del cliente PE");
				if($edoemb=odbc_fetch_array($dato))
					$estado_impresion=$edoemb['ZD1_EDOCVE'];
				odbc_free_result($dato);				
			}
			//------------------------------------------------------------------------------------------------------------------
			echo ">
					<td align='center'>
						<img src='images/edit.png' title='Agregar Observaciones a la Factura' onclick=javascript:window.open('guardaObsP.php?Ped=".$datos["C5_NUM"]."&tipo=emb&regis=".$datos['registro']."','_blank','resisable=no')>
						".$obsEmb."&nbsp;&nbsp;$datos[C5_NUM]
					</td>
					<td title='$datos[A1_COD]' style='cursor: pointer;'><input type='hidden' id='txtCli".$datos['registro']."' value='$datos[C5_CLIENTE]'>".substr(trim($datos['A1_NOME']),0,20)."</td>
					<td align='center'>$datos[ZZO_FECFAC]</td>
					<td align='center'>$datos[C5_FYHSURT]</td>
					<td align='center'><a class='fxe' id='$datos[registro]' title='Embarca e imprime el sobre'>$datos[ZZO_FACT]</a></td>
					<!--<td align='center'>$datos[A1_EST]</td>-->
					<td align='center'>$estado_impresion</td>
					<td><input type='text' id='txtObs".$datos['registro']."' maxlength='50' width='20' value='".trim($datos['ZZO_OBSERV'])."'></td>
					<td>
						<select id='cmbC".$datos['registro']."' title='$ ".number_format($datos['valor'],2,".",",")."'>
							<option value='Al Regreso' ";
								if($datos['C5_APFLETE']=="T" || $datos['valor']>15000 || $color=="bko" || $datos['tipo']==0)
									echo "selected>Al Regreso</option><option value='Por Cobrar'>";
								else
									echo ">Al Regreso</option><option value='Por Cobrar' selected>";
								echo "Por Cobrar</option>
						</select>
					</td>
					<td><select id='cmbF".$datos['registro']."' class='fletera' title='".$fletera_blq."'><option value='".$fletera_cod."' title='".$fletera_nom."' selected>".substr(trim($fletera_nomr),0,15)."</option>$fleteras</select></td>
					<td><input type='text' id='xDefecto".$datos['registro']."' maxlength='30' size='10' disabled value='".substr(trim($fletera_nomr),0,15)."' title='".$fletera_cod."|".$fletera_nom."'></td>
					<td>";
			$dirEmb.="<option value='".$datos['C5_DIREMB']."' selected title='";
			if(trim($datos['C5_DIREMB'])=="FISCAL")
				$dirEmb.=$datos["dir"];
			else{
				$dE=odbc_exec($conn,"SELECT LTRIM(ZD1_CLAVE),RTRIM(ZD1_DIRECC)+' | '+ZD1_POBLAC+', '+ZD1_EDO AS 'dir' FROM ZD1010 WHERE ZD1_CLAVE='$datos[C5_DIREMB]' AND ZD1_CLIENT='$datos[C5_CLIENTE]' AND D_E_L_E_T_=''");
				$dat1=odbc_fetch_array($dE);
				$dirEmb.=$dat1["dir"];
				odbc_free_result($dE);
			}
			$dirEmb.="'>$datos[C5_DIREMB]</option>";		
			if(trim($datos['C5_DIREMB'])<>"FISCAL")
				$dirEmb.="<option value='FISCAL' title='$datos[dir]'>FISCAL</option>";
			$dat=odbc_exec($conn,"SELECT ZD1_CLAVE,LTRIM(ZD1_DIRECC)+' | '+RTRIM(ZD1_POBLAC) AS 'ZD1_DIRECC' FROM ZD1010 WHERE ZD1_CLIENT='".$datos['C5_CLIENTE']."' AND ZD1_CLAVE<>'".$datos['C5_DIREMB']."' AND D_E_L_E_T_='' ORDER BY ZD1_CLAVE")
			or die("Error al obtener las direcciones de embarque");
			while($dEmb=odbc_fetch_array($dat))
				$dirEmb.="<option value='".$dEmb['ZD1_CLAVE']."' title='".$dEmb['ZD1_DIRECC']."'>".$dEmb['ZD1_CLAVE']."</option>";
			odbc_free_result($dat);
			$dirEmb.="</select>";
			echo $dirEmb."</td></tr>";
	/*		  if($color=="#FFFFFF")
					$color="#C0C0C0";
			  else
					$color="#FFFFFF";*/
		}
	}
	odbc_free_result($sql);
	odbc_close($conn);
?>
</table>
</form>