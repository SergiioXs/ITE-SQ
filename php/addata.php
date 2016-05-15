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
			
			default:
				# code...
				break;
		}
	}
?>