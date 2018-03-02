<?php

namespace App\Http\Controllers;

use App\Models\Urljson;
use Illuminate\Http\Request;

class SubastaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $url = "https://eu.api.battle.net/wow/auction/data/shen'dralar?locale=es_ES&apikey=8hw8e9kun6sf8kfh2qvjzw22b9wzzjek";
        $contenido = json_decode(file_get_contents($url));

        $existentJson = Urljson::Datenum($contenido->files[0]->lastModified)->first();
        if(is_null($existentJson)){
            /*$filejson = new Urljson;
            $filejson->url = $contenido->files[0]->url;
            $filejson->datenum = $contenido->files[0]->lastModified;
            $filejson->date = date("Y-m-d H:i:s",$contenido->files[0]->lastModified);

            $filejson->save();*/

            //$contenido_url = json_decode(file_get_contents($contenido->files[0]->url));
            $client = new \GuzzleHttp\Client();
            $res = $client->request('GET', $contenido->files[0]->url);
            $res->getStatusCode();
            // 200
            $res->getHeaderLine('content-type');
            // 'application/json; charset=utf8'
            $contents = $res->getBody()->getContents();                
            //$contenido_url = (string) $body;
            $contenido_url = str_replace("\r\n", "", $contents);
            $contenido_url = str_replace("\t", "", $contenido_url);
            $contenido_url = trim($contenido_url,'"');
            // '{"id": 1420053, "name": "guzzle", ...}'

            /*// Send an asynchronous request.
            $request = new \GuzzleHttp\Psr7\Request('GET', 'http://httpbin.org');
            $promise = $client->sendAsync($request)->then(function ($response) {
                echo 'I completed! ' . $response->getBody();
            });
            $promise->wait();*/

            dd($contenido_url);

            return 'Insertado con exito';
        }
        else{
            return 'Insertado previamente';
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
