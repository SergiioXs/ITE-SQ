<?php 
require "LDS.php";

if(isset($_POST)){
	$user = getData("SELECT id FROM modulo WHERE Usuario = '".$_POST['user']."' AND Contrasena = '".$_POST['pass']."'");
	if(rowCount($user))
		echo $user[0]['id'];
	else
		echo 0;
}

?>