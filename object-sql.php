<?php
    
    class SQL{
        public $Text = ''; //liberado pra acessar SQL->Text
        
        public function Text($text = ''){
            if($text) $this->text = $text;
            return $this->text;
        }        
        
        public function SaveToFile($caminho){
            if($caminho != ''){
                file_put_contents($caminho, $this->Text);
            }else throw new Exception("Defina um caminho para o arquivo");
        }
        
        public function LoadFromFile($caminho){
            if($caminho != ''){
                $this->Text = file_get_contents($caminho);
            }else throw new Exception("Defina um caminho para o arquivo");
        }
    }
    
?>