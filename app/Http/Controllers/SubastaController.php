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

            //$contenido_url = file_get_contents($contenido->files[0]->url);

            $contenido_url = '';
            
            $handle = fopen($contenido->files[0]->url, "r");
            if ($handle) {
                while (fgets($handle) !== false || !feof($handle)) {
                    $line = fgets($handle);
                    if(strstr($line, '"auc"')){
                        $line = str_replace("\r\n",'', $line);
                        $line = str_replace("\t",'', $line);
                        $line = trim($line);
                        $line = trim($line,',');
                        $elementos_a_tratar = explode(',', $line);
                        foreach($elementos_a_tratar as $key=> $pareja_campo_valor){

                            $pareja_campo_valor = trim(str_replace('"','', $pareja_campo_valor));
                            $pareja_campo_valor = str_replace('{','', $pareja_campo_valor);
                            $pareja_campo_valor = str_replace('}','', $pareja_campo_valor);
                            $pareja_campo_valor = str_replace('[','', $pareja_campo_valor);
                            $pareja_campo_valor = str_replace(']','', $pareja_campo_valor);
                            $valores = explode(':', $pareja_campo_valor);
                            if(isset($valores[1])){
                                $item_subasta[$valores[0]] = $valores[1];
                            }
                            else{
                               
                            }
                        }

                        $subasta[] = $item_subasta;
                        unset($item_subasta);
                    }
                    else{
                         
                    }
                    //$contenido_url .=$line;
                }
                fclose($handle);
            } else {
                dd('Error al abrir el archivo');
            }

            dd($subasta);

            return 'Insertado con exito';
        }
        else{
            return 'Insertado previamente';
        }
    }

    public function json_validate($string)
    {
        // decode the JSON data
        $result = json_decode($string);

        // switch and check possible JSON errors
        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                $error = ''; // JSON is valid // No error has occurred
                break;
            case JSON_ERROR_DEPTH:
                $error = 'The maximum stack depth has been exceeded.';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $error = 'Invalid or malformed JSON.';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $error = 'Control character error, possibly incorrectly encoded.';
                break;
            case JSON_ERROR_SYNTAX:
                $error = 'Syntax error, malformed JSON.';
                break;
            // PHP >= 5.3.3
            case JSON_ERROR_UTF8:
                $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
                break;
            // PHP >= 5.5.0
            case JSON_ERROR_RECURSION:
                $error = 'One or more recursive references in the value to be encoded.';
                break;
            // PHP >= 5.5.0
            case JSON_ERROR_INF_OR_NAN:
                $error = 'One or more NAN or INF values in the value to be encoded.';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $error = 'A value of a type that cannot be encoded was given.';
                break;
            default:
                $error = 'Unknown JSON error occured.';
                break;
        }

        if ($error !== '') {
            // throw the Exception or exit // or whatever :)
            exit($error);
        }

        // everything is OK
        return $result;
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
