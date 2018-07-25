<?php

namespace App\Http\Services;

use App\Models\Json;
use App\Models\Realm;
use App\Models\Item;
use App\Models\Owner;
use App\Models\ClassSubclass;
use App\Models\Price;
use App\Http\Services\ServiceWeb;
use Illuminate\Http\Request;

class ServiceOwner extends Service
{
	public function getOwners($subastas, $datos) {
        $arrayOwners = array();
        $subastaOwners = [];
        $retorno['realms'] = [];
        $todosLosOwner = Owner::all()->toArray();
        if(isset($todosLosOwner)){
            foreach($todosLosOwner as $duenno){
                $arrayOwners[$duenno['Nombre']] = $duenno['Faccion'];
            }
        }
        
        $todosRealms = Realm::all()->toArray();
        foreach($todosRealms as $reino){
            $arrayRealms[$reino['Nombre']] = $reino['Id'];
        }

        foreach ($subastas as $key => $subasta) {
            if($subasta['owner'] == '???'){
                unset($subasta);
                continue;
            }
            $owner['Nombre'] = $subasta['owner'];
            $owner['ReinoNombre'] = $subasta['ownerRealm'];
            if(!in_array($arrayRealms[$owner['ReinoNombre']], $retorno['realms'])){
                $retorno['realms'][] = $arrayRealms[$owner['ReinoNombre']];
            }
            if(!isset($arrayOwners[$owner['Nombre']])){
                $subastaOwners[$owner['Nombre']] = $owner;
                unset($owner);
            }
        }
        
        if(isset($subastaOwners)){
            foreach ($subastaOwners as $keyOwner => $ownerToInsert) {
                set_time_limit(20);
                try{

                    $url = "https://eu.api.battle.net/wow/character/{$ownerToInsert['ReinoNombre']}/{$ownerToInsert['Nombre']}?locale=es_ES&apikey=8hw8e9kun6sf8kfh2qvjzw22b9wzzjek";
                    //echo $url;die();
                    $faccionExtraida = json_decode(ServiceWeb::curl($url), TRUE);
                    /*echo $url;
                    var_dump($faccionExtraida);
                    die();*/
                    //$faccionExtraida = json_decode(file_get_contents($url), TRUE);
                    $faccion = $faccionExtraida;
                    unset($faccionExtraida);

                    if (!isset($faccion['faction'])) {
                        $faccion['faction'] = 3;
                    }
                    $ownerToInsert['Id'] = NULL;
                    $ownerToInsert['Faccion'] = $faccion['faction'];
                    $ownerToInsert['Realm_id'] = $arrayRealms[$ownerToInsert['ReinoNombre']];
                    unset($ownerToInsert['ReinoNombre']);
                   \DB::table('owner')->insert($ownerToInsert);
                }
                catch(\Exception $e){
                    echo $url;
                    echo $e->getMessage();
                }

            }
        }
        $retorno['num_owners'] = count($subastaOwners);
        return $retorno;
    }

    public function existsOwner($nombre) {
    	$jsonExists = Json::Fecha_numerica($fecha)->get()->toArray();

    	return $jsonExists;
    }

    public function saveJson($url,$fecha_numerica,$fecha) {
    	$newJson = new Json;
        $newJson->Url = $url;
        $newJson->Fecha_numerica = $fecha_numerica;
        $newJson->Fecha = $fecha;
        $saved = $newJson->save();
        if(!$saved){
            return FALSE;
        }
        $retorno['id'] = $newJson->Id;
        $retorno['url'] = $url;
        $retorno['fecha'] = round($fecha_numerica);

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
