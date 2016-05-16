<?php
require "conexion.php";
require "LDS.php";
//obtenemos el archivo .csv

$i=0;
$reg=0;
if(isset($_FILES['archivo'])){
	$lineas = file($_FILES['archivo']['tmp_name']);
}

$procesos = explode(",", $_POST["procesos"]);
$modulos  = $_POST["modulos"];

function truncateALL(){
	SQL("TRUNCATE TABLE registro");
	SQL("TRUNCATE TABLE cliente");
	SQL("TRUNCATE TABLE modulo");
	SQL("TRUNCATE TABLE proceso");
	SQL("TRUNCATE TABLE atendidos");
}

truncateALL();

if(isset($_FILES['archivo'])){
	//Recorremos un bucle para leer línea por línea
	foreach ($lineas as $linea_num => $linea) {
	/*si es diferente a 0 significa que no se encuentra en la primera línea (con los títulos de las columnas) y por lo tanto puede leerla*/

			//La funcion explode nos ayuda a delimitar los campos, por lo tanto irá leyendo hasta que encuentre un ;
			$datos = explode(',',$linea);

			//Almacenamos los datos que vamos leyendo en una variable
			
			if(trim($datos[0]) != "" || trim($datos[1]) != "" || trim($datos[2]) != "")
				try {
					SQL("INSERT INTO cliente(id,Nombre,Carrera) VALUES(".utf8_encode(trim($datos[0])).", '".utf8_encode(trim($datos[1]))."', '".utf8_encode(trim($datos[2]))."')");	
					$reg++;
				} catch (Exception $e) {
					
					truncateALL();
					Echo "<br><div class='ui negative message'><div class='header'>Error en la base de datos!</div> Al parecer algo anda mal en la linea ".($i+1).", por lo tanto el proceso se a detenido. Vuelve a seleccionar la base de datos e intentalo de nuevo</div> ";
					return 0;
				}

	$i++;
	}
}
		try {

			for($i = 0; count($procesos) > $i; $i++)
				SQL("INSERT INTO proceso (Nombre) VALUES ('".$procesos[$i]."')");

		} catch (Exception $e) { 
			
			truncateALL();
			Echo "<br><div class='ui negative message'><div class='header'>Error al guardar los procesos!</div> Al parecer algo anda mal con el proceso <".$procesos[$i].">, por lo tanto el proceso se a detenido. </div> ";
			return 0;
		}

		try {

			SQL("INSERT INTO modulo (Serial, Nombre, Dependencia, Usuario, Contrasena, Activo) VALUES(1, 'Registro', 'Usuario', 'Registro', 'itecontrol', 1 )");
			for($i = 0; $modulos > $i; $i++)
				SQL("INSERT INTO modulo (Serial, Nombre, Dependencia, Usuario, Contrasena, Activo) VALUES(".($i+2).",'Modulo".($i+1)."', 'x', 'Modulo".($i+1)."', 'Modulo".($i+1)."', 0 )");	
			
		} catch (Exception $e) { 

			truncateALL();
			Echo "<br><div class='ui negative message'><div class='header'>Error al guardar los modulos!</div> Al parecer algo anda mal en el sistema. </div> ";
			return 0;
		}
		SQL("UPDATE config SET Activo = 1");
		Echo "
			<div class='ui positive message'>
				<div class='header'>
					Se ha agregado toda la informacion con exito!
				</div> 
				Se encontraron ".($reg)." registros en la base de datos.
				<br>
				¿Que sigue?
				<br>
				<ul>
					<li>Configurar los modulos.</li>
					<li>Configurar los procesos.</li>
					<li>Recuerda que puedes configurar el sistema en la pestaña de 'Configuraciones'</li>
					<li>Manos a la obra, da click en 'continuar' para acceder a las nuevas pestañas!</li>
				</ul>


			</div> ";

?>
