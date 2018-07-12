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
        $objetosEncontrados = Item::NombreIsNull()->get()->toArray();
        $arrayClaseSubclase = array();
        $arrayIdClaseSubclase = array();

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
                        $jsonWeb = json_decode($salida[1][0],TRUE);
                        if(isset($jsonWeb['name_eses'])) {
                            $enc = mb_detect_encoding($jsonWeb['name_eses'], "UTF-8,ISO-8859-1");
                            $nombre = iconv($enc, "UTF-8", $jsonWeb['name_eses']);
                        }
                        if(isset($jsonWeb['quality'])) {
                            $calidad = $jsonWeb['quality'];
                        }
                        if(isset($jsonWeb['icon'])) {
                            $icono = $jsonWeb['icon'];
                        }
                        if(isset($jsonWeb['reqlevel'])) {
                            $icono = $jsonWeb['reqlevel'];
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
                                    } else {
                                        print_r($contenido);
                                        return "Error on item's expansion.";
                                    }
                                }
                            }
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

                    preg_match_all("/PageTemplate.set\({ breadcrumb: \[(.*)\]}\);/Um", $contenido, $salida, PREG_PATTERN_ORDER);
                    if (isset($salida[1][0])) {
                        $rawTipo = explode(',', $salida[1][0]);
                        $clase = $rawTipo[2];
                        $subclase = $rawTipo[3];
                        $subclaseNombre = '';
                        unset($salida);
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
                        return "Error on item's class and sublclass.";
                    }

                    if (!in_array($clase . '_' . $subclase, $arrayClaseSubclase)) {
                        if (isset($clase) && isset($subclase)) {
                            $classSubclassExists = ClassSubclass::Clase_Subclase($clase, $subclase)->get()->toArray();
                            if (!$classSubclassExists) {
                                $newClassSubclass = new ClassSubclass;
                                $newClassSubclass->Clase_id = $clase;
                                $newClassSubclass->Clase_nombre = $claseNombre;
                                $newClassSubclass->Subclase_id = $subclase;
                                $newClassSubclass->Subclase_nombre = $subclaseNombre;
                                $saved = $newClassSubclass->save();
                                if(!$saved){
                                    return 'Error on saving subClass.';
                                }
                                $classSubclassExists[0]['Id'] = $newClassSubclass->Id;
                            }
                            $arrayClaseSubclase[] = $clase . '_' . $subclase;
                            $arrayIdClaseSubclase[$clase . '_' . $subclase] = $classSubclassExists[0]['Id'];
                        }
                    }

                    if (isset($nombre) && isset($descripcion) && isset($calidad) && isset($icono) && isset($nivelRequerido) && isset($nivelObjeto) && isset($expansion)) {
                        $updateItem = Item::find($objeto['Id']);
                        $updateItem->Nombre = $nombre;
                        $updateItem->Descripcion = $descripcion;
                        $updateItem->Calidad = $calidad;
                        $updateItem->Icono = $icono;
                        $updateItem->Nivel_requerido = $nivelRequerido;
                        $updateItem->Nivel_objeto = $nivelObjeto;
                        $updateItem->Expansion = $expansion;
                        $updateItem->Class_subclass_id = $classSubclassExists[0]['Id'];
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
                    }
            }
            }
        }
        return 'All items completed.';
    }

}