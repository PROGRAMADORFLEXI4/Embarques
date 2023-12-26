<?php
	include 'conectabd.php';
	if(isset($_POST['txtDepto']))
	{
		if(trim($_POST['txtDepto'])<>"" || trim($_POST['txtDescrip'])<>"")
		{
			$sql=odbc_exec($conn,"SELECT ISNULL(MAX(R_E_C_N_O_),0)+1 AS 'reg' FROM embrec") or die("Error al obtener el consecutivo");
			$datos=odbc_fetch_array($sql);
			odbc_free_result($sql);
			odbc_exec($conn,"INSERT INTO embrec(chofer,depto,descrip,fyHora,R_E_C_N_O_) VALUES('".$_POST['cmbChof']."','".$_POST['txtDepto']."','".$_POST['txtDescrip']."','".date("d/m/Y H:m:s",time())."',".$datos['reg'].")")
			or die("Error al insertar los datos");
			odbc_close($conn);
			echo "<script language='javascript'>window.close();</script>";
		}
	}
	elseif(isset($_POST['txtObsEmb']))
	{
		odbc_exec($conn,"UPDATE SC5010 SET C5_OBSEMB='".trim($_POST['txtObsEmb'])."' WHERE C5_NUM='$_POST[txtP]' AND D_E_L_E_T_=''");
		odbc_close($conn);		
		echo "<script>location.href='embarques.php'</script>";
	}
	elseif(isset($_POST['txtOEmb']))
	{
		odbc_exec($conn,"UPDATE SA1010 SET A1_OBSEMB='".$_POST['txtOEmb']."' WHERE A1_COD='".$_POST['codCli']."' AND D_E_L_E_T_=''");
		echo "<script language='javascript'>location.href='embarques.php';</script>";
	}
	odbc_close($conn);
?>