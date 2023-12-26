<?php 
	function reemplaza_caracter($value){
		$value = trim($value);
		$value = html_entity_decode($value);
		$value = str_replace(chr(160), ' ', $value); //nbsp
		//acentos
		$value = str_replace('Á','A',$value); //Á
		$value = str_replace('É','E',$value); //É
		$value = str_replace('Í','I',$value); //Í
		$value = str_replace('Ó','O',$value); //Ó
		$value = str_replace('Ú','U',$value); //Ú
		$value = str_replace('Ü','U',$value); //Ü
		$value = str_replace('á','a',$value); //á
		$value = str_replace('é','e',$value); //é
		$value = str_replace('í','i',$value); //í
		$value = str_replace('ó','o',$value); //ó
		$value = str_replace('ú','u',$value); //ú
		$value = str_replace('ü','u',$value); //ü
		$value = str_replace('´','',$value); //´

		//----- caracteres especiales
		$value = str_replace(chr(241), '&#241;', $value); //ñ
		$value = str_replace(chr(209), '&#209;', $value); //Ñ
		$value = str_replace(chr(188), '&#188;', $value); //1/4
		$value = str_replace(chr(189), '&#189;', $value); //1/2
		$value = str_replace(chr(190), '&#190;', $value); //3/4
		$value = str_replace(chr(176), '&#176;', $value); //°
		$value = str_replace(chr(147), '&#147;', $value); //"
		$value = str_replace(chr(148), '&#148;', $value); //"
		$value = str_replace(chr(215), '&#215;', $value); //X
		$value = str_replace(chr(252), '&#252;', $value); //ü
		$value = str_replace(chr(220), '&#220;', $value); //Ü
		$value = str_replace(chr(180), '', $value); //´

		/*$value = str_replace(chr(220), '&#220;', $value); //ü
		$value = str_replace(chr(193), '&#193;', $value); //Á
		$value = str_replace(chr(201), '&#201;', $value); //É
		$value = str_replace(chr(205), '&#205;', $value); //Í
		$value = str_replace(chr(211), '&#211;', $value); //Ó
		$value = str_replace(chr(218), '&#218;', $value); //Ú
		$value = str_replace(chr(225), '&#225;', $value); //á
		$value = str_replace(chr(233), '&#233;', $value); //é
		$value = str_replace(chr(237), '&#237;', $value); //í
		$value = str_replace(chr(243), '&#243;', $value); //ó
		$value = str_replace(chr(250), '&#250;', $value); //ú*/
		return $value;
	}
 ?>