<?php
    /**
     * Accesso a la base de datos.
     */
    define('USER', 'root');
    define('PASS', '');
    define('HOST', 'localhost');
    define('DB', 'itecontrol');

    final class Conexion {
         
        private $conexion;
         
        function __construct()
        {
            $this->conexion = new PDO("mysql:host=".HOST.";dbname=".DB.";charset=utf8", USER, PASS);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }
         
        public function getConexion() {
            return $this->conexion;
        }
         
        public function cerrar() {
            $conexion = null;
        }
         
        public function __destruct() {
            $this->cerrar();
        }
    }
     
?>