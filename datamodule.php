<?php
    require_once 'connection.php';
    class DataModule{	
        public $usuario;
        public $empresa;
        public $conexao;

        function __construct(){
            if(isset($_SESSION['usuario'])) $this->usuario = $_SESSION['usuario'];
            if(isset($_SESSION['empresa'])) $this->empresa = $_SESSION['empresa'];
            $this->conexao = new Connection();
        }
    }
?>