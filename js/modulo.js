/* 
	Tareas:
	Asignar la informacion del dependiente
	Tabla de atendidos
	Proemdios de tiempos
	Busca quien sigue y lo muestra

*/
$(document).ready(function () {
	if(localStorage.getItem("id")>0){
		//Informacion sobre el que atiende 
		var userID = localStorage.getItem("id");
		var user;
		var t;
		$.ajax({
	        type: "POST",
	        url: "php/modulouses.php",
	        data: "Action=All&id="+userID,
	        success: function(result) {
        		user = '{"Usuario": '+result+'}';
        		obj = JSON.parse(user);
				$("#Nombre").html(obj.Usuario[0].Nombre);
	        	$("#Atiende").html(obj.Usuario[0].Encargado);
	        	localStorage.setItem("serial", obj.Usuario[0].Serial);
	        	if(obj.Usuario[0].Activo == 1){
	        		$("#active").prop("checked", "checked");
	        		$("#activestatus").html("Conectado");
	        		if(obj.Usuario[0].Busy == 0){
	        			clearTimeout(t);
	        			searchClient(obj.Usuario[0].Proceso,obj.Usuario[0].Dependencia,obj.Usuario[0].Serial,userID,obj.Usuario[0].Nombre);
	        		}
	        	} else {
	        		$("#active").prop("checked", "");
	        		$("#activestatus").html("Desconectado");
	        	}		
	        	$("#content").load("html/atendiendo.html");
	        	$("#table").load("html/modulohistorial.html");
	        	$("#loader").html("");	
				$("#loader").removeAttr("class");	        			 
	        }, 
	        error: function(data) {
	        	$("#msgerr").html("<div class='ui negative message'><div class='header'>Error critico!</div> Al parecer algo anda mal con el sistema, comunicate con el desarrollador.</div>");
	        }
    	});  

		
		$('table').filterTable({
	    	inputSelector: '#buscar'
		});

		
		$("#buscar").on("keyup", function () {
				$("#registros").html($('#tabla >tbody >tr:visible').length);
		});	

		function searchClient(procesos,dependencia,serial,id,Nombre){
		    t = setTimeout(
		        function(){
		        	$.ajax({
				        type: "POST",
				        url: "php/modulouses.php",
				        data: "Action=Search&procesos="+procesos+"&dependencia="+dependencia+"&serial="+serial+"&id="+id+"&Nombre="+Nombre,
				        success: function(result) {
				  
				        	if(result == 1) {
				        		clearTimeout(t);
								$("#content").load("html/atendiendo.html");  
			        		} else {
			        			
			        		}

				        }, 
				        error: function(data) {
				        	$("#table").html("Aqui hay un error ");
				        }
			    	});
		          searchClient(procesos,dependencia,serial,id,Nombre);
		        }, 
		    1000);
		}

		$("#active").on("click", function () {
			var dataString = "Action=Active&id="+(obj.Usuario[0].id);
			$.ajax({
		        type: "POST",
		        url: "php/modulouses.php",
		        data: dataString,
		        success: function(result) {
	        	
	        		if(result == 1){
	        			$("#activestatus").html("Conectado");
	        			$("#cerrarSesion").addClass("disabled");
		    			
	        			//Empezar a buscar gente en espera
	        			clearTimeout(t);
	        			searchClient(obj.Usuario[0].Proceso,obj.Usuario[0].Dependencia,obj.Usuario[0].Serial,userID,obj.Usuario[0].Nombre);		

					} else {
						clearTimeout(t);
						$("#activestatus").html("Desconectado");
						$("#cerrarSesion").removeClass("disabled");
		    			
					}   			 
		        }, 
		        error: function(data) {
		        	$("#msgerr").html("<div class='ui negative message'><div class='header'>Error critico!</div> Al parecer algo anda mal con el sistema, comunicate con el desarrollador.</div>");
		        }
	    	});  	
		});


	} else {
		window.location.href = "login.html";

	}
});