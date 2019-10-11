<?php

namespace App\Http\Classes;


class RequestAllProducts extends Request implements IrequestAllProducts
{
    public $response = array();
    private $counter = 0;

    /**
     * @param Url $url
     * @return $this
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function make(Url $url)
    {
        $this->tryRequest($url, 0);
        $this->setCountRequests($this->getInnerResponse()->meta->total_count);
        $qtyIterations = (intval($this->getCountRequests() / 100));

        for ($i = 0; $i <= $qtyIterations; $i++) {
            $this->tryRequest($url, $i + 1);
        }
        return $this;
    }

    /**
     * @param Url $url
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function tryRequest(Url $url, $i)
    {
        $client = new \GuzzleHttp\Client();
        if ($i >= 1) {
            $url->setShape('api/latest/items?limit=100&offset=' . $i * 100);
            $url->make();
        }

        try {
            $response = $client->request('GET',
                $url->getUrl(), ['auth' => [env('HANDSHAKE_USER'), env('HANDSHAKE_PASSWORD')]]);
        } catch (Exception $e) {
            $error = 'Caught exception: ' . $e->getMessage() . "\n";
            throw new Exception($error);
        }


        $this->setInnerResponse(json_decode($response->getBody()->getContents()));
        $this->fillResponse($url, $client);
        $this->setCountRequestsRemain($this->getCountRequests() - 100);
    }

    /**
     * @param Url $url
     * @param \GuzzleHttp\Client $client
     */
    public function fillResponse(Url $url, \GuzzleHttp\Client $client)
    {
        $products = $this->getInnerResponse()->objects;

        foreach ($products as $key => $product) {

            $stockInfo = $this->getStockInfo($product->sku, $url, $client);

            $this->response[$this->counter]['id'] = $product->objID;
            $this->response[$this->counter]['position'] = $this->counter;
            $this->response[$this->counter]['name'] = $product->name;
            $this->response[$this->counter]['code'] = $product->sku;

            if (!is_null($stockInfo)) {
                foreach ($stockInfo as $info) {
                    $this->response[$this->counter]['stock'][$info['position']]['qty'] = $info['shelfQty'];
                    $this->response[$this->counter]['stock'][$info['position']]['isAvailable'] = $info['isAvailable'];
                    $this->response[$this->counter]['stock'][$info['position']]['officeId'] = $info['officeId'];
                    $this->response[$this->counter]['stock'][$info['position']]['wherehouseName'] = $info['wherehouseName'];
                }
            }
            $this->counter++;
        }
    }
}