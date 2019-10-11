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

    /**
     * @param $productId
     * @param Url $url
     * @param $client
     * @return array
     */
    public function getStockInfo($productId,Url $url, $client)
    {
        $url->setShape('api/latest/item_stock_units?item__sku='.$productId);
        $url->make();
        $result = $client->request('GET',
            $url->getUrl(), ['auth' => [env('HANDSHAKE_USER'), env('HANDSHAKE_PASSWORD')]]);


        $stockInfo = json_decode($result->getBody())->objects ?? null;


        foreach($stockInfo as $key => $info)
        {
            $wherehpuseInfo= $this->getWhereHouseInfo($url,$info, $client);

            $response[] = [
                'position' => $key,
                'shelfQty' => $info->shelfQty,
                'isAvailable' => $info->isAvailable,
                'officeId' => $wherehpuseInfo->objID,
                'wherehouseName' => $wherehpuseInfo->name,
            ];
        }
        return $response;
    }

    /**
     * @param Url $url
     * @param $stockInfo
     * @param $client
     * @return mixed
     */
    public function getWhereHouseInfo(Url $url, $stockInfo, $client)
    {

            $url->setShape(ltrim($stockInfo->warehouse, '/'));
            $url->make();
            $result = $client->request('GET',
                $url->getUrl(), ['auth' => [env('HANDSHAKE_USER'), env('HANDSHAKE_PASSWORD')]]);

            return json_decode($result->getBody());
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