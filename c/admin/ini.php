<?php 
	session_start();

	if(empty($_SESSION['logueo']))
	{
		echo "			
			<!DOCTYPE html>
			 <head>
			 <meta charset='utf-8'>
			 <meta http-equiv='X-UA-Compatible' content='IE=edge'>
			 <meta name='viewport' content='width=device-width, initial-scale=1'>
		   
			 <title>Usuario no autorizado</title>
			 <link href='assets/bootstrap/css/bootstrap.min.css' rel='stylesheet'>
			 </head>
			 <body style='text-align:center;padding:200px;'>							
				<img src='assets/img/403.jpg'><br><br>
				<h1>No est&aacute; autorizado para ingresar a esta opci&oacute;n. </h1>
				<p>Use el bot&oacute;n <b>regresar</b> de su navegador para navegar a la p&aacute;gina de la que proviene.</p>
				<p><b>O simplemente puede presionar este peque&ntilde;o bot&oacute;n:</b></p>
				<button class='btn btn-danger btn-lg' onclick=\"location.href='index.php'\"> Ir a inicio</button>
			 </body>
			 </html>			
			";
	}
	else
	{
		//Con esto evaluamos si viene algún parametro por metodo GET o POST
		$op = (empty($_REQUEST['op'])) ? '' : $_REQUEST['op'];
		
		//Colocamos la condicion para evaluar la salida del cms
		if($op=="salir")
		{
			//Destruimos la session actual
			session_destroy();
			//Redireccionamos al index
			header("Location:index.php");
		}
		
		//Llamamos la función plantilla
		plantilla();
		//Colocamos en una variable de sesion el valor asignado a la variable
		echo $_SESSION['plantilla'];
	}


	function plantilla()
	{
		//Abrimos el archivo de la plantilla en modo de lectura
		$archivo = fopen('views/templates/cmsmain.html','r');
		//asignamos a la variable html el archivo leido anteriormente
		$html = fread($archivo, filesize('views/templates/cmsmain.html'));
		
		//Cerramos el archivo de la plantilla
		fclose($archivo);
		
		//Reemplazamos los tags de sustitucion de nuestra plantilla
		$html = preg_replace('/--nombre--/', $_SESSION['nombre'], $html);
		//Colocamos en una variable de sesion el valor de nuestra variable html
		$_SESSION['plantilla'] = $html;
	}

?>

