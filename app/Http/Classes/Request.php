<?php

namespace App\Http\Classes;

abstract class Request
{
    private $innerResponse;
    public $response = [];


    private $countRequests;

    /**
     * @param Url $url
     * @return $this
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function make(Url $url)
    {

    }

    public function getStockInfo($productId,Url $url, $client)
    {
        $url->setShape('latest/item_stock_units?item__sku='.$productId);
        $url->make();

        $result = $client->request('GET',
            $url->getUrl(), ['auth' => [env('HANDSHAKE_USER'), env('HANDSHAKE_PASSWORD')]]);

        $response = json_decode($result->getBody()->getContents())->objects[0];

        return $response;
    }

    /**
     * @return mixed
     */
    public function getInnerResponse()
    {
        return $this->innerResponse;
    }

    /**
     * @param mixed $innerResponse
     */
    public function setInnerResponse($innerResponse): void
    {
        $this->innerResponse = $innerResponse;
    }

    /**
     * @return mixed
     */
    public function getCountRequestsRemain()
    {
        return $this->countRequestsRemain;
    }

    /**
     * @param mixed $countRequestsRemain
     */
    public function setCountRequestsRemain($countRequestsRemain): void
    {
        $this->countRequestsRemain = $countRequestsRemain;
    }

    /**
     * @return mixed
     */
    public function getCountRequests()
    {
        return $this->countRequests;
    }

    /**
     * @param mixed $countRequests
     */
    public function setCountRequests($countRequests): void
    {
        $this->countRequests = $countRequests;
    }
    private $countRequestsRemain;

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param array $response
     */
    public function setResponse(array $response): void
    {
        $this->response = $response;
    }
}