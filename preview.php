<html>
<head>
	<script type="text/javascript" src='js/jquery.js'></script>
	<script type="text/javascript" src='js/jquery.min.js'></script>
		<link rel="stylesheet" type="text/css" href="semantic/dist/semantic.min.css">
		<link rel="stylesheet" type="text/css" href="css/style.css">
		<script src="semantic/dist/semantic.min.js"></script>
		<script src="semantic/dist/semantic.js"></script>
	<script>
		function refresh(){
		    setTimeout(
		        function(){
		          $('#contenti').load('home.html');
		          refresh();
		        }, 1000);
		    }

		 refresh();
		
	</script>
</head>

<body>

	<div id='contenti' name='content' style='width: 100%;'>

		<div class="ui segment" style='height: 100%;'>
		  <div class="ui active dimmer">
		    <div class="ui text loader">Cargando vista previa...</div>
		  </div>
</div>
	</div>

</body>

</html>