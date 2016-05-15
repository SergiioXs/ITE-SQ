<?php 
/*
Lista de valores retornados

0 No existe en la base de datos
1 Todo esta bien y se registra
2 El proceso no tiene continuacion
3 Ya estaba registrado en la cola

*/
include 'conexion.php';
include 'LDS.php';

$id = $_POST["id"];

//Get Proceso
$proceso = $_POST['Proceso'];

//Get next module by process rule
$rule = getData("SELECT Rule FROM proceso WHERE id = ".$proceso);

if(rowCount($rule)){
	$NextModule = preg_split("[,]", $rule[0]['Rule']);
	if(isset($NextModule[1]))
		$NextModule = $NextModule[1];
	else {
		echo 2;
		return 0;
	}

}

//Saber si existe en la bd
$rows = getData("SELECT id FROM cliente WHERE id = ".$id." LIMIT 1");

//Si existe
if(rowCount($rows)){

	//Saber si ya esta registrado
	$rows = getData("SELECT Cliente_id FROM registro WHERE Cliente_id = ".$rows[0]['id']." LIMIT 1");
	
	//Si ya esta registrado
	if(rowCount($rows)){
		echo "3";
		return 0;

	//Si no esta registrado
	} else {
		$inicio = date("H:i:s");
		SQL("INSERT INTO registro (Cliente_id, Proceso, Inicio, Tiempo_Espera, Modulo_Siguiente) values (".$id.", '".$proceso."', '".$inicio."', '".$inicio."', $NextModule)");
		echo 1;
		return 0;
	}

//Si no existe
} else {
	echo 0;
	return 0;
}

?>