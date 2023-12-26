 <center><h1><u>Facturas en Ruta</u></h1></center>
 
<?php
	include("conectabd.php");
/*	$sql=odbc_exec($conn,"SELECT ZZO.R_E_C_N_O_ AS 'registro',ZZN_CODIGO,ZZN_NOMBRE,ZZO_FACT,A1_NOME,ZZO_FEMBAR,ISNULL(A2_NOME,'LOCAL') AS A2_NOME FROM ZZO010 ZZO INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD LEFT JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD INNER JOIN ZZN010 ZZN ON ZZO_CHOFER=ZZN_CODIGO LEFT JOIN ZZM010 ZZM ON ZZO_FACT=ZZM_FATURA WHERE ZZO_CHOFER<>'' AND ZZO_FECHEN='' AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' AND ZZN.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_='' ORDER BY ZZO_CHOFER,ZZO_CONSEC") or die("Error al obtener el listado de facturas por chofer");*/
	//$sql=odbc_exec($conn,"SELECT ZZO.R_E_C_N_O_ AS 'registro',ZZN_CODIGO,ZZN_NOMBRE,ZZO_FACT,A1_NOME,ZZO_FEMBAR,ISNULL(A2_NOME,'LOCAL') AS A2_NOME FROM ZZO010 ZZO INNER JOIN ZZM010 ZZM ON ZZO_FACT=ZZM_FATURA LEFT JOIN ZZN010 ZZN ON ZZO_CHOFER=ZZN_CODIGO INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD LEFT JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD WHERE ZZO_CHOFER<>'' AND ZZO_FECHEN='' AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_='' AND ZZN.D_E_L_E_T_='' ORDER BY ZZO_CHOFER,ZZO_CONSEC") or die("Error al obtener el listado de facturas por chofer");
	//$textosql="SELECT ZZO.R_E_C_N_O_ AS 'registro',ZZN_CODIGO,ZZN_NOMBRE,ZZO_FACT,A1_NOME,ZZO_FEMBAR,ISNULL(A2_NOME,'LOCAL') AS A2_NOME,ZZO_GUIA,ZZO_VALORG,ZZO_OBSERV,ZZO_FECENT,ZZO_FECHEN FROM ZZO010 ZZO INNER JOIN ZZM010 ZZM ON ZZO_FACT=ZZM_FATURA LEFT JOIN ZZN010 ZZN ON ZZO_CHOFER=ZZN_CODIGO INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD LEFT JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD WHERE ZZO_CHOFER<>'' AND (ZZO_FECHEN='') AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' AND ZZM.D_E_L_E_T_='' AND ZZN.D_E_L_E_T_='' ORDER BY ZZO_CHOFER,ZZO_CONSEC"; //20190123
	$textosql="SELECT ZZO.R_E_C_N_O_ AS 'registro',ZZN_CODIGO,ZZN_NOMBRE,ZZO_FACT,A1_NOME,ZZO_FEMBAR,ISNULL(A2_NOME,'LOCAL') AS A2_NOME,ZZO_GUIA,ZZO_VALORG,ZZO_OBSERV,ZZO_FECENT,ZZO_FECHEN FROM ZZO010 ZZO LEFT JOIN ZZM010 ZZM ON ZZO_FACT=ZZM_FATURA AND ZZM.D_E_L_E_T_='' LEFT JOIN ZZN010 ZZN ON ZZO_CHOFER=ZZN_CODIGO INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD LEFT JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD WHERE ZZO_CHOFER<>'' AND (ZZO_FECHEN='') AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' AND ZZN.D_E_L_E_T_='' AND (NOT ZZM.R_E_C_N_O_ IS NULL OR (ZZM.R_E_C_N_O_ IS NULL AND ZZO_PEDIDO='000000')) ORDER BY ZZO_CHOFER,ZZO_CONSEC"; //20190123
	//echo $textosql;
	$sql=odbc_exec($conn,$textosql) or die("Error al obtener el listado de facturas por chofer - ".$textosql);
	$chof="";
	while($datos=odbc_fetch_array($sql)){
		if($chof<>$datos['ZZN_NOMBRE'])
		{
			$chof=$datos['ZZN_NOMBRE'];
			if($chof<>"")
				echo "</ul>";
			echo "<ul><a class='impRuta' id='$datos[ZZN_CODIGO]' name=$chof>$chof</a>";
		}
		//echo "<li class='ruta' title='Cancela Factura de ruta y Re asigna chofer' id='li$datos[registro]'><a id='qRuta' class='$datos[registro]'>".$datos['ZZO_FACT']."</a>&nbsp;|&nbsp;".$datos['ZZO_FEMBAR']."&nbsp;|&nbsp;".substr($datos['A1_NOME'],0,35)."&nbsp;|&nbsp;".trim($datos['A2_NOME'])."</li>";		
		$fecha=trim($datos['ZZO_FECHEN']);
		if (strlen($fecha)==19)
		{
			$fecha=substr($fecha,6,2)."/".substr($fecha,4,2)."/".substr($fecha,0,4);
		}
		elseif(strlen($fecha)==17)
		{
			$fecha=substr($fecha,5,2)."/".substr($fecha,3,2)."/".substr($fecha,1,2);
		}
		else
		{$fecha="";}
		$valorguia="";
		if($datos['ZZO_VALORG']>0)
		{$valorguia=number_format($datos['ZZO_VALORG'],2,".",",");}
		else
		{$valorguia="";}
		echo "<li class='ruta' title='Cancela Factura de ruta y Re asigna chofer' id='li$datos[registro]'>
			<img src='images/truck3.png' title='Cambio de Chofer' class='cf' id='cf$datos[registro]' style='cursor:pointer;'/>
			<a id='qRuta' class='$datos[registro]'>".$datos['ZZO_FACT']."</a>&nbsp;|
			&nbsp;".$datos['ZZO_FEMBAR']."&nbsp;|
			&nbsp;".substr($datos['A1_NOME'],0,35)."&nbsp;|
			&nbsp;".trim($datos['A2_NOME'])."&nbsp;|
			&nbsp;".trim($datos['ZZO_GUIA'])."&nbsp;|
			&nbsp;".trim($datos['ZZO_FECHEN'])."&nbsp;|
			&nbsp;".$valorguia."&nbsp;|
			&nbsp;".trim($datos['ZZO_OBSERV'])."</li>";
	}
	//Muestra los registros de otras rutas
	odbc_free_result($sql);
	$sql=odbc_exec($conn,"SELECT e.R_E_C_N_O_ AS 'reg',e.observaciones,ZZN_CODIGO,ZZN.ZZN_NOMBRE,e.depto,e.descrip FROM embRec e LEFT JOIN ZZN010 ZZN ON e.chofer=ZZN.ZZN_CODIGO WHERE e.estatus=0 AND ZZN.D_E_L_E_T_='' ORDER BY ZZN_CODIGO")
	or die("Error al obtener los registros de otras rutas");
	if(odbc_num_rows($sql)>0)
	{
		echo "<h2><u><center>Otras Rutas</center></u></h2>";
		$chof="";
		while($datos=odbc_fetch_array($sql))
		{
			if($chof<>$datos['ZZN_NOMBRE'])
			{
				if($chof<>"")
					echo "</ul>";
				$chof=$datos['ZZN_NOMBRE'];
				echo "<ul><a class='impRuta' id='$datos[ZZN_CODIGO]' name='$chof'>$chof</a>";
			}
			echo "<li class='ruta' id='liR$datos[reg]' title='Finaliza el pendiente de otra ruta'><img src='images/cancel.png' class='eOR' id='$datos[reg]'/>&nbsp;&nbsp;&nbsp;<a class='eOR' id='datos[reg]'>".$datos['depto']."</a>&nbsp;&nbsp;|&nbsp;&nbsp;".$datos['descrip']."&nbsp;&nbsp;";
			if($datos['observaciones']<>'')
				echo "[".$datos['observaciones']."]";
			echo "</li>";
		}
	}
	echo "</ul>";
	odbc_free_result($sql);
	
	//muestra las facturas entregadas hoy
	$textosql="SELECT ZZO.R_E_C_N_O_ AS 'registro',ZZN_CODIGO,ZZN_NOMBRE,ZZO_FACT,A1_NOME,ZZO_FEMBAR,ISNULL(A2_NOME,'LOCAL') AS A2_NOME,ZZO_GUIA,ZZO_VALORG,ZZO_OBSERV,ZZO_FECENT,ZZO_FECHEN FROM ZZO010 ZZO LEFT JOIN ZZM010 ZZM ON ZZO_FACT=ZZM_FATURA AND ZZM.D_E_L_E_T_='' and zzm_audito<>'' LEFT JOIN ZZN010 ZZN ON ZZO_CHOFER=ZZN_CODIGO INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD LEFT JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD WHERE ZZO_CHOFER<>'' AND (SUBSTRING(ZZO_FECHEN,1,10) ='".date("d/m/Y")."' OR SUBSTRING(ZZO_FECHEN,1,9) ='".date("d/m/y")." ' OR (ZZO_GUIA = 'PENDIENTE' AND ZZO_FACT>='00000000000000107115')) AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' AND ZZN.D_E_L_E_T_='' ORDER BY ZZO_CHOFER,ZZO_CONSEC";
	$sql=odbc_exec($conn,$textosql) or die("Error al obtener el listado de facturas entregadas hoy - ".$textosql)
		or die("Error al obtener los registros de otras rutas");
	if(odbc_num_rows($sql)>0)
	{
		echo "<h2><u><center>Facturas Entregadas</center></u></h2>";
		$chof="";
		while($datos=odbc_fetch_array($sql))
		{
			if($chof<>$datos['ZZN_NOMBRE'])
			{
				if($chof<>"")
					echo "</ul>";
				$chof=$datos['ZZN_NOMBRE'];
				echo "<ul><a class='' id='' name='$chof'>$chof</a>";
			}
			$fecha=trim($datos['ZZO_FECHEN']);
			if (strlen($fecha)==19)
			{
				$fecha=substr($fecha,6,2)."/".substr($fecha,4,2)."/".substr($fecha,0,4);
			}
			elseif(strlen($fecha)==17)
			{
				$fecha=substr($fecha,5,2)."/".substr($fecha,3,2)."/".substr($fecha,1,2);
			}
			else
			{$fecha="";}
			$valorguia="";
			if($datos['ZZO_VALORG']>0)
			{$valorguia=number_format($datos['ZZO_VALORG'],2,".",",");}
			else
			{$valorguia="";}
			echo "<li class='' title='' id=''>".$datos['ZZO_FACT']."-&nbsp;|
				&nbsp;".$datos['ZZO_FEMBAR']."&nbsp;|
				&nbsp;".substr($datos['A1_NOME'],0,35)."&nbsp;|
				&nbsp;".trim($datos['A2_NOME'])."&nbsp;|
				&nbsp;".trim($datos['ZZO_GUIA'])."&nbsp;|
				&nbsp;".trim($datos['ZZO_FECHEN'])."&nbsp;|
				&nbsp;".$valorguia."&nbsp;|
				&nbsp;".trim($datos['ZZO_OBSERV'])."</li>";
		}
	}
	else
	{
		echo odbc_num_rows($sql);
	}
	odbc_free_result($sql);
	echo "</ul>
	<div id='cambio' style='display:none;z-index=1001;position:absolute;height:120px;width:250px;top:50px;left:50px;border:solid 1px white;background-color:#3F5372;' >
		<p style='font-size:12px;'>&nbsp;&nbsp;Confirma el nuevo chofer</p>
		&nbsp;&nbsp;
		<select name='selChof' style='width:200px;' id='selchof'>";
		$sql=odbc_exec($conn,"SELECT ZZN_CODIGO,ZZN_NOMBRE FROM ZZN010 WHERE ZZN_TIPO=1 AND D_E_L_E_T_=''") or die("Error al obtener las observaciones del pedido de embarques");
		while($datos=odbc_fetch_array($sql))
			echo "<option value='".$datos['ZZN_CODIGO']."'>".$datos['ZZN_NOMBRE']."</option>";
		echo "
		</select>
		<br>
		<br>
		&nbsp;&nbsp;&nbsp;<input type='button' value='Cancelar' id='canchof'>&nbsp;&nbsp;<input type='button' value='Aceptar' id='acechof'>
	</div>
	<input type='hidden' value='' id='txtidfac'>";
		odbc_free_result($sql);
	
	
	
	odbc_close($conn);
?>