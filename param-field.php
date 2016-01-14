<?php

class Parametro{
    private $nome;
    private $tipo;
    private $valor;
    
    
    function __construct($nome) {
        $this->nome = $nome;      
    }
    
     public function __set($name, $value){ /*magic method*/
        switch($name){
            case 'String':
            case 'S':
                $this->AsString($value);
            break;
            case 'Float':
            case 'F':
                $this->AsFloat($value);
            break;         
            case 'Double':
            case 'D':
                $this->AsDouble($value);
            break;         
            case 'Integer':
            case 'I':
                $this->AsInteger($value);
            break;
            case 'Boolean':
            case 'B':
                $this->AsBoolean($value);
            default:
                $this->AsObject($value);
            break;
         }
        
    }
    
    public function __get($name){ /*magic method*/
        switch($name){
            case 'String':
            case 'S':
                return $this->AsString();
            break;
            case 'Float':
            case 'F':
                return $this->AsFloat();
            break;         
            case 'Double':
            case 'D':
                return $this->AsDouble();
            break;         
            case 'Integer':
            case 'I':
                return $this->AsInteger();
            break;
            case 'Boolean':
            case 'B':
                return $this->AsBoolean();
            default:
                return $this->AsObject();
            break;
         }
        
    }
            
    public function AsString($value = ''){
        if($value) $this->valor = (string) $value;
        $this->tipo = 'string';
        return $this->valor;
    }    
    
    private function AsFloat($value = ''){
        if($value) $this->valor = floatval($value);
        $this->tipo = 'float';
        return floatval($this->valor);
    }
    
    public function AsDouble($value = ''){
        if($value) $this->valor = doubleval($value);
        $this->tipo = 'double';
        return doubleval($this->valor);
    }
    
    public function AsInteger($value = ''){
        if($value) $this->valor = intval($value);
        $this->tipo = 'int';
        return intval($this->valor);
    }
    
    public function AsObject($value = ''){
        if($value) $this->valor = $value;
        $this->tipo = 'object';
        return $this->valor;
    }
    
    public function AsBoolean($value = false){
        if($value) $this->valor = (bool) $value;
        $this->tipo = 'boolean';
        return $this->valor;
    }
    
    public function getTipoParametro(){
        return $this->tipo;
    }
    
    public function getValor(){
        return $this->valor;
    }
}
?>