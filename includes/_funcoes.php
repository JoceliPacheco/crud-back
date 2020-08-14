<?php



function soNumero($str) {

	return preg_replace("/[^0-9]/", "", $str);

}

	

function limpa_campo($string)



{



	$string = preg_replace("/[^a-zA-Z0-9.]/", "", $string);



	return $string;



}



function addHora($data,$tempo)



{



	$date = $data;



	$newdate = strtotime ( $tempo , strtotime ( $date ) ) ;



	$newdate = date ( 'Y-m-d H:i:s' , $newdate );



	return $newdate;



}





function limpa($string)

{



    $string = utf8_decode($string);



    $string = @ereg_replace("[���aaa]", "a", $string);

    $string = @ereg_replace("[���AA]", "A", $string);

    $string = @ereg_replace("[�ee]", "e", $string);

    $string = @ereg_replace("[�EE]", "E", $string);

    $string = @ereg_replace("[�i]", "i", $string);

    $string = @ereg_replace("[�I]", "I", $string);

    $string = @ereg_replace("[���oo]", "o", $string);

    $string = @ereg_replace("[���O]", "O", $string);

    $string = @ereg_replace("[�uu]", "u", $string);

    $string = @ereg_replace("[�UU]", "U", $string);

    $string = @ereg_replace("�", "c", $string);

    $string = @ereg_replace("�", "C", $string);

    $string = @ereg_replace("[][><}{)(:;,!?*%~^`&#@]", "", $string);

    $string = @ereg_replace(" ", "_", $string);

    $string = @ereg_replace("-", "_", $string);



    $vString = explode('.', $string);

    $newString = '';

    for ($i = 0; $i < count($vString); $i++) {

        if ($i == count($vString) - 1) {

            $newString .= '.';

            $newString .= $vString[$i];

        } else {

            if ($i == 0) {

                $newString .= $vString[$i];

            } else {

                $newString .= '_';

                $newString .= $vString[$i];

            }

        }



    }

    $string = $newString;



    $string = utf8_encode($string);



    return $string;

}



//-----------------------------------------------

function youVideo($url)

//-----------------------------------------------

{

    $final = explode('v=', $url);

    $final = explode('&', $final[1]);

    $final = $final[0];

    return $final;

}



//-----------------------------------------------

function youVideo_old($url)

//-----------------------------------------------

{

    $c = 1;

    $txt = $url;





    $posI = strpos($url, "http://");

    $cort = substr($url, $posI);

    $posF = strpos($cort, '@');



    if (strlen($posF) > 0) {

        $final = substr($url, $posI, $posF);

    } else {

        $final = $url;

    }



    $final = str_replace('embed/', 'v/', $final);

    if ($c == 1) {

        $c = 2;

        return $final;

    }

}



//-----------------------------------------------

function site_($site)

//-----------------------------------------------

{

    if (!eregi('http://', $site)) {

        $site = 'http://' . $site;

    }

    return $site;

}

//-----------------------------------------------

function data_($d)

//-----------------------------------------------

{

    // leitura das datas

    $d = explode('/',$d);

    $dia = $d[0];

    $mes = $d[1];

    $ano = $d[2];

    

    // configura��o mes

    switch ($mes) {

        case 1:

            $mes = "Janeiro";

            break;

        case 2:

            $mes = "Fevereiro";

            break;

        case 3:

            $mes = "Mar&ccedil;o";

            break;

        case 4:

            $mes = "Abril";

            break;

        case 5:

            $mes = "Maio";

            break;

        case 6:

            $mes = "Junho";

            break;

        case 7:

            $mes = "Julho";

            break;

        case 8:

            $mes = "Agosto";

            break;

        case 9:

            $mes = "Setembro";

            break;

        case 10:

            $mes = "Outubro";

            break;

        case 11:

            $mes = "Novembro";

            break;

        case 12:

            $mes = "Dezembro";

            break;

    }

 

    //Agora basta imprimir na tela...

    return "$mes";

}



//-----------------------------------------------

function truncate($str, $len = 80, $etc = '')

//-----------------------------------------------

{

    $end = array(' ', '.', ',', ';', ':', '!', '?');



    if (strlen($str) <= $len)

        return $str;



    if (!in_array($str{$len - 1}, $end) && !in_array($str{$len}, $end))

        while (--$len && !in_array($str{$len - 1}, $end)) ;



    return rtrim(substr($str, 0, $len)) . $etc;

}



// --------------------------------------------

function virg2ponto($valor)

//-----------------------------------------------

{

    return str_replace(',', '.', $valor);

}



// --------------------------------------------

function ponto2virg($valor)

//-----------------------------------------------

{

    return str_replace('.', ',', $valor);

}



//-----------------------------------------------

function data2mysql($data)

//-----------------------------------------------

{

    return substr($data, 6, 4) . '-' . substr($data, 3, 2)

        . '-' . substr($data, 0, 2);

}



//-----------------------------------------------

function mysql2data($data)

//-----------------------------------------------

{

    if ($data == '')

        return '';

    else

        return substr($data, 8, 2) . '/' . substr($data, 5, 2)

            . '/' . substr($data, 0, 4);

}



//-----------------------------------------------

function sub_data($data, $dias)

//-----------------------------------------------

{

    $data_e = explode("/", $data);

    $data2 = date("m/d/Y", mktime(0, 0, 0, $data_e[1], $data_e[0] - $dias, $data_e[2]));

    $data2_e = explode("/", $data2);

    $data_final = $data2_e[1] . "/" . $data2_e[0] . "/" . $data2_e[2];

    return $data_final;

}



//-----------------------------------------------

function som_data($data, $dias)

//-----------------------------------------------

{

    $data_e = explode("/", $data);

    $data2 = date("m/d/Y", mktime(0, 0, 0, $data_e[1], $data_e[0] + $dias, $data_e[2]));

    $data2_e = explode("/", $data2);

    $data_final = $data2_e[1] . "/" . $data2_e[0] . "/" . $data2_e[2];

    return $data_final;

}

