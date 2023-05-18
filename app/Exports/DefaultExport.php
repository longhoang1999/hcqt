<?php

namespace App\Exports;
use DB;

class DefaultExport
{
    public function dataExceptDelete($query) {
        foreach($query as $key => $qr){
            if($qr->deleted_at != null && $qr->deleted_at != ""){
                unset($query[$key]);
            }
        }
        return $query;
    }
}
