<?php
header("Content-type: image/png");

// Variables obtenidas desde la URL (pueden ser cambiadas)
$bkg = "58759f";
$dat = isset($_GET['dat']) ? $_GET['dat'] : '';
$alm = isset($_GET['alm']) ? $_GET['alm'] : '';

$valores = explode(",", str_replace(" ", "", $dat));
$almacenistas = explode(",", $alm);
$nvars = count($valores);

if ($nvars > 0 && $nvars <= 50) {
    // Cálculo del ancho del gráfico
    if ($nvars > 0 && $nvars <= 10)
        $ladoy = 25;
    elseif ($nvars > 10 && $nvars <= 20)
        $ladoy = 18;
    elseif ($nvars > 20 && $nvars <= 30)
        $ladoy = 14;
    elseif ($nvars > 30 && $nvars <= 40)
        $ladoy = 13;
    elseif ($nvars > 40 && $nvars <= 50)
        $ladoy = 11;

    $ancho = 123 + ($nvars * $ladoy);
    $equis1 = 60; // equis IZQUIERDA
    $equis2 = $equis1 + $ladoy; // equis DERECHA

    // Creación de la imagen
    $image = imagecreate($ancho, 380);
    $colorbkg = imagecolorallocate($image, hexdec(substr($bkg, 0, 2)), hexdec(substr($bkg, 2, 2)), hexdec(substr($bkg, 4, 2)));
    imagefilledrectangle($image, 0, 0, $ancho, 380, $colorbkg); // crea bkg blanco

		ImageString($image,1,12, 47,"100%",$colores[4]);
		ImageLine($image,36,50,45,41,$colores[2]);   // diagonal
		ImageLine($image,46,41,$ancho - 32,41,$colores[3]);
		ImageString($image,1,17, 92,"75%",$colores[4]);
		ImageLine($image,36,95,45,86,$colores[2]);   // diagonal
		ImageLine($image,46,86,$ancho - 32,86,$colores[3]);
		ImageString($image,1,17, 137,"50%",$colores[4]);
		ImageLine($image,36,140,45,131,$colores[2]);    // diagonal
		ImageLine($image,46,131,$ancho - 32,131,$colores[3]);
		ImageString($image,1,17, 182,"25%",$colores[4]);
		ImageLine($image,36,185,45,176,$colores[2]);    // diagonal
		ImageLine($image,46,176,$ancho - 32,176,$colores[3]);
		ImageLine($image,46,221,$ancho - 32,221,$colores[3]);
		// BASE DE LA IMAGEN
		$puntos = CargaEsquinas(45, 221, 36, 230, $ancho - 40, 230, $ancho - 31, 221);
		imagefilledpolygon($image, $puntos, 4, $colores[3]);
		// Marco del grafico
		// superior izquierdo
		$puntos[0] = 45;                 // X
		$puntos[1] = 30;                 // Y
		// inferior izquierdo
		$puntos[2] = 45;
		$puntos[3] = 221;
		// inferior izquierdo DOS
		$puntos[4] = 36;
		$puntos[5] = 230;
		// inferior derecho
		$puntos[6] = $ancho - 40;
		$puntos[7] = 230;
		// inferior derecho DOS
		$puntos[8] = $ancho - 31;
		$puntos[9] = 221;
		// superior derecho
		$puntos[10] = $ancho - 31;
		$puntos[11] = 30;
		imagepolygon($image, $puntos, 6, $colores[0]);
//		ImageString($image,2,31,8,$ttl,$red); // Imprime el titulo
		for ($semestre = 0; $semestre < $nvars; $semestre++) {
			$porcentaje = (($valores[$semestre])*180)/max($valores);
			$puntos = array();
			// lado DERECHO DE LOS CUBOS (oscura)
			$puntos = CargaEsquinas($equis2 + 1, 49 + (180 - $porcentaje), $equis2 + 1, 229, $equis2 + 9, 220, $equis2 + 9, 41 + (180 - $porcentaje));
			imagefilledpolygon($image, $puntos, 4, $colores[$idcolor + 1]);
			// TOPE DE LOS CUBOS 3D (clara)
			$puntos = CargaEsquinas($equis1 + 9, 41 + (180 - $porcentaje), $equis1+1, 49 + (180 - $porcentaje), $equis2+1, 49 + (180 - $porcentaje), $equis2 + 9, 41 + (180 - $porcentaje));
			imagefilledpolygon($image, $puntos, 4, $colores[$idcolor + 2]);
			ImageFilledRectangle($image,$equis1 + 1,50 + (180 - $porcentaje),$equis2,229,$colores[$idcolor]);   // crea rectangulo
			//ImageString($image,1,$equis1 + 2, 232,$semestre,$colores[0]);
			ImageString($image,1,$equis1 + 2, 51 + (180 - $porcentaje),$valores[$semestre],$colores[0]);
			if(($semestre%2)==0)
			{
				$nombre=strlen(substr($almacenistas[$semestre/2],0,17));
				while($nombre>0)
				{
					ImageString($image,1,$equis1 + 20, ($nombre*8)+230,substr($almacenistas[$semestre/2],$nombre-1,1),$colores[4]);
					$nombre-=1;
				}
			}
			//ImageString($image,1,$equis1 + 2, 232,$valores[$semestre],$colores[0]);
			if ($idcolor < 12)
				$idcolor += 18;
			else
				$idcolor = 11;
			$equis1 += $ladoy;
			$equis2 += $ladoy;
		}
	} else {
		/* crea imagen */
		$image = imagecreate(300,60);
		$blanco = ImageColorAllocate($image,255,255,255);  // blanco
		$rojo = ImageColorAllocate($image,255,0,0);      // Rojo
		ImageFilledRectangle($image,0,0,300,60,$blanco);   // crea bkg blanco
		ImageString($image,2,23,23,"Solo es posible graficar de 0 a 50 valores",$rojo);
	}
	/* Realiza la generacion del grafico */
	ImagePNG($image);
	/* Vacia la memoria */
	ImageDestroy($image);
?>