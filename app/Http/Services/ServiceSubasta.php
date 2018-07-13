<?php

namespace App\Http\Services;

use App\Models\Json;
use App\Models\Realm;
use App\Models\Item;
use App\Models\Owner;
use App\Models\ClassSubclass;
use App\Models\Price;
use Illuminate\Http\Request;

class ServiceSubasta extends Service
{
    public function getSubastas($url) {
        //Bucles para obtener las subastas extraidas del JSON
        //Probar con un file_get_contents estandar también
        $contenido = json_decode(file_get_contents($url),TRUE);
        return $contenido['auctions'];
    }

    public function getPrices($subastas, $datos, $allItems) {
        $arrayItems = array();
        $arrayRealms = array();
        $json_id = $datos['id'];
        $totalSubastas = count($subastas);
        $cadaIteracion = 0;
        $todosLosItems = [];

        $todosLosItemsBD = $allItems;



        $todosRealms = Realm::all()->toArray();
        foreach($todosRealms as $reino){
            $arrayRealms[$reino['Nombre']] = $reino['Id'];
        }

        $arrayOwners = array();
        $todosLosOwner = Owner::all()->toArray();
        foreach($todosLosOwner as $owner){
            $arrayOwners[$owner['Nombre']] = $owner['Faccion'];
        }

        foreach ($subastas as $key => $subasta):
            if($subasta['item'] == 113263){
                echo '<pre>';
                var_dump($subasta);
                echo '</pre>';
            }
            /**
             * [De momento no usaremos las subastas sin precio de compra]
             */
            if (isset($subasta['buyout'])) {
                $subastas[$key]['idJson'] = $json_id;
                /**
                 * [Inicializamos el tiempo limite de ejecucion en cada subasta para que no expire]
                 */
                set_time_limit(15);
                /**
                 * [Guardado del reino si no existe]
                 * [Usamos un array para la lista de reinos del json actual]
                 */

                if (!array_key_exists($subasta['ownerRealm'], $arrayRealms)) {
                    $newRealm = new Realm;
                    $newRealm->Nombre = $subasta['ownerRealm'];
                    $saved = $newRealm->save();
                    if(!$saved){
                        dd('Error al guardar Realm');
                    }
                    $realmExists[$subasta['ownerRealm']] = $newRealm->Id;
                    $arrayRealms[$subasta['ownerRealm']] = $realmExists[$subasta['ownerRealm']];
                }

                $subastas[$key]['reinoReal'] = $arrayRealms[$subasta['ownerRealm']];
                
                /**
                 * [Comprobamos la facción a la que pertenece la subasta]
                 */
                
                //if(isset($arrayOwners[$subasta['owner']])){
                //    $retornoFaccion['faction'] = $arrayOwners[$subasta['owner']];
                //}
                //else {
                //    $retornoFaccion = $this->getFaction($subasta, $arrayRealms);
                //    $arrayOwners[$subasta['owner']] = $retornoFaccion['idOwner'];
                //}

                /**
                 * [Si no encontramos facción para una subasta]
                 * [Quizás hemos llegado al limite de peticiones]
                 * [1:Horda;]
                 */
                
                //if (!$retornoFaccion) {
                //    //dd('No hay faccion disponible');
                //    $retornoFaccion['faction'] = 3;
                //}
                //$faccionSubasta = $retornoFaccion['faction'];
                //$subastas[$key]['faccionReal'] = $faccionSubasta;

                /**
                 * [Inicializamos array de un objeto si no existe]
                 */
                
                if (!isset($items[$arrayOwners[$subasta['owner']]][$subasta['item']])) {
                    $items[$arrayOwners[$subasta['owner']]][$subasta['item']] = array();
                    $items[$arrayOwners[$subasta['owner']]][$subasta['item']]['maximo'] = 0;
                    $items[$arrayOwners[$subasta['owner']]][$subasta['item']]['calculo_pmp'] = 0;
                    $items[$arrayOwners[$subasta['owner']]][$subasta['item']]['total_items'] = 0;
                    $items[$arrayOwners[$subasta['owner']]][$subasta['item']]['different_bout'] = 0;
                }

                /**
                 * [Si existe precio de compra, calculamos máximo]
                 */
                
                if ($items[$arrayOwners[$subasta['owner']]][$subasta['item']]['maximo'] < (round($subasta['buyout'] / $subasta['quantity'], 0, PHP_ROUND_HALF_UP))) {
                    $items[$arrayOwners[$subasta['owner']]][$subasta['item']]['maximo'] = round($subasta['buyout'] / $subasta['quantity'], 0, PHP_ROUND_HALF_UP);
                }
                /**
                 * [Si no existe minimo, asignamos el primero por objeto por defecto]
                 */
                if (!isset($items[$arrayOwners[$subasta['owner']]][$subasta['item']]['minimo'])) {
                    $items[$arrayOwners[$subasta['owner']]][$subasta['item']]['minimo'] = round($subasta['buyout'] / $subasta['quantity'], 0, PHP_ROUND_HALF_UP);
                }
                /**
                 * [Calculamos minimo]
                 */
                if ($items[$arrayOwners[$subasta['owner']]][$subasta['item']]['minimo'] > (round($subasta['buyout'] / $subasta['quantity'], 0, PHP_ROUND_HALF_UP))) {
                    $items[$arrayOwners[$subasta['owner']]][$subasta['item']]['minimo'] = round($subasta['buyout'] / $subasta['quantity'], 0, PHP_ROUND_HALF_UP);
                }
                /**
                 * [Obtenemos valores para calcular el precio medio ponderado]
                 */
                $items[$arrayOwners[$subasta['owner']]][$subasta['item']]['calculo_pmp'] += ($subasta['quantity'] * $subasta['buyout']);
                $items[$arrayOwners[$subasta['owner']]][$subasta['item']]['total_items'] += $subasta['quantity'];
                $items[$arrayOwners[$subasta['owner']]][$subasta['item']]['different_bout']++;

                /**
                 * [Guardado del objeto si no existe]
                 * [Usamos un array para la lista de objetos del json actual]
                 */
                
                if(!in_array($subasta['item'], $todosLosItems)){
                    $todosLosItems[] = $subasta['item'];
                }
                /*if (!in_array($subasta['item'], $arrayItems)) {
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
                }*/

            } else {
                unset($subastas[$key]);
            }
           /* echo '<pre>';
            $cadaIteracion ++;
            print_r($items);
            echo $cadaIteracion.'/'.$totalSubastas.'<br>';
            echo '</pre>';*/
            //dd($items);
        endforeach;

        $newTodosLosItemsBD = [];

        

        if(count($todosLosItemsBD)>0){
            foreach ($todosLosItemsBD as $keyTItem => $tItem) {
                $newTodosLosItemsBD[$tItem['Id']] = $tItem;
            }
        }
        

        foreach($todosLosItems as $keyItemsJson => $itemJson){
            set_time_limit(15);
            if(!array_key_exists($itemJson,$newTodosLosItemsBD)){
                $itemFaltante[] = [
                        'Id' => $itemJson,
                        'Nombre' => NULL,
                        'Descripcion' => NULL,
                        'Icono' => NULL,
                        'Calidad' => NULL,
                        'Nivel_objeto' => NULL,
                        'Nivel_requerido' => NULL
                    ];
            }
        }

        if(isset($itemFaltante) && count($itemFaltante)>0){
            foreach ($itemFaltante as $key => $iFaltante) {
                try{
                    \DB::table('item')->insert($iFaltante);
                }
                catch(Exception $e) {
                    echo 'Captured exception: ',  $e->getMessage(), "\n";
                }
            }
        }

        /**
         * [Calculamos el precio medio ponderado por objeto]
         */
        
        foreach ($items as $duenno => $kObject) {
            foreach ($kObject as $keyItem => $item) {
                $items[$duenno][$keyItem]['pmp'] = round($item['calculo_pmp'] / $item['total_items'], 0, PHP_ROUND_HALF_UP);
                //Parada para comprobar si el precio máximo es inferior al precio medio(INCONCEBIBLE)
                if($items[$duenno][$keyItem]['maximo']<$items[$duenno][$keyItem]['pmp']){
                    echo $keyItem;
                    dd($items[$duenno][$keyItem]);
                }
            }
        }
        $arrayRetorno['items'] = $items;
        $arrayRetorno['subastas'] = $subastas;
        $arrayRetorno['reinos'] = $arrayRealms;

        return $arrayRetorno;
    }

    public function putPrices($precios, $fecha, $insertedDatePrices) {
        $arrayPrecios = array();
        $arrayPrecios = $insertedDatePrices;

        foreach ($precios as $keyFaccion => $elementPrecio) {
            foreach ($elementPrecio as $elemento => $precio) {
                set_time_limit(15);
                /**
                 * [Si el precio no ha sido insertado en esta tanda, comprobamos en BD]
                 */

                if (!array_key_exists($elemento . '-' . $keyFaccion. '-' . $fecha, $arrayPrecios)) {
                    $newPrice = new Price;
                    $newPrice->Precio_minimo = $precio['minimo'];
                    $newPrice->Precio_maximo = $precio['maximo'];
                    $newPrice->Precio_medio = $precio['pmp'];
                    $newPrice->Item_id = $elemento;
                    $newPrice->Fecha = $fecha;
                    $newPrice->Total_objetos = $precio['total_items'];
                    $newPrice->Faccion = $keyFaccion;
                    $saved = $newPrice->save();
                    if(!$saved){
                        dd('Error on saving price');
                    }
                    $arrayPrecios[$elemento . '-' . $keyFaccion. '-' . $fecha] = $elemento . '-' . $keyFaccion. '-' . $fecha;
                }
            }
        }
        return TRUE;
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

        return 'ok';
    }

    public function getFaction($subasta,$arrayRealms) {

        $ownerRealm = str_replace("'", "", $subasta['ownerRealm']);
        /**
         * [Para la búsqueda de faccion por web]
         * [Buscar Logo--alliance o Logo--horde]
         */
        $url_web = "https://worldofwarcraft.com/es-es/character/{$ownerRealm}/{$subasta['owner']}";
        $url = "https://eu.api.battle.net/wow/character/{$subasta['ownerRealm']}/{$subasta['owner']}?locale=es_ES&apikey=8hw8e9kun6sf8kfh2qvjzw22b9wzzjek";
        $faccionExtraida = json_decode(@file_get_contents($url), TRUE);

        $faccion = $faccionExtraida;
        unset($faccionExtraida);

        echo $url.'<br>';
        //preg_match_all("/Nivel de objeto <!--ilvl-->(.*)<\/span>/Um", $contenido, $salida, PREG_PATTERN_ORDER);
        /**
         * [Si ha array de retorno, lo devolvemos, si no devolvemos FALSE]
         */

        $newOwner = new Owner;
        $newOwner->nombre = $subasta['owner'];
        $newOwner->realm_id = $arrayRealms[$subasta['ownerRealm']];
        if (!isset($faccion['faction'])) {
            $faccion['faction'] = 3;
            $newOwner->faccion = $faccion['faction'];
        }
        else {
            $newOwner->faccion = $faccion['faction'];
        }
        $saved = $newOwner->save();
        if(!$saved){
            dd('Error al guardar Owner');
        }
        $faccion['idOwner'] = $newOwner->id;

        return $faccion;

    }

}
