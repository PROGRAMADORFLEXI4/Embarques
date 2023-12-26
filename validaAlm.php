<?PHP
	include("conectabd.php");
	session_start();
	$_SESSION['user'];
	echo "
	  	<div class='close'>
	  		<button id='bC' class='cerrar'>x</button>
	  	</div>
	  	<br>";
	if($_POST['urg']==2){
		echo "
		<table>
			<tr>
				<td>
					<strong>Num. Pedido:</strong>
				</td>
				<td>
					<input type='text' id='txtDes' value='".$_POST["Ped"]."' readonly width='6'>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Marcar como:</strong>
				</td>
				<td>
				<select id='opc' class='cmbOpc'>
					<option value='reimp'>Reimprimir pedido</option>
					<option value='pxa'>Pedido por auditar</option>
					<option value='pps'>Pedido en proceso de surtido</option>
					<option value='pxs'>Pedido por surtir</option>
				</select>
			</td>
		</table>
		<br>
		<div class='btnLinea'>
			<input type='button' value='Aceptar' onclick=location.href='reimpPed.php?ped=".$_POST['Ped']."&ord_sur=".$_POST['ordsur']."&desmarca=0&opc='+opc.value/>
		</div>";
	}elseif($_POST['urg']==1){
		//Marca/Desmarca como urgente el pedido
		$exce=odbc_exec($conn,"SELECT C5_URGENTE FROM SC5010 WHERE C5_NUM='$_POST[Ped]' AND D_E_L_E_T_=''")or die("Error al obtener la prioridad del pedido");
		$dato=odbc_fetch_array($exce);
		if($dato["C5_URGENTE"]=="T"){
			odbc_exec($conn, "UPDATE SC5010 SET C5_URGENTE='F' WHERE C5_NUM='$_POST[Ped]' AND D_E_L_E_T_=''")or die("Error al modificar la prioridad del pedido");
			echo "<h1>Pedido desmarcado</h1><script>location.href='index.php';</script>";
			
		}else{
			odbc_exec($conn, "UPDATE SC5010 SET C5_URGENTE='T' WHERE C5_NUM='$_POST[Ped]' AND D_E_L_E_T_=''")or die("Error al modificar la prioridad del pedido");
			echo "<h1>Pedido marcado como urgente</h1><script>location.href='index.php';</script>";
			//echo "<h1>Desea Imprimir el pedido?</h1><div class='btnLinea'><input type='button' onclick=location.href='validaAlm.php?est=1&urg=0&Ped=".$_POST['Ped']."' value='Aceptar' /></div>";
		}
		odbc_free_result($exce);
		odbc_close($conn);
		exit;
	}else{
		//Valida si existen pedidos urgentes
		$opc=0;
		if($_POST['est']==1){
			$sql=odbc_exec($conn, "SELECT COUNT(*) AS cuantos FROM SC5010 WHERE C5_URGENTE='T' AND C5_NUM<>'$_POST[Ped]' AND C5_IMPRESO='F' AND C5_FYHRCYC<>'' AND D_E_L_E_T_=''")or die("Error al ejecutar la consulta de los pedidos urgentes");
			$pedido=odbc_fetch_array($sql);
			odbc_free_result($sql);
			if($pedido['cuantos']>0){
				odbc_exec($conn,"");
				$sql=odbc_exec($conn, "SELECT COUNT(*) AS cuantos FROM SC5010 WHERE C5_NUM='$_POST[Ped]' AND C5_URGENTE='T' AND D_E_L_E_T_=''")or die("Error al ejecutar la consulta de los pedidos urgentes");
				$pedidos=odbc_fetch_array($sql);
				odbc_free_result($sql);
				if($pedidos['cuantos']==0){
					$opc=1;
					echo "<h1>No es posible surtir el pedido ya que existen pedidos urgentes</h1>";
				}else{
					//Reviza que el pedido seleccionado sea el primero de los urgentes
					$sql=odbc_exec($conn, "SELECT C5_NUM FROM SC5010 WHERE C5_URGENTE='T' AND C5_IMPRESO='F' AND D_E_L_E_T_='' ORDER BY CONVERT(datetime,C5_FYHRCYC)")or die("Error al obtener el orden de pedidos urgentes sin imprimir");
					$pedidos=odbc_fetch_array($sql);
					odbc_free_result($sql);
					if($pedidos['C5_NUM']!=$_POST['Ped']){
						$opc=1;
						echo "<h1>No es posible surtir este pedido ya que no corresponde al orden de los pedidos urgentes. El pedido por surtir es el: <u>$pedidos[C5_NUM]</u>";
					}
				}
			}else{
				$sql=odbc_exec($conn, "SELECT COUNT(*) AS cuantos FROM SC5010 WHERE C5_NUM='$_POST[Ped]' AND C5_URGENTE='T' AND D_E_L_E_T_=''")or die("Error al ejecutar la consulta de los pedidos urgentes");
				$pedidos=odbc_fetch_array($sql);
				odbc_free_result($sql);
				if($pedidos['cuantos']==0){
					$opc=1;
					echo "<h1>No hay pedidos marcados para poder surtir";
				}
			}
		}

		if($opc<>1){
			echo "
				<form name='form1' method='post' action='guardaPed.php'>
					<script>document.getElementById('txtCodAlm').focus();</sc>
				   	<table>
						<tr>
							<td>
								<strong>C&oacute;d. Almacenista:</strong>
							</td>
							<td>
								<input type='password' id='txtCodAlm' name='txtCodAlm' class='txtForm' maxlength='6'>
							</td>
						</tr>
						<tr>
							<td>
								<strong>N&uacute;mero de Pedido:</strong>
							</td>
							<td>
								<input type='text' value='".$_POST["Ped"]."' class='txtForm' readonly id='txtDes' name='txtPed'>
							</td>
						</tr>
						<tr>
							<td>
								<strong>Orden de surtido:</strong>
							</td>
							<td>
								<input type='text' value='".$_POST["ordsur"]."' class='txtForm' readonly style='background-color: #CCC; text-align: right;' name='txtordsur'>
							</td>
						</tr>
				  	</table>
				  	<br>
				  	<div class='btnLinea'>
					   	<center>
							<button id='Acept'>Aceptar</button>
						</center>
				  </div>
			  </form>";
		}
	}
	odbc_close($conn);  
?>
</body>
</html>