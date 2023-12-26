<?php
	$num_dias=1;
	$fpe=strtotime("26-06-2017");
	echo $fpe."<br>";
	for ($r=1 ;$r<=$num_dias;$r++)
	{
		$fpe=strtotime ( '+1 day' , $fpe);
		echo $fpe."<br>";
		echo date('N',$fpe)."<br>";
		if(date('N',$fpe)==6)
			$fpe=strtotime ( '+2 day' , $fpe);
		elseif(date('N',$fpe)==7)
			$fpe=strtotime ( '+1 day' , $fpe);
			
	}
	$fpe=date("Ymd", $fpe);
	echo $fpe."<br>";
	$dias=intval((strtotime("06/09/17 10:40:48 ")-strtotime("2017-09-26 15:00:00"))/60/60/24);
	$horas=intval(((strtotime("06/09/17 10:40:48 ")-strtotime("2017-09-26 15:00:00"))/60/60)-($dias*24));
	$minutos=intval((strtotime("06/09/17 10:40:48 ")-strtotime("2017-09-26 15:00:00"))/60)-($dias*24)-($horas*60);
	echo ($dias>0?$dias.'D':'').($horas>0?$horas.'H':'').($minutos>0?$minutos.'M':'');
	echo "<br>".$dias.'D'.$horas.'H'.$minutos.'M    '.substr("06/09/17 10:40:48 ",6,2)."-".substr("06/09/17 10:40:48 ",3,2)."-".substr("06/09/17 10:40:48 ",0,2).substr("06/09/17 10:40:48 ",8);
	echo "<br>".($dias<=0 && $horas<=0 && ($minutos<=0 || $minutos>=60)?'Vencido':"puto");
	
?>