<?php

namespace App\Http\Classes;


class RequestProductsBySqu extends Request implements IRequestProductsBySqu
{


    /**
     * @param Url $url
     * @return $this|Request
     */
    public function make(Url $url)
    {
        $this->tryRequest($url);
        return $this;
    }

    /**
     * @param Url $url
     * @return $this
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function tryRequest(Url $url)
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET',
            $url->getUrl(), ['auth' => [env('HANDSHAKE_USER'), env('HANDSHAKE_PASSWORD')]]);

        $this->setInnerResponse(json_decode($response->getBody()->getContents()));
        $this->fillResponse($url, $client);
        return $this;
    }

    /**
     * @param Url $url
     * @param $client
     */
    public function fillResponse(Url $url, $client)
    {
        $product = $this->getInnerResponse()->objects[0];
        $stockInfo = $this->getStockInfo($product->sku, $url, $client);
        $response['id'] = $product->objID;
        $response['name'] = $product->name;
        $response['code'] = $product->sku;

        if( !is_null($stockInfo)) {
            foreach ($stockInfo as $key => $info)
            {
                $response['stock'][$key]['qty'] = $info['shelfQty'];
                $response['stock'][$key]['isAvailable'] = $info['isAvailable'];
                $response['stock'][$key]['officeId'] = $info['officeId'];
                $response['stock'][$key]['wherehouseName'] = $info['wherehouseName'];
            }
        }

        $this->setResponse($response);
    }
}