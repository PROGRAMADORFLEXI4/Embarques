<?php 
	//¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡¡ I M P O R T A N T E !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//																																							 //
	// Si $DEBUGGER_FLAG == True, los correos se envían a los debugger mail, de lo contrario se envía a los destinatarios originales; 							 //
	//																																							 //
	// Dejar como array vacios en caso de que se quieran omitir los destinatarios 																				 //
	//																																							 //
	// SIEMPRE DEJARLOS DECLARADOS, CON O SIN INFORMACIÓN PARA EVITAR CORREOS ACCIDENTALES																		 //
	//																																							 //
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function enviar_correo($From, $FromName, $Body, $Subject, $To, $ToCC, $ToCCO, $Attachments, $doc, $docName, $docCod, $docType, $imgRoute = array(), $imgAlias = array(), $imgName = array()) {
		include("conectabd.php");

		$DEBUGGER_FLAG = False; // True / False



		$sql_registra_correo = "DECLARE @RECNO INT;
			SET @RECNO = (SELECT (ISNULL(MAX(R_E_C_N_O_), 0) + 1) rec FROM Z62010);
			INSERT INTO [dbo].[Z62010]
	           ([Z62_FILIAL]
	           ,[Z62_TO]
	           ,[Z62_TOCC]
	           ,[Z62_TOCCO]
	           ,[Z62_SUBJET]
	           ,[Z62_MODULO]
	           ,[Z62_FYHCRE]
	           ,[Z62_FYHPRO]
	           ,[Z62_FYHENV]
	           ,[D_E_L_E_T_]
	           ,[R_E_C_N_O_]
	           ,[Z62_FROM]
	           ,[Z62_CBODY]
	           ,[Z62_ATTACH]
	           ,[Z62_USERAC])
	     	VALUES
	           (''
	           ,'".($DEBUGGER_FLAG == True ? 'desarrollo@fleximatic.com.mx' : implode(';', $To))."'
	           ,'".($DEBUGGER_FLAG == True ? '' : implode(';', $ToCC))."'
	           ,'".($DEBUGGER_FLAG == True ? '' : implode(';', $ToCCO))."'
	           ,'".$Subject."'
	           ,'PEDIDOS_NET'
	           ,CONVERT(VARCHAR(MAX), GETDATE(), 120)
	           ,''
	           ,''
	           ,''
	           ,@RECNO
	           ,'".$From."'
	           ,'".$Body."'
	           ,''
	           ,'".strtoupper(gethostbyaddr($_SERVER['REMOTE_ADDR']))."');
		";

		return odbc_exec($conn,$sql_registra_correo);
	}
 ?>