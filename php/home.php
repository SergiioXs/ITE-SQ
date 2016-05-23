<span id='initloader'></span>
<script type="text/javascript">
	var procesos = [];
	var modulos  =  0;
	var cant     =  0;

	$("#btnite").on('click', function () {
		if($("#pro1in").val() != ""){
			$("#errmsg1").html("");
			procesos[cant] = $("#pro1in").val(); 
			$("#pro1in").val("");
			thisString = "";
			for (var i = 0; i <= cant; i++) {
				thisString = "<a class='ui black label' onClick='deleteval("+i+")'>"+procesos[i]+"<i class='delete icon'></i></a>"+thisString;
			};

			$("#valores").html(thisString);
			cant++;
			if(cant) $("#pro2").removeClass("disabled");	
		} else {
			$("#errmsg1").html("<div class='ui negative message'><div class='header'>Debes ingresar un nombre</div></div>");
		}
		
	});

	function deleteval(x) {

		procesos.splice(x,1);
		cant--;
		thisString = "";
		for (var i = 0; i < cant; i++) {
			thisString = "<a class='ui black label' onClick='deleteval("+i+")'>"+procesos[i]+"<i class='delete icon'></i></a>"+thisString;
		};

		$("#valores").html(thisString);
		if(!cant) $("#pro2").addClass("disabled");
	}


	$('#pro2in').on('keyup', function () {
		if($('#pro2in').val() > 0){
			$("#pro3").removeClass("disabled");
		} else {
			$("#pro3").addClass("disabled");
		}
	});



	$("#btnfile").on('click', function () {
		$("#archivo").click();
	});

	$("#archivo").on('change', function () {
		
		if($("#archivo").val()) {
			$("#pro4").removeClass('disabled');
			$("#valores4").html("<div class='ui pointing basic label'>"+ ($("#archivo").val().replace(/C:\\fakepath\\/i, ''))+ "</div>");
		} else {

			$("#valores4").html("");
			$("#pro4").addClass('disabled');
		}
			
	});


$("#confirm").on('click', function () {
	
	
	var errPro = 0, errMo = 0;
	var inputfile = document.getElementById('archivo');
	var file = inputfile.files[0];
	var data = new FormData();
    data.append('archivo',file);
    data.append('procesos',procesos);
    data.append('modulos',$("#pro2in").val());

	if(procesos.length == 0)
		errPro = 1;
	if($("#pro2in").val() <= 0)
		errMo = 1;
		
	if(errMo || errPro){
		errMsg = '';
		if(errMo) errMsg  +='<li>Debes ingresar un numero de modulos positivos.</li>';
		if(errPro) errMsg +='<li>Debes de ingresar por lo menos 1 proceso.</li>';
		$('#errmsg6').html("<br><div class='ui error message'></i><div class='header'>Debes de llenar todos los campos de manera correcta</div><ul class='list'>"+errMsg+"</ul></div>");
			 $('html, body').animate({
		        scrollTop: $("#errmsg6").offset().top
		    }, 2000);

	$("#initloader").html("");
	} else {
		$("#initloader").html("<div class='ui active inverted dimmer'><div class='ui large text loader'>Espere porfavor.</div></div>");
		$("#errmsg5").html("Cargando...");
		$('html, body').animate({
	        scrollTop: $("#processsegment").offset().top
	    }, 2000);
		$.ajax({
	        type: "POST",
	        url: "php/newsystem.php",
	        contentType:false,
			data:data,
			processData:false,
			cache:false,
	        success: function(result) {
	        	$("#errmsg5").html(result);
	        	$("#newsysmod").modal({closable: false}).modal('show').modal({
					blurring: true,
					onDeny: function(){
				    	location.reload();
				    },
				    onApprove: function() {
				    	location.reload();         
				    }
				});	
	        }, 
	        error: function(data) {
	        	$("#errmsg5").html("<div class='ui negative message'><div class='header'>Error critico!</div> Al parecer algo anda mal con el sistema, comunicate con el desarrollador.</div>");
	        		$("#initloader").html("");
	        }
		});
	}

});

</script>





<center>
	<img src="img/logo1.png" style="width: 40%;">
	<h2 class="ui center aligned icon header">
	  Bienvenido al sistema gestor de colas de espera ITE-SQ
	</h2>
</center>
<p>
 Bienvenido administrador! Con el sistema ITE-SQ podras crear tantos procesos como asi los requiera su empresa y los modulos necesarios para poder atender dichos procesos, con la facilidad de editar el flujo que tendran estos modulos dentro de los procesos para que sus clientes puedan realizarlos de manera clara y ordenada. Ademas si asi lo requiere, podra tratar a sus clientes de una manera mas personalizada, ya que puede introducir una base de datos con los posibles clientes que realizaran sus procesos o bien agregarlos en el modulo de registro. 
<br><br>
 Ademas podras personalzar los modulos creados asignandoles un nombre de modulo, encargado y si asi lo requiere combinar 1 o mas modulos para que actueen de la misma manera(Ejemplo: 3 modulos de atención al cliente, donde el cliente podra pasar a cualquiera de los 3 siempre y cuando este desocupado). Cada encargado de modulo tendra su propia pantalla donde podra visualizar los clientes que el sistema le otorgara para atender, asi como sus estadisticas de tiempos de atencion al cliente, espera, entre otros.
 <br> <br>
 Porque nos preocupa la espera de sus clientes, el sistema tendra una pantalla donde usted podra escojer algun tipo de entretenimiento de espera, ya sea algun video, el logo de su compañia, instrucciones, etc. Ademas de avisar al siguiente cliente para ser atendido a que modulo debe acudir. 
<br><br>

 Para comenzar a utilizar ITE-SQ debes de llenar la siguiente informacion:
</p>

<div class="ui blue segment" id='processsegment'>
	<div class="description">
		<div class="ui grid">
	    	<div class="four wide column">
    		    <div class="ui medium image">
			      <img src="img/pro1.png" style='width: 100%;'>
			    </div>
	    	</div>
	    	<div class="ui twelve wide column">

		      <div class="ui header" style="font-size: 30px;">Procesos.</div>
		      <p>Un proceso es un conjunto de actividades mutuamente relacionadas o que al interactuar juntas   en los  elementos de entrada los convierten en resultados</p>
		      <b>
		    	Nombre del/los proceso(s)
		  	  </b>
		  	<br>
			  	<div class="ui input">
					<input placeholder="Ejemplo: Inscripción" type="text" id='pro1in'>
					<button class='ui right labeled icon button' id='btnite'>
						<i class="plus icon"></i>
						Agregar
					</button>
				</div>
				<br><br>
				<div id='valores'>

				</div>
				<div id='errmsg1'></div>
		    </div>
		</div>
	</div>
</div>

<div class="ui blue segment">
	<div class="ui grid">
		<div class='ui four wide column'>
			<img src="img/pro2.png" style='width: 100%;'>
		</div>
		<div class='ui twelve wide column'>
		    <div class="description">
		      <div class="ui header" style="font-size: 30px;">Modulos.</div>
		      <p>Un modulo es el lugar donde los usuarios acuden para realizar las actividades descritas dentro de un proceso, estos pueden ser uno o mas que al cumplir con todos ellos se realiza satisfactoriamente el proceso.
		      	<br>
		      	<i><b>El modulo de registro ya esta incluido por default.</b></i>
		      </p>
		      <b>
		    	Cantidad de modulos
		  	  </b>
		  	<br>
			  	<div class="ui input">
					<input placeholder="Ejemplo: 3" type="number" id='pro2in'>
				</div>
				<br><br>
				<div id='valores2'>

				</div>
				<div id='errmsg2'></div>
		    </div>
		</div>
	</div>
</div>

<div class='ui blue segment'>
	<div class="ui grid">
		<div class='ui four wide column'>
			<img src="img/pro3.png" style='width: 100%;'>
		</div>
		<div class="ui twelve wide column">
		    <div class="description">
		      <div class="ui header" style="font-size: 30px;">Base de datos.</div>
		      <p>La base de datos debe contener las siguientes columnas
		      	<br>
		      	<li>Identificador (Numero que lo identifique de los demas usuarios)</li>
		      	<li>Nombre</li>
		      	<li>Area/Carrera</li>
		      	<br>
		      	El archivo no debe llevar cabezera
		      	<br>
		      	<i><b>Ejemplo.</b></i>
		      </p>
		      	<img src="img/ejemplodb.png" >
		  		<br>
		  		 <div class="ui teal button" id='btnfile' style="width: 100%;">
			      Seleccionar archivo
			    </div>
			    <input type='file' class='ui teal button' name='archivo' id='archivo' accept='.csv' style='display: none;' enctype='multipart/form-data'>
			    <div id='valores4'></div>
		  		<br>
				<i>El archivo tiene que estar en formato .CSV (delimitado por comas)</i>

				<div id='errmsg4'></div>
		    </div>
		</div>
	</div>
</div>


<center>
	<button class="ui huge button" id='confirm' tabindex="0" style='background-color: #014E82; color: white; width: 100%;'>
		Continuar
	</button>
</center>
<div id='errmsg6'></div>

<div class="ui basic modal" id='newsysmod'>
      <div id='errmsg5'></div>
<center>
    <div class="actions">
    	<button class="ui positive button" style="width: 100%; margin-left: 0px;">Continuar</button>
    </div>
</center>
</div>
