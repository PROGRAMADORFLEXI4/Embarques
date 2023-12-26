<?php
	echo date("Y-m-d");
	echo "<br>".strtotime(date("Y-m-d"));
	echo "<br>".strtotime ( '+4320 minute' , strtotime(date("Y-m-d"))) ;
	echo "<br>".date("Ymd", strtotime ( '+1 day' , strtotime(date("d-m-Y"))));