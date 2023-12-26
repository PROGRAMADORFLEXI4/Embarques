<?PHP
	include("conectabd.php");
	//Si qe es 1, actualiza la Cantidad de embalaje...
	if(isset($_GET['qe']) && trim($_GET['qe'])==1){
		odbc_exec($conn, "UPDATE tempo SET Qe='$_GET[cant]' WHERE Id='$_GET[cod]' AND Pedido='".base64_decode($_GET["Ped"])."'") or die("Error al actualiza la QE (SB1)");
	}else if(isset($_GET['qe']) && trim($_GET['qe'])==2){
		odbc_exec($conn, "UPDATE tempo SET Empaque='".$_GET['cant']."' WHERE Id='".$_GET['cod']."' AND Pedido='".base64_decode($_GET["Ped"])."'") or die("Error al actualizar el tipo d empaque (SB1");
	}else{
		/*$exc=odbc_exec($conn,"SELECT ZZM_FECSUR FROM ZZM010 WHERE ZZM_PEDIDO='".base64_decode($_GET["Ped"])."' AND D_E_L_E_T_='' ORDER BY ZZM_FECSUR") or die("Error al validar la salida");*/
		$sql_inicio_rev = "SELECT ZZM_FECSUR FROM ZZM010 WHERE ZZM_ORDSUR='".base64_decode($_GET["ordsur"])."' AND D_E_L_E_T_='' ORDER BY ZZM_FECSUR";
		$exc=odbc_exec($conn, $sql_inicio_rev) or die("Error al validar la salida");
		$datos=odbc_fetch_array($exc);
		if(trim($datos["ZZM_FECSUR"])!=""){
			odbc_free_result($exc);
			odbc_close($conn);
			echo "<script languaje='JavaScript'>
					console.log(\"".$sql_inicio_rev."\");
					alert('No es posible modificar la salida ya que ha sido auditada');
				  </script>";
			exit;
		}
		odbc_free_result($exc);	
		//Nuevo Registro tempo
		if($_GET["opc"]=="N"){
			//Valida si la salida ya esta capturada
			/*$sql=odbc_exec($conn,"SELECT ZZS_SALIDA FROM ZZS010 WHERE ZZS_PEDIDO='".base64_decode($_GET["Ped"])."' AND ZZS_FAC2='F' AND D_E_L_E_T_=''")or die("Error al buscar la salida");*/
			$sql=odbc_exec($conn,"SELECT ZZS_SALIDA FROM ZZS010 WHERE ZZS_ORDSUR='".base64_decode($_GET["ordsur"])."' AND ZZS_FAC2='F' AND D_E_L_E_T_=''")or die("Error al buscar la salida");

			if(odbc_num_rows($sql)>0){
				odbc_free_result($sql);
				echo "<script languaje='JavaScript'>
						if(!confirm('Ya existe una salida registrada para el pedido: ".base64_decode($_GET["Ped"]).". Desea modificarla?'))
							location.href='index.php';
					  </script>";
			}
			odbc_exec($conn,"DELETE FROM tempo WHERE Pedido='".base64_decode($_GET["Ped"])."'")
			or die("Error al ejecutar la consulta");
			$exce=odbc_exec($conn,"
				SELECT 
					SUM(C9_QTDLIB) AS solicitado,
					C6_PRODUTO,C6_PEDIM,
					B1_QE,B1_BOLSA 
				FROM SC6010 SC6 
					LEFT JOIN SB1010 SB1 ON C6_PRODUTO=B1_COD 
					LEFT JOIN SC9010 SC9 ON C6_NUM=C9_PEDIDO 
				WHERE 
					C6_NUM='".base64_decode($_GET["Ped"])."' AND C6_PRODUTO=C9_PRODUTO AND C6_ITEM=C9_ITEM AND C9_BLCRED='' AND 
					C6_QTDVEN-C6_QTDENT>0 AND C6_TES IN ('501', '502', '507', '508', '522', '523', '535') AND 
					SC6.D_E_L_E_T_='' AND SB1.D_E_L_E_T_='' AND SC9.D_E_L_E_T_='' 
				GROUP BY 
					C6_PRODUTO,C6_PEDIM,B1_QE,B1_BOLSA 
				ORDER BY 
					C6_PRODUTO,B1_QE")or die("Error al realizar actualización tabla TEMPO de las partidas del pedido");
			//$exce=odbc_exec($conn,"SELECT SUM(C6_QTDVEN-C6_QTDENT) AS solicitado,C6_PRODUTO,C6_PEDIM,B1_QE,B1_BOLSA FROM SC6010 SC6 LEFT JOIN SB1010 SB1 ON C6_PRODUTO=B1_COD LEFT JOIN SC9010 SC9 ON C6_NUM=C9_PEDIDO WHERE C6_NUM='".$_GET["Ped"]."' AND C6_PRODUTO=C9_PRODUTO AND C6_ITEM=C9_ITEM AND C9_BLCRED='' AND C6_QTDVEN-C6_QTDENT>0 AND SC6.D_E_L_E_T_='' AND SB1.D_E_L_E_T_='' AND SC9.D_E_L_E_T_='' GROUP BY C6_PRODUTO,C6_PEDIM,B1_QE,B1_BOLSA ORDER BY C6_PRODUTO,B1_QE")or die("Error al realizar actualizacion tabla TEMPO de las partidas del pedido");
			$id=1;
			$cant=0;
			while($datos=odbc_fetch_array($exce)){
				if(($datos['B1_QE']>0) && ($datos['solicitado']>$datos['B1_QE'])){
					if(intval($datos['solicitado']/$datos['B1_QE'])>0){
						$cant=intval($datos['solicitado']/$datos['B1_QE'])*$datos['B1_QE'];
						odbc_exec($conn,"INSERT INTO tempo(id,Pedido,Producto,Cant,Pedimento,Qe,Empaque) VALUES(".$id.",'".base64_decode($_GET["Ped"])."','".trim($datos["C6_PRODUTO"])."',".$cant.",'".trim($datos["C6_PEDIM"])."',".intval($datos["B1_QE"]).",'".$datos["B1_BOLSA"]."')")or die("Error al insertar partida TEMPO");
					}if($datos['solicitado']%$datos['B1_QE']>0){
						$id++;						
						odbc_exec($conn,"INSERT INTO tempo(id,Pedido,Producto,Cant,Pedimento,Qe,Empaque) VALUES(".$id.",'".base64_decode($_GET["Ped"])."','".trim($datos["C6_PRODUTO"])."',".intval($datos['solicitado']-$cant).",'".trim($datos["C6_PEDIM"])."',".intval($datos["B1_QE"]).",'".$datos["B1_BOLSA"]."')")
						or die("Error al insertar partida TEMPO");
					}
				}else{
					odbc_exec($conn,"INSERT INTO tempo(id,Pedido,Producto,Cant,Pedimento,Qe,Empaque) VALUES(".$id.",'".base64_decode($_GET["Ped"])."','".trim($datos["C6_PRODUTO"])."',".round($datos["solicitado"]).",'".trim($datos["C6_PEDIM"])."',".intval($datos["B1_QE"]).",'".$datos["B1_BOLSA"]."')")
					or die("Error al insertar partida TEMPO");					
				}
				$id++;
			}
			odbc_free_result($exce);
			/*Por cada tinaco agregar 1 caja adicional para la barilla, hasta 12 por caja. Agregar caja adicional de hasta 500 piezas por 2449*/
			$exce=odbc_exec($conn,"SELECT SUM(Cant) AS 'tinacos' FROM tempo t INNER JOIN SB1010 SB1 ON t.Producto=SB1.B1_COD WHERE t.pedido='".base64_decode($_GET["Ped"])."' AND B1_TIPO BETWEEN '01' AND '02' AND B1_CLASE='11' AND B1_MSBLQL=2  AND SB1.D_E_L_E_T_=''")or die("Error en las varillas del tinaco");
			$varillas=odbc_fetch_array($exce);
			if($varillas['tinacos']>0){
				$cant=0;
				if(intval($varillas['tinacos']/12)>0){
					$cant=intval($varillas['tinacos']/12);
					odbc_exec($conn,"INSERT INTO tempo(id,Pedido,Producto,Cant,Pedimento,Qe,Empaque) VALUES($id,'".base64_decode($_GET["Ped"])."','+Kit+',$cant*12,'',12,'F')")or die("Error al insertar Kit");
				}
				if(($varillas['tinacos']%12)>0){
					$id++;
					odbc_exec($conn,"INSERT INTO tempo(id,Pedido,Producto,Cant,Pedimento,Qe,Empaque) VALUES($id,'".base64_decode($_GET["Ped"])."','+Kit+',".intval($varillas['tinacos']-($cant*12)).",'',12,'F')")or die("Error al insertar Kit");
				}
				$id++;
			}
			odbc_free_result($exce);
			/*Por cada 500 piezas de 2449, agregar unas caja de varillas*/
			$exce=odbc_exec($conn,"SELECT SUM(Cant) AS 'varilla' FROM tempo t INNER JOIN SB1010 SB1 ON t.Producto=SB1.B1_COD WHERE t.pedido='".base64_decode($_GET["Ped"])."' AND B1_COD='2449' AND SB1.D_E_L_E_T_='' /*AND 'YA NO' = 'APLICA'*/")or die("Error en las varillas de 2449");
			$varillas=odbc_fetch_array($exce);
			if($varillas['varilla']>0){
				$cant=0;
				if(intval($varillas['varilla']/500)>0){
					$cant=intval(($varillas['varilla']/500))*500;
					odbc_exec($conn,"INSERT INTO tempo(id,Pedido,Producto,Cant,Pedimento,Qe,Empaque) VALUES($id,'".base64_decode($_GET["Ped"])."','+Varilla+',$cant,'',500,'F')")or die("Error al insertar la varilla");
				}
				if(($varillas['varilla']%500)>0){
					$id++;
					odbc_exec($conn,"INSERT INTO tempo(id,Pedido,Producto,Cant,Pedimento,Qe,Empaque) VALUES($id,'".base64_decode($_GET["Ped"])."','+Varilla+',".intval($varillas['varilla']-$cant).",'',500,'F')")or die("Error al insertar la varilla");
				}
				$id++;
			}			
			odbc_free_result($exce);
		}else{
			if($_GET["cant"]==0){
				odbc_exec($conn,"DELETE FROM tempo WHERE Pedido='".base64_decode($_GET["Ped"])."' AND Id=".$_GET["cod"])
				or die("Error al eliminar en tempo");
			}else{
				if($_GET["opc"]=="U"){
					odbc_exec($conn,"UPDATE tempo SET cant=".$_GET["cant"]." WHERE Pedido='".base64_decode($_GET["Ped"])."' AND Id='".$_GET["cod"]."'")
					or die("Error al actualizar cantidad en producto");
				}else{
					$sql=odbc_exec($conn,"SELECT Cant FROM tempo WHERE Pedido='".base64_decode($_GET["Ped"])."' AND Id=".$_GET["cod"])
					or die("Error al seleccionar la cantidad solicitada por partida");
					$sol=odbc_fetch_array($sql);
					odbc_free_result($sql);
					odbc_exec($conn,"UPDATE tempo SET cant=".$_GET["cant"]." WHERE Pedido='".base64_decode($_GET["Ped"])."' AND Id='".$_GET["cod"]."'")
					or die("Error al actualizar cantidad en producto");
					if(($sol['Cant']-$_GET["cant"])>0){
						$sql=odbc_exec($conn,"SELECT MAX(Id)+1 AS maximo FROM tempo WHERE Pedido='".base64_decode($_GET["Ped"])."'")
						or die("Error al obtener el maximo del pedido");
						$max=odbc_fetch_array($sql);
						odbc_free_result($sql);
						$sql=odbc_exec($conn,"SELECT * FROM tempo WHERE Pedido='".base64_decode($_GET["Ped"])."' AND Id='".$_GET["cod"]."'")
						or die("Error al ejecutar la consulta en tempo");
						$datos=odbc_fetch_array($sql);
						odbc_free_result($sql);
						odbc_exec($conn,"INSERT INTO tempo(Id,Pedido,Producto,Cant,Pedimento,Qe,Empaque) VALUES(".$max["maximo"].",'".base64_decode($_GET["Ped"])."','".$datos["Producto"]."','".round($sol["Cant"]-$_GET["cant"])."','','".$datos["Qe"]."','".$datos["Empaque"]."')")						
						or die("Error al insertar la partida");
					}
				}
			}
		}
	}
	odbc_close($conn);
	echo "<script languaje='JavaScript'>
			location.href='cap2Sal.php?Ped=".$_GET["Ped"]."&ordsur=".$_GET['ordsur']."';
		  </script>";
?>