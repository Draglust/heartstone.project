<?php

namespace App\Http\Services;

use App\Models\Json;
use App\Models\Realm;
use App\Models\Item;
use App\Models\Owner;
use App\Models\ClassSubclass;
use App\Models\Price;
use Illuminate\Http\Request;

class ServiceJson extends Service
{
	public function getJson($url) {
        $contenido = json_decode(file_get_contents($url),TRUE);
        $jsonExists = $this->existsJson($contenido['files'][0]['lastModified']);
        /**
         * [Guardamos json si no existe]
         */
        if (!$jsonExists) {
        	$fecha = date('Y-m-d H:i:s', $contenido['files'][0]['lastModified']/1000);
        	$jsonGuardado = $this->saveJson($contenido['files'][0]['url'],$contenido['files'][0]['lastModified'],$fecha);

        	if($jsonGuardado){
        		return $jsonGuardado;
        	}
        }
        return FALSE;
    }

    public function existsJson($fecha) {
    	$jsonExists = Json::Fecha_numerica($fecha)->get()->toArray();

    	return $jsonExists;
    }

    public function saveJson($url,$fecha_numerica,$fecha) {
    	$newJson = new Json;
        $newJson->url = $url;
        $newJson->fecha_numerica = $fecha_numerica;
        $newJson->fecha = $fecha;
        $saved = $newJson->save();
        if(!$saved){
            return FALSE;
        }
        $retorno['id'] = $newJson->id;
        $retorno['url'] = $url;
        $retorno['fecha'] = $fecha_numerica;

        return $retorno;
    }

    public function getAuctions($url){
        $contenido_url = '';
            
        $handle = fopen($url, "r");
        if ($handle) {
            while (fgets($handle) !== false || !feof($handle)):
                $line = fgets($handle);
                if(strstr($line, '"auc"')){
                    $line = str_replace("\r\n",'', $line);
                    $line = str_replace("\t",'', $line);
                    $line = trim($line);
                    $line = trim($line,',');
                    $elementos_a_tratar = explode(',', $line);
                    foreach($elementos_a_tratar as $key=> $pareja_campo_valor){

                        $pareja_campo_valor = trim(str_replace('"','', $pareja_campo_valor));
                        $pareja_campo_valor = str_replace('{','', $pareja_campo_valor);
                        $pareja_campo_valor = str_replace('}','', $pareja_campo_valor);
                        $pareja_campo_valor = str_replace('[','', $pareja_campo_valor);
                        $pareja_campo_valor = str_replace(']','', $pareja_campo_valor);
                        $valores = explode(':', $pareja_campo_valor);
                        if(isset($valores[1])){
                            $item_subasta[$valores[0]] = $valores[1];
                        }
                        else{
                           
                        }
                    }

                    $subasta[] = $item_subasta;
                    unset($item_subasta);
                }
                else{
                     
                }
                //$contenido_url .=$line;
            endwhile;
            fclose($handle);
        } else {
            return 'Error on file opening.';
        }

        return $subasta;

    }
}
