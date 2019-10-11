<?php
/**
 * Created by PhpStorm.
 * User: super
 * Date: 09/10/19
 * Time: 10:21 PM
 */

namespace App\Http\Classes;


interface IRequestProductsBySqu
{

    public function tryRequest(Url $url);
}