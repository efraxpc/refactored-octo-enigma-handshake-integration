<?php

namespace App\Http\Classes;


class RequestAllProducts extends Request implements IrequestAllProducts
{
    public $response = array();
    /**
     * @param Url $url
     * @return $this
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function make(Url $url)
    {
        $this->tryRequest($url,0);
        $this->setCountRequests($this->getInnerResponse()->meta->total_count);
        $qtyIterations = (intval($this->getCountRequests()/100));

        for($i=0;$i<=$qtyIterations;$i++)
        {
            if($i=3){
                $this->tryRequest($url,$i+1);
                return $this;
            }
        }
        return $this;
    }

    /**
     * @param Url $url
     * @param $i
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function tryRequest(Url $url,$i)
    {
        $client = new \GuzzleHttp\Client();
        if($i > 1)
        {
            $url->setShape('latest/items?limit=100&offset='.$i*100);
            $url->make();
        }

        $response = $client->request('GET',
            $url->getUrl(), ['auth' => [env('HANDSHAKE_USER'), env('HANDSHAKE_PASSWORD')]]);

        $this->setInnerResponse(json_decode($response->getBody()->getContents()));
        $this->fillResponse($url,$client);
        $this->setCountRequestsRemain($this->getCountRequests() - 100);
    }

    /**
     * @param Url $url
     * @param \GuzzleHttp\Client $client
     */
    public function fillResponse(Url $url,\GuzzleHttp\Client $client)
    {
        $products = $this->getInnerResponse()->objects;
        $products_count = count($this->getInnerResponse()->objects);
        $i = 0;
        foreach ($products as $key => $product)
        {
            if($i>0)
            {
                $count_response = count($this->getResponse());
                $i = $count_response;
            }
            $stockInfo = $this->getStockInfo($product->sku, $url, $client);

            $this->response[$i]['id'] = $product->objID;
            $this->response[$i]['name'] = $product->name;
            $this->response[$i]['code'] = $product->sku;
            $this->response[$i]['qty'] = $stockInfo->shelfQty;
            $this->response[$i]['isAvailable'] = $stockInfo->isAvailable;

            $i++;
            if($key == $products_count -1 && $i > 0)
            {
                //$this->response[$i-1]['last_element'] = $i-1;
            }
            if($key === 199)
            {
                dd($this->response);
            }

        }
    }
}