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
			case 'UPDATE_sistema':
					if($_POST['todo'] == "borrar"){
						try {
							SQL("TRUNCATE TABLE registro");
							SQL("TRUNCATE TABLE atendidos");
								
							echo 1;
						} catch (Exception $e) {
							echo 0;
						}
					}

					if($_POST['todo'] == "reestablecer"){
						try {
							 SQL("TRUNCATE TABLE registro");
							 SQL("TRUNCATE TABLE cliente");
							 SQL("TRUNCATE TABLE modulo");
							 SQL("TRUNCATE TABLE proceso");
							 SQL("TRUNCATE TABLE atendidos");
							 SQL("UPDATE config SET Activo = 0");
							echo 1;
						} catch (Exception $e) {
							echo 0;
						}
					}
				return 0;
			break;
			//Regresa los labels y cantidad de usuarios por hora
			case 'GET_line_chart':
				$rows = getData("SELECT Inicio FROM registro");
				$count = rowCount($rows);
				$c = 0;
				$index = 0;
				if($count){
					$y = (substr($rows[0]['Inicio'], 0, 2))*1;
					$data = array();
					for ($i=0; $i < $count ; $i++) { 
						$x = substr($rows[$i]['Inicio'], 0, 2);
						
						if($x != $y){
							if($y < 12)
								$y = $y." AM";
							else 
								$y = ($y-12)." PM";
							$data[$index]['label'] = $y;
							$data[$index]['data']  = $c;
							$index++;
							$c=0;
						} 
							$c++;
						
						
						$y = $x*1;
					}
					if($y < 12)
						$y = $y." AM";
					else 
						$y = ($y-12)." PM";
					$data[$index]['label'] = $y;
					$data[$index]['data']  = $c;
					$json['data']   = $data;	
					$json['_count'] = $index+1;			
					echo json_encode($json); 
					
				} else {
					echo 0;
				}
				return 0;
			break;
			case 'GET_general': 
				$json=[];
				$firstTime = getData("SELECT MIN(Inicio) As Inicio FROM registro");
				$json['Tiempo'] =  cnvSeconds(cnvTimeToSeconds(date("H:i:s")) - cnvTimeToSeconds($firstTime[0]['Inicio']));
				$t = getData("SELECT 
								(SELECT count(id) FROM cliente) AS count,
								(SELECT count(id) FROM registro WHERE Estado != 'Completado!') AS enProceso,
								(SELECT count(id) FROM registro WHERE Estado = 'Completado!') AS completado
								
							");
				$pr = getData("SELECT Tiempo FROM atendidos");
				$ts  = 0;
				for ($i=0; $i < rowCount($pr) ; $i++) { 
						$ts = $ts + cnvTimeToSeconds($pr[$i]['Tiempo']);
					}	



				$json['totalUsuarios'] = $t[0]['count']; 
				$json['enProceso']     = $t[0]['enProceso'];
				$json['completado']    = $t[0]['completado'];
				$json['promedio']      = cnvSeconds(ceil($ts/rowCount($pr)));
				
				echo json_encode($json);
					/*$rows = getData("SELECT ")*/
			break;
			case 'GET_modulos':
				$data = getData("SELECT (SELECT count(id) FROM modulo) AS count, id, activo, Nombre, Encargado, (SELECT count(id) FROM atendidos WHERE id_modulo = modulo.id) AS total 
					FROM modulo");
				$pr= "";
				for($i = 0; $i < rowCount($data); $i++){ 
					$pr = getData("SELECT Tiempo FROM atendidos WHERE id_modulo = ".$data[$i]['id']);
					$ts  = 0;
					for ($a=0; $a < rowCount($pr) ; $a++) { 
							$ts = $ts + cnvTimeToSeconds($pr[$a]['Tiempo']);
					}
					$json[$i]['count'] = $data[$i]['count'];
					$json[$i]['id'] = $data[$i]['id'];
					$json[$i]['Nombre'] = $data[$i]['Nombre'];
					$json[$i]['Encargado'] = $data[$i]['Encargado'];
					$json[$i]['total'] = $data[$i]['total'];
					$json[$i]['activo'] = $data[$i]['activo'];
					if($ts > 0)
						$json[$i]['promedio'] = cnvSeconds(ceil($ts/rowCount($pr)));	
					else 
						$json[$i]['promedio'] = 0;
						
				}		

				echo json_encode($json);
				return 0;
			break;
			default:
				# code...
				break;
		}
	}
?>