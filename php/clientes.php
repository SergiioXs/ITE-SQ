<script type="text/javascript" src="js/jquery.filtertable.min.js"></script>
<script>
	$('table').filterTable({
    	inputSelector: '#buscar'
	});
	$("#newuser").on('click', function () {
		$("#msgmodal").html("<div class='ui segment' id='newsegment'><center><div style='width: 50%; margin-top: 5px;' class='ui left icon input'><input type='number' id='newid' placeholder='identificador'><i class='protect icon'></i></div><br><div style='width: 50%; margin-top: 5px;' class='ui left icon input'><input type='text' id='newname' placeholder='Apellidos Nombres'><i class='user icon'></i></div><br><div style='width: 50%; margin-top: 5px;' class='ui left icon input'><input type='text' id='newcarrera' placeholder='Carrera/Area (opcional)'><i class='suitcase icon'></i></div><br><button id='newsubmit' style='width: 50%; margin-top: 5px;' class='ui blue button'>Agregar nuevo usuario</button><button id='newcancel' style='width: 50%; margin-top: 5px;' class='ui button'>Cancelar</button></center><div id='newlog' style='margin-top: 20px;'></div></div>");
		$("#newcancel").on('click', function () {
			$("#msgmodal").html("");
		});

		$("#newsubmit").on('click', function () {
			if( $("#newid").val() != "" && $("#newname").val() != "" )   {
				name=$("#newname").val();
				dataString = "id=0&Action=addtodb&identificador="+($("#newid").val())+"&nombre="+name+"&carrera="+$("#newcarrera").val();
				$.ajax({
			        type: "POST",
			        url: "php/modulouses.php",
			        data: dataString,
			        success: function(result) {
			        	switch(result.trim()) {
					    case "0":
					        $("#newlog").html("<div class='ui warning message'><b>"+$("#newid").val()+"</b> ya se encuentra registrado en la base de datos</div>");
					        break;
					    case "1":
					    	alert(name+" se ha agrgado a la base de datos, ahora ya puedes agregarlo(a) a la cola de espera.");
					        $("#content").load("php/clientes.php");	 
					        break;
					    case "2":
					        $("#newlog").html("<div class='ui warning message'>Al parcer hay un error en el sistema, intentalo de nuevo</div>");
					        break;
					    default:
					    //code
					}
						
			        }
		    	});
			} else {
				$("#newlog").html("<div class='ui warning message'>Debes de llenar los campos</div>");
			}
		});
	});

	$("#deleteuser").on('click', function () {
		c = document.getElementsByName("selected");
			all = "";
			for(i = 0; i < c.length ; i++){
				if(c[i].checked)
					all += c[i].value+", "; 
			}
		$("#selectedtodelete").html(all);	
		$('#deletemodal')
		  .modal({
		    blurring: true
		  })
		  .modal('show')
		;

	});


	$("#processdelete").on('click', function () {
		$.ajax({
	        type: "POST",
	        url: "php/modulouses.php",
	        data: "id=0&Action=DeleteFromDb&data="+all,
	        success: function(result) {
	        	if(result.trim() == 1){
	        		$('#deletemodal')
					  .modal('hide')
					;
	        		$("#content").load("php/clientes.php");
	        	} else{
	        		$('#deletemodal')
					  .modal('hide')
					;
	        		alert("Se ha producido un error inesperado."+result+".");
	        	}
	        }
		});

	});



	$("#allselected").change(function(){
      $(".checkbox").prop('checked', $(this).prop("checked"));
    });

	$("input[type=checkbox]").change( function() {
		count = $(".checkbox:checked").length;
		if(count > 0){
			$("#deleteuser").attr("disabled", false);
		} else {
			$("#deleteuser").attr("disabled", true);
		}
	});
</script>
<?php
include 'conexion.php';
include 'LDS.php';

if(isset($_GET['Filtro']))
	$filtro = $_GET['Filtro'];
else
	$filtro = "";

$rows = getData("SELECT cliente.id, cliente.Nombre, cliente.Carrera 
				FROM cliente
				WHERE cliente.id
				LIKE '%$filtro%'
				ORDER BY cliente.id 
				DESC
				
				");

if(!rowCount($rows)){
	echo "<div class='ui warning message'>
			<div class='header'>Sin registros!</div> No hay usuarios en la base de datos.</div>
		  </div>";
} else {

?>

<button class='ui teal icon button' id='newuser'>
	  <i class="icon add user"></i>
		Nuevo registro
</button>

<button class='ui icon button' disabled id='deleteuser'>
	  <i class="icon delete user"></i>
		Eliminar seleccion
</button>
<div id="msgmodal" style="margin-top: 10px;"></div>
<div id='tablecontent' style="margin-top: 10px;">
	<table class="ui compact celled table">
		<thead>
		<tr>
		  <th>
		  	<center>
						<div class='ui checkbox'>
							<input type='checkbox' id='allselected'>
							<label></label>
				    	</div>
				    </center>
		  </th>
		  <th>Identificador</th>
		  <th>Nombre</th>
		  <th>Carrera</th>
		</tr>
		</thead>
		<tbody id='tbody'>
		
		<?php 
		
		for ($i=0; rowCount($rows) > $i ; $i++) { 
			echo "
			<tr>
				<td>
					<center>
						<div class='ui checkbox'>
							<input id='checkbox' class='checkbox' value='".$rows[$i]["id"]."' type='checkbox' name='selected'>
							<label></label>
				    	</div>
				    </center>	
				</td>
				<td>".$rows[$i]["id"]."</td>
				<td>".$rows[$i]["Nombre"]."</td>
				<td>".$rows[$i]["Carrera"]."</td>
			</tr>
			";
		}

		?>
		</tbody>
	</table>
</div>

<?php 

}

?>


<div class="ui basic modal" id='deletemodal'>
  <i class="close icon"></i>
  <div class="header">
    Eliminar registros
  </div>
  <div class="image content">
    <div class="image">
      <i class="delete user 
      icon"></i>
    </div>
    <div class="description">
      <p class='ui inverted header'>Estas apunto de eliminar los registros seleccionados.</p> <br><span id='selectedtodelete'></span><p class='ui inverted header'>¿Estas seguro que deseas continuar?</p>
      <span id='consolog'></span>
    </div>
  </div>
  <div class="actions">
    <div class="two fluid ui inverted buttons">
      <div class="ui negative basic inverted button" >
        <i class="remove icon"></i>
        No
      </div>
      <div id='processdelete' class="ui positive basic inverted button">
        <i class="checkmark icon"></i>
        si
      </div>
    </div>
  </div>
</div>