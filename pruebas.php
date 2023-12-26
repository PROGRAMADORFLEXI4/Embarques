<?php
	include("conectabd.php");
	
	$registros=odbc_exec($conn,"SELECT * FROM 
											(select c5_num from sc5010 c5 where c5.d_e_l_e_t_='' and c5_emissao>='20170701' and c5_fecesll='') SC5010
											INNER JOIN (SELECT ZZO_PEDIDO,ZZO_FECHEN,ZZO_FACT FROM ZZO010 ZZO WHERE ZZO.D_E_L_E_T_='' AND ZZO_FECHEN<>'' AND ZZO_PEDIDO>='061312') ZZO010
											ON ZZO_PEDIDO=C5_NUM") or die("Problemas en el select");
	while($reg=odbc_fetch_array($registros))
	{
		echo "<br>".$reg['c5_num']." - ".$reg['ZZO_FACT']." - ";
		$CODIGOFACTURA=$reg['ZZO_FACT'];
		//AQUI VA EL CODIGO PARA GENERAR LA FECHA POSIBLE DE ENTREGA
		//SELECT C5_DIREMB FROM SC6010 C6 INNER JOIN SC5010 C5 ON C5_NUM=C6_NUM WHERE C6_NOTA='00000000000000109695' GROUP BY C5_DIREMB
		//OBTIENE EL NUMERO DE FACTURA Y FLETERA
		$sql=odbc_exec($conn,"SELECT ZZO_FACT,ZZO_CODFLE FROM ZZO010 WHERE ZZO_FACT='$CODIGOFACTURA';") or die("Error al obtener factura");
		$datos=odbc_fetch_array($sql);
		$num_fac=$datos["ZZO_FACT"];
		$cod_fle=$datos["ZZO_CODFLE"];
		odbc_free_result($sql);
		//OBTIENE DIR EMBARQUE
		$sql=odbc_exec($conn,"SELECT C5_NUM,C5_DIREMB FROM SC6010 C6 INNER JOIN SC5010 C5 ON C5_NUM=C6_NUM WHERE C6_NOTA='".$num_fac."' GROUP BY C5_NUM,C5_DIREMB") or die("Error al obtener DIR EMBARQUE");
		$datos=odbc_fetch_array($sql);
		$dir_emb=trim($datos["C5_DIREMB"]);
		$num_ped=$datos["C5_NUM"];
		odbc_free_result($sql);
		//OBTIENE EL CLIENTE
		$sql=odbc_exec($conn,"SELECT F2_CLIENTE FROM SF2010 WHERE F2_DOC='".$num_fac."' AND D_E_L_E_T_='';") or die("Error al obtener DIR EMBARQUE");
		$datos=odbc_fetch_array($sql);
		$cliente_fac=trim($datos["F2_CLIENTE"]);
		odbc_free_result($sql);
		if($dir_emb!="" && $dir_emb!="FISCAL")
		{
			//BUSCAR LA DIR DE ENTREGA PARA OBTENER EL ESTADO
			$sql=odbc_exec($conn,"SELECT ZD1_EDOCVE FROM ZD1010 WHERE ZD1_CLAVE='".$dir_emb."' AND ZD1_CLIENT='".$cliente_fac."' AND D_E_L_E_T_='' GROUP BY ZD1_EDOCVE;") or die("Error al obtener DIR EMBARQUE");
			$datos=odbc_fetch_array($sql);
			$estado_cli=trim($datos["ZD1_EDOCVE"]);
			odbc_free_result($sql);
		}
		Else
		{
			//obtiene el estado fiscal del cliente
			$sql=odbc_exec($conn,"SELECT A1_EST FROM SA1010 WHERE A1_COD='".$cliente_fac."' AND D_E_L_E_T_='';") or die("Error al obtener DIR EMBARQUE");
			echo "SELECT A1_EST FROM SA1010 WHERE A1_COD='".$cliente_fac."' AND D_E_L_E_T_=''; - ";
			$datos=odbc_fetch_array($sql);
			$estado_cli=trim($datos["A1_EST"]);
			odbc_free_result($sql);
		}
		//obtiene los dias segun el estado y la fletera
		//$sql=odbc_exec($conn,"SELECT MAX(DDF_DIAS) DDF_DIAS FROM DDF010 WHERE D_E_L_E_T_='' AND DDF_COD='".$cod_fle."' AND DDF_EDO='".$estado_cli."'");
		//POR EL MOMENTO SOLO FILTRA POR ESTADO
		echo " SELECT CASE WHEN MAX(DDF_DIAS) IS NULL THEN 0 ELSE MAX(DDF_DIAS) END DDF_DIAS FROM DDF010 WHERE D_E_L_E_T_='' AND DDF_EDO='".$estado_cli."';";
		$sql=odbc_exec($conn,"SELECT CASE WHEN MAX(DDF_DIAS) IS NULL THEN 0 ELSE MAX(DDF_DIAS) END DDF_DIAS FROM DDF010 WHERE D_E_L_E_T_='' AND DDF_EDO='".$estado_cli."';");
		if($datos=odbc_fetch_array($sql))
		{
			$num_dias=$datos["DDF_DIAS"];
			$num_dias=round($num_dias);
			if($num_dias>0)
			{
				//POR EL MOMENTO SOLO SI ENCUENTRA ESTE PARAMETRO ACTUALIZA LA FECHA
				//sumar dias a hoy para sacar la fecha de posible entrega
				$fpe=strtotime(date("d-M-Y"));
				for ($r=1 ;$r<=$num_dias;$r++)
				{
					$fpe=strtotime ( '+1 day' , $fpe);
					if(date('N',$fpe)==6)
						$fpe=strtotime ( '+2 day' , $fpe);
					elseif(date('N',$fpe)==7)
						$fpe=strtotime ( '+1 day' , $fpe);
				}
				$fpe=date("Ymd", $fpe);
				//$fpe=date("Ymd", strtotime ( '+'.$num_dias.' day' , strtotime(date("d-M-Y"))));
				//ACTUALIZAR LA FECHA EN PROTHEUS
				odbc_exec($conn,"UPDATE SC5010 SET C5_FECESLL='".$fpe."'  WHERE C5_NUM='".$num_ped."' AND D_E_L_E_T_='';") or die("Problemas en el select");
				echo "UPDATE SC5010 SET C5_FECESLL='".$fpe."'  WHERE C5_NUM='".$num_ped."' AND D_E_L_E_T_='';";
			}
		}
		else
			$num_dias=0;
		odbc_free_result($sql);
	}
	odbc_free_result($registros);
	
	
	
?>