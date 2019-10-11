<?php
namespace App\Http\Controllers;

use App\Http\Classes\ProductInfo;
use App\Http\Classes\RequestAllProducts;
use App\Http\Classes\RequestProductsBySqu;

use Illuminate\Http\Request;
use App\Http\Classes\Url;

class RequestController extends Controller
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function getAll(Request $request)
    {
        $url = new Url();
        $url->setBaseUrl(env('BASE_URL'));
        $url->setShape('api/latest/items');
      
        $sku = $request->code;
        if($sku)
        {
            return $this->getBySku($sku);
        }
        $fullUrl = $url->make();
        $request = new RequestAllProducts();

        $product_info = new ProductInfo($fullUrl,$request);

        return response()->json($product_info->getAll()->getResponse());
    }

    /**
     * @param $sku
     * @return mixed
     */
    public function getBySku($sku)
    {
        $url = new Url();
        $url->setBaseUrl(env('BASE_URL'));
        $url->setShape('api/latest/items');
      
        $fullUrl = $url->make('sku='.$sku);
        $request = new RequestProductsBySqu();
        $product_info = new ProductInfo($fullUrl,$request);

        return response()->json($product_info->getBySku()->getResponse());
    }
}

