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

class ShowController extends Controller {

	public function showMain() {
		$rawClases = ClassSubclass::orderBy('Clase_nombre')->get(['Clase_nombre','Subclase_nombre','Clase_id','Subclase_id'])->toArray();
		foreach ($rawClases as $loopKey => $loopValue) {
			$clases[$loopValue['Clase_id']][$loopValue['Subclase_id']] = $loopValue;
            $nombres[$loopValue['Clase_id']] = $loopValue['Clase_nombre'];
		}

		return view('comun.layout', array(
            'clases' => $clases,
            'nombres' => $nombres
        ));

    }
    public function showAll($clase){
    	$todosObjetos = Item::join('class_subclass', 'class_subclass.Id', '=', 'item.Class_Subclass_Id');
    	$todosObjetos->join('price', 'price.Item_id', '=', 'item.Id');
    	$todosObjetos->join('json', function($q)
        {
            $q->on('json.Fecha','=', 'price.Fecha')
                ->whereRaw('json.Id ='."(".\DB::raw('SELECT json.Id FROM json ORDER BY json.Fecha DESC LIMIT 1').")");
        });
        
        $todosObjetos->leftJoin('auction', function($q)
        {
            $q->on('auction.Item_id','=', 'item.Id')
                ->on('auction.Json_id', 'json.Id');
        });
    	
    	$todosObjetos->where('class_subclass.Clase_id',$clase);
    	$retornoObjetos = $todosObjetos->get(['item.Nombre','item.Id',
    						'item.Icono','item.Expansion',
    						'class_subclass.Clase_id',
    						'class_subclass.Subclase_id',
    						'class_subclass.Clase_nombre',
    						'class_subclass.Subclase_nombre',
    						'price.Precio_medio',
    						'price.Precio_minimo',
    						'price.Precio_maximo',
    						'price.Faccion',
    						'price.Fecha',
    						'price.Total_objetos',
    						'auction.Apuesta',
    						'auction.Compra',
    						'auction.Tiempo_restante'
    					])->toArray();
        
        if(isset($retornoObjetos[0])){
            $fecha = $retornoObjetos[0]['Fecha'];
        }
		foreach ($retornoObjetos as $key => $retornoObjeto) {
			$objetos[$retornoObjeto['Faccion']][$retornoObjeto['Id']] = $retornoObjeto;
		}

		return view('show.allitems', array(
            'objetos' => $objetos,
            'clase' => $clase,
            'fecha' => $fecha,
        ));
    }

}