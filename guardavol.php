<?php
	$vpn=$_POST['vpn'];
	$vpb=$_POST['vpb'];
	$vlargo=$_POST['vlargo'];
	$vancho=$_POST['vancho'];
	$valto=$_POST['valto'];
	$vmts=$_POST['vmts'];
	$pn=explode("|",$vpn);
	$pb=explode("|",$vpb);
	$largo=explode("|",$vlargo);
	$ancho=explode("|",$vancho);
	$alto=explode("|",$valto);
	$mts=explode("|",$vmts);
	echo "GUARDADO";
?>