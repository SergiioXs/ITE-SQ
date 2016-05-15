<?php
            include_once("config.php");  
            $con = new Conexion();
            $db = $con->getConexion();
            if(is_null($db))
              echo "error";
?>