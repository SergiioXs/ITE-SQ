<?php
	include 'conexion.php';
	include 'LDS.php';

	if(isset($_POST['init'])){
		switch ($_POST['init']) {
			case 'start':
				try {
					$rows = getData("SELECT * FROM config");
					echo json_encode($rows[0]);	
				} catch (Exception $e) {
					echo 0;
				}
				return 0;
			break;
			
			case 'UPDATE_empresa':
				$expr = "/^[a-zA-Z0-9áéíóñÑ\_\-\.]{3}[a-zA-Z0-9áéíóñÑ\_\-\. ]+$/";
				$nombre = validate($expr, $_POST['nombre']);
				$slogan = validate($expr, $_POST['slogan']);
				if($nombre && $slogan){  
					try {
						SQL("UPDATE config SET Nombre = '$nombre', Slogan = '$slogan'");
						echo 1;	
					} catch (Exception $e) {
						echo 0;
					}
				} else {
					echo 0;
				}
				
				return 0;
			break;
			
			default:
				# code...
				break;
		}
	}
?>