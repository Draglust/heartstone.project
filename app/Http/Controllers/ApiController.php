<?php

namespace App\Http\Controllers;

use App\Models\Json;
use App\Models\Realm;
use App\Models\Item;
use App\Models\Owner;
use App\Models\ClassSubclass;
use App\Models\Price;
use App\Http\Services\ServiceJson;
use App\Http\Services\ServiceSubasta;
use App\Http\Services\ServiceItem;
use App\Http\Services\ServiceOwner;
use Illuminate\Http\Request;

class ApiController extends Controller {
    
    protected $ServiceJson;
    protected $ServiceSubasta;
    protected $ServiceItem;
    protected $ServiceOwner;

    public function __construct(ServiceJson $ServiceJson,ServiceSubasta $ServiceSubasta, ServiceItem $ServiceItem, ServiceOwner $ServiceOwner)
    {
        $this->ServiceJson = $ServiceJson;
        $this->ServiceSubasta = $ServiceSubasta;
        $this->ServiceItem = $ServiceItem;
        $this->ServiceOwner = $ServiceOwner;
    }

    public function index() {

        try{
            ini_set('memory_limit', '750M');
            $tiempo_inicial = microtime(true);
            $url = "https://eu.api.battle.net/wow/auction/data/shen'dralar?locale=es_ES&apikey=8hw8e9kun6sf8kfh2qvjzw22b9wzzjek";
            $retorno = $this->ServiceJson->getJson($url);
            $tiempo['getJson'] =   microtime(true) - $tiempo_inicial;
            $retornoItems = $this->ServiceItem->getAllItems();
            $retornoPrices = $this->ServiceItem->getDateFactionItemPrices($retorno['fecha']);

            if($retorno){
                $rawSubastas = $this->ServiceJson->getAuctions($retorno['url']);
                $tiempo['getAuctions'] =   microtime(true) - $tiempo_inicial;
                //Mismo método que getAuctions pero decodificando mediante json_decode
                //$rawSubastas = $this->ServiceSubasta->getSubastas($retorno['url']);

                if (count($rawSubastas) > 0) {
                    $retornoOwners = $this->ServiceOwner->getOwners($rawSubastas, $retorno);

                    $tiempo['getOwners'] =   microtime(true) - $tiempo_inicial;
                    $retornoSubastas = $this->ServiceSubasta->delAuctions($retornoOwners['realms']);
                    $tiempo['delAuctions'] =   microtime(true) - $tiempo_inicial;
                    $retornoPrecios = $this->ServiceSubasta->getPrices($rawSubastas, $retorno, $retornoItems);
                    $tiempo['getPrices'] =   microtime(true) - $tiempo_inicial;
                    $precios = $retornoPrecios['items'];
                    $treatedSubastas = $retornoPrecios['subastas'];
                    $reinos = $retornoPrecios['reinos'];
                }
                else {
                    return 'No auctions or Json already inserted.';
                }
                
                if (isset($precios)) {
                    $preciosInsertados = $this->ServiceSubasta->putPrices($precios, $retorno['fecha'],$retornoPrices);
                    $tiempo['putPrices'] =   microtime(true) - $tiempo_inicial;
                }
                if ($preciosInsertados) {
                    $subastasReales = $this->ServiceSubasta->putSubastas($precios, $treatedSubastas);
                    $tiempo['putSubastas'] =   microtime(true) - $tiempo_inicial;
                } else {
                    return 'No prices inserted.';
                }
            }
            else{
               return 'Json already inserted.';
            }
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }
        var_dump($tiempo);
    }

    public function items() {
        $retorno = $this->ServiceItem->treatItems();

        return $retorno;
    }

    public function owners(){
        $retorno = $this->ServiceJson->getLastJson();
        
        if($retorno){
            $rawSubastas = $this->ServiceJson->getAuctions($retorno['url']);
            //Mismo método que getAuctions pero decodificando mediante json_decode
            //$rawSubastas = $this->ServiceSubasta->getSubastas($retorno['url']);

            if (count($rawSubastas) > 0) {
                $retornoOwners = $this->ServiceOwner->getOwners($rawSubastas, $retorno);
                dd($retornoOwners);
            }
            else{
                return '0';
            }
        }
    }
}
