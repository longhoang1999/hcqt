<?php

namespace App\Http\Controllers\Base;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class ResourceBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    // add the index method 
    protected function resourceAbilityMap()
    {
        return [
            'index' => 'index',
            'create' => 'create',
            'store' => 'create',
            'edit' => 'update',
            'update' => 'update',
            'destroy' => 'delete',
            'show' => 'view',
        ];
    }

    public function responseJson($data, $status = 200, array $headers = [], $options = JSON_UNESCAPED_UNICODE)
    {
        $headersR = array_merge(['Content-type' => 'application/json; charset=utf-8'], $headers);
        return response()->json($data, $status, $headersR, $options);
    }
}
