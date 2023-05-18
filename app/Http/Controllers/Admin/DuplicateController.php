<?php namespace App\Http\Controllers\Admin;

use App\Http\Controllers\DefinedController;
use App\Http\Requests\UserRequest;
use App\Mail\Register;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use File;
use Hash;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;
use Redirect;
use Sentinel;
use URL;
use Lang;
use View;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Validator;
use App\Mail\Restore;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Country;
# imports the Google Cloud client library
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Everyman\Neo4j\Client as NeoClient;
use Everyman\Neo4j\Cypher\Query as CypherQuery;
use Everyman\Neo4j\Transport\Stream as NeoStream;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

use App\Providers\MP3File;

class DuplicateController 
{
    public function index(Request $req){
        return view('admin.dashboard.index');
    }

    public function checkduplicate_bk(Request $req){
        
        $dir = public_path() . "\\uploads\\testprac\\";
        $arrdir = array();
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {              
                    array_push($arrdir, $entry);                    
                }
            }
            closedir($handle);
        }

        $textout = "";

        for ($i=0; $i < sizeof($arrdir) - 1; $i++) { 
            for ($j=$i + 1; $j < sizeof($arrdir); $j++) { 
                $subdir1 = $dir . $arrdir[$i] . '\\';
                $subdir2 = $dir . $arrdir[$j] . '\\';
                $arr1 = array();
                $arr2 = array();

                if ($handle = opendir($subdir1)) {
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry != "." && $entry != "..") {              
                            array_push($arr1, $entry);                    
                        }
                    }
                    closedir($handle);
                }

                if ($handle = opendir($subdir2)) {
                    while (false !== ($entry = readdir($handle))) {
                        if ($entry != "." && $entry != "..") {              
                            array_push($arr2, $entry);                    
                        }
                    }
                    closedir($handle);
                }
                $count = 0;
                for ($ij1=0; $ij1 < sizeof($arr1); $ij1++) { 
                    for ($ij2=0; $ij2 < sizeof($arr2); $ij2++) { 
                        $file1 = $subdir1 . $arr1[$ij1];
                        $file2 = $subdir2 . $arr2[$ij2];
                        $res = $this->checkfile($file1,$file2);
                        if($res > 0.9){
                            $count++;
                            break;
                        }
                    }         
                }
                if(floatval($count / sizeof($arr1)) > 0.9){
                    $textout .= $subdir1 . " giong voi " . $subdir2 . "tren 90%<br/>\n";
                }  
            }         
        }
        echo $textout . "<br/>" . "Kết thúc!";        
        $f = fopen(public_path() . "\\uploads\\result.txt", 'w');
        fwrite($f, $textout);
        fclose($f);
    }
    
    public function getfilename($dir, $arrdir){
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {              
                    $file = $dir . '\\' . $entry;
                    if(is_dir($file)){
                        $arrdir = $this->getfilename($file,$arrdir);
                    }else{
                        array_push($arrdir, $file);    
                    }                    
                }
            }
            closedir($handle);
        }
        return $arrdir;
    }

    public function checkduplicate(Request $req){
        
        $dir = public_path() . "\\uploads\\testprac\\";
        $arrdir = array();
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {              
                    array_push($arrdir, $entry);                    
                }
            }
            closedir($handle);
        }

        $textout = "";

        for ($i=0; $i < sizeof($arrdir) - 1; $i++) { 
            for ($j=$i + 1; $j < sizeof($arrdir); $j++) { 
                $subdir1 = $dir . $arrdir[$i];
                $subdir2 = $dir . $arrdir[$j];
                $arr1 = array();
                $arr2 = array();

                $arr1 = $this->getfilename($subdir1, $arr1);
                $arr2 = $this->getfilename($subdir2, $arr2);
                
                if(sizeof($arr1) > 0 && sizeof($arr2) > 0){
                    $count = 0;
                    for ($ij1=0; $ij1 < sizeof($arr1); $ij1++) { 
                        for ($ij2=0; $ij2 < sizeof($arr2); $ij2++) { 
                            $file1 = $arr1[$ij1];
                            $file2 = $arr2[$ij2];
                            $res = $this->checkfile($file1,$file2);
                            if($res > 0.9){
                                $count++;
                                break;
                            }
                        }         
                    }
                    if(floatval($count / sizeof($arr1)) > 0.9){
                        $textout .= $subdir1 . " giong voi " . $subdir2 . "tren 90%<br/>\n";
                    }  
                }
            }         
        }
        echo $textout . "<br/>Kết thúc!";
        $f = fopen(public_path() . "\\uploads\\result.txt", 'w');
        fwrite($f, $textout);
        fclose($f);
    }

    public function checkfile($filename1, $filename2){
        $file1 = fopen($filename1, 'r');
        $file2 = fopen($filename2, 'r');
        $arr1 = array();
        $arr2 = array();
        while(!feof($file1)){            
            $str = fgets($file1);
            $str = trim($str);
            if($str != ''){
                $str = $this->removespace($str);
                array_push($arr1, $str);
            }
        }
        if(sizeof($arr1) == 0) return 0;
        while(!feof($file2)){            
            $str = fgets($file2);
            $str = trim($str);
            if($str != ''){
                $str = $this->removespace($str);
                array_push($arr2, $str);
            }
        }
        $count = 0;
        for ($i=0; $i < sizeof($arr1); $i++) { 
            for ($j=0; $j < sizeof($arr2); $j++) { 
                if($arr1[$i] == $arr2[$j]){
                    $count++;
                    break;
                }                
            }
        }        
        fclose($file1);
        fclose($file2);
        return floatval($count) / sizeof($arr1);
    }

    public function removespace($str){
        return str_replace(" ", "", $str);
    }


    
}

