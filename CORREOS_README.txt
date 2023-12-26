Searching 282 files for "send()"

C:\xampp\htdocs\pedidos_net\Inserta_flete_recomendada.php:
   68  			$mail->Body = utf8_decode($complemento);
   69  
   70:             if ($mail->Send()) {
   71  
   72                  $res = "CORRECTO";

C:\xampp\htdocs\pedidos_net\pedPost.php:
  206  				$mail->Subject = "Pedido postergado";
  207  				$mail->Body="El pedido:$_POST[ped] ha sido postergado para su aprobación por el usuario:".substr($nUsu,0,strpos($nUsu,".")).", con la observación: $_POST[obs]";
  208: 				$mail->Send();
  209  						
  210  			
  ...
  250  			$mail->Subject = "Pedido postergado";
  251  			$mail->Body="El pedido:$_POST[ped] ha sido postergado para su aprobación por el usuario:".substr($nUsu,0,strpos($nUsu,".")).", con la observación: $_POST[obs]";
  252: 			$mail->Send();	
  253  		}
  254  	}

C:\xampp\htdocs\pedidos_net\sm.php:
   43  				$body = eregi_replace("[\]",'',$body);
   44  				$mail->MsgHTML($body);
   45: 				$mail->Send();	
   46  			}			
   47  			odbc_close($conn);

C:\xampp\htdocs\pedidos_net\sm3.php:
   43  				$body = eregi_replace("[\]",'',$body);
   44  				$mail->MsgHTML($body);
   45: 				$mail->Send();	
   46  			}			
   47  			odbc_close($conn);

C:\xampp\htdocs\pedidos_net\WS.php:
  684  			$body = eregi_replace("[\]",'',$body);
  685  			$mail->MsgHTML($body);
  686: 			//$mail->Send();
  687: 			if(!$mail->Send()) 
  688  			{
  689  				$errormail=$mail->ErrorInfo;
  ...
  711  				$body = eregi_replace("[\]",'',$body);
  712  				$mail->MsgHTML($body);
  713: 				$mail->Send();
  714  			} 
  715  			//else 

C:\xampp\htdocs\pedidos_net\WSLocal.php:
  684  			$body = eregi_replace("[\]",'',$body);
  685  			$mail->MsgHTML($body);
  686: 			//$mail->Send();
  687: 			if(!$mail->Send()) 
  688  			{
  689  				$errormail=$mail->ErrorInfo;
  ...
  711  				$body = eregi_replace("[\]",'',$body);
  712  				$mail->MsgHTML($body);
  713: 				$mail->Send();
  714  			} 
  715  			//else 

C:\xampp\htdocs\pedidos_net\WSX.php:
  620  			$body = eregi_replace("[\]",'',$body);
  621  			$mail->MsgHTML($body);
  622: 			//$mail->Send();
  623: 			if(!$mail->Send()) 
  624  			{
  625  				$errormail=$mail->ErrorInfo;
  ...
  647  				$body = eregi_replace("[\]",'',$body);
  648  				$mail->MsgHTML($body);
  649: 				$mail->Send();
  650  			} 
  651  			//else 