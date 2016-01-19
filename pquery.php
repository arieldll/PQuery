<?php
    /*PQuery by Ariel G. Dalla Costa - 12-01-2015*/
    require_once 'param-field.php';
    require_once 'connection.php';
    require_once 'object-sql.php';

    class PQuery{
        /*propriedades*/
        /*variaveis*/
        public $connection;    
        public $SQL;
        public $record_count = 0;
        public $EOF;
        public $indice_result_set = 0;
        public $rows_affected = 0;
        public $quant_paginador = 20; //quantidade de registros que vai ser exibido por página
        public $quant_paginas = 0;
        public $pagina_selecionada = 0;
        
        /*vetores*/
        private $params = array();
        private $fields = array();
        private $result_set = array();
        
        function __construct(){
            $this->SQL = new SQL();
        }
        
        public function SQL(){
            return $this->SQL;
        }

        public function Field($nome){
            $i = $this->indice_result_set;
            if(isset($this->result_set[$i])){
                $this->fields = $this->result_set[$i];
                if(isset($this->fields) && $this->fields){
                    if(is_array($this->fields)){
                        if(is_numeric($nome)){
                            if(isset($this->fields[$nome])){
                                return $this->fields[(int)$nome];
                            }
                        }else if(isset($this->fields[$nome])){
                            return $this->fields[$nome];
                        }else throw new Exception("Campo não encontrado");
                    }
                }else throw new Exception("Não existem campos para esta query!");
            }
        }
        
        public function Param($nome){
            if(!isset($this->params[$nome]))
            $this->params[$nome] = new Parametro($nome);
            return $this->params[$nome];
        }
        
        public function F($nome){
            return $this->Field($nome);
        }
        
        public function P($nome){
            return $this->Param($nome);
        }
        
        public function Open($osql = ''){
            $sql_processado = $this->SQL->Text;
            if(strlen($osql) > 0){ //teve q colocar chave, pq não funfava... eita php bugado da porra
                $sql_processado = $osql;
            }
            $stmt = $this->connection->prepare($sql_processado);
            if(isset($this->params) && sizeof($this->params) > 0){
                foreach($this->params as $d => $v){
                    $nome = ':'.$d;
                    $tipo = $v->getTipoParametro();
                    $valor = $v->getValor();
                    $bind_banco = "";
                    switch($tipo){
                        case 'string':
                            $bind_banco = PDO::PARAM_STR;
                        break;
                        case 'int':
                            $bind_banco = PDO::PARAM_INT;
                        break;
                        case 'boolean':
                            $bind_banco = PDO::PARAM_BOOL;
                        break;
                        default:
                            $bind_banco = FALSE;
                    }
                    if(defined('DEBUG-PQUERY')){
                        print "setando parametro: $nome ($bind_banco) => $valor<br />";
                    }
                    $stmt->bindValue($nome, $valor, $bind_banco);
                }
            }
            $k = $stmt->execute();
            $this->rows_affected = $stmt->rowCount();
            $erros = "";
            if(!$k){
                echo "Erro! ";
                foreach($stmt->errorInfo() as $d => $raise){
                    $erros .= $raise.'<br />';
                }
                exit($erros);
            }
            $this->EOF = false;
            $this->record_count = $stmt->rowCount();
            $this->result_set = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->indice_result_set = 0;            
        }
        
        public function Next(){
            //anda no vetor result_set
            if(isset($this->result_set)){
                $this->EOF = false;
                if($this->indice_result_set < $this->record_count - 1){
                    $this->indice_result_set++;
                }else $this->EOF = true;
            }
        }
        
        public function First(){
            $this->indice_result_set = 0;
        }
        
        public function Last(){
            if(sizeof($this->result_set) > 0){
                $this->indice_result_set = sizeof($this->result_set) - 1;
            }
        }
        
        public function Prior(){
            if($this->indice_result_set > 0){
                $this->indice_result_set--;
            }
        }
        
        public function ExecSQL($useTransaction = false, $osql = ''){
            $sql_processado = $this->SQL->Text;
            if(strlen($osql) > 0){ //teve q colocar chave, pq não funfava... eita php bugado da porra
                $sql_processado = $osql;
            }
            $stmt = $this->connection->prepare($sql_processado);
            if(isset($this->params) && sizeof($this->params) > 0){
                foreach($this->params as $d => $v){
                    $nome = ':'.$d;
                    $tipo = $v->getTipoParametro();
                    $valor = $v->getValor();
                    $bind_banco = "";
                    switch($tipo){
                        case 'string':
                            $bind_banco = PDO::PARAM_STR;
                        break;
                        case 'int':
                            $bind_banco = PDO::PARAM_INT;
                        break;
                        case 'boolean':
                            $bind_banco = PDO::PARAM_BOOL;
                        break;
                        default:
                            $bind_banco = FALSE;
                    }
                    if(defined('DEBUG-PQUERY')){
                        print "setando parametro: $nome ($bind_banco) => $valor<br />";
                    }
                    $stmt->bindValue($nome, $valor, $bind_banco);
                }
            }
            try {
                $k = null;
                $erros = "";
                if($useTransaction){
                    if(strlen(trim($osql)) > 0){
                        $k = $this->connection->beginTransaction();
                        $k = $this->connection->exec($osql);
                        $this->rows_affected = $this->connection->rowCount();
                        if(!$k){
                            foreach($this->connection->errorInfo() as $d => $raise){
                                $erros .= $raise.'<br />';
                            }
                        }
                        if(!$k) throw new Exception($erros);
                    }else{
                        throw new Exception("É necessário passar o SQL como parâmetro!");
                    }
                }else{
                    $k = $stmt->execute();
                    $this->rows_affected = $stmt->rowCount();
                    if(!$k){
                        foreach($stmt->errorInfo() as $d => $raise){
                            $erros .= $raise.'<br />';
                        }
                    }
                    if(!$k) throw new Exception($erros);
                }
                $this->result_set = array();
                $this->indice_result_set = 0;
                if($useTransaction){
                    $k = $this->connection->commit();
                }
                return (int) $k;
            } catch (Exception $exc) {
                if($useTransaction && strlen(trim($osql)) > 0){                    
                    $this->connection->rollBack();
                }
                exit($exc->getMessage());
                return "";
            }         
            
        }
        
        public function __sleep(){
            /*método para serialização*/
            //incluir todos os atributos da classe PQuery as variáveis aqui
            return array('SQL', 'record_count', 'EOF', 'indice_result_set', 'rows_affected', 
                'params', 'fields', 'result_set', 'quant_paginador', 'quant_paginas', 'record_count');
        }
            
        public function __wakeup(){
            /*método para desserialização*/
        } 
                        
        public function paginador(){
            if($this->record_count > 0){
                $this->quant_paginas = ceil($this->record_count / $this->quant_paginador);
            }
        }
    }
    
    function SaveMemoryQuery($name, $PQuery){
        if(!($PQuery instanceof PQuery)) throw new Exception("Objeto não é do tipo PQuery!");
        else{
            $_SESSION[$name] = serialize($PQuery);
            return 1;
        }
    }
    
    function LoadMemoryQuery($name){
        if(isset($_SESSION[$name])){
            $objeto = $_SESSION[$name];
            return unserialize($_SESSION[$name]);
        }else{
            throw new Exception("Objeto não encontrado");
        }
    }

    
    /*
     * Open Ok
     * Next Ok
     * First Ok
     * Last Ok
     * Prior (anterior) Ok
     * ExecSQL Ok
     * RecordCount Ok
     * SQL.SaveToFile Ok
     * ToJSON -> retornar json objetos
     */
    
?>