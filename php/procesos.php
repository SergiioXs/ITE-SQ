
<script type="text/javascript">

function showFluxogram(x){
	$('#modal'+x).modal('show');
}

	$(".ui.dropdown")
		.dropdown()
;


	
	$("#guardarcambios").on('click', function () {
			$("#loader").html("<div class='ui active inverted dimmer'><div class='ui medium text loader'>Guardando sus preferencias</div></div>");

   		$.ajax({
           type: "POST",
           url: "php/manageprocesos.php",
           data: $("#data").serialize(), // Adjuntar los campos del formulario enviado.
           success: function(data)
           {
           	
            $('#content').load('php/procesos.php');
           },
           error: function(data){
           	
           }
         });

	
	});

	$("#newproceso").click(function () {
		dataString = "new=proceso"; 
		$.ajax({
           type: "POST",
           url: "php/createnew.php",
           data: dataString, 
           success: function(data)
           {
               $("#content").load("php/procesos.php");
           },
           error: function(datas){
           		$('#logmsg').html("No se pudo agregar un nuevo proceso, al parecer algo anda mal en el sistema");
           }
         });
	});

</script>
<?php
include 'conexion.php';
include 'LDS.php';

$rows = getData("SELECT id, Nombre, Rule  
				FROM proceso
				ORDER BY id 
				ASC
				");

$modulos = getData("SELECT DISTINCT Serial, Nombre FROM modulo WHERE Serial > 1");
	$pros = "";

for ($a=0; $a < rowCount($modulos); $a++) { 

		$pros .= "<div class='item' data-value='".$modulos[$a]['Serial']."'>".$modulos[$a]['Nombre']."</div>";
}

if(!rowCount($rows)){
	echo "<div class='ui warning message'>
			<div class='header'>Sin resultados!</div> No hay modulos que mostrar.</div>
		  </div>";
} else {

?>
<div id='loader'></div>

<button class="ui teal icon button" id='newproceso'>
  <i class="add user icon"></i>
  Nuevo Proceso
</button>

<button class="ui icon button" id='guardarcambios'>
  <i class="save icon"></i>
  Guardar cambios
</button>
<div class='ui message' id='logmsg'> 
	Seleccione el orden de los modulos en que se deben de realizar para llevar acabo el proceso. El modulo de registro viene por default como primero modulo.
</div>
<form method='POST' action='' id='data' style='margin-top: 10px;'>
	<table class="ui compact celled table">
		<thead>
		<tr>
		  <th>Nombre</th>
		  <th>Servicios/Modulos</th>
		  <th>Diagrama de flujo</th>
		</tr>
		</thead>
		<tbody>
		
		<?php 
		
		for ($i=0; rowCount($rows) > $i ; $i++) { 

		?>
		<input type='hidden' <?php echo "value='".$rows[$i]['id']."'"; ?> name='id[]' ></input>
			<tr>
				<td>
					<div class='ui transparent input' style='width: 100%;'>
					  <input type='text' <?php echo "value='".$rows[$i]["Nombre"]."'"; ?> name='Nombre[]'>
					</div>

				</td>
				<td>
					<label style="">
						<label class="ui label" style="margin-top: 5px; position: absolute;">Registro</label>
					</label>
					<div class='ui multiple selection dropdown' style="margin-left: 70px;">
					    <input type='hidden' name='Rule[]' value=<?php echo $rows[$i]["Rule"]; ?>>
					    <i class='dropdown icon'></i>
					    <div class='default text'>Seleccione el orden en que deben de ir los modulos</div>
					  	<div class='menu'>
					    	<?php echo $pros; ?>
						</div>
					</div>
				</td>
				<td>										
					<label class='ui fluid button' id='btn' <?php echo "onClick='showFluxogram(".$i.");'"; ?> >Ver</label>
				</td>
			</tr>

<div class='ui basic modal' <?php echo "id='modal".$i."'"; ?> >
  <i class='close icon'></i>
  <div class='header'>
    Diagrama de flujo de <?php echo $rows[$i]["Nombre"]; ?>
  </div>

    <div class='description'>
      <div>
      	<center>
      		<br>
			<div class='ui huge circular labels'>
			  <label class='ui basic label'>
			    Inicio
			  </label>
			</div>
			<?php
				$RuleArray = preg_split("[,]", $rows[$i]['Rule']);
				$ArrayLength = sizeof($RuleArray); 

				for($z=0; $z<$ArrayLength; $z++){

			?>

					<label class='ui pointing basic label'>
						<h3 class='ui header'>
						  <?php 
						  	if($RuleArray[$z]){
						  		$name = getData("SELECT Nombre FROM modulo WHERE Serial = ".$RuleArray[$z]);
						  		if(rowCount($name)){
									echo $name[0]['Nombre'];
						  		}
						  	} else {
						  		echo "No hay modulos";
						  	}
						  	 
						  ?>
						  <div class='sub header'>
						  	<!-- Seleccionar los nombres de los modulos -->
						  	<?php
						  		if($RuleArray[$z]){
									$Encargado = getData("SELECT Encargado FROM modulo WHERE Serial = ".$RuleArray[$z]);
							  		for($c = 0; $c < rowCount($Encargado); $c++){
							  				echo $Encargado[$c]['Encargado'].", ";

							  		}
								} else {
									echo "";
								}
						  		
						  	?>
						  </div>						
						</h3>
					</label>
					<br>

			<?php
				}
			?>
			
			<div class='ui huge circular labels'><br>
			    <label class='ui red pointing basic label'>
			   		Fin
			  	</label>
			</div>
		</center>
	   </div>
    </div>
</div>
		<?php
		}

		?>
		</tbody>
	</table>

	<div id='log'></div>
</form>
<?php 

}

?>



