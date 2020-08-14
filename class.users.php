<?php    

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');

require_once("includes/_db.php");

$post_date = file_get_contents("php://input");
$data = json_decode($post_date);

define('TABELA','users');
define('IDTABELA','id');
define('TABELAID',IDTABELA);

//--------------------------------

class banco extends db{


    public $var = array();


	public function cadastrar($cadastro) { 


        $sql = "select * from ".TABELA." where email = '$cadastro->email' limit 1";
        $this->query($sql);

        if ($this->num_rows() > 0) {
            echo json_encode(array("erro" => true, "msg" => "Email já esta cadastrado!")); exit();
        }
 

		$res = $this->inserir(TABELA,$cadastro);


		$id = $res->id;


        if ($id > 0) {


        	$this->updateCores($id,$cadastro->cores);

            echo json_encode(array(

                "status" => true,
				"msg" => "Cadastro realizado com sucesso!"));

        } else {

            echo json_encode(array("erro" => true, "msg" => "Houve um erro, por favor tente novamente.!"));

        }


	}

	//Atualiza as cores do usuario
	public function updateCores($id,$cores){
	 	
	 	$cores = (Array)$cores;

	 	$ids = "0";
	 	foreach ($cores as $key => $value) {
	 		 
	 		if ($value > 0){
	 			$ids .= ",$key";
	 		}
	 	}
		 
		 
		$this->query("delete from user_colors  where user_id = '".$id."'",2);
		$this->query("select id from  colors where id IN ($ids) ",2);
	 
		 if ($this->num_rows(2) > 0){
		 	while($s = $this->fetch(2)){
		  
		 		$d = (object)Array();
				$d->user_id = $id;
	 			$d->color_id = $s->id;
	 			$this->inserir('user_colors',$d);
		 	}
		 }
	}


	


	public function atualizar($cadastro) {
 


		$res = $this->update(TABELA,$cadastro);

		$id = $res->status;
	
		$this->updateCores($cadastro->id,$cadastro->cores);

        echo json_encode(array(
			"status" => true,
			"msg" => "Cadastro atualizado com sucesso!"));


	}

	public function listar($d=''){


		$q = '';

		if (isset($d->user)){
			if (isset($d->user->id)){
				$q .= " && id = '".$d->user->id."' ";
			}
		}
	 
		$lista = Array();

		$this->query("select *, 0 as checked from ".TABELA." s where 1=1  $q ");

		if ($this->num_rows() > 0){

			while($l = $this->fetch()){

				 $l = $this->preOut($l);

				 $lista[] = $l->name;


				 $l->cores = Array();
				 $this->query("select id from  colors",2);
				 if ($this->num_rows(2) > 0){
				 	while($s = $this->fetch(2)){
				 		
				 		$l->cores[$s->id] = false;

				 	}
				 }

				 $this->query("select color_id from user_colors	 where user_id  = '".$l->id."'",2);
				 if ($this->num_rows(2) > 0){
				 	while($s = $this->fetch(2)){

						 $l->cores[$s->color_id] = true;
						 
				 	}
				 }

				$this->var[] = $l;

			}


			echo json_encode(array("status" => true, "lista" => $this->var), JSON_NUMERIC_CHECK);

		}


		else{

			echo json_encode(array("erro" => true, "msg" => 'Nenhum registro encontrado!', "lista" => array()), JSON_NUMERIC_CHECK);

		}


		


	} 





	 public function deletar($p) {
	 	

 		$id = IDTABELA;
		$this->query("delete from profissionais_x_servicos  where idProfissional = '".$p->$id."'");


        $sql = "DELETE FROM ".TABELA." WHERE ".IDTABELA." = '".$p->$id."'  LIMIT 1";
		$this->query($sql);


    }





}

 


//---------------------------------





if (isset($data->acao) && $data->acao != '') {


    $v = new banco();
    switch ($data->acao) {

	   case 'atualizar':
	   		$v->atualizar($data->dados);
			break;
		case 'excluir':

			for($i=0; $i < count($data->dados); $i++){
				$v->deletar($data->dados[$i]);
			}

	   		echo json_encode(array("erro" => false, 'msg' => count($data->dados).' iten(s) deletado(s)' ));

			break;
        case 'cadastrar':


			$id = TABELAID;


			if (isset($data->dados->$id) && $data->dados->$id != ''){


				$v->atualizar($data->dados);


			}else {


            	$v->cadastrar($data->dados);


			}


            break;


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