<?php  

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');
header('Content-Type: text/html; charset=utf-8');

require_once("includes/_db.php");





$post_date = file_get_contents("php://input");


$data = json_decode($post_date);


 


define('TABELA','colors');


define('IDTABELA','id');


define('TABELAID',IDTABELA);





 


//--------------------------------


class banco extends db


{





    public $var = array();

 


	public function listar()


	{


		$q = '';


	 

		$this->query("select *, 0 as checked  from ".TABELA." s where 1=1  $q ");


		if ($this->num_rows() > 0)


		{


			while($l = $this->fetch())


			{


 			


				 $l = $this->preOut($l);
 


				$this->var[] = $l;


			}


			echo json_encode(array("status" => true, "lista" => $this->var ), JSON_NUMERIC_CHECK);


		}


		else


		{


			echo json_encode(array("erro" => true, "msg" => 'Nenhum registro encontrado!', "lista" => array()), JSON_NUMERIC_CHECK);


		}


		


	} 


 




   





   


    


}





 


 		


 





//---------------------------------





if (isset($data->acao) && $data->acao != '') {


    $v = new banco();


    switch ($data->acao) {

 

		default:


			if (method_exists($v,$data->acao))			{


				$metodo = $data->acao;


				$v->$metodo($data);


			}


			break;		 


    }


}

if (isset($_GET['acao']) && $_GET['acao'] == 'lista'){
	$v = new banco();
	$v->listar();
}


?>