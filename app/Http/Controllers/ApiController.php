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
use App\Http\Services\Serviceitem;
use Illuminate\Http\Request;

class ApiController extends Controller {
    
    protected $ServiceJson;
    protected $ServiceSubasta;
    protected $ServiceItem;

    public function __construct(ServiceJson $ServiceJson,ServiceSubasta $ServiceSubasta, Serviceitem $ServiceItem)
    {
        $this->ServiceJson = $ServiceJson;
        $this->ServiceSubasta = $ServiceSubasta;
        $this->Serviceitem = $ServiceItem;
    }

    public function index() {
        $url = "https://eu.api.battle.net/wow/auction/data/shen'dralar?locale=es_ES&apikey=8hw8e9kun6sf8kfh2qvjzw22b9wzzjek";
        $retorno = $this->ServiceJson->getJson($url);
        
        if($retorno){
            $rawSubastas = $this->ServiceJson->getAuctions($retorno['url']);
            //Mismo método que getAuctions pero decodificando mediante json_decode
            //$rawSubastas = $this->ServiceSubasta->getSubastas($retorno['url']);

            if (count($rawSubastas) > 0) {
                $retornoPrecios = $this->ServiceSubasta->getPrices($rawSubastas, $retorno);
                dd($retornoPrecios);
                $precios = $retornoPrecios['items'];
                $treatedSubastas = $retornoPrecios['subastas'];
                $reinos = $retornoPrecios['reinos'];
            }
            else {
                return 'No auction or Json already inserted.';
            }

            if (isset($precios)) {
                $preciosInsertados = $this->ServiceSubasta->putPrices($precios, $retorno['fecha']);
            }
            if ($preciosInsertados) {
                $subastasReales = $this->ServiceSubasta->putSubastas($precios, $treatedSubastas);
            } else {
                return 'No prices inserted.';
            }
        }
        else{
            return 'Json already inserted.';
        }
    }

    public function items() {
        $retorno = $this->ServiceItem->treatItems();

        return $retorno;
    }
}
