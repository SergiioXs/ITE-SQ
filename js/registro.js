
$(document).ready( function () {
	
	$('#procesodb,#dpp')
	  .dropdown()
	;

	$('#nuevo').on('click', function () {
		$('#modal')
				.modal('show')
		;
	});

	$('table').filterTable({
    	inputSelector: '#buscar'
	});
	
	$('#closemodal').on('click', function () {
		$('#modal')
				.modal('hide')
		;
	});	

		$("#registros").html($('#tabla >tbody >tr').length);
		$("#buscar").on("keyup", function () {
			
				$("#registros").html($('#tabla >tbody >tr:visible').length);
		});	
	
	$("#asignarmodal").on('click', function () {

		if( ($("#iddb").val() != "") && ($("#procesodb option:selected").val() != "") ){
			$("#msgmodal").html("<div class='ui icon positive message'><i class='notched circle loading icon'></i><div class='content'><div class='header'>Espere un momento</div><p>Verificando el identificador.</p></div></div>");

			dataString = "id="+($("#iddb").val())+"&Proceso="+($("#procesodb").val());
	        $.ajax({
		        type: "POST",
		        url: "php/registro.php",
		        data: dataString,
		        success: function(result) {
		        	
		        	switch(result.trim()) {
					    case "0":
					        $("#msgmodal").html("<div class='ui negative message'><div class='header'>Error!</div> El identificador no se encuentra registrado en la base de datos.</div>");
					        break;
					    case "1":
					        $("#msgmodal").html("<div class='ui positive message'><div class='header'>Verificado!</div> Se ha añadido a la lista de espera.</div>");
							$("#iddb").val("");
							$("#content").load("php/cola.php");	 
					        break;
					    case "2":
					        $("#msgmodal").html("<div class='ui negative message'><div class='header'>Error!</div> El proceso seleccionado no tiene continuidad, profavor seleccione otro.</div>");
					        break;
					    case "3":
					        $("#msgmodal").html("<div class='ui warning message'><div class='header'>Error!</div> Ya se encuentra en cola.</div>");
					        break;
					    default:
					    //code
					}
					

		        }, 
		        error: function(data) {
		        	$("#msgmodal").html("<div class='ui negative message'><div class='header'>Error critico!</div> Al parecer algo anda mal con el sistema, comunicate con el desarrollador.</div>");
		        }
	    	});   

		} else {
			$("#msgmodal").html("<div class='ui warning message'><div class='header'>Error!</div> Tienes que llenar los campos.</div>");
		}
	});
	
	$("#newuser").on('click', function () {
		$("#msgmodal").html("<div class='ui segment' ><center><div style='width: 50%; margin-top: 5px;' class='ui left icon input'><input type='number' id='newid' placeholder='identificador'><i class='protect icon'></i></div><br><div style='width: 50%; margin-top: 5px;' class='ui left icon input'><input type='text' id='newname' placeholder='Apellidos Nombres'><i class='user icon'></i></div><br><div style='width: 50%; margin-top: 5px;' class='ui left icon input'><input type='text' id='newcarrera' placeholder='Carrera/Area (opcional)'><i class='suitcase icon'></i></div><br><button id='newsubmit' style='width: 50%; margin-top: 5px;' class='ui blue button'>Agregar nuevo usuario</button></center><div id='newlog' style='margin-top: 20px;'></div></div>");
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
					        $("#content").load("php/cola.php");	 
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


	var t;
	
	$("#refresh").on('click', function (){
		$("#content").load("php/cola.php");
	});

	var today = new Date();
    var h = today.getHours();
    var m = today.getMinutes();
    var s = today.getSeconds();

    if(localStorage.getItem("t1") === null )
 	localStorage.setItem("t1", h+":"+m+":"+s);
		

	function countTime(){
		today = new Date();
	    h = today.getHours();
	    m = today.getMinutes();
	    s = today.getSeconds();
		localStorage.setItem("t2", h+":"+m+":"+s);
		t = setTimeout( function(){ 
			countTime();	
		}, 1000);
		
	}
	
	countTime();

	function getTime(A,B){
		A = A.split(":");
		B = B.split(":");

		//Convertir todo a segundos
		h1 = parseInt(A[0])*3600;
		h2 = parseInt(B[0])*3600;
		m1 = parseInt(A[1])*60;
		m2 = parseInt(B[1])*60;
		s1 = parseInt(A[2]);
		s2 = parseInt(B[2]);

		//Sumar todos los segundos
		A = h1 + m1 + s1;
		B = h2 + m2 + s2;

		//Hacer la operacion
		R = A - B;

		//Dar formato TIME
		s = R;
		h = 0;
		m = 0;

		if(s > 59){
			mP = parseInt(s / 60);
			s = s - (mP*60);
			m = m + mP;
		}

		if(m > 59){
			hP = parseInt(m / 60);
			m = m - (hP*60);
			h = h + hP;
		}

		if(s < 10)
			s = "0"+s;
			
		if(m < 10)
			m = "0"+m;

		if(h < 10)
			h = "0"+h;

		return h+":"+m+":"+s;
	}

	$("#check").on("click", function () {
	    localStorage.setItem("prom", (getTime(localStorage.getItem("t2"), localStorage.getItem("t1") ) ) );
		$("#promedio").html(localStorage.getItem("prom")); 
	});
}); 

	