
<script type="text/javascript">
	$(".ui.dropdown")
		.dropdown()
;

	$("#verid").on('click', function () {
		dataString = "new=modulo"; 
		$.ajax({
           type: "POST",
           url: "php/createnew.php",
           data: dataString, 
           success: function(data)
           {
               $("#content").load("php/modulos.php");
           },
           error: function(datas){
           		$('#logmsg').html("No se pudo agregar un nuevo proceso, al parecer algo anda mal en el sistema");
           }
         });
	});


	
	$("#guardarcambios").on('click', function () {
			$("#loader").html("<div class='ui active inverted dimmer'><div class='ui medium text loader'>Guardando sus preferencias</div></div>");

    $.ajax({
           type: "POST",
           url: "php/managemodulos.php",
           data: $("#data").serialize(), // Adjuntar los campos del formulario enviado.
           success: function(data)
           {
               $('#content').load('php/modulos.php');
           }
         });

	
	});

	$("#serialput").keyup(function () {
		alert("algo");
		
	});

</script>
<?php
include 'conexion.php';
include 'LDS.php';

if(isset($_GET['Filtro']))
	$filtro = $_GET['Filtro'];
else
	$filtro = "";

$rows = getData("SELECT id, Serial, Encargado, Nombre, Proceso, Dependencia, Usuario, Contrasena, Activo 
				FROM modulo
				WHERE Nombre
				LIKE '%$filtro%'
				ORDER BY id 
				ASC
				
				");

$proceso = getData("SELECT id, Nombre FROM Proceso");
	$pros = "";
	$modules = "";

for ($a=0; $a < rowCount($rows); $a++) { 
	$modules .= "<div class='item' data-value='".$rows[$a]['id']."'>".$rows[$a]['Nombre']."</div>";
}

for ($a=0; $a < rowCount($proceso); $a++) { 
	$pros .= "<div class='item' data-value='".$proceso[$a]['id']."'>".$proceso[$a]['Nombre']."</div>";
}

if(!rowCount($rows)){
	echo "<div class='ui warning message'>
			<div class='header'>Sin resultados!</div> No hay modulos que mostrar.</div>
		  </div>";
} else {

?>
<div id='loader'></div>

<button class="ui teal icon button" id='verid'>
  <i class="add user icon"></i>
  Nuevo modulo
</button>

<button class="ui icon button" id='guardarcambios'>
  <i class="save icon"></i>
  Guardar cambios
</button>

<form method='POST' action='' id='data' style='margin-top: 10px;'>
	<table class="ui compact celled table">
		<thead>
		<tr>
		  <th>Serial</th>
		  <th>Nombre</th>
		  <th>Encargado</th>
		  <th>Dependencia</th>
		  <th>Proceso</th>
		  <th>Estado</th>
		</tr>
		</thead>
		<tbody>
		
		<?php 
		
		for ($i=0; rowCount($rows) > $i ; $i++) { 
			if($rows[$i]["Dependencia"] == 0){
				$depname = "Usuario";
			} else
				$depname = $rows[($rows[$i]["Dependencia"])-1]["Nombre"];
			if($i == 0)
				$disabled = "disabled";
			else
				$disabled = "";
			if($rows[$i]['Activo']){
				$Status = "Activo";
				$color = "green";
			} else {
				$Status = "Inactivo";
				$color = "red";
			}

			echo "
			
			<tr>
				<input type='hidden' value='".$rows[$i]['id']."' name='id[]'></input>
				<td>
					<div class='ui $disabled transparent input' style='width: 40px;'>
					  <input type='number' value='".$rows[$i]["Serial"]."' id='serialput' min='2' name='Serial[]'>
					</div>
				</td>
				<td>
					<div class='ui transparent $disabled input' style='width: 100%;'>
					  <input type='text' value='".$rows[$i]["Nombre"]."' name='Nombre[]'>
					</div>
				</td>
				<td>
					<div class='ui transparent input' style='width: 100%;'>
					  <input type='text' value='".$rows[$i]["Encargado"]."' name='Encargado[]'>
					</div>
				</td>
				<td>";
				$DependencyArray = preg_split("[,]", $rows[$i]['Dependencia']);
			
				$DependencyArrayLength = sizeof($DependencyArray); 
					for($a = 0; $a < $DependencyArrayLength; $a++){

						if($DependencyArray[$a] != 0 ) {
							$name = getData("SELECT Nombre FROM modulo WHERE serial = ".$DependencyArray[$a]);
							echo "<label class='ui label' style='margin-top: 5px;'>".$name[0]['Nombre']."</label>"; 
						}
					}
				
					
				echo "
				</td>
				<td>";
				$ProcessArray = preg_split("[,]", $rows[$i]['Proceso']);
				$ArrayLength = sizeof($ProcessArray); 
					for($a = 0; $a < $ArrayLength; $a++)
						if($ProcessArray[$a] != 0)
							echo "<label class='ui label' style='margin-top: 5px;'>".$proceso[$ProcessArray[$a]-1]['Nombre']."</label>"; 
				
					
				echo "
				</td>
				<td><label class='ui $color label'>$Status</label></td>
			</tr>
			";
		}

		?>
		</tbody>
	</table>
</form>
<?php 

}

?>