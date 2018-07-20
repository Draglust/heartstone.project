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
			$clases[$loopValue['Clase_nombre']][$loopValue['Subclase_id']] = $loopValue;
		}

		return view('comun.layout', array(
            'clases' => $clases
        ));

    }

}