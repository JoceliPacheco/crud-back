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


  
 


//--------------------------------


class banco extends db{

	public $var = array();
	
	public function save($d){

		for($i=0; $i < count($d->emotions); $i++){
			$ids = "X";
			
			for($a=0; $a < count($d->emotions[$i]->emotions); $a++){
				$arr = $d->emotions[$i]->emotions[$a];
		 
				 if ($arr->selected){
					
					$ids .= ",$arr->emotions_alternatives_id";
					$emotions_alternatives_id = $arr->emotions_alternatives_id;
				}
			}
 
			$ids = str_replace('X,','',$ids);
			$ids = str_replace('X','',$ids);

			if($ids != 'X'){
				$this->query("
						INSERT INTO users_assoc_evaluations(
							user_id, 
							evaluations_id, 
							evaluations_questions_id, 
							emotions_alternatives_id, 
							content,
							created_at
						)VALUES(
							".$d->idUser.", 
							".$d->evaluations_id.", 
							".$d->emotions[$i]->id.", 
							".$emotions_alternatives_id.", 
							'".$ids."',
							'".$d->created_at."'
						)
				");
			}
			
		} 

		 echo json_encode(array("status" => true, "msg" => 'Etapa registrada com sucesso'), JSON_NUMERIC_CHECK);

	}

 


	public function listar(){


		$q = '';
		$this->query("SELECT id, name, emotions_types_id  FROM evaluations_questions WHERE status = 'active' && evaluations_id = '$_GET[id]' && deleted_at IS NULL " );


		if ($this->num_rows() > 0)


		{


			while($l = $this->fetch())


			{


 			
				$l->selecionados = 0;

				$l = $this->preOut($l);
				
				$l->emotions = Array();
				$this->query("
					SELECT
						ea.deleted_at,
						ea.name, 
						e.url_image as image  ,
						ea.id as emotions_alternatives_id
					FROM  
						emotions_alternatives ea 
					INNER JOIN 
						emoticons e
					ON
						e.id = ea.emoticons_id
					WHERE emotions_types_id = $l->emotions_types_id  ",2);
				 if ($this->num_rows(2) > 0){
				 	while($s = $this->fetch(2)){
						$s->selected = false;
						//Se nao possui imagem, nao entra no array
						if (isset($s->image) && $s->image != '' && $s->deleted_at == null){
							$s->image = "https://muriterapias.tk/assets/images/icons/".$s->image."";
							$l->emotions[] = $s;
						}

				 	}
				 }


				$this->var[] = $l;


			}
 

			return $this->var;


		}


		else


		{


			return $this->var;


		}


		


	} 


	public function geraGrafico($d){
	 

		$selecionados = array();

		//$d->date = data2mysql($d->date);
		$sql = "SELECT
					content
				FROM  
					users_assoc_evaluations  
				WHERE 
					evaluations_id = $d->evaluations_id &&
					user_id = '".$d->idUser."' &&
					created_at >= '".$d->date." 00:00:00' && created_at <= '".$d->date." 23:59:59'";
 
			$this->query($sql);
			if ($this->num_rows() > 0){
					while($s = $this->fetch()){
					$opcoes = explode(',',$s->content);
					for($i=0; $i < count($opcoes); $i++){
						if (isset($opcoes[$i]) && $opcoes[$i] != ''){
							$selecionados[$opcoes[$i]] = true;
						}
							
					}
			
				}
			}
	
			return $selecionados;
		 
	}





 




   





   


    


}





 


 		


 





//---------------------------------





if (isset($data->acao) && $data->acao != '') {


    $v = new banco();


    switch ($data->acao) {


		case 'save':
			$v->save($data);
		break;
		case 'listar':
			$lista = $v->listar($data);
			if (count($lista) > 0){
				echo json_encode(array("status" => true, "lista" => $lista ), JSON_NUMERIC_CHECK);
			}
			else{
				echo json_encode(array("erro" => true, "msg" => 'Nenhum registro encontrado!', "lista" => array()), JSON_NUMERIC_CHECK);
			}
		break;
		case 'geraGrafico':
			$lista = $v->geraGrafico($data);
			if (count($lista) > 0){
				echo json_encode(array("status" => true, "lista" => $lista ), JSON_NUMERIC_CHECK);
			}
			else{
				echo json_encode(array("erro" => true, "msg" => 'Nenhum registro encontrado!', "lista" => array()), JSON_NUMERIC_CHECK);
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
	$lista = $v->listar($data);
	if (count($lista) > 0){
		echo json_encode(array("status" => true, "lista" => $lista ), JSON_NUMERIC_CHECK);
	}
	else{
		echo json_encode(array("erro" => true, "msg" => 'Nenhum registro encontrado!', "lista" => array()), JSON_NUMERIC_CHECK);
	}
}


?>