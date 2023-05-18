<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use PHPHtmlParser\Dom;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::get('test_html',function (Request $req){    
    $kehoachbao = DB::table('kehoach_baocao')
                            ->first();
    $keHoachTieuChuanList = DB::table('kehoach_tieuchuan')
                            ->where('id_kh_baocao',$kehoachbao->id)
                            ->get();
    $toEncode["str"] = array();
    foreach($keHoachTieuChuanList as $keHoachTieuChuan){
        $keHoachTieuChuan->keHoachTieuChiList = DB::table('kehoach_tieuchi')
                                                    ->where('id_kh_tieuchuan',$keHoachTieuChuan->id)
                                                    ->get();
        foreach($keHoachTieuChuan->keHoachTieuChiList as $keHoachTieuChi){
            $keHoachTieuChi->keHoachMenhDeList = DB::table('kehoach_menhde')
                                                    ->where('id_kh_tieuchi',$keHoachTieuChi->id)
                                                    ->get();

            foreach($keHoachTieuChi->keHoachMenhDeList as $keHoachMenhDe){
                $keHoachMenhDe->baoCaoMenhDe = DB::table('baocao_menhde')
                                                    ->where('id_kehoach_bc',$kehoachbao->id)
                                                    ->where('id_kh_menhde',$keHoachMenhDe->id)
                                                    ->where('id_menhde',$keHoachMenhDe->id_menhde)
                                                    ->first();


                $dom = new Dom;
                $dom->loadStr($keHoachMenhDe->baoCaoMenhDe->mota);
                $contents = $dom->find('.danMinhChung');  
                $arr = array();
                foreach ($contents as $key => $value) {
                    $html = $value->innerHtml;
                    array_push($arr,[$value->getAttribute('id'), $html]);
                }
                array_push($toEncode["str"],$arr);
            }
        }
    }

    $toEncode["result"] = "ok";
    return response($toEncode,200); 
    
});

