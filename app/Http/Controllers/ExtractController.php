<?php

namespace App\Http\Controllers;

use App\Models\Json;
use App\Models\Realm;
use App\Models\Item;
use App\Models\Owner;
use App\Models\ClassSubclass;
use App\Models\Price;
use Illuminate\Http\Request;

class ExtractController extends Controller {

    public function treatJson() {
        $rawJson = $this->getJson();
        if ($rawJson) {
            $rawSubastas = $this->getSubastas($rawJson['url']);
        } else {
            $rawSubastas = array();
        }
        if (count($rawSubastas) > 0) {
            $retornoPrecios = $this->getPrices($rawSubastas, $rawJson);
            $precios = $retornoPrecios['items'];
            $treatedSubastas = $retornoPrecios['subastas'];
            $reinos = $retornoPrecios['reinos'];
        } else {
            dd('Sin subastas o url ya insertada');
        }
        if (isset($precios)) {
            $preciosInsertados = $this->putPrices($precios, $rawJson['fecha']);
        }
        if ($preciosInsertados) {
            $subastasReales = $this->putSubastas($precios, $treatedSubastas);
        } else {
            dd('Sin precios insertados');
        }
    }

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
                $contenido = file_get_contents("http://es.wowhead.com/item={$objeto['id']}");
                if ($contenido) {
                    $preg = "_" . $objeto['id'] . "=(.*);";
                    preg_match_all("/$preg/Um", $contenido, $salida, PREG_PATTERN_ORDER);
                    if (isset($salida[1][0])) {
                        $var = explode(',', $salida[1][0]);
                        foreach ($var as $keyVar => $valores) {
                            if (strpos($valores, 'name_eses')) {
                                $jsonNombre = explode(':', $valores);
                                $nombre = html_entity_decode(str_replace('"', '', $jsonNombre[1]));
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
                            }
                        }
                        unset($salida);
                    } else {
                        dd('Error en la web del objeto');
                    }

                    preg_match_all("/Nivel de objeto <!--ilvl-->(.*)<\/span>/Um", $contenido, $salida, PREG_PATTERN_ORDER);
                    if (isset($salida[1][0])) {
                        $nivelObjeto = $salida[1][0];
                        unset($salida);
                    } else {
                        dd('Error en el nivel del objeto');
                    }

                    preg_match_all("/<meta name=\"description\" content=\"(.*)\">/Um", $contenido, $salida, PREG_PATTERN_ORDER);
                    if (isset($salida[1][0])) {
                        $descripcion = html_entity_decode($salida[1][0]);
                        unset($salida);
                    } else {
                        dd('Error en la descripcion del objeto');
                    }

                    if (strpos($contenido, 'World of Warcraft Clásico.')) {
                        $expansion = 'Clásico';
                    } else {
                        preg_match_all("/World of Warcraft:(.*)\./Um", $contenido, $salida, PREG_PATTERN_ORDER);
                        if (isset($salida[1][0])) {
                            $expansion = html_entity_decode($salida[1][0]);
                            unset($salida);
                        } else {
                            dd('Error en la expansion del objeto');
                        }
                    }

                    preg_match_all("/PageTemplate.set\({ breadcrumb: \[(.*)\]}\);/Um", $contenido, $salida, PREG_PATTERN_ORDER);
                    if (isset($salida[1][0])) {
                        $rawTipo = explode(',', $salida[1][0]);
                        $clase = $rawTipo[2];
                        $subclase = $rawTipo[3];
                        unset($salida);
                        foreach ($jsonClasses['classes'] as $keyClass => $valueClass) {
                            if ($valueClass['class'] == $clase) {
                                $claseNombre = $valueClass['name'];
                                foreach ($valueClass['subclasses'] as $keySub => $valueSub) {
                                    if ($valueSub['subclass'] == $subclase) {
                                        $subclaseNombre = $valueSub['name'];
                                        break 2;
                                    }
                                }
                            }
                        }
                    } else {
                        dd('Error en la clase y subclase del objeto');
                    }

                    if (!in_array($clase . '_' . $subclase, $arrayClaseSubclase)) {
                        if (isset($clase) && isset($subclase)) {
                            $classSubclassExists = Class_Subclass::Clase_Subclase($clase, $subclase)->get() - toArray();
                            if (!$classSubclassExists) {
                                $newClassSubclass = new Class_Subclass;
                                $newClassSubclass->clase_id = $clase;
                                $newClassSubclass->clase_nombre = $claseNombre;
                                $newClassSubclass->subclase_id = $subclase;
                                $newClassSubclass->subclase_nombre = $subclaseNombre;
                                $saved = $newClassSubclass->save();
                                if(!$saved){
                                    dd('Error al guardar ClassSubclass');
                                }
                                $classSubclassExists[0]['id'] = $newClassSubclass->id;
                            }
                            $arrayClaseSubclase[] = $clase . '_' . $subclase;
                            $arrayIdClaseSubclase[$clase . '_' . $subclase] = $classSubclassExists[0]['id'];
                        }
                    }

                    if (isset($nombre) && isset($descripcion) && isset($calidad) && isset($icono) && isset($nivelRequerido) && isset($nivelObjeto) && isset($expansion)) {
                        $newItem = new Item;
                        $newItem->nombre = $nombre;
                        $newItem->descripcion = $descripcion;
                        $newItem->calidad = $calidad;
                        $newItem->icono = $icono;
                        $newItem->nivel_requerido = $nivelRequerido;
                        $newItem->nivel_objeto = $nivelObjeto;
                        $newItem->expansion = $expansion;
                        $newItem->class_subclass_id = $classSubclassExists[0]['id'];
                        $saved = $newItem->save();
                        if(!$saved){
                            dd('Error al guardar Item');
                        }
                    }
                }
            }
        }
    }

    public function getJson() {
        $url = "https://eu.api.battle.net/wow/auction/data/shen'dralar?locale=es_ES&apikey=8hw8e9kun6sf8kfh2qvjzw22b9wzzjek";
        $contenido = json_decode(file_get_contents($url),TRUE);
        $jsonExists = Json::Fecha_numerica($contenido['files'][0]['lastModified'])->get()->toArray();
        /**
         * [Guardamos json si no existe]
         */
        if (!$jsonExists) {
            $newJson = new Json;
            $newJson->url = $contenido['files'][0]['url'];
            $newJson->fecha_numerica = $contenido['files'][0]['lastModified'];
            $newJson->fecha = date('Y-m-d H:i:s', $contenido['files'][0]['lastModified']);
            $saved = $newJson->save();
            if(!$saved){
                dd('Error al guardar Json');
            }
            $retorno['id'] = $newJson->id;
            $retorno['url'] = $contenido['files'][0]['url'];
            $retorno['fecha'] = $contenido['files'][0]['lastModified'];
            return $retorno;
        }
        return FALSE;
    }

    public function getSubastas($url) {
        //Bucles para obtener las subastas extraidas del JSON
        //Probar con un file_get_contents estandar también

        $contenido = json_decode(file_get_contents($url),TRUE);

        return $contenido['auctions'];
    }

    public function getPrices($subastas, $datos) {
        $arrayItems = array();
        $arrayRealms = array();
        $json_id = $datos['id'];
        foreach ($subastas as $key => $subasta) {
            $subastas[$key]['idJson'] = $json_id;
            /**
             * [Inicializamos el tiempo limite de ejecucion en cada subasta para que no expire]
             */
            set_time_limit(15);
            /**
             * [Guardado del reino si no existe]
             * [Usamos un array para la lista de reinos del json actual]
             */
            if (!in_array($subasta['ownerRealm'], $arrayRealms)) {
                $realmExists = Realm::Nombre($subasta['ownerRealm'])->get()->toArray();
                if (!$realmExists) {
                    $newRealm = new Realm;
                    $newRealm->Nombre = $subasta['ownerRealm'];
                    $saved = $newRealm->save();
                    if(!$saved){
                        dd('Error al guardar Realm');
                    }
                    $realmExists[0]['Id'] = $newRealm->Id;
                }
                $arrayRealms[] = $subasta['ownerRealm'];
            }
            if(isset($realmExists[0]['Id'])) {
                $subastas[$key]['reinoReal'] = $realmExists[0]['Id'];
            }else{dd($realmExists);}
            /**
             * [Comprobamos la facción a la que pertenece la subasta]
             */
            $retornoFaccion = $this->getFaction($subasta);
            /**
             * [Si no encontramos facción para una subasta]
             * [Quizás hemos llegado al limite de peticiones]
             * [1:Horda;]
             */
            if (!$retornoFaccion) {
                //dd('No hay faccion disponible');
                $retornoFaccion['faction'] = -1;
            }
            $faccionSubasta = $retornoFaccion['faction'];
            $subastas[$key]['faccionReal'] = $faccionSubasta;
            /**
             * [Guardado del objeto si no existe]
             * [Usamos un array para la lista de objetos del json actual]
             */
            if (!in_array($subasta['item'], $arrayItems)) {
                $itemExists = Item::Id($subasta['item'])->get()->toArray();
                if (!$itemExists) {
                    $newItem = new Item;
                    $newItem->Id = $subasta['item'];
                    $saved = $newItem->save();
                    if(!$saved){
                        dd('Error al guardar Item');
                    }
                }
                $arrayItems[] = $subasta['item'];
            }
            /**
             * [Inicializamos array de un objeto si no existe]
             */
            if (!isset($items[$faccionSubasta][$subasta['item']])) {
                $items[$faccionSubasta][$subasta['item']] = array();
                $items[$faccionSubasta][$subasta['item']]['maximo'] = 0;
                $items[$faccionSubasta][$subasta['item']]['calculo_pmp'] = 0;
                $items[$faccionSubasta][$subasta['item']]['total_items'] = 0;
            }
            /**
             * [Si existe precio de compra, calculamos máximo]
             */
            if (isset($subasta['buyout'])) {
                if ($items[$faccionSubasta][$subasta['item']]['maximo'] < (round($subasta['buyout'] / $subasta['quantity'], 0, PHP_ROUND_HALF_UP))) {
                    $items[$faccionSubasta][$subasta['item']]['maximo'] = round($subasta['buyout'] / $subasta['quantity'], 0, PHP_ROUND_HALF_UP);
                }
                /**
                 * [Si no existe minimo, asignamos el primero por objeto por defecto]
                 */
                if (!isset($items[$faccionSubasta][$subasta['item']]['minimo'])) {
                    $items[$faccionSubasta][$subasta['item']]['minimo'] = round($subasta['buyout'] / $subasta['quantity'], 0, PHP_ROUND_HALF_UP);
                }
                /**
                 * [Calculamos minimo]
                 */
                if ($items[$faccionSubasta][$subasta['item']]['minimo'] > (round($subasta['buyout'] / $subasta['quantity'], 0, PHP_ROUND_HALF_UP))) {
                    $items[$faccionSubasta][$subasta['item']]['minimo'] = round($subasta['buyout'] / $subasta['quantity'], 0, PHP_ROUND_HALF_UP);
                }
                /**
                 * [Obtenemos valores para calcular el precio medio ponderado]
                 */
                $items[$faccionSubasta][$subasta['item']]['calculo_pmp'] += $subasta['quantity'] * $subasta['buyout'];
                $items[$faccionSubasta][$subasta['item']]['total_items'] += $subasta['quantity'];
            } else {
                /**
                 * [De momento no usaremos las subastas sin precio de compra]
                 */
                unset($subastas[$key]);
            }
        }
        /**
         * [Calculamos el precio medio ponderado por objeto]
         */
        foreach ($items as $keyFaccion => $itemElement) {
            foreach ($itemElement as $keyItem => $item) {
                $items[$keyFaccion][$keyItem]['pmp'] = $item['calculo_pmp'] / $item['total_items'];
            }
        }
        $arrayRetorno['items'] = $items;
        $arrayRetorno['subastas'] = $subastas;
        $arrayRetorno['reinos'] = $arrayRealms;

        return $arrayRetorno;
    }

    public function putPrices($precios, $fecha) {
        $arrayPrecios = array();
        foreach ($precios as $keyFaccion => $elementPrecio) {
            foreach ($elementPrecio as $keyPrecio => $precio) {
                /**
                 * [Si el precio no ha sido insertado en esta tanda, comprobamos en BD]
                 */
                if (!in_array($keyFaccion . '-' . $keyPrecio, $arrayPrecios)) {
                    $priceExists = Price::Item_fecha_faccion($keyPrecio, $fecha, $keyFaccion)->get()->toArray();
                    if (!$priceExists) {
                        $newPrice = new Price;
                        $newPrice->precio_minimo = $precio['minimo'];
                        $newPrice->precio_maximo = $precio['maximo'];
                        $newPrice->precio_medio = $precio['pmp'];
                        $newPrice->total_objetos = $precio['total_items'];
                        $newPrice->faccion = $keyFaccion;
                        $saved = $newPrice->save();
                        if(!$saved){
                            dd('Error al guardar Price');
                        }
                    }
                    $arrayPrecios[] = $keyFaccion . '-' . $keyPrecio;
                }
            }
        }
        return TRUE;
    }

    public function getFaction($subasta) {
        $ownerRealm = str_replace("'", "", $subasta['ownerRealm']);
        /**
         * [Para la búsqueda de faccion por web]
         * [Buscar Logo--alliance o Logo--horde]
         */
        $url_web = "https://worldofwarcraft.com/es-es/character/{$ownerRealm}/{$subasta['owner']}";
        $url = "https://eu.api.battle.net/wow/character/{$subasta['ownerRealm']}/{$subasta['owner']}?locale=es_ES&apikey=8hw8e9kun6sf8kfh2qvjzw22b9wzzjek";
        $faccion = json_decode(@file_get_contents($url), TRUE);

        //preg_match_all("/Nivel de objeto <!--ilvl-->(.*)<\/span>/Um", $contenido, $salida, PREG_PATTERN_ORDER);
        /**
         * [Si ha array de retorno, lo devolvemos, si no devolvemos FALSE]
         */
        if (isset($faccion['faction'])) {
            return $faccion;
        }

        return FALSE;
    }

    public function putSubastas($precios, $subastas) {
        $arrayOwners = array();
        $arrayIdOwners = array();
        foreach ($subastas as $keySubasta => $subasta) {
            foreach ($precios as $keyFaccion => $itemPrecio) {
                foreach ($itemPrecio as $keyPrecio => $precio) {
                    if ($subasta['faccionReal'] == $keyFaccion) {
                        if ($subasta['buyout'] < ($precio['pmp'] * (0.80))) {
                            if (!in_array($subasta['owner'], $arrayOwners)) {
                                $ownerExists = Owner::Nombre($subasta['owner'])->get()->toArray();
                                if (!$ownerExists) {
                                    $newOwner = new Owner;
                                    $newOwner->nombre = $subasta['owner'];
                                    $newOwner->realm_id = $subasta['reinoReal'];
                                    $newOwner->faccion = $subasta['faccionReal'];
                                    $saved = $newOwner->save();
                                    if(!$saved){
                                        dd('Error al guardar Owner');
                                    }
                                    $ownerExists[0]['Id'] = $newOwner->id;
                                }
                                $arrayOwners[] = $subasta['owner'];
                                $arrayIdOwners[$subasta['owner']] = $ownerExists[0]['Id'];
                            }

                            $newAuction = new Auction;
                            $newAuction->apuesta = $subasta['bid'];
                            $newAuction->compra = $subasta['buyout'];
                            $newAuction->cantidad = $subasta['quantity'];
                            $newAuction->tiempo_restante = $subasta['timeLeft'];
                            $newAuction->item_id = $subasta['item'];
                            $newAuction->realm_id = $subasta['reinoReal'];
                            $newAuction->json_id = $subasta['idJson'];
                            $newAuction->owner_id = $arrayIdOwners[$subasta['owner']];
                            $saved = $newAuction->save();
                            if(!$saved){
                                dd('Error al guardar Auction');
                            }
                        }
                    }
                }
            }
        }
    }

}
