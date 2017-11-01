<?php 
	require_once "db/conexion.php";

	//habilitamos la sesion
	session_start();

	//evaluamos que este logueado
	if (!empty($_SESSION['logueo']))
	{
		$acc = (empty($_REQUEST['acc'])) ? '' : $_REQUEST['acc']; //Evaluando si viene un parametro con una accion a realizar
			if ($acc == "crearPagina") {crear_pagina();}
			if ($acc == "editarPagina") {editar_pagina();}
			if ($acc == "guardarPagina") {guardar_pagina();}
	}

	//funcion para crear las paginas
	function crear_pagina()
	{
		//Creamos una instancia de la clase conexion
		$dbconn = new baseDatos();
		//accedemos al metodo conectar()
		$mysqli = $dbconn::conectar();

		//decirle cual sera la ubicacion base para cada pagina de nuestro sitio, en este caso necesitamos que queden fuera de la carpeta admin
		$base = "../";
		//obtenemos el nombre de la pagina que el usuario a digitado o ingresado en el formulario
		$nombre = $_REQUEST['nbpagina'];
		//le agregamos la extension
		$nombre2 = $nombre.".html";

		//obtenemos el nombre de la plantilla que el usuario necesita utilizar
		$plantilla = $_REQUEST['plantilla'];

		//creando un nuevo archivo con la funcion fopen()
		$archivo = fopen($base.$nombre2, 'w'); //w = escritura
		//cerrando el archivo creado
		fclose($archivo);

		//insertando en la base de datos
		$query = mysqli_query($mysqli, "insert into pagina(nombre,plantilla) values('$nombre2','$plantilla')");

		//evaluamos
		if ($query)
		{
			echo 1;
		}
		else
		{
			echo 2;
		}

		$dbconn::desconectar($mysqli);
	}

	function editar_pagina()
	{
		//Creamos una instancia de la clase conexion
		$dbconn = new baseDatos();
		//accedemos al metodo conectar()
		$mysqli = $dbconn::conectar();

		$pagina = $_REQUEST['pagina'];

		//seleccionando desde la base de datos
		$query = mysqli_query($mysqli, "select idpagina,plantilla,contenido from pagina where nombre='$pagina'");

		if($query == FALSE)
				echo 2;
		else
		{
			$fila = mysqli_fetch_assoc($query);
			$_SESSION['idpagina'] = $fila['idpagina'];
			$_SESSION['plantilla'] = $fila['plantilla'];
			$_SESSION['destino']=$pagina;
			if (is_null($fila['contenido'])) //evaluamos si el contenido viene nulo 
			{
				$contenido = '';
			}
			else
			{
				$contenido = html_entity_decode($fila['contenido'],ENT_QUOTES,'UTF-8'); 
				//Si ya tiene contenido lo decodificamos para poder trabajar correctamente con los caracteres en html
			}

			$fp=fopen('views/templates/'.$_SESSION['plantilla'].'a', 'r');
			$html = fread($fp, filesize('views/templates/'.$_SESSION['plantilla'].'a'));
			fclose($fp);

			$html = preg_replace('/--contenido--/', $contenido, $html);
			$html = preg_replace('/--menu--/', hacerMenu(), $html); //utilizaremos una funcion para hacer el menu sobre la marcha, segun los datos de la base

			echo $html;
		}

		mysqli_free_result($query);

		$dbconn::desconectar($mysqli);
	}

	function hacerMenu()
	{
		global $menu;
		//Creamos una instancia de la clase conexion
		$dbconn = new baseDatos();
		//accedemos al metodo conectar()
		$mysqli = $dbconn::conectar();

		//seleccionando desde la base de datos
		$query = mysqli_query($mysqli, "select idpagina,nombre from pagina where estado='A'");

		if($query == FALSE)
				echo 2;
		else
		{			
			$menu = "<div class='list-group'>";
			
			while($fila = mysqli_fetch_assoc($query))
			{				
				$menu.="<a href='".$fila['nombre']."' class='list-group-item'>".substr($fila['nombre'], 0, -5)."</a>";		
			}

			$menu .= "</div>";			
		}

		return $menu;

		mysqli_free_result($query);

		$dbconn::desconectar($mysqli);
	}

	function guardar_pagina()
	{
		$dbconn = new baseDatos();
		//accedemos al metodo conectar()
		$mysqli = $dbconn::conectar();

		$contenido = rawurldecode($_REQUEST['codigo']);

		//actualizando la base de datos, colocando el contenido del sitio con htmlentities
		$query = mysqli_query($mysqli, "update pagina set contenido='".htmlentities($contenido,ENT_QUOTES,"UTF-8")."' where idpagina=".$_SESSION['idpagina']);

		if(mysqli_affected_rows($mysqli) == 0)
				echo 2;
		else
		{
			$fpin = fopen('views/templates/'.$_SESSION['plantilla'], 'r');
			$html = fread($fpin, filesize('views/templates/'.$_SESSION['plantilla']));
			fclose($fpin);

			$html = preg_replace('/--Contenido--/', $contenido, $html);
			$html = preg_replace('/--Menu--/', hacerMenu(), $html);

			$fpout = fopen('../'.$_SESSION['destino'],'w');
			fwrite($fpout, $html);
			fclose($fpout);

			echo "P&aacute;gina guardada correctamente!!";
		}

		$dbconn::desconectar($mysqli);
	}
?>