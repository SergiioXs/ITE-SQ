<script type="text/javascript" src="js/jquery.filtertable.min.js"></script>
<script type="text/javascript" src='js/registro.js'></script>

<?php
include 'conexion.php';
include 'LDS.php';

if(isset($_GET['Filtro']))
	$filtro = $_GET['Filtro'];
else
	$filtro = "";

$rows = getData("SELECT registro.id, registro.Proceso, registro.Estado, cliente.Nombre, cliente.Carrera, registro.Inicio, (SELECT Nombre FROM proceso WHERE id = registro.Proceso) AS Np
				FROM registro
				INNER JOIN cliente
				ON registro.cliente_id = cliente.id
				WHERE 
					registro.Estado = 'En espera' AND 
					registro.Ultimo_Modulo = 1    AND 
					cliente.Nombre
				LIKE '%$filtro%'
				ORDER BY registro.Inicio 
				DESC
				
				");
$totalRegistrados = getData("SELECT 
	(SELECT count(id) FROM registro              ) AS Total,
	(SELECT count(id) FROM registro WHERE Estado = 'Completado!') AS Completado, 
	(SELECT count(id) FROM registro WHERE Estado = 'En espera' AND Ultimo_Modulo = 1) AS Esperando, 
	(SELECT count(id) FROM registro WHERE (Ultimo_Modulo != 1 AND Estado != 'Completado!')  ) AS Activo 

	FROM registro");

if(!rowCount($totalRegistrados)){
	$totalRegistrados[0]["Total"] = 0;
	$totalRegistrados[0]["Completado"] = 0;
	$totalRegistrados[0]["Esperando"] = 0;
	$totalRegistrados[0]["Activo"] = 0;
}

$p = "";
$Rules = []; 
$procesos = getData("SELECT Nombre, id, Rule FROM proceso");
for ($i=0; $i < rowCount($procesos) ; $i++) {
	$var = (preg_split("[,]", $procesos[$i]['Rule']));
	if(isset($var[1])) 
		$Rules[$i] = $var[1]; 
	$p .= "<option value='".$procesos[$i]['id']."'>".$procesos[$i]['Nombre']."</option>";
}

//Sacar promedio por primeros modulos
for ($i=0; $i < sizeof($Rules); $i++) { 
	$ModList = getData("SELECT Tiempo FROM atendidos WHERE Serial = ".$Rules[$i]);
	$TiempoPromedio = 0;
	for($b=0;$b<rowCount($ModList);$b++){
		$TiempoPromedio += SumTime($ModList[$b]['Tiempo']);
	}
	if($TiempoPromedio){
		$TiempoPromedio = ceil($TiempoPromedio / rowCount($ModList));
	}

	$Promedios[$procesos[$i]['id']] = convertSecond($TiempoPromedio);
	$X[$procesos[$i]['id']] = 1;
}

function sumTime($x){
	$Time = (preg_split("[:]", $x));
	$h = $Time[0] * 3600;
	$m = $Time[1] * 60;
	$s = $Time[2];
	//Return wholeSeconds
	return $h+$m+$s;
}

function convertSecond($x){
	//Dar formato TIME
		$s = $x;
		$h = 0;
		$m = 0;

		if($s > 59){
			$mP = intval($s / 60);
			$s = $s - ($mP*60);
			$m = $m + $mP;
		}

		if($m > 59){
			$hP = intval($m / 60);
			$m = $m - ($hP*60);
			$h = $h + $hP;
		}

		if($s < 10)
			$s = "0".$s;
			
		if($m < 10)
			$m = "0".$m;

		if($h < 10)
			$h = "0".$h;

		return $h.":".$m.":".$s;
}



?>

	<i class="circular inverted grey refresh icon" style='cursor: pointer;' id='refresh'></i>

	<div class="ui action input">
	  <input type="number" placeholder="Identificador" id='iddb'>
	  <select class="ui compact selection dropdown" id='procesodb'>
	    <option value="" selected>Proceso</option>
	    <?php echo $p; ?>
	  </select>
	  	<div type="submit" class="ui teal icon button" id='asignarmodal'>
	  		<i class='plus icon'></i>
	  		Agregar
	  	</div>
		<label id='newuser' class="ui blue button">
		  <i class="add user icon"></i>
			Nuevo
		</label>
		<label class="ui violet button">
		  <i class="mobile icon"></i>
		  Pre-registro
		</label>	
	  
	</div>




<div id='msgmodal' style='margin-top: 15px;'>
	<div class='ui message'>
		<center>
			<div class="ui equal width grid">
			  <div class="column">
				  <div class="statistic">
				    <div class="value">
				      <i class="users icon"></i> <?php echo $totalRegistrados[0]['Esperando']; ?>
				    </div>
				    <div class="label">
				      <b>En espera</b>
				    </div>
				  </div>
			  </div>
			  <div class="column">
				  <div class="statistic">
				    <div class="value">
				      <i class="male icon"></i> <?php echo $totalRegistrados[0]['Activo']; ?>
				    </div>
				    <div class="label">
				      <b>En proceso</b>
				    </div>
				  </div>
			  </div>
			  <div class="column">
				  <div class="statistic">
				    <div class="value">
				      <i class="child icon"></i> <?php echo $totalRegistrados[0]['Completado']; ?>
				    </div>
				    <div class="label">
				      <b>Completados</b>
				    </div>
				  </div>
			  </div>
			  <div class="column">
				  <div class="statistic">
				    <div class="value">
				      <i class="world icon"></i> <?php echo $totalRegistrados[0]['Total']; ?>
				    </div>
				    <div class="label">
				      <b>Total registrados</b>
				    </div>
				  </div>
			  </div>
			</div>
		</center>	
	</div>
</div>
<?php
if(!rowCount($rows)){
	echo "
		<div id='msgmodal' style='margin-top: 15px;'>
			<div class='ui warning message' >
				<div class='header'>Sin registros!</div> No hay usuarios en cola.</div>
			  </div>
		</div>";
} else {

?>





<table class="ui selectable compact celled table" id='tabla'>
	<thead>
	<tr>
	  <th>Turno</th>
	  <th>Nombre</th>
	  <th>Carrera</th>
	  <th>Proceso</th>
	  <th>Tiempo para iniciar</th>
	</tr>
	</thead>
	<tbody>
	
	<?php 
	
	for ($i=0; rowCount($rows) > $i ; $i++) { 
		echo "
		<tr>
			<td>#".$rows[$i]["id"]."</td>
			<td>".$rows[$i]["Nombre"]."</td>
			<td>".$rows[$i]["Carrera"]."</td>
			<td>".$rows[$i]["Np"]."</td>
			<td>".MultTime($Promedios[$rows[$i]['Proceso']], $X[$rows[$i]['Proceso']])."</td>
		</tr>
		";
		$X[$rows[$i]['Proceso']]++;
	}

	?>
	</tbody>
</table>

<?php 

}

function MultTime($Time, $x){
	$R = sumTime($Time) * $x;
	return convertSecond($R);
}

?>


