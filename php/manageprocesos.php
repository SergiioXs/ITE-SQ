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
		echo $_POST['Rule'][$i]." / ";
		try {

			SQL("UPDATE proceso
			SET 
				Nombre = '".$_POST['Nombre'][$i]."',
				Rule = '".$_POST['Rule'][$i]."'
			WHERE id = ".$_POST['id'][$i]."
			");
			
			$RuleArray = preg_split("[,]", $_POST['Rule'][$i]);
			$ArrayLength = sizeof($RuleArray); 
			$val = "";		
			//Recorre toda la regla
			for($a = 0 ; $a < $ArrayLength; $a++){
				if($i == 0)
					SQL("UPDATE modulo SET Proceso = '".$_POST['id'][$i]."' WHERE Serial = ".$RuleArray[$a]);
				
				if($i > 0)
					SQL("UPDATE modulo SET Proceso = CONCAT(Proceso, ',".$_POST['id'][$i]."') WHERE Serial = ".$RuleArray[$a]);	

				if($a == 0){
					SQL("UPDATE modulo SET Dependencia = 'Usuario' WHERE Serial = 1");
				}
					
				if($a > 0){
					$module = getData("SELECT Serial FROM modulo WHERE Serial = ".$RuleArray[$a]);
					
					if($i > 0)
						SQL("UPDATE modulo SET Dependencia = CONCAT(Dependencia, '".$val."') WHERE Serial = ".$module[0]['Serial']);
					else 
						SQL("UPDATE modulo SET Dependencia = CONCAT(Dependencia, '".$val.",') WHERE Serial = ".$module[0]['Serial']);
					
				}	
				$val = $RuleArray[$a];

			}



		} catch (Exception $e) {
			echo $e;
		}
	
	}
}



?>

