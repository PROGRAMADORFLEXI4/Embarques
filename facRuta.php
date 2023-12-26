<form name="embarques" id="embarques">
<table>
	<tr>
    	<th align="center"><u><h1>Facturas Pendientes de Ruta</h1></u></th>
        <th><input type="button" value="Otras Rutas" id="otraRuta">&nbsp;&nbsp;<input type="button" id="asChof" Value="Guardar"></th>
	</tr>
</table>
<?php
	include("conectabd.php");
	//Choferes
	$chofer="<option value='' select></option>";
	$sql=odbc_exec($conn, "SELECT ZZN_CODIGO,ZZN_NOMBRE FROM ZZN010 WHERE ZZN_TIPO='1' AND D_E_L_E_T_='' ORDER BY ZZN_NOMBRE")
	or die("Error al obtener el catalogo de chofer");
	while($datos=odbc_fetch_array($sql))
		$chofer.="<option value='".$datos['ZZN_CODIGO']."'>".trim($datos['ZZN_NOMBRE'])."</option>";
	odbc_free_result($sql);
	odbc_exec($conn,"SET LANGUAGE 'Spanish';");		
	//$sql=odbc_exec($conn, "SELECT CASE WHEN DATEDIFF(hh,C5_FYHSURT,GETDATE())>0 THEN 1 ELSE 0 END AS 'pxV',C5_NUM,ZZO.R_E_C_N_O_ AS 'registro',ZZO_FACT,A1_NOME,A1_TIPO,C5_APALM,C5_RECCLIE,C5_FYHRCYC,C5_FYHSURT,CASE WHEN C5_FPROG>0 THEN 'Prog' ELSE C5_USER END AS 'tipo',ZZO_FECFAC,ZZO_FEMBAR,ISNULL(A2_COD,'LOCAL') AS A2_COD,ISNULL(A2_NOME,'LOCAL') AS A2_NOME,ISNULL(A2_NREDUZ,'LOCAL') AS A2_NREDUZ FROM ZZO010 ZZO INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD LEFT JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD LEFT JOIN SC5010 SC5 ON ZZO_PEDIDO=C5_NUM WHERE ZZO_CODFLE<>'EXPORT' AND ZZO_CODFLE<>'ACLARA' AND A1_COD<>'V00018' AND ZZO_FEMBAR<>'' AND ZZO_CHOFER='' AND ZZO_FECHEN='' AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' AND SC5.D_E_L_E_T_='' ORDER BY CONVERT(DATETIME,ZZO_FEMBAR)")
	$sql=odbc_exec($conn, "SELECT CASE WHEN DATEDIFF(hh,C5_FYHSURT,GETDATE())>0 THEN 1 ELSE 0 END AS 'pxV',C5_NUM,ZZO.R_E_C_N_O_ AS 'registro',ZZO_FACT,A1_NOME,A1_TIPO,C5_APALM,C5_RECCLIE,C5_FYHRCYC,C5_FYHSURT,CASE WHEN C5_FPROG>0 THEN 'Prog' ELSE C5_USER END AS 'tipo',ZZO_FECFAC,ZZO_FEMBAR,ISNULL(A2_COD,'LOCAL') AS A2_COD,ISNULL(A2_NOME,'LOCAL') AS A2_NOME,ZZO_CODFLE,ISNULL(A2_NREDUZ,'LOCAL') AS A2_NREDUZ FROM ZZO010 ZZO INNER JOIN SA1010 SA1 ON ZZO_CLTE=A1_COD LEFT JOIN SA2010 SA2 ON ZZO_CODFLE=A2_COD LEFT JOIN SC5010 SC5 ON ZZO_PEDIDO=C5_NUM WHERE ZZO_CODFLE<>'EXPORT' AND ZZO_CODFLE<>'ACLARA' AND A1_COD<>'V00018' AND ZZO_CODFLE<>'' AND ZZO_CHOFER='' AND ZZO_FECHEN='' AND ZZO.D_E_L_E_T_='' AND SA1.D_E_L_E_T_='' AND SC5.D_E_L_E_T_='' ORDER BY CONVERT(DATETIME,ZZO_FEMBAR)")
		or die("Error al obtener las facturas por distribuir");
		if(odbc_num_rows($sql)>0)
		{
			echo "<table><tr><th>Factura</th><th>Cliente</th><th>Fecha Factura</th><th>Fecha Embarque</th><th>Fecha Lim Surt</th><th>Fletera</th><th>Zona</th><th>Chofer</th><th>Auxiliar</th></tr>";
			while($datos=odbc_fetch_array($sql))
			{
				$cont=odbc_exec($conn, "SELECT COUNT(*) AS 'cuantos' FROM ZZM010 WHERE ZZM_PEDIDO='".$datos['C5_NUM']."' AND D_E_L_E_T_=''") or die("Error al obtener los pedidos en backOrder");
				$reg=odbc_fetch_array($cont);
				if($reg['cuantos']>1)
					$color='bko';
				elseif(strtolower(trim($datos['tipo']))=='atencion a cliente')
					$color='aclaracion';
				elseif(trim($datos['tipo'])=='Prog')
					$color='programados';
				elseif($datos['A1_TIPO']==1)
					$color='vip';
				elseif($datos['A1_TIPO']==2)
				{
					if($datos['C5_RECCLIE']=="SI")
						$color="rc";
					else					
						$color='local';
				}
				elseif($datos['A1_TIPO']==3)
					$color='foraneos';
				elseif($datos['A1_TIPO']==4)
					$color='exportacion';
				elseif($datos['A1_TIPO']==5)
					$color='especiales';
				elseif($datos['A1_TIPO']==6)
					$color='cita';
				odbc_free_result($cont);
				
				$cont=odbc_exec($conn, "SELECT Z14_NREDUZ,Z14_DESC FROM Z15010 Z15 INNER JOIN Z14010 Z14 ON Z14_COD=Z15_CODZON WHERE Z15_CODFLE='".$datos['ZZO_CODFLE']."' AND Z15.D_E_L_E_T_=''") or die("Error al obtener los pedidos en backOrder");
				if($reg=odbc_fetch_array($cont))
				{
					$zona=$reg['Z14_NREDUZ'];
					$zonadesc=$reg['Z14_DESC'];
				}
				else
				{
					$zona="";
					$zonadesc="";
				}
				odbc_free_result($cont);
				
				echo "<tr class='$color' id='tr$datos[registro]'>
						<td>";
						if($datos['pxV']==1)
							echo "<img src='images/cancel.png' /> &nbsp;";
				  echo "<a class='$color' id='cFacxEmb' name='$datos[registro]' title='Mover a Facturas por Embarcar'>".$datos['ZZO_FACT']."</a></td>
						<td>".$datos['A1_NOME']."</td>
						<td align='center'>".$datos['ZZO_FECFAC']."</td>
						<td align='center'>".$datos['ZZO_FEMBAR']."</td>
						<td align='center'>".$datos['C5_FYHSURT']."</td>
						<td title='".$datos['A2_NOME']."'>".substr(trim($datos['A2_NREDUZ']),0,15)."</td>
						<td title='".$zonadesc."'>".$zona."</td>
						<td align='center'><select name='cmbChof".$datos['registro']."'>".$chofer."</select></td>
						<td align='center'><select name='cmbAux".$datos['registro']."'>".$chofer."</select></td>
					  </tr>";
			}
		}
		echo "</table><br>";
		odbc_free_result($sql);
		odbc_close($conn);
?>
	</form>