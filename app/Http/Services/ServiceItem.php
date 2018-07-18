<?php

namespace App\Http\Services;

use App\Models\Json;
use App\Models\Realm;
use App\Models\Item;
use App\Models\Owner;
use App\Models\ClassSubclass;
use App\Models\Price;
use Illuminate\Http\Request;

class ServiceItem extends Service
{
	public function treatItems() {
        $objetosEncontrados = Item::NombreIsNull()->orderBy('Id','DESC')->get()->toArray();
        $arrayClaseSubclase = array();

        $prevClassSubclass = ClassSubclass::all()->toArray();
        foreach ($prevClassSubclass as $key => $csValue) {
            $arrayClaseSubclase[$csValue['Clase_id'].'_'.$csValue['Subclase_id']] = $csValue['Id'];
        }
        /**
         * [Json que contiene todas las clases y subclases del juego]
         */
        $jsonClasses = json_decode(file_get_contents("https://eu.api.battle.net/wow/data/item/classes?locale=es_ES&apikey=8hw8e9kun6sf8kfh2qvjzw22b9wzzjek"), TRUE);

        if (count($objetosEncontrados) > 0) {
                    foreach ($objetosEncontrados as $keyObjeto => $objeto) {
                        set_time_limit(15);
                        $context = stream_context_create(
                        array(
                            'http' => array(
                                'max_redirects' => 101
                                //'follow_location' => false
                            )
                        )
                    );
            $contenido = file_get_contents("http://es.wowhead.com/item={$objeto['Id']}", false, $context);

                if ($contenido) {
                    $preg = "_\[" . $objeto['Id'] . "\]=(.*);";
                    preg_match_all("/$preg/Um", $contenido, $salida, PREG_PATTERN_ORDER);
                    if (isset($salida[1][0])) {
                        foreach ($salida[1] as $key => $jsonValores) {
                            $jsonWeb = json_decode($salida[1][$key],TRUE);
                            if(isset($jsonWeb['name_eses'])) {
                               break;
                            }
                        }
                        if(isset($jsonWeb['name_eses'])) {
                            $enc = mb_detect_encoding($jsonWeb['name_eses'], "UTF-8,ISO-8859-1");
                            $nombre = iconv($enc, "UTF-8", $jsonWeb['name_eses']);
                        }
                        else{
                            var_dump($jsonWeb);
                            dd($objeto['Id']);
                            return 'Error on extracting name.';
                        }
                        if(isset($jsonWeb['quality'])) {
                            $calidad = $jsonWeb['quality'];
                        }
                        if(isset($jsonWeb['icon'])) {
                            $icono = $jsonWeb['icon'];
                        }
                        if(isset($jsonWeb['reqlevel'])) {
                            $nivelRequerido = $jsonWeb['reqlevel'];
                        }
                        elseif(isset($jsonWeb['jsonequip']['reqlevel'])){
                            $nivelRequerido = $jsonWeb['jsonequip']['reqlevel'];
                        }
                        else {
                            $nivelRequerido = 0;
                        }
                        /*$var = explode(',', $salida[1][0]);
                        foreach ($var as $keyVar => $valores) {
                            /*if (strpos($valores, 'name_eses')) {
                                $jsonNombre = explode(':', $valores);
                                $nombre = utf8_decode(html_entity_decode(str_replace('"', '', $jsonNombre[1])));
                            }
                            if (strpos($valores, 'quality')) {
                                $jsonCalidad = explode(':', $valores);
                                $calidad = str_replace('"', '', $jsonCalidad[1]);
                            }
                            if (strpos($valores, 'icon')) {
                                $jsonIcono = explode(':', $valores);
                                $icono = str_replace('"', '', $jsonIcono[1]);
                            }
                            if (strpos($valores, 'reqlevel')) {
                                $jsonNivelReq = explode(':', $valores);
                                $nivelRequerido = str_replace('"', '', $jsonNivelReq[1]);
                                $nivelRequerido = str_replace('}', '', $nivelRequerido);
                            }
                        }*/
                        unset($salida);
                    } else {
                        print_r($preg);
                        print_r($contenido);
                        return "Error on item's web.";
                    }

                    preg_match_all("/Nivel de objeto <!--ilvl-->(.*)<\/span>/Um", $contenido, $salida, PREG_PATTERN_ORDER);
                    if (isset($salida[1][0])) {
                        $nivelObjeto = $salida[1][0];
                        $nivelObjeto = str_replace('+','', $nivelObjeto);
                        unset($salida);
                    } else {
                        return "Error on item's level.";
                    }

                    preg_match_all("/<meta name=\"description\" content=\"(.*)\">/Um", $contenido, $salida, PREG_PATTERN_ORDER);
                    if (isset($salida[1][0])) {
                        $descripcion = html_entity_decode($salida[1][0]);
                        unset($salida);
                    } else {
                        return "Error on item's description.";
                    }

                    if (strpos($contenido, 'World of Warcraft Clásico.')) {
                        $expansion = 'Clásico';
                    } else {
                        preg_match_all("/<meta name=\"keywords\" content=\"(.*)\">/Um", $contenido, $salida3, PREG_PATTERN_ORDER);
                        if (isset($salida3[1][0])) {
                            if (strpos($salida3[1][0], 'Clásico')) {
                                $expansion = 'Clásico';
                            }
                            elseif (strpos($salida3[1][0], 'The Burning Crusade')) {
                                $expansion = 'The Burning Crusade';
                            }
                            elseif (strpos($salida3[1][0], 'Mists of Pandaria')) {
                                $expansion = 'Mists of Pandaria';
                            }
                            elseif (strpos($salida3[1][0], 'Cataclysm')) {
                                $expansion = 'Cataclysm';
                            }
                            elseif (strpos($salida3[1][0], 'Legion')) {
                                $expansion = 'Legion';
                            }
                            elseif (strpos($salida3[1][0], 'Warlords of Draenor')) {
                                $expansion = 'Warlords of Draenor';
                            }
                            elseif (strpos($salida3[1][0], 'Wrath of the Lich King')) {
                                $expansion = 'Wrath of the Lich King';
                            }
                            else{
                                preg_match_all("/World of Warcraft:(.*)\./Um", $contenido, $salida, PREG_PATTERN_ORDER);
                                if (isset($salida[1][0])) {
                                    $expansion = html_entity_decode($salida[1][0]);
                                    unset($salida);
                                } else {
                                    preg_match_all("/<meta name=\"keywords\" content=\"(.*)\">/Um", $contenido, $salida2, PREG_PATTERN_ORDER);
                                    if (isset($salida2[1][0])) {
                                        if (strpos($contenido, 'The Burning Crusade')) {
                                            $expansion = 'The Burning Crusade';
                                        }
                                        if (strpos($contenido, 'Mists of Pandaria')) {
                                            $expansion = 'Mists of Pandaria';
                                        }
                                        if (strpos($contenido, 'Cataclysm')) {
                                            $expansion = 'Cataclysm';
                                        }
                                        if (strpos($contenido, 'Legion')) {
                                            $expansion = 'Legion';
                                        }
                                        if (strpos($contenido, 'Warlords of Draenor')) {
                                            $expansion = 'Warlords of Draenor';
                                        }
                                        if (strpos($contenido, 'Wrath of the Lich King')) {
                                            $expansion = 'Wrath of the Lich King';
                                        }
                                    } else {
                                        print_r($contenido);
                                        return "Error on item's expansion.";
                                    }
                                }
                            }
                            //print_r($expansion);die();
                        }
                        else {
                            preg_match_all("/World of Warcraft:(.*)\./Um", $contenido, $salida, PREG_PATTERN_ORDER);
                            if (isset($salida[1][0])) {
                                $expansion = html_entity_decode($salida[1][0]);
                                unset($salida);
                            } else {
                                preg_match_all("/<meta name=\"keywords\" content=\"(.*)\">/Um", $contenido, $salida2, PREG_PATTERN_ORDER);
                                if (isset($salida2[1][0])) {
                                    if (strpos($contenido, 'The Burning Crusade')) {
                                        $expansion = 'The Burning Crusade';
                                    }
                                } else {
                                    print_r($contenido);
                                    return "Error on item's expansion.";
                                }
                            }
                        }
                    }
                    if(!isset($expansion)){
                        dd($objeto['Id']);
                    }
                    $preg = "\\$\.extend\(g_items\[".$objeto['Id']."\], (.*)\);";
                    preg_match_all("/$preg/Um", $contenido, $salida, PREG_PATTERN_ORDER);
                    //preg_match_all("/PageTemplate.set\({ breadcrumb: \[(.*)\]}\);/Um", $contenido, $salida, PREG_PATTERN_ORDER);
                    if (isset($salida[1][0])) {
                        $rawTipo = json_decode($salida[1][0],TRUE);
                        //dd($rawTipo);
                        //$rawTipo = explode(',', $salida[1][0]);
                        $clase = trim($rawTipo['classs']);
                        $subclase = trim($rawTipo['subclass']);
                        $claseNombre = '';
                        $subclaseNombre = '';
                        //unset($salida);
                        foreach ($jsonClasses['classes'] as $keyClass => $valueClass) {
                            if ($valueClass['class'] == $clase) {
                                $claseNombre = $valueClass['name'];
                                foreach ($valueClass['subclasses'] as $keySub => $valueSub) {
                                    if(isset($valueSub['subclass'])){
                                        if ($valueSub['subclass'] == $subclase) {
                                            $subclaseNombre = $valueSub['name'];
                                            break 2;
                                        }
                                    }
                                    else{
                                        foreach($valueSub as $nValueSub){
                                            if ($nValueSub['subclass'] == $subclase) {
                                                $subclaseNombre = $valueSub['name'];
                                                break 2;
                                            }
                                        }
                                    }

                                }
                            }
                        }
                    } else {
                        echo $preg;
                        return "Error on item's class and sublclass.";
                    }

                    if (!array_key_exists($clase . '_' . $subclase, $arrayClaseSubclase)) {
                        if (isset($clase) && isset($subclase) && is_numeric($clase) && is_numeric($subclase)) {
                            //$classSubclassExists = ClassSubclass::Clase_Subclase($clase, $subclase)->get()->toArray();
                            //if (!$classSubclassExists) {
                                $newClassSubclass = new ClassSubclass;
                                $newClassSubclass->Clase_id = $clase;
                                $newClassSubclass->Clase_nombre = $claseNombre;
                                $newClassSubclass->Subclase_id = $subclase;
                                $newClassSubclass->Subclase_nombre = $subclaseNombre;
                                $saved = $newClassSubclass->save();
                                if(!$saved){
                                    return 'Error on saving subClass.';
                                }
                            //}
                            $returningId = ClassSubclass::Clase_Subclase($clase, $subclase)->get()->toArray();
                            $arrayClaseSubclase[$clase . '_' . $subclase] = $returningId[0]['Id'];
                        }
                        else{
                            return 'No class or subclass.';
                        }
                    }
                    $classSubclassId = $arrayClaseSubclase[$clase . '_' . $subclase];
                    if(strlen($expansion)>100){
                        dd($objeto['Id']);
                    }
                    if (isset($nombre) && isset($descripcion) && isset($calidad) && isset($icono) && isset($nivelRequerido) && isset($nivelObjeto) && isset($expansion) && $classSubclassId != '' && $classSubclassId != NULL) {
                        $updateItem = Item::find($objeto['Id']);
                        $updateItem->Nombre = $nombre;
                        $updateItem->Descripcion = $descripcion;
                        $updateItem->Calidad = $calidad;
                        $updateItem->Icono = $icono;
                        $updateItem->Nivel_requerido = $nivelRequerido;
                        $updateItem->Nivel_objeto = $nivelObjeto;
                        $updateItem->Expansion = $expansion;
                        $updateItem->Class_subclass_id = $classSubclassId;
                        $saved = $updateItem->save();
                        if(!$saved){
                            return 'Error on saving item.';
                        }
                        unset($nombre);
                        unset($descripcion);
                        unset($calidad);
                        unset($icono);
                        unset($nivelRequerido);
                        unset($nivelObjeto);
                        unset($expansion);
                        unset($classSubclassId);
                    }
                    else{
                        var_dump($objeto['Id']);
                        var_dump($returningId);
                        var_dump($arrayClaseSubclase);
                        return 'Error on saving item data.';
                    }
            }
            }
        }
        return 'All items completed.';
    }

    public function getAllItems(){
        $todosLosItemsBD = Item::whereNotNull('Id')->get(['Id'])->toArray();

        return $todosLosItemsBD;
    }

    public function getDateFactionItemPrices($fecha){
        $dateFactionItemPrices = Price::Fecha($fecha)->get(['Item_id','Faccion','Fecha'])->toArray();
        foreach ($dateFactionItemPrices as $key => $value) {
            $retornoDateFactionItemPrices[$value['Item_id'].'-'.$value['Faccion'].'-'.$value['Fecha']] = $value['Item_id'].'-'.$value['Faccion'].'-'.$value['Fecha'];
        }

        return $dateFactionItemPrices;
    }
}