<?php
	require "LDS.php";
	//date_default_timezone_set('America/Los_Angeles');
	if(isset($_POST)){
		$id=$_POST['id']; 
		switch ($_POST['Action']) {
			case 'All':
					
				$result = getData("SELECT * FROM modulo where id = ".$_POST['id']);
				echo json_encode($result);
				return 0;
				break;
			# Estar activo o inactivo
			case 'Active':

				$Activo = getData("SELECT Activo FROM modulo WHERE id = ".$id);
				if($Activo[0]['Activo'] == 1){
					SQL("UPDATE modulo SET Activo = 0 WHERE id = ".$id);
					echo 0;
					return 0;
				}else{
					SQL("UPDATE modulo SET Activo = 1 WHERE id = ".$id);
					echo 1;
					return 0;
				}
				return 0;
				break;

			case 'Atendiendo':

				$result = getData("SELECT Busy, Rest, Cliente, Time_Start, Nombre, Dependencia, Proceso FROM modulo WHERE id = ".$id);
				echo json_encode($result);

				return 0;
				break;

			case 'Busy':
				$Busy = getData("SELECT Busy FROM modulo WHERE id=".$id);
				$result = $Busy[0]['Busy'];
					if($result == 1){
						sQL("UPDATE modulo SET Busy = 2 WHERE id = ".$id);
					}
				echo $Busy[0]['Busy'];
				return 0;	
				break;

			case 'forzar':
					$cliente= trim($_POST['id_cliente']);
					$descanso = date("H:i:s");
					

					try {
					//Regresar al modulo en descanso e inactivo	
						SQL("UPDATE modulo 
								SET
									Activo      = 0,
									Busy        = 0,
									Rest        = '$descanso',
									Cliente     = 0,
									Time_Start  = '00:00:00'
								WHERE id = $id;
						");
					} catch (Exception $e) {
						echo $e;
					}
					try {
					//Sacar al cliente de atendiendo a en espera	
						SQL("UPDATE registro 
								SET Estado = 'En espera' 
							WHERE Cliente_id = $cliente;
							");
						
					} catch (Exception $e) {
						echo $e;
					}
					
					return 0;
					break;

			case 'Cliente':
				$cliente = getData("SELECT * FROM cliente WHERE id = ".$id);
				echo json_encode($cliente);
				return 0;
				break;
			case 'Siguiente':
				try {
						SQL("INSERT INTO atendidos (id_modulo, id_cliente, Tiempo, Serial) values (".$id.", ".$_POST['id_cliente'].", '".$_POST['ResultTime']."', ".$_POST['serial'].")");
						$descanso = date("H:i:s");
						
						//get next module
						$proceso = getData("SELECT Proceso FROM registro WHERE Cliente_id = ".$_POST['id_cliente']);
						$NextModule = getData("SELECT Rule FROM proceso WHERE id = ".$proceso[0]['Proceso']);
						$Rule = preg_split("[,]", $NextModule[0]['Rule']);
						$found = false;

						for($i=0; $i < sizeof($Rule); $i++){
							if($Rule[$i] == $_POST['serial']){
								if($Rule[$i+1]){
									$NextModule = $Rule[$i+1];
									$found = true;
								}
							}
						}
						$estado = "";
						if($found){
							$estado = "En espera";
						} else {
							$estado = "Completado!";
							$NextModule = 0;
						}

						SQL("UPDATE registro 
								SET 
									Estado = '".$estado."', 
									Ultimo_Modulo = ".$_POST['serial'].", 
									Modulo_Siguiente = ".$NextModule.", 
									Tiempo_Espera = '".$descanso."' 
								WHERE 
									Cliente_id = ".$_POST['id_cliente']
							);
						
						SQL("UPDATE modulo SET Busy = 0, Rest = '$descanso', Cliente = 0, Time_Start = '00:00:00' WHERE id = ".$id);		
						echo 1;
					} catch (Exception $e) {
						echo 0;
					}	
				
				return 0;
				break;
			case 'historial':
			
				try {
					$historial = getData("SELECT (SELECT count(id) FROM atendidos WHERE id_modulo = $id) AS conteo, cliente.Nombre AS Nombre, cliente.Carrera AS Carrera, cliente.id AS id, atendidos.Tiempo AS Tiempo 
											FROM atendidos 
											INNER JOIN cliente 
											ON atendidos.id_cliente = cliente.id
											WHERE atendidos.id_modulo = $id
											ORDER BY atendidos.id
											DESC
											"
										);
					echo json_encode($historial);
				
				} catch (Exception $e) {
					echo 0;
				}
				return 0;
				break;

			case 'Search':
			try {
				$pros = ""; $dep = "";
				$Serial = $_POST['serial'];
				$Nombre = $_POST['Nombre'];
				//Procesos
				$procesos = preg_split("[,]", $_POST['procesos']);
				
				for($i=0; $i < sizeof($procesos); $i++){
					if(!$i)
						$pros = " Proceso = ".$procesos[$i];
					else
						$pros .= " OR Proceso = ".$procesos[$i];
				} 

				//Dependencia
				$dependencia = preg_split("[,]", $_POST['dependencia']);
				for ($i=0; $i < sizeof($dependencia); $i++) { 
					if(!$i)
						$dep = " Ultimo_Modulo = ".$dependencia[$i];
					else {	
						if(preg_match("/[0-9]+/", $dependencia[$i]))
							$dep .= " OR Ultimo_Modulo = ".$dependencia[$i];
					}
				}
				$sql="SELECT Cliente_id, id FROM registro 
													WHERE ($pros) AND ($dep) AND Modulo_Siguiente = $Serial AND (Estado = 'En espera')
													ORDER BY Tiempo_Espera ASC
													LIMIT 1
													";
				$search = getData($sql);

				if(rowCount($search)){
					$cid = $search[0]['id'];
					$clientid = $search[0]['Cliente_id'];
					SQL("UPDATE registro SET
							Estado = '".$Nombre."'
						WHERE id = $cid
							 ");
					$inicio = date("H:i:s");
					
					SQL("UPDATE modulo SET
							Cliente = $clientid,
							Busy = 1,
							Time_Start = '".$inicio."'
						WHERE id = $id
						");

					echo 1;
				} else {
					echo 0;
				}

				
			} catch (Exception $e) {

				echo "Error";
			}
				
				return 0;
				break;

			case 'Display':

				try {
					$modulos = preg_split("[,]", $_POST['Modulos']);
					$str= "";
					for ($i=0; $i < sizeof($modulos); $i++) { 
						$modulos[$i] = trim($modulos[$i]);	
						
						if($i==0){
							$str .= "modulo.Serial = ".$modulos[$i];
						} else {

							$str .= " OR modulo.Serial = ".$modulos[$i];
						}
					}

					$result = getData("SELECT modulo.Busy AS Busy, modulo.id AS id, modulo.Cliente AS Cliente, modulo.Nombre AS Modulo, cliente.Nombre AS Nombre
						FROM modulo 
						INNER JOIN cliente 
						ON cliente.id = modulo.Cliente 
						WHERE ($str) AND modulo.Busy = '1' 
						ORDER BY Time_Start ASC
						LIMIT 1");
					$turno = getData("SELECT id FROM registro WHERE Cliente_id = ".$result[0]['Cliente']." LIMIT 1");
					$result[0]['Turno'] = $turno[0]['id'];
					echo json_encode($result);	
				} catch (Exception $e) {
					echo "";
				}
				
				return 0;
				break;
			
			case 'getModulos':
				try {
					$rows = getData("SELECT Rule FROM proceso WHERE Rule != '' AND Rule != '1' ");
					if(rowCount($rows)){
						$String = "";
						for($i=0;$i<rowCount($rows);$i++){
						$modulos[$i] = preg_split("[,]", $rows[$i]['Rule']);
						$String .= $modulos[$i][1].",";	
						}

						$String = substr($String, 0, -1);
					} else {
						$String = null;
					}

					echo $String;
				} catch (Exception $e) {
					echo 0;
				}
				
				return 0;
				break;
				case 'setBusy':
					try {
						SQL("UPDATE modulo SET Busy = 2 WHERE id = ".$_POST['Modulo']);
						echo 1;
					} catch (Exception $e) {
						echo 0;
					}
					return 0;
				break;
				case 'addtodb':
					try {
						$rows = getData("SELECT id FROM cliente WHERE id = ".$_POST['identificador']);

						if(rowCount($rows)){
							echo 0;
							return 0;
						} else {
							try {
								SQL("INSERT INTO cliente (id,Nombre,Carrera) VALUES (".$_POST['identificador'].", '".$_POST['nombre']."', '".$_POST['carrera']."')");
								echo 1;
								return 0;
							} catch (Exception $e) {
								echo 2;
								return 0;
							}
							
							echo 1;
							return 0;
						}
					} catch (Exception $e) {
						echo 2;
						return 0;
					}
					
					return 0;
					break;
				case 'DeleteFromDb':
					$data = preg_split("[,]", $_POST['data']);
					$cant = sizeof($data);
					$info = "";
					for ($i=0; $i < $cant-1 ; $i++) { 
						if($i>0)
							$info .= ",";
						$info .= trim($data[$i]);
					}
					try {
						SQL("DELETE FROM cliente WHERE id IN (".$info.")");
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



	echo "error";

?>