<?php 

include 'conexion.php';
include 'LDS.php';


$cant = rowCount(getData("SELECT id FROM proceso"));
if($cant){
	SQL("UPDATE modulo SET Proceso = '', Dependencia = '';");
	

	for ($i=0; $i < $cant ; $i++) { 

		if(!isset($_POST['Rule'][$i][0]))
			$_POST['Rule'][$i] = "1";
		else {
			if($_POST['Rule'][$i][0] != "1")
				$_POST['Rule'][$i] = "1,".$_POST['Rule'][$i];
		}
		
		try {

			//Agregamos los datos de los procesos
			SQL("UPDATE proceso
			SET 
				Nombre = '".$_POST['Nombre'][$i]."',
				Rule = '".$_POST['Rule'][$i]."'
			WHERE id = ".$_POST['id'][$i]."
			");
			
			//Obtenemos los datos de la regla del proceso en curso en un arreglo sin comas
			$RuleArray = preg_split("[,]", $_POST['Rule'][$i]);
			$ArrayLength = sizeof($RuleArray); 
			$val = "";		

			//Recorre toda la regla
			for($a = 0 ; $a < $ArrayLength; $a++){
				if($i == 0) //Fijamos como inicio el proceso donde el serial sea el primero de la regla 
					SQL("UPDATE modulo SET Proceso = '".$_POST['id'][$i]."' WHERE Serial = ".$RuleArray[$a]);
				
				if($i > 0) //Concatenamos si 
					SQL("UPDATE modulo SET Proceso = CONCAT(Proceso, ',".$_POST['id'][$i]."') WHERE Serial = ".$RuleArray[$a]);	

				if($a == 0){
					SQL("UPDATE modulo SET Dependencia = 'Usuario' WHERE Serial = 1");
				}
					
				if($a > 0){
					$match = getData("SELECT serial FROM modulo WHERE Dependencia LIKE '%$val,%' AND Serial = ".$RuleArray[$a]);
					if(!rowCount($match))
						SQL("UPDATE modulo SET Dependencia = CONCAT(Dependencia, '".$val.",') WHERE Serial = ".$RuleArray[$a]);
					
				}	
				$val = $RuleArray[$a];

			}



		} catch (Exception $e) {
			echo $e;
		}
	
	}
}



?>

