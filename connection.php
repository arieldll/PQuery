<?php
    class Connection extends PDO{
        public $connection;

            
			public $ip = "";
            public $banco = "";
            public $usuario = "";
            public $senha = "";
        
            function __construct($banco = ''){
            try{
                parent::__construct("pgsql:dbname={$this->banco};host={$this->ip}", $this->usuario, $this->senha);
                $this->connection = new PDO("pgsql:dbname={$this->banco};host={$this->ip}", $this->usuario, $this->senha); 
            }catch (Exception $exc) {
				exit("Não foi possível construir a conexão... com {$this->ip}: ".$exc->getMessage().' / '.$exc->getTraceAsString());
            }
        } 
    }

?>