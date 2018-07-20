<?php

namespace App\Http\Services;

use App\Models\Json;
use App\Models\Realm;
use App\Models\Item;
use App\Models\Owner;
use App\Models\ClassSubclass;
use App\Models\Price;
use Illuminate\Http\Request;

class ServiceWeb extends Service
{
	static public function curl($url) {
		$curl_handle=curl_init();
		curl_setopt($curl_handle, CURLOPT_URL,$url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($curl_handle, CURLOPT_HEADER, FALSE);
		curl_setopt($curl_handle, CURLOPT_ENCODING, TRUE);
		curl_setopt($curl_handle, CURLOPT_AUTOREFERER , TRUE);
		curl_setopt($curl_handle, CURLOPT_MAXREDIRS , 10);
		curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION , TRUE);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/67.0.3396.99 Safari/537.36');
		$content = curl_exec($curl_handle);
		curl_close($curl_handle);

		return $content;
	}
}