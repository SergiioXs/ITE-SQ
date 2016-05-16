<?php 

include 'conexion.php';
include 'LDS.php';

$cant = rowCount(getData("SELECT id FROM modulo"));

for ($i=0; $i < $cant ; $i++) { 
	
	try {
		
		SQL("UPDATE modulo
			SET 
				Nombre      = '".$_POST['Nombre'][$i]."',
				Encargado   = '".$_POST['Encargado'][$i]."',
				Serial      =  ".$_POST['Serial'][$i]."
			WHERE id = ".$_POST['id'][$i]);

		SQL("UPDATE modulo SET Nombre = '".$_POST['Nombre'][$i]."' WHERE Serial = ".$_POST['Serial'][$i]);	
	} catch (Exception $e) {
		echo $e;
	}
	
}


?>