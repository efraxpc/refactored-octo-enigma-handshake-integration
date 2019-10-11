<?php

namespace App\Http\Classes;

class ProductInfo
{
    private $request;
    private $fullUrl;

    /**
     * ProductInfo constructor.
     */
    public function __construct(Url $fullUrl, Request $request)
    {
        $this->fullUrl = $fullUrl;
        $this->request = $request;
    }

    public function getAll(){
        return $this->request->make($this->fullUrl);
    }

    public function getBySku(){
        return $this->request->make($this->fullUrl);
    }

}