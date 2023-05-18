<?php

namespace App\Http\Controllers\Admin;

use App\Models\Blog;
use Illuminate\Support\MessageBag;
use Sentinel;
use Analytics;
use View;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\DataTables;
use App\Charts\Highcharts;
use App\Models\User;
//use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Spatie\Analytics\Period;
use File;
use Artisan;
use Str;
use DB;
use Lang;
use Storage;
use Illuminate\Support\Facades\Response;

class DefinedController {
	
    public function upload($file, $dest){
        try {
            if (!$file->isValid()) {
                // $response['success'] = false;
                // $response['message'] = 'Lỗi khi upload file';
                // return response()->json($response);
                return false;
            }
            
            if($dest == 'minhchung'){
                $year = date('Y');
                $month = date('m');
                $day = date('d');

                if(!Storage::disk('public')->exists("minhchung/$year")){
                    Storage::disk('public')->makeDirectory("minhchung/$year");
                };

                if(!Storage::disk('public')->exists("minhchung/$year/$month")){
                    Storage::disk('public')->makeDirectory("minhchung/$year/$month");
                };

                if(!Storage::disk('public')->exists("minhchung/$year/$month/$day")){
                    Storage::disk('public')->makeDirectory("minhchung/$year/$month/$day");
                };                
                $dest = "minhchung/$year/$month/$day";
            }else{

            }
            $path = $file->store($dest, 'public');
            
            return $path;
            // return true;
            // $response['success'] = true;
            // $response['message'] = 'File đã được upload';            
            // return response()->json($response);
        } catch (\Exception $e) {
            // $response['success'] = false;
            // $response['message'] = $e->getMessage();
            // return response()->json($response);
            return false;
        }        
    }

    public function getlinkfile($link){
        return Storage::disk('public')->path($link);
    }

    public function downloadfile($link,$tenfile){

        if (!Storage::disk('public')->exists($link)) {
            return abort(422, Lang::get('project/quanlyminhchung/title.minhchungkhongtontai'));
        }

        $filePath = Storage::disk('public')->path($link);
        $mimeType = File::mimeType($filePath);

        $fileName = title_case(str_slug(File::name($tenfile), ' '));
        $fileExt = File::extension($tenfile);
        $fileContent = File::get(Storage::disk('public')->path($link));

        $headers = array(
            "Content-Type: $mimeType",
            "Content-Transfer-Encoding: binary",
        );

        if (in_array($fileExt, ['jpg', 'png', 'jpeg'])) {
            $response = Response::make($fileContent, 200);
            $response->header("Content-Type", $fileExt);
            ob_end_clean();
            ob_start();
            return $response;
        }

        if (in_array($fileExt, ['pdf', 'PDF'])) {
            $response = Response::make($fileContent, 200);
            $response->header("Content-Type", 'application/pdf');
            ob_end_clean();
            ob_start();
            return $response;
        }else{
            $file = File::get($filePath);
            $type = File::mimeType($filePath);
            $response = Response::make($file, 200);
            $response->header("Content-Type", $type);
            return $response;
        }

        //ob_end_clean();
        //ob_start();
       // return response()->download($filePath, $fileName . "." . $fileExt, $headers);
        //return response($filePath);
    }

    public function dataExceptDelete($query) {
        return $query->where("deleted_at", null);
    }

    public function toDBDate($date){
        if($date == ''){
            return '';
        }else{
            $st = explode('/',$date);
            if(sizeof($st) == 3){
                return $st[2] . '-' . $st[1] . '-' . $st[0];
            }else{
                $st = explode('-',$date);
                if(sizeof($st) == 3){
                    return $st[2] . '-' . $st[1] . '-' . $st[0];
                }
            } 
        }
    }

    public function toShowDate($date){
        if($date == '' || $date == '0000-00-00'){
            return '';
        }else{
            $st = explode('-',$date);
            if(sizeof($st) == 3){
                return $st[2] . '-' . $st[1] . '-' . $st[0];
            }else{
                return '';
            } 
        }
    }



    public function checkPermissions($number){            
        if(Sentinel::inRole('admin')){
            return true;
        }else if(Sentinel::inRole('bgh')){
            $per = DB::table("roles")->where("name", "bgh")->first()->perStr;
            $array = explode("|", $per);
            if(in_array($number, $array)){
                return true;
            }else{
                return false;
            }
        }else if(Sentinel::inRole('bpth')){
            $per = DB::table("roles")->where("name", "bpth")->first()->perStr;
            $array = explode("|", $per);
            if(in_array($number, $array)){
                return true;
            }else{
                return false;
            }
        }else if(Sentinel::inRole('tdv')){
            $per = DB::table("roles")->where("name", "tdv")->first()->perStr;
            $array = explode("|", $per);
            if(in_array($number, $array)){
                return true;
            }else{
                return false;
            }
        }else if(Sentinel::inRole('nv')){
            $per = DB::table("roles")->where("name", "nv")->first()->perStr;
            $array = explode("|", $per);
            if(in_array($number, $array)){
                return true;
            }else{
                return false;
            }
        }else if(Sentinel::inRole('thuthu')){
            $per = DB::table("roles")->where("name", "thuthu")->first()->perStr;
            $array = explode("|", $per);
            if(in_array($number, $array)){
                return true;
            }else{
                return false;
            }
        }else if(Sentinel::inRole('xlvb')){
            $per = DB::table("roles")->where("name", "xlvb")->first()->perStr;
            $array = explode("|", $per);
            if(in_array($number, $array)){
                return true;
            }else{
                return false;
            }
        }
    }
}