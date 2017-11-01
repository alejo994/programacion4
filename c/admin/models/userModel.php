<?php 
	require_once "../db/conexion.php";

	class userModel
	{
		private $db;
		private $usuarios;

		public function __construct()
		{
			$this->db=baseDatos::conectar();
			$this->usuarios="";
		}

		public function getAccess($user,$pass)
		{
			$query = mysqli_query($this->db,"select idusuario, nombre, clave from usuario where usuario = '$user' and estado = 'A'");

			if($query == FALSE)
				$this->usuarios="Sentencia incorrecta llamando a la tabla: usuarios";
			else
			{
				$fila = mysqli_fetch_assoc($query);

				if($pass == $fila['clave'])
				{
					session_start();

					$_SESSION['logueo'] = 1;
					$_SESSION['idusuario'] = $fila['idusuario'];
					$_SESSION['nombre'] = $fila['nombre'];
					$_SESSION['usuario'] = $user;

					$this->usuarios="Bienvenid@";
				}
				else
				{
					$this->usuarios="Credenciales incorrectas, favor verifique usuario y password";
				}
			}

			mysqli_free_result($query);

			$this->db=basedatos::desconectar($this->db);

			return $this->usuarios;
		}

	}
?>