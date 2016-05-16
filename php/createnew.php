<?php
	include 'conexion.php';
	include 'LDS.php';

	if(isset($_POST['new'])){
		switch ($_POST['new']) {
			case 'proceso':
				try {
					SQL("INSERT INTO proceso (Nombre) VALUES ('Sin nombre')");
					echo 1;	
				} catch (Exception $e) {
					echo 0;
				}
				return 0;
				break;

			case 'modulo':
				try {
					$id = getData("SELECT MAX(id)+1 AS ms FROM modulo");
					$serial = getData("SELECT MAX(Serial)+1 AS Serial FROM modulo");
					SQL("INSERT INTO modulo (Serial, Nombre, Dependencia, Usuario, Contrasena, Activo) 
						VALUES (
							".$serial[0]['Serial'].", 
							'Sin nombre', 
							'', 
							'Modulo".$id[0]['ms']."', 
							'Modulo".$id[0]['ms']."', 
							0)
						");
					echo 1;	
				} catch (Exception $e) {
					echo $e;
				}
				return 0;
			break;
			
			default:
				# code...
				break;
		}
	}
?>