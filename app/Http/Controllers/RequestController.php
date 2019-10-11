<?php
namespace App\Http\Controllers;

use App\Http\Classes\ProductInfo;
use App\Http\Classes\RequestAllProducts;
use App\Http\Classes\RequestProductsBySqu;

use Illuminate\Http\Request;
use App\Http\Classes\Url;

class RequestController extends Controller
{
    private $url;

    /**
     * RequestController constructor.
     */
    public function __construct()
    {
        $this->url = new Url();
        $this->url->setBaseUrl(env('BASE_URL'));
        $this->url->setShape('latest/items');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getAll(Request $request)
    {
        $sku = $request->code;
        if($sku)
        {
            return $this->getBySku($sku);
        }
        $fullUrl = $this->url->make();
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
        $fullUrl = $this->url->make('sku='.$sku);
        $request = new RequestProductsBySqu();
        $product_info = new ProductInfo($fullUrl,$request);

        return response()->json($product_info->getBySku()->getResponse());
    }
}

