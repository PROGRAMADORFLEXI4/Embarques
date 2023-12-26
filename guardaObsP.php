<!DOCTYPE>
<html>
 <head>
  <title>Observaciones del Pedido</title>
 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"></head>
 <?php
 if(isset($_GET['tipo'])=="emb")
 	echo"<form name='frmGObs' method='post' action='guardaPed.php?actPedi2=T&emb=1&regis=".$_GET["regis"]."'>";
 else
	echo"<form name='frmGObs' method='post' action='guardaPed.php?actPedi2=T'>";
 ?>
<body background="images/fondo.png" text="#FFFFFF">
<center>
<?php
	include('conectabd.php');
	if(isset($_GET['tipo'])=="emb")
		$sql=odbc_exec($conn,"SELECT ISNULL(ZZO_OBSEMB,'') AS 'observ' FROM ZZO010 WHERE R_E_C_N_O_='".$_GET['regis']."' AND D_E_L_E_T_=''")
		or die("Error al obtener las observaciones del pedido de embarques");
	else
		$sql=odbc_exec($conn,"SELECT ISNULL(C5_OBSPED,'') AS 'observ' FROM SC5010 WHERE C5_NUM='".$_GET['Ped']."' AND D_E_L_E_T_=''")
		or die("Error al obtener las observaciones del pedido");
	$datos=odbc_fetch_array($sql);
	echo "<h2>Observaciones del Pedido: $_GET[Ped]</h2><br>
		  <input type='hidden' value='$_GET[Ped]' name='txtPed'>
	 	  <textarea name='txtObs' rows='15' cols='30'>".trim($datos['observ'])."</textarea>";
	odbc_free_result($sql);	
	odbc_close($conn);
?>
<br><br>
<input type="submit" value="Guardar">&nbsp;&nbsp;<input type="button" value="Cancelar" onClick="window.close();">
</center>
</form>
</body>
</html>