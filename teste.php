<?php    
    define('DEBUG-PQUERY', 1);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
    
	try{
		session_start();
	}catch(Exception $e){
		exit("Erro na sessão...".$e->getMessage());
	}
    
    require_once 'pquery.php';
    require_once 'datamodule.php';
    
    try {
        $data_module = new DataModule();
        $q1 = new PQuery();
        $q1->connection = new Connection();
        //$q1->connection = $data_module->conexao;
        
	/*var_dump($data_module->conexao);
        $q1->SQL()->Text("select * from clifor where codigo = :codigo");
        $q1->SQL->Text = "select * from clifor where codigo = :codigo";
        
        //$q1->ParamByName("teste")->AsInteger(5);        
        $q1->Param("teste")->Integer = 5; //assim não funciona o tipo pro statement
        var_dump($q1->Param("teste")->Integer);
        
        $q1->Param("parametro1")->String = "oi oi oi oi ";
        echo "<br><br>[".$q1->Param("parametro1")->getTipoParametro()."]";
        $q1->Open();*/
        /*while(!$q1->EOF){
            
            $q1->Next();
        } */
        
        /*$q1->SQL->Text = "select * from cidades where codigo_ibge in (:aa, :bb, :cc)";
        $q1->P("aa")->I = '1100189';
        $q1->P("bb")->I = '1100114';
        $q1->P("cc")->I = '1100403';
        $q1->Open(); 
        while(!$q1->EOF){
            echo "// Campo ".$q1->F("codigo_ibge").' //<br />';
            $q1->Next();
        } */
        
        /*$q1->SQL->Text = 'select * from pessoas p
                          left join enderecos e on e.pessoa_ref = p.codigo 
                          where p.codigo = :codigo
                          and p.codigo = :ocodigo
                          order by p.codigo';
        $q1->P("codigo")->I = 8;
        $q1->P("ocodigo")->I = 8;
        $q1->Open();
        while(!$q1->EOF){
            echo $q1->F("nome").' / / / / '.$q1->F("cnpj").'<br />';
            $q1->Next();
        } */
		
		 $conexao = new Connection();
    
        $q1 = new PQuery();
        $q1->connection = $conexao;
                
        $q1->SQL->Text = "select codigo_ibge from cidades limit 1";
       
        //$q1->P("email")->S = "ludviggf@yahoo.com.br";
        //$q1->P("senha")->S = "123";
        $q1->Open();
        @session_start();
        $gravou = SaveMemoryQuery("q1", $q1);
        echo "gravou....".$gravou;
       // $_SESSION["oat"] = serialize($q1);
        
        
        if ($q1->F("codigo_ibge") != 0) {
            echo "logou";
        } else {
            echo "nao logou";
        }
        
        $aquery = LoadMemoryQuery("q1");
        //print_r($aquery);
        //$aquery = unserialize($_SESSION["oat"]);
        echo "tentando....[".$aquery->record_count."]";
        
        /*$q1->SQL->Text = 'delete from tipo_operacao where codigo = :codigo';
        $q1->P('codigo')->I = 14;
        $da = $q1->ExecSQL();
        print_r($da); */
        //$se_sim = $q1->ExecSQL(true, "insert into tipo_operacao(descricao) values('testadores, testadores')");
        
      //  echo "parametro teste vale: ".$q1->Param("teste")->Integer;
    } catch (Exception $exc) {
        //echo "Erro::: >>>>>>>>>>> ".$exc->getMessage().'<br />';    
    }
?>