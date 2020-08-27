<?php  


date_default_timezone_set("Brazil/East");
 /*
define('DB_HOST', 'sql384.main-hosting.eu');
define('DB_USER', 'u755225111_holanda');
define('DB_PASS', 'K:r5Y*Os');
define('DB_DATABASE', 'u755225111_muriterapias');


define('DB_HOST', 'mysql669.umbler.com');
define('DB_USER', 'u755225111_holan');
define('DB_PASS', '{jF?-,U2a9');
define('DB_DATABASE', 'u755225111_murit');
*/

define('DB_HOST', 'sql384.main-hosting.eu');
define('DB_USER', 'u755225111_holanda');
define('DB_PASS', 'K:r5Y*Os');
define('DB_DATABASE', 'u755225111_muriterapias');


require_once('_funcoes.php');  

 
class db

{

    // propriedades
    public $con;
    public $res = array();
    public $op;
	public $id='';
	public $status='';

    //Funções
    public function __construct(){

        $this->con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_DATABASE);
        $this->con->set_charset("utf8");
        if ($this->con->connect_errno) {
            exit("Falha ao conectar: (" . $this->con->connect_errno . ") " . $this->con->connect_error);
        }
    }

    public function query($sql, $op = 1){
        $this->res[$op] =$this->con->query($sql);

    }

    public function insert_id($op = 1){
         return $this->con->insert_id;
     }

    public function num_rows($op = 1){
        return $this->res[$op]->num_rows;
    }

    public function affected_rows($op = 1){
        return $this->con->affected_rows;
    }

    public function fetch($op = 1){
        return $this->res[$op]->fetch_object();
    }

     public function fetch_campos($op = 1){
        return $this->res[$op]->fetch_field();
    }

    
    //Fun para coletar dados curtos no banco  
    public function getId($tab, $tab_id, $param){
        
        $n = rand(9, 99);
        $sql = "select $tab_id from $tab $param";
        $l = (object)array();

        $this->query($sql, $n);

        if ($this->num_rows($n) > 0) {
            $l = $this->fetch($n);
            $l->status = true;
            return $l;

        } else {
            $l->status = false;
            return $l;
        }

    }
 
    //Prepara dados para entrada no banco
	public function preIn($info){
        
		foreach($info as $i => $v){

			switch($i){

				case 'cep':
					@$info->cep = soNumero(str_replace(' ','',$info->cep));
					break;
				case 'email':
					@$info->email = str_replace(" ", "", strtolower(@$info->email));
					break;
                default: $info->$i = $v;break;
			}
		}
		return $info;
	}

    //Prepara dados para exibir no FRONT
	public function preOut($info){

		foreach($info as $i => $v){

			$info->$i = stripslashes( ($v));
			switch($i){
                
                case 'dataHora':
                     $info->dataHora = "".(str_replace(' ','T',$info->dataHora)); 
                     break;
               
			}
		}
		return $info;
    }
    
    
    // INSERT padrão
    public function inserir($tab,$dados){

        

		$dados = $this->preIn($dados);
        $sql = "select * from " . $tab . " limit 0,1";
        $this->query($sql);
        $campos = '';
        $valores = "";

        while ($c = $this->fetch_campos()) {

            $campo = $c->name;
            if (isset($dados->$campo)) {
                $campos .= "$campo,";
                $valores .= "'" . addslashes($dados->$campo) . "',";
            }
        }

      

        $campos = substr($campos, 0, -1);
        $valores = substr($valores, 0, -1);

        $sql = "insert into " . $tab . " ($campos) values($valores)";
        $this->query($sql);
        $v = (object)array("id" => '', "status" => false);
        
       
        $id = $this->insert_id();
        if ($id > 0) {

            $v->id = $id;
            $v->status = true;
            return $v;

        } else {
            $v->status = false;
            return $v;
        }

    }

    
    // UPDATE padrão
    public function update($tab, $dados){

		$v = (object)array("id" => '', "status" => false);
		$dados = $this->preIn($dados);

        $query_campos = '';
        $sql = "select * from " . $tab . " limit 0,1";
        $this->query($sql);

        //exit('SAIDA->'.date('H:i:s'));
		$index = "";
        while ($c = $this->fetch_campos()) {
            $campo = $c->name;
			if ($index == ''){$index = "$c->name";}
            if (isset($dados->$campo)) {
                    $query_campos .= "$campo = '" . @addslashes($dados->$campo) . "',";
            }
        }

        $query_campos = substr($query_campos, 0, -1);
        $v->status = false;

       // print_r($query_campos);exit();

        if (strlen($query_campos) > 5) {

            $sql = "update " . $tab . " set
					 $query_campos
					where $index = '".$dados->$index."' limit 1";

                     
            $this->query($sql);

            if ($this->affected_rows() > 0) {
                $v->status = true;
            }
        }

        return $v;

    }

    
    // DELETE padrão
    public function delete($tab, $id){

        $sql = "select * from " . $tab . " limit 0,1";
        $this->query($sql);
        $c = $this->fetch_campos();
        $primary = $c->name;

        $sql = "delete from $tab where $primary = '$id' limit 1";
        $this->query($sql);

        if ($this->affected_rows() > 0) {
            $this->status = true;
        } else {
            $this->status = false;
        }

    }
}
 

?>