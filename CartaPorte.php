<!DOCTYPE html>
<html>
<head>

    <style>
        #customers {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        .container {
            font-size: 15px;
        }

    </style>


    <link href="css/styles.css" rel="stylesheet" />
    <title>CartaPorte</title>
    <script src="css/jquery.js"></script>
    <link rel="shortcut icon" href="images/icono.ico" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body>
    <center>
        <?php

        include("conectabd.php");
        date_default_timezone_set('America/Monterrey');
        setlocale(LC_ALL,"spanish");

        $recno = trim(base64_decode($_GET['rec']));
        $factura = trim(base64_decode($_GET['fac']));
        $inhouse = trim(base64_decode($_GET['inhouse']));
        $pedido ="";
        $codCliente ="";
        $cliente ="";
        $codFletera ="";
        $fletera ="";
        $diremb="";
        $res ="";

        $sql_chofer=odbc_exec($conn, "SELECT ZZN_CODIGO,ZZN_NOMBRE FROM ZZN010 WHERE ZZN_TIPO='1' AND D_E_L_E_T_='' ORDER BY ZZN_NOMBRE")
        or die("Error al obtener el catalogo de chofer");
        while($data=odbc_fetch_array($sql_chofer))
            $chofer[]=array(
                "codChofer" => trim($data['ZZN_CODIGO']),
                "nombreChofer" => trim($data['ZZN_NOMBRE']),
            );
        odbc_free_result($sql_chofer);

        if($inhouse=='NO'){
            $hoy = date("Y-m-d H:i:s");    
            $fecha_salida= date("Y-m-d H:i",strtotime('+10 minutes',strtotime($hoy)));//10 minutos despues de la fecha de actual
            $fecha_llegada= date("Y-m-d H:i",strtotime('+610 minutes',strtotime($hoy)));//10 horas despues de la fecha de salida
            $fecha_salida= str_replace(" ","T",$fecha_salida);
            $fecha_llegada= str_replace(" ","T",$fecha_llegada);

            $sql_fact=odbc_exec($conn, "SELECT * FROM 
            (SELECT ZZO_PEDIDO,ZZO_CLTE,ZZO_FACT FROM ZZO010 WHERE D_E_L_E_T_='') ZO
            INNER JOIN
            (SELECT C5_NUM,C5_DIREMB,C5_TRANSP FROM SC5010 WHERE D_E_L_E_T_='') C5 ON ZZO_PEDIDO=C5_NUM
            INNER JOIN
            (SELECT A1_COD,A1_NREDUZ,A1_NOME FROM SA1010 WHERE D_E_L_E_T_='') A1 ON ZZO_CLTE=A1_COD
            INNER JOIN
            (SELECT A2_COD,A2_NREDUZ,A2_NOME FROM SA2010 WHERE D_E_L_E_T_='') A2 ON C5_TRANSP=A2_COD
            WHERE ZZO_FACT='".$factura."';") or die("Error al obtener los vehiculos");
            if($data=odbc_fetch_array($sql_fact)){
                $pedido = trim($data['ZZO_PEDIDO']);
                $diremb = trim($data['C5_DIREMB']);
                $codCliente = trim($data['ZZO_CLTE']);
                $cliente = trim($data['A1_NOME']);
                $codFletera= trim($data['C5_TRANSP']);
                $fletera = trim($data['A2_NREDUZ']);
            }
            odbc_free_result($sql_fact); 

            $res .="
            <!------------------------- VARIABLES EN HTML ----------------------------------->		
            <input value='".$factura."' id='nFactura' type='hidden' ></input>
            <input value='".$codCliente."' id='idcliente' type='hidden' ></input>
            <input value='".$diremb."' id='diremb' type='hidden' ></input>
            <input value='".$codFletera."' id='idFletera' type='hidden' ></input>
            <input value='".$recno."' id='recnoZZO' type='hidden' ></input>
            <input value='".$fecha_salida."' id='fyh_salid_php' type='hidden' ></input>
            <input value='".$fecha_llegada."' id='fyh_lleg_php' type='hidden' ></input>

            <!------------------------- GRUPO DE ELEMENTOS ----------------------------------->		
            <div class='container' style='width:1000px; margin:20px auto;overflow:hidden; border-style:ridge;' >
                <h2>DATOS PARA CARTA PORTE DE LA FACTURA DE VENTA [".$factura."]</h2>

                <div class='container' style='width:100%; margin:40px 0 20px 40px;overflow:hidden; text-align: left;'>
                    <div style='width:180px; margin:0 auto;overflow:hidden; float: left;'>
                        <strong>Tipo de entrega:</strong>
                    </div>
                    <div style='width:600px; margin:0 auto;overflow:hidden; float: left;'>
                        <select id='tipoEntrega' name='tipoEntrega' style='width:600px'>
                            <option value='-1'>Selecciona una opción</option>
                            <option value='0'>[Cliente: ".$codCliente."] - ".$cliente."</option>
                            <option value='1'>[Fletera: ".$codFletera."] - ".$fletera."</option>
                        </select>
                    </div>
                </div>

                <div class='container' style='width:100%; margin:40px 0 20px 40px;overflow:hidden; text-align: left;'>
                    <div style='width:180px; margin:0 auto;overflow:hidden; float: left;'>
                        <strong>Selecciona el Destino:</strong>
                    </div>
                    <div style='width:600px; margin:0 auto;overflow:hidden; float: left;'>
                        <select id='destino' name='tipoEntrega' style='width:600px' disabled>
                            <option value='-1'>Selecciona una opción</option>
                        </select>
                    </div>
                </div>

                <div class='container' style='width:100%; margin:40px 0 20px 40px;overflow:hidden; text-align: left;'>
                    <div style='width:180px; margin:0 auto;overflow:hidden; float: left;' disabled>
                        <strong>Selecciona el Vehiculo:</strong>
                    </div>
                    <div style='width:600px; margin:0 auto;overflow:hidden; float: left;'>
                        <select id='vehiculo' name='vehiculo' style='width:600px' disabled>
                            <option value='-1'>Selecciona una opción</option>";

            $sql_vehiculos=odbc_exec($conn, "SELECT * FROM Z80010 WHERE D_E_L_E_T_='';") or die("Error al obtener los vehiculos");
            while($data=odbc_fetch_array($sql_vehiculos)){
                $res .="    <option value='".trim($data['R_E_C_N_O_'])."'>[".trim($data['Z80_PLACA'])."] - ".trim($data['Z80_DESC'])."</option>";
            }
            odbc_free_result($sql_vehiculos);
                    
            $res .= "
                        </select>
                    </div>
                </div>

                <div class='container' style='width:100%; margin:40px 0 20px 40px;overflow:hidden; text-align: left;'>
                    <div style='width:180px; margin:0 auto;overflow:hidden; float: left;'>
                        <strong>Selecciona el Chofer:</strong>
                    </div>
                    <div style='width:600px; margin:0 auto;overflow:hidden; float: left;'>
                        <select id='chofer' name='chofer' style='width:600px' disabled>
                            <option value='-1'>Selecciona una opción</option>";

            $sql_tipEntrega=odbc_exec($conn, "SELECT * FROM ZZN010 WHERE D_E_L_E_T_='' AND ZZN_TIPO='1' AND ZZN_RFC<>'' AND ZZN_LICENC<>'';
            ;") or die("Error al obtener los choferes");
            while($data=odbc_fetch_array($sql_tipEntrega)){
                $res .="    <option value='".trim($data['ZZN_CODIGO'])."'>[".trim($data['ZZN_LICENC'])."] - ".trim($data['ZZN_NOMBRE'])."</option>";
            }
            odbc_free_result($sql_tipEntrega);
                                    
            $res .= "
                        </select>
                    </div>                    
                </div>
                
                <div class='container' style='width:100%; margin:40px 0 20px 40px;overflow:hidden; text-align: left;'>
                    <div style='width:180px; margin:0 auto;overflow:hidden; float: left;'>
                        <strong>FYH salida al destino:</strong>
                    </div>
                    <div style='width:600px; margin:0 auto;overflow:hidden; float: left;'>
                        <input type='datetime-local' name='fyh_salida' id='fech_salida' disabled>
                    </div>                    
                </div>
                
                <div class='container' style='width:100%; margin:40px 0 20px 40px;overflow:hidden; text-align: left;'>
                    <div style='width:180px; margin:0 auto;overflow:hidden; float: left;'>
                        <strong>FYH llegada al destino:</strong>
                    </div>
                    <div style='width:600px; margin:0 auto;overflow:hidden; float: left;'>
                        <input type='datetime-local' name='fyh_llegada' id='fech_llegada' disabled>
                    </div>                    
                </div>

                ";

        }else{  

            $res .="
            <!------------------------- VARIABLES EN HTML ----------------------------------->		
            <input value='".$factura."' id='nFactura' type='hidden' ></input>
            <input value='".$recno."' id='recnoZZO' type='hidden' ></input>

            <!------------------------- GRUPO DE ELEMENTOS ----------------------------------->		
            <div class='container' style='width:1000px; margin:20px auto;overflow:hidden; border-style:ridge;' >
                <h2>DATOS PARA EMBARQUE DE LA FACTURA DE VENTA [".$factura."]</h2>

                <div class='container' style='width:100%; margin:40px 0 20px 40px;overflow:hidden; text-align: left;'>
                    <div style='width:180px; margin:0 auto;overflow:hidden; float: left;'>
                        <strong>Selecciona el Chofer:</strong>
                    </div>
                    <div style='width:600px; margin:0 auto;overflow:hidden; float: left;'>
                        <select id='chofer' name='chofer' style='width:600px'>
                            <option value='-1'>Selecciona una opción</option>";
            
            foreach ($chofer as $key => $value) {
                $res .="
                            <option value='".$value['codChofer']."'>".$value['nombreChofer']."</option>";
            }
                                    
            $res .= "
                        </select>
                    </div>                    
                </div>";

        }

        $res .="
                <div class='container' style='width:100%; margin:40px 0 20px 40px;overflow:hidden; text-align: left;'>
                    <div style='width:180px; margin:0 auto;overflow:hidden; float: left;'>
                        <strong>Selecciona el Auxiliar:</strong>
                    </div>
                    <div style='width:600px; margin:0 auto;overflow:hidden; float: left;'>
                        <select id='auxiliar' name='auxiliar' style='width:600px' ".($inhouse=='NO'?"disabled":"").">
                            <option value='-1'>Selecciona una opción</option>";

        foreach ($chofer as $key => $value) {
            $res .="
                            <option value='".$value['codChofer']."'>".$value['nombreChofer']."</option>";
        }
        $res .= "
                        </select>
                    </div>                    
                </div>";
        $res .="
            </div>
            <!------------------------- BOTONES----------------------------------->	
            <div style='width:800px; margin:0 auto;overflow:hidden;' id='botones'>
                <br><input type='submit' value='Generar' onClick=Generar(\"".$inhouse."\") id='Generar' >&nbsp;&nbsp;<input type='button' value='Cancelar' onClick=cerrar() class='cerrar'>
            </div>";

        echo $res;
        ?>

    </center>
    </form>
        <script>
            
            $(function(){
                $('#fech_salida').val($('#fyh_salid_php').val());
                $('#fech_llegada').val($('#fyh_lleg_php').val());
            });

            $('#tipoEntrega').change(function() {
                let valorSelect = $("#tipoEntrega").val();
                let codCliente = $("#idcliente").val();
                let diremb = $("#diremb").val();
                let codFletera = $("#idFletera").val();

                if (valorSelect != -1){
                    $('#destino').removeAttr('disabled');
                    $('#vehiculo').removeAttr('disabled');
                    $('#chofer').removeAttr('disabled');
                    $('#auxiliar').removeAttr('disabled');
                    $('#fech_salida').removeAttr('disabled');
                    $('#fech_llegada').removeAttr('disabled');

                    $.ajax({
                        url: 'consulta_destino.php', 
                        type: 'POST',
                        data: {
                            valorSelect:valorSelect,
                            codCliente: codCliente,
                            diremb:diremb,
                            codFletera:codFletera 
                        },
                        beforeSend: function(){
                            $("#destino").html("<option value='-1'>Cargando...</option>");
                        },
                        success: function(data){
                            $("#destino").html(data);
                        },
                        error: function(data) {

                            $("#destino").html("<option value='-1'>Vuelve a seleccionar el tipo</option>");
                            $('#destino').attr('disabled','disabled');
                            $('#vehiculo').attr('disabled','disabled');
                            $('#chofer').attr('disabled','disabled');
                            $('#auxiliar').attr('disabled','disabled');
                            $('#fech_salida').attr('disabled','disabled');
                            $('#fech_llegada').attr('disabled','disabled');

                        }
                    });
                }else{
                    $("#destino").html("<option value='-1'>Selecciona una opción</option>");
                    $('#destino').attr('disabled','disabled');
                    $('#vehiculo').attr('disabled','disabled');
                    $('#chofer').attr('disabled','disabled');
                    $('#auxiliar').attr('disabled','disabled');
                    $('#fech_salida').attr('disabled','disabled');
                    $('#fech_llegada').attr('disabled','disabled');
                }
            });

            function Generar(inhouse){

                let texto = inhouse=="NO"? "¿Realmente deseas generar la Factura CartaPorte?":"¿Realmente guardar los datos?";
                let flag =confirm(texto); 

                if (flag==true && inhouse=="NO"){
                    let flag=true;
                    let msj="";
                    let recnoZZO = $("#recnoZZO").val();
                    let cliente = $("#idcliente").val();
                    let factura = $("#nFactura").val();
                    let tipEntrega = $("#tipoEntrega").val();
                    let destino = $("#destino").val();
                    let vehiculo = $("#vehiculo").val();
                    let chofer = $("#chofer").val();
                    let auxiliar = $("#auxiliar").val();
                    let fyh_salida = $("#fech_salida").val();
                    let fyh_llegada = $("#fech_llegada").val();

                    if(tipEntrega=='-1' || destino=='-1' || vehiculo=='-1' || chofer=='-1' || auxiliar=='-1'){
                        flag=false;
                        mensaje='Favor de seleccionar todos los datos';
                    }else if(Date.parse(fyh_salida)>=Date.parse(fyh_llegada)){
                        flag=false;
                        mensaje='La fecha y hora de salida no puede ser mayor a la fecha y hora de llegada';
                    }else if(Date.parse(fyh_salida)<Date.parse(Date())){
                        flag=false;
                        mensaje='La fecha y hora de salida debe ser mayor a la actual';
                    }
                    //debugger;
                    //flag=false;
                    if(flag){
                        $.ajax({
                            type: "POST",
                            url: "genera_factura.php",
                            data: {
                                inhouse:inhouse,
                                cliente:cliente,
                                factura:factura,
                                tipEntrega:tipEntrega,
                                destino:destino,
                                vehiculo:vehiculo,
                                chofer:chofer,
                                auxiliar:auxiliar,
                                recnoZZO:recnoZZO,
                                fyh_salida:fyh_salida,
                                fyh_llegada:fyh_llegada
                            },
                            beforeSend: function(){
                                $("#botones").html("<h1>Guardando Registros... Espere</h1>");
                            },
                            success: function (response) {
                                let respuesta=response.trim();//se realizo esto porque el response regresaba el valor con espacio y no supe de donde sale
    
                                let arreglo = respuesta.split('|-|');
                                if (arreglo.length > 1){ 
                                    mensaje = arreglo[0];
                                    dato = arreglo[1];
                                }else{
                                    mensaje=respuesta;
                                    dato = respuesta;
                                }
    
                                if(mensaje=='CORRECTO'){
                                    
                                    generaTXT(dato);
    
                                }else if(mensaje=='REPET'){
                                    alert("ERROR: Ya un registro con esta factura, favor de validar.");
                                }else{
                                    alert("Ocurrio un error al generar la factura: "+dato);
                                }
    
                                $("#botones").html("<br><input type='submit' value='Generar' onClick=Generar(\""+inhouse+"\") id='Generar' >&nbsp;&nbsp;<input type='button' value='Cancelar' onClick=cerrar() class='cerrar'>");
                            },
                            error: function(response) {
                                $("#botones").html("<br><input type='submit' value='Generar' onClick=Generar(\""+inhouse+"\") id='Generar' >&nbsp;&nbsp;<input type='button' value='Cancelar' onClick=cerrar() class='cerrar'>");
                            }
                        });
                    }else{
                        alert(mensaje);
                    }
                }else if(flag==true && inhouse=="SI"){
                    let flag=true;
                    let msj="";
                    let factura = $("#nFactura").val();
                    let recnoZZO = $("#recnoZZO").val();
                    let chofer = $("#chofer").val();
                    let auxiliar = $("#auxiliar").val();

                    if(chofer=='-1' || auxiliar=='-1'){
                        flag=false;
                        mensaje='Favor de seleccionar todos los datos';
                    }

                    if(flag){
                        $.ajax({
                            type: "POST",
                            url: "genera_factura.php",
                            data: {
                                inhouse:inhouse,
                                factura:factura,
                                chofer:chofer,
                                auxiliar:auxiliar,
                                recnoZZO:recnoZZO
                            },
                            beforeSend: function(){
                                $("#botones").html("<h1>Guardando Registros... Espere</h1>");
                            },
                            success: function (response) {
                                
                                let respuesta=response.trim();//se realizo esto porque el response regresaba el valor con espacio y no supe de donde sale

                                let arreglo = respuesta.split('|-|');
                                if (arreglo.length > 1){ 
                                    mensaje = arreglo[0];
                                    dato = arreglo[1];
                                }else{
                                    mensaje=respuesta;
                                    dato = respuesta;
                                }

                                if(mensaje=='CORRECTO'){
                                    
                                    alert("Información guardada correctamente");
                                    location.href='Embarques.php';

                                }else{
                                    alert("Ocurrio un error al guardar la información: "+dato);
                                }

                                $("#botones").html("<br><input type='submit' value='Generar' onClick=Generar(\""+inhouse+"\") id='Generar' >&nbsp;&nbsp;<input type='button' value='Cancelar' onClick=cerrar() class='cerrar'>");
                            },
                            error: function(response) {
                                $("#botones").html("<br><input type='submit' value='Generar' onClick=Generar(\""+inhouse+"\") id='Generar' >&nbsp;&nbsp;<input type='button' value='Cancelar' onClick=cerrar() class='cerrar'>");
                            }
                        });
                    }
                }
            }

            function generaTXT(nuevaFactura){
                $.ajax({
                    type: "POST",
                    url: "genera_txtFactura.php",
                    data: {
                        factura:nuevaFactura
                    },
                    beforeSend: function(){
                        $("#botones").html("<h1>Generando TXT... Espere</h1>");
                    },
                    success: function (response) {
                        let respuesta=response.trim();//se realizo esto porque el response regresaba el valor con espacio y no supe de donde sale
                        
                        if(respuesta=='CORRECTO'){
                            alert("Se genero la factura "+nuevaFactura+" correctamente.");
                            location.href='Embarques.php';
                        }else if(respuesta=='ERROR_ENCA'){
                            alert("Error al generar el encabezado del TXT.");
                        }else if(respuesta=='ERROR_DETA'){
                            alert("Error al generar el detalle del TXT.");
                        }else{
                            alert("Error desconocidao al generar del TXT: "+respuesta);
                        }
                    },                 
                    error: function(response) {
                        alert("Ocurrio un error al generar el TXT de la factura: "+nuevaFactura)
                    }
                });
            }

            function cerrar(){
                location.href='Embarques.php';
            }
        </script>
</body>
</html>