<?php

namespace App\Http\Classes;


interface IrequestAllProducts
{
    public function tryRequest(Url $url,$i);
    public function fillResponse(Url $url,\GuzzleHttp\Client $client);
}