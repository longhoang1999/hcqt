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

class DashboardController 
{
    public function index(Request $req){
        return view('admin.dashboard.index');
    }

    public function getList(Request $req){
        putenv("GOOGLE_APPLICATION_CREDENTIALS=D:\\xampp7\\htdocs\\default\\mykey.json");
        $client = new NeoClient(env("DB_HOST_NEO", "18.158.132.112"), env("DB_PORT_NEO", "7474"));
        $client->getTransport()->useHttps(null)->setAuth(env("DB_USERNAME_NEO", "neo4j"), env("DB_PASSWORD_NEO", "Vietnam99"));
        
        $query_string = 'MATCH (b:State) RETURN b';
        $results = $this->insertDB($query_string,$client);
        $state = array();
        if (count($results) > 0) {            
            $out = array();
            foreach ($results as $key => $value) {
                $val = $value['b']->getproperty('name');
                //$val = preg_replace('/[^A-Za-z0-9\-]/', '', trim($val));
                if(!in_array($val, $state)) array_push($state, $val);                
            }
        }

        $query_string = 'MATCH (b:Category) RETURN b';
        $results = $this->insertDB($query_string,$client);
        $cate = array();
        if (count($results) > 0) {            
            $out = array();
            foreach ($results as $key => $value) {
                $val = $value['b']->getproperty('name');
                //$val = preg_replace('/[^A-Za-z0-9\-]/', '', trim($val));
                if(!in_array($val, $cate)) array_push($cate, $val);                
            }
        }

        return array(0 => $cate, 1 => $state);
    }

    public function getTags(Request $req){
        putenv("GOOGLE_APPLICATION_CREDENTIALS=D:\\xampp7\\htdocs\\default\\mykey.json");
        $client = new NeoClient(env("DB_HOST_NEO", "18.158.132.112"), env("DB_PORT_NEO", "7474"));
        $client->getTransport()->useHttps(null)->setAuth(env("DB_USERNAME_NEO", "neo4j"), env("DB_PASSWORD_NEO", "Vietnam99"));
        
        if($req->state != ''){
            $str = explode(';', $req->state);
            $state = "";
            foreach ($str as $key => $value) {
                if($value != ''){
                    $state .= ',"' . $value . '"';
                }
            }
            $state = substr($state, 1);
            $state = "[" . $state . "]";

            if($req->city != ''){
                $str = explode(';', $req->city);
                $city = "";
                foreach ($str as $key => $value) {
                    if($value != ''){
                        $city .= ',"' . $value . '"';
                    }
                }   
                $city = substr($city, 1);
                $city = "[" . $city . "]";       

                if($req->category != ''){
                    $str = explode(';', $req->category);
                    $category = "";
                    foreach ($str as $key => $value) {
                        if($value != ''){
                            $category .= ',"' . $value . '"';
                        }
                    }   
                    $category = substr($category, 1);
                    $category = "[" . $category . "]";

                    $query_string = 'Match(a:City), (c:Image), (b:Label), (d:Category) Where a.name IN '. $city . ' AND d.name IN ' . $category . ' AND (a)-->(c)-->(b)-->(d) Return b';
                }else{
                    $query_string = 'Match(a:City), (c:Image), (b:Label), (d:Category) Where a.name IN '. $city . ' AND (a)-->(c)-->(b)-->(d) Return d';    
                }
            }else{
                $query_string = 'Match(a:State),(b:City) Where a.name IN '. $state . ' AND (a)-->(b) Return b';    
            } 
        }else{
            $query_string = 'MATCH (b:State) RETURN b';
        }

        // echo $query_string;
        // die;

        $query = new CypherQuery($client, $query_string);
        $results = $query->getResultSet();        
        if (count($results) > 0) {            
            $out = array();
            foreach ($results as $key => $value) {
                if($req->city != '') $val = $value['b']->getproperty('description');  
                else $val = $value['b']->getproperty('name');
                $val = preg_replace('/[^A-Za-z0-9\-]/', '', trim($val));
                if(!in_array($val, $out)) array_push($out, $val);                
            }
            return $out;
        }else return 0;
    }

    public function showimg(Request $req){        

        putenv("GOOGLE_APPLICATION_CREDENTIALS=D:\\xampp7\\htdocs\\default\\mykey.json");
        $client = new NeoClient(env("DB_HOST_NEO", "localhost"), env("DB_PORT_NEO", "7474"));
        $client->getTransport()->useHttps(null)->setAuth(env("DB_USERNAME_NEO", "neo4j"), env("DB_PASSWORD_NEO", "Vietnam99"));
        
        $out = new Collection;

        if(isset($req->city) && isset($req->label) && $req->city != '' && $req->label != ''){
            $req->city = str_replace("_______", ";", $req->city);
            $req->label = str_replace("_______", ";", $req->label);
            $req->category = str_replace("_______", ";", $req->category);

            $req->city = str_replace("_____", " ", $req->city);
            $req->label = str_replace("_____", " ", $req->label);
            $req->category = str_replace("_____", ";", $req->category);

            $str = explode(';', $req->city);
            $city = "";
            foreach ($str as $key => $value) {
                if($value != ''){
                    $city .= ',"' . trim($value) . '"';
                }
            }
            $city = substr($city, 1);
            $city = "[" . $city . "]";
            
            $str = explode(';', $req->label);
            $label = "";
            foreach ($str as $key => $value) {
                if($value != ''){
                    $label .= ',"' . trim($value) . '"';
                }
            }
            $label = substr($label, 1);
            $label = "[" . $label . "]";

            $query_string = 'Match(a:City), (b:Image), (c:Label) Where a.name IN '. $city . ' AND c.description IN '. $label . ' AND (a)-->(b)-->(c) Return b';

            // echo $query_string;
            // die;

            $query = new CypherQuery($client, $query_string);
            $results = $query->getResultSet();        
            if (count($results) > 0) {
                foreach ($results as $key => $value) {                
                    $basepath = asset('uploads/exp') . '/';
                    $props = $value['b']->getproperties();
                    $props["link"] = $basepath . $props["name"];
                    
                    $out->push([
                        'title' => $props['title'],
                        'name' => $props['name'],
                        'author'  => $props['author'],
                        'lat'    => $props['lat'],
                        'lon' => $props['lon'],
                        'credit'      => $props['credit'],
                        'image'      => '<img src="' . $props['link'] . '" width="200" height="200"/>',                        
                    ]);  
                }
            }
        }
        return DataTables::of($out)->rawColumns(['image'])->make(true);                
    }

    public function index1(Request $req){
        putenv("GOOGLE_APPLICATION_CREDENTIALS=D:\\xampp7\\htdocs\\default\\mykey.json");
        $client = new NeoClient(env("DB_HOST_NEO", "localhost"), env("DB_PORT_NEO", "7474"));
        $client->getTransport()->useHttps(null)->setAuth(env("DB_USERNAME_NEO", "neo4j"), env("DB_PASSWORD_NEO", "Vietnam99"));

        $file = fopen(public_path() . "/uploads/exp.csv","r");
        while(! feof($file)){
            $s = fgetcsv($file);    
            if(strlen($s[0]) > 0 && strlen($s[3]) > 0 && strlen($s[5]) > 0){       
                $query_string = 'MERGE (n:City {name: "' . $this->preStr($s[0]) . '" , ZipCode: "' . $this->preStr($s[1]) . '"})';
                $this->insertDB($query_string,$client);

                $query_string = 'MERGE (n:State {name: "' . $this->preStr($s[2]) . '"})';
                $this->insertDB($query_string,$client);

                $query_string = 'MATCH (a:City), (b:State) WHERE a.name = "' . $this->preStr($s[0]) . '" AND b.name = "' . $this->preStr($s[2]) . '" MERGE (a)-[r:IS_IN_STATE {description: ""}]->(b) RETURN r.name';
                $this->insertDB($query_string,$client);

                $query_string = 'MATCH (a:City), (b:State) WHERE a.name = "' . $this->preStr($s[0]) . '" AND b.name = "' . $this->preStr($s[2]) . '" MERGE (b)-[r:HAS_CITY {description: ""}]->(a) RETURN r.name';
                $this->insertDB($query_string,$client);

                $query_string = 'MERGE (n:Image {name: "' . $this->preStr($s[5]) . '", lat: "' . $this->preStr($s[3]) . '", lon: "' . $this->preStr($s[4]) . '", author: "' . $this->preStr($s[6]) . '", source: "' . $this->preStr($s[7]) . '", credit: "' . $this->preStr($s[8]) . '", title: "' . $this->preStr($s[9]) . '"})';
                $this->insertDB($query_string,$client);

                $query_string = 'MATCH (a:City), (b:Image) WHERE a.name = "' . $this->preStr($s[0]) . '" AND b.name = "' . $this->preStr($s[5]) . '" AND b.lat = "' . $this->preStr($s[3]) . '" AND b.lon = "' . $this->preStr($s[4]) . '" AND b.author = "' . $this->preStr($s[6]) . '" AND b.source = "' . $this->preStr($s[7]) . '" AND b.credit = "' . $this->preStr($s[8]) . '" AND b.title = "' . $this->preStr($s[9]) . '" MERGE (a)-[r:CITY_HAS_IMAGE {description: ""}]->(b) RETURN r.name';
                $this->insertDB($query_string,$client);

                $query_string = 'MATCH (a:City), (b:Image) WHERE a.name = "' . $this->preStr($s[0]) . '" AND b.name = "' . $this->preStr($s[5]) . '" AND b.lat = "' . $this->preStr($s[3]) . '" AND b.lon = "' . $this->preStr($s[4]) . '" AND b.author = "' . $this->preStr($s[6]) . '" AND b.source = "' . $this->preStr($s[7]) . '" AND b.credit = "' . $this->preStr($s[8]) . '" AND b.title = "' . $this->preStr($s[9]) . '" MERGE (b)-[r:IMG_IS_IN_CITY {description: ""}]->(a) RETURN r.name';            
                $this->insertDB($query_string,$client);
            }else break;
        }
        fclose($file);
        echo "done";
    }


    public function index2_bk(Request $req){
        
        $dir = public_path() . "/uploads/exp/";
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {                                        
                    //$filename = asset('/uploads/exp/' . $entry);
                    $realfilename = $dir . $entry;
                    $this->solve($entry, $realfilename);
                    echo $entry . "<br/>";                    
                    if(file_exists($realfilename)){
                        unlink($realfilename);
                    }
                }
            }
            closedir($handle);
        }    
        

        //$filename = asset('/uploads/exp/18763_barcellona_la_sagrada_familia.jpg');
        //$this->solve($filename);   
    }

    public function index2(Request $req){
        
        $file = public_path() . "/uploads/export.txt";
        $fileout = public_path() . "/uploads/exportout.txt";
        $f = fopen($file, "r");
        $fout = fopen($fileout, "w");
        $s = array();
        $mark = array();
        $n = 0;
        while(!feof($f)){            
            $str = fgets($f);
            $s[$n] = $str;
            $mark[$n] = 1;
            $n++;
        }
        $m = $n;
        for ($i=0; $i < $n - 1; $i++) { 
            if($i % 1000 == 0) echo "i=" . $i . "<br/>";
            for($j = $i + 1; $j<$n; $j++){
                if($mark[$i] != -1 && $mark[$j] != -1 && $s[$i] == $s[$j]){
                    $mark[$i]++;
                    $mark[$j] = -1;                    
                }
            }
        }

        for($i = 0; $i < $n ; $i++){
            if($mark[$i] > 0){
                fwrite($fout, $s[$i] . "," . $mark[$i] . "\n");
            }
        }
        
        fclose($f);
        fclose($fout);

        //$filename = asset('/uploads/exp/18763_barcellona_la_sagrada_familia.jpg');
        //$this->solve($filename);   
    }


    public function solve($filename, $realfilename){
        
        putenv("GOOGLE_APPLICATION_CREDENTIALS=D:\\xampp7\\htdocs\\default\\mykey.json");

        $client = new NeoClient(env("DB_HOST_NEO", "18.158.132.112"), env("DB_PORT_NEO", "7474"));
        $client->getTransport()->useHttps(null)->setAuth(env("DB_USERNAME_NEO", "neo4j"), env("DB_PASSWORD_NEO", "Vietnam99"));
        
        # instantiates a client
        $imageAnnotator = new ImageAnnotatorClient();
        # prepare the image to be annotated
        $image = file_get_contents($realfilename);

        # performs label detection on the image file
        $response = $imageAnnotator->labelDetection($image);
        $labels = $response->getLabelAnnotations();

        // $response = $imageAnnotator->imagePropertiesDetection($image);
        // $props = $response->getImagePropertiesAnnotation();

        $response = $imageAnnotator->landmarkDetection($image);        
        $landmarks = $response->getLandmarkAnnotations();

        $response = $imageAnnotator->objectLocalization($image);
        $objects = $response->getLocalizedObjectAnnotations();

        $imageAnnotator->close();
        $props = null;
        $this->insertNeo($labels,$props,$landmarks,$objects,$client,$filename);
        
        
    }    

    private function insertNeo($labels,$props,$landmarks,$objects,$client,$filename){
        $query_string = 'MERGE (n:Image {name:"' . $filename . '"})';
        $this->insertDB($query_string,$client);

        foreach ($landmarks as $landmark) {
            $query_string = 'MERGE (n:Landmark {description: "' . $this->preStr($landmark->getDescription()) . '"})';
            $this->insertDB($query_string,$client);

            $latlon = $landmark->getLocations()[0]->getLatLng(); 
            if($latlon){
                $lat = $latlon->getLatitude() != null ? $latlon->getLatitude() : 0;
                $lon = $latlon->getLongitude() != null ? $latlon->getLongitude() : 0;
            }else{

            }
            $query_string = 'MATCH (a:Image), (b:Landmark) WHERE a.name = "' . $filename . '" AND b.description = "' . $this->preStr($landmark->getDescription()) . '" MERGE (a)-[r:PLACE {lat: "' . $lat . '", lon: "' . $lon . '"}]->(b) RETURN r.name';
            $this->insertDB($query_string,$client);
            $query_string = 'MATCH (a:Image), (b:Landmark) WHERE a.name = "' . $filename . '" AND b.description = "' . $this->preStr($landmark->getDescription()) . '" MERGE (b)-[r:IS_LOCATED {lat: "' . $lat . '", lon: "' . $lon . '"}]->(a) RETURN r.name';
            $this->insertDB($query_string,$client);            
        }

        foreach ($labels as $label) {
            $query_string = 'MERGE (n:Label {description: "' . $this->preStr($label->getDescription()) . '"})';
            $this->insertDB($query_string,$client); 
            $score = $label->getScore() != null ? $label->getScore() : 0;
            $topicality = $label->getTopicality() != null ? $label->getTopicality() : 0;
            $query_string = 'MATCH (a:Image), (b:Label) WHERE a.name = "' . $filename . '" AND b.description = "' . $this->preStr($label->getDescription()) . '" MERGE (b)-[r:TAG {score: "' . $score . '", topicality: "' . $topicality . '"}]->(a) RETURN r.score';
            $this->insertDB($query_string,$client);

            $query_string = 'MATCH (a:Image), (b:Label) WHERE a.name = "' . $filename . '" AND b.description = "' . $this->preStr($label->getDescription()) . '" MERGE (a)-[r:IS_TAGGED {score: "' . $score . '", topicality: "' . $topicality . '"}]->(b) RETURN r.score';
            $this->insertDB($query_string,$client); 
        }

        foreach ($objects as $object) {
            $query_string = 'MERGE (n:Object {description: "' . $this->preStr($object->getName()) . '"})';
            $this->insertDB($query_string,$client); 
            $score = $object->getScore();
            $query_string = 'MATCH (a:Image), (b:Object) WHERE a.name = "' . $filename . '" AND b.description = "' . $this->preStr($object->getName()) . '" MERGE (b)-[r:IS_IN_IMAGE {score: "' . $score . '"}]->(a) RETURN r.score';
            $this->insertDB($query_string,$client);

            $query_string = 'MATCH (a:Image), (b:Object) WHERE a.name = "' . $filename . '" AND b.description = "' . $this->preStr($object->getName()) . '" MERGE (a)-[r:HAS_OBJECT {score: "' . $score . '"}]->(b) RETURN r.score';
            $this->insertDB($query_string,$client); 
        }
        
/*
        print("<br/>Properties:" . PHP_EOL . "<br/>");
        foreach ($props->getDominantColors()->getColors() as $colorInfo) {
            printf("Fraction: %s" . PHP_EOL, $colorInfo->getPixelFraction());
            $color = $colorInfo->getColor();
            printf("Red: %s" . PHP_EOL, $color->getRed());
            printf("Green: %s" . PHP_EOL, $color->getGreen());
            printf("Blue: %s" . PHP_EOL, $color->getBlue());
            print(PHP_EOL);
            echo "<br/>";
        }        
*/      
        //echo "done";
    }

    private function insertNeo_bk($labels,$props,$landmarks,$objects,$client,$filename){
        $query_string = 'MERGE (n:Image {name:"' . $filename . '"})';
        $this->insertDB($query_string,$client);

        foreach ($landmarks as $landmark) {
            $query_string = 'create (n:Landmark {description: "' . $this->preStr($landmark->getDescription()) . '"})';
            $this->insertDB($query_string,$client);

            $latlon = $landmark->getLocations()[0]->getLatLng(); 
            if($latlon){
                $lat = $latlon->getLatitude() != null ? $latlon->getLatitude() : 0;
                $lon = $latlon->getLongitude() != null ? $latlon->getLongitude() : 0;
            }else{

            }
            $query_string = 'MATCH (a:Image), (b:Landmark) WHERE a.name = "' . $filename . '" AND b.description = "' . $this->preStr($landmark->getDescription()) . '" CREATE (a)-[r:PLACE {lat: "' . $lat . '", lon: "' . $lon . '"}]->(b) RETURN r.name';
            $this->insertDB($query_string,$client);
            $query_string = 'MATCH (a:Image), (b:Landmark) WHERE a.name = "' . $filename . '" AND b.description = "' . $this->preStr($landmark->getDescription()) . '" CREATE (b)-[r:IS_LOCATED {lat: "' . $lat . '", lon: "' . $lon . '"}]->(a) RETURN r.name';
            $this->insertDB($query_string,$client);            
        }

        foreach ($labels as $label) {
            $query_string = 'create (n:Label {description: "' . $this->preStr($label->getDescription()) . '"})';
            $this->insertDB($query_string,$client); 
            $score = $label->getScore() != null ? $label->getScore() : 0;
            $topicality = $label->getTopicality() != null ? $label->getTopicality() : 0;
            $query_string = 'MATCH (a:Image), (b:Label) WHERE a.name = "' . $filename . '" AND b.description = "' . $this->preStr($label->getDescription()) . '" CREATE (b)-[r:TAG {score: "' . $score . '", topicality: "' . $topicality . '"}]->(a) RETURN r.score';
            $this->insertDB($query_string,$client);

            $query_string = 'MATCH (a:Image), (b:Label) WHERE a.name = "' . $filename . '" AND b.description = "' . $this->preStr($label->getDescription()) . '" CREATE (a)-[r:IS_TAGGED {score: "' . $score . '", topicality: "' . $topicality . '"}]->(b) RETURN r.score';
            $this->insertDB($query_string,$client); 
        }

        foreach ($objects as $object) {
            $query_string = 'create (n:Object {description: "' . $this->preStr($object->getName()) . '"})';
            $this->insertDB($query_string,$client); 
            $score = $object->getScore();
            $query_string = 'MATCH (a:Image), (b:Object) WHERE a.name = "' . $filename . '" AND b.description = "' . $this->preStr($object->getName()) . '" CREATE (b)-[r:IS_IN_IMAGE {score: "' . $score . '"}]->(a) RETURN r.score';
            $this->insertDB($query_string,$client);

            $query_string = 'MATCH (a:Image), (b:Object) WHERE a.name = "' . $filename . '" AND b.description = "' . $this->preStr($object->getName()) . '" CREATE (a)-[r:HAS_OBJECT {score: "' . $score . '"}]->(b) RETURN r.score';
            $this->insertDB($query_string,$client); 
        }
        
/*
        print("<br/>Properties:" . PHP_EOL . "<br/>");
        foreach ($props->getDominantColors()->getColors() as $colorInfo) {
            printf("Fraction: %s" . PHP_EOL, $colorInfo->getPixelFraction());
            $color = $colorInfo->getColor();
            printf("Red: %s" . PHP_EOL, $color->getRed());
            printf("Green: %s" . PHP_EOL, $color->getGreen());
            printf("Blue: %s" . PHP_EOL, $color->getBlue());
            print(PHP_EOL);
            echo "<br/>";
        }        
*/      
        //echo "done";
    }

    public function classifyTags(Request $req){

        $clientneo = new NeoClient(env("DB_HOST_NEO", "18.158.132.112"), env("DB_PORT_NEO", "7474"));
        $clientneo->getTransport()->useHttps(null)->setAuth(env("DB_USERNAME_NEO", "neo4j"), env("DB_PASSWORD_NEO", "Vietnam99"));

        $headers = array(
            //'Content-type' => 'application/xml',   //;charset=utf-8', Content-Type: text/xml
            'Accept' => '*/*',
            'x-rapidapi-host'   => 'wordsapiv1.p.rapidapi.com',
            'x-rapidapi-key'    => 'c014cd1e2bmshfada66a7f7d2eb8p1c2c9ajsn87cdc2a7744a',
            'useQueryString'    => 'true',
        );

        $client = new Client();

        $fin = fopen(public_path() . '/uploads/label.csv', 'r');
        
        while(!feof($fin)){
            $s = fgets($fin);
            if(trim($s) == '') break;            
            $str = explode(',', $s);
            if(sizeof($str) > 2 && $str[1] != ''){
                $tag = $str[0];
                $cate = $str[1];
                $query_string = 'MERGE (n:Category {name: "' . $this->preStr($cate) . '"})';
                echo "add only: " . $query_string . "<br/>";
                $this->insertDB($query_string,$clientneo); 
                
                $query_string = 'MATCH (a:Category), (b:Label) WHERE a.name = "' . $this->preStr($cate) . '" AND b.description = "' . $tag . '" MERGE (b)-[r:HAS_CATEGORY {description: ""}]->(a) RETURN r';
                echo "match only: " . $query_string . "<br/>";
                $this->insertDB($query_string,$clientneo);
            }else{
                $s = $str[0];
                $scheck = $s;
                if(strpos($s, ' ') !== false){
                    $str = explode(' ', $s);
                    $st = '';
                    foreach ($str as $key => $value) {
                        if($st == '') $st = $value;
                        else $st .= '%20' . $value;
                    }
                    $scheck = $st;
                }

                $url = 'https://wordsapiv1.p.rapidapi.com/words/' . $scheck . '/similarTo';
                echo $url . "<br/>";
                
                $isok = false;
                try {
                    $request = $client->get($url,array(
                        'headers' => $headers, 
                            //'body' => json_encode($this->data)
                    ));
                    $result = $request->getBody()->getContents();
                    echo 'result:' . $result . "<br/>";
                    $result = json_decode($result);                
                    $isok = true;
                } catch (\Exception $e) {
                    $isok = false;
                }

                if($isok && isset($result->similarTo) && sizeof($result->similarTo) > 0){
                    $isadd = true;
                    foreach ($result->similarTo as $key => $value) {
                        $query_string = 'MATCH (n:Category {name: "' . $this->preStr($value) . '"}) RETURN n';
                        echo "add: " . $query_string . "<br/>";
                        $resu = $this->insertDB($query_string,$clientneo); 
                        $cou = count($resu);
                        if($cou > 0){
                            $query_string = 'MATCH (a:Category), (b:Label) WHERE a.name = "' . $this->preStr($value) . '" AND b.description = "' . $s . '" MERGE (b)-[r:HAS_CATEGORY {description: ""}]->(a) RETURN r';
                            echo "match: " . $query_string . "<br/>";
                            $this->insertDB($query_string,$clientneo);
                            $isadd = false;
                            break;
                        }                        
                    }
                    if($isadd){
                        $query_string = 'MERGE (n:Category {name: "others"})';
                        echo "add not ok: " . $query_string . "<br/>";
                        $this->insertDB($query_string,$clientneo); 
                        
                        $query_string = 'MATCH (a:Category), (b:Label) WHERE a.name = "others" AND b.description = "' . $s . '" MERGE (b)-[r:HAS_CATEGORY {description: ""}]->(a) RETURN r';
                        echo "match not ok: " . $query_string . "<br/>";
                        $this->insertDB($query_string,$clientneo);
                    }
                }else{
                    $query_string = 'MERGE (n:Category {name: "others"})';
                    echo "add not ok: " . $query_string . "<br/>";
                    $this->insertDB($query_string,$clientneo); 
                    
                    $query_string = 'MATCH (a:Category), (b:Label) WHERE a.name = "others" AND b.description = "' . $s . '" MERGE (b)-[r:HAS_CATEGORY {description: ""}]->(a) RETURN r';
                    echo "match not ok: " . $query_string . "<br/>";
                    $this->insertDB($query_string,$clientneo);
                }
                //break;
            }
        }
        fclose($fin);        
        echo "done";
    }

    private function insertDB($query_string,$client){
        $query = new CypherQuery($client, $query_string);
        $results = $query->getResultSet();    
        return $results;   
        //echo $query_string . "<br/>";
    }   
    
    private function preStr($string){        
        return addslashes(str_replace("_____", ",", $string));
    } 

    private function preStr1($string){
        $str = str_replace(' ', '_____', $string); // Replaces all spaces with hyphens.

        $str = preg_replace('/[^A-Za-z0-9\-]/', '', $str);
        return str_replace('_____', ' ', $str);        
    }

    public function index_cate(Request $req){
        putenv("GOOGLE_APPLICATION_CREDENTIALS=D:\\xampp7\\htdocs\\default\\mykey.json");
        $client = new NeoClient(env("DB_HOST_NEO", "18.158.132.112"), env("DB_PORT_NEO", "7474"));
        $client->getTransport()->useHttps(null)->setAuth(env("DB_USERNAME_NEO", "neo4j"), env("DB_PASSWORD_NEO", "Vietnam99"));
        
        $query_string = 'MATCH (b:Category) RETURN b ORDER BY b.name';        
        $query = new CypherQuery($client, $query_string);
        $results = $query->getResultSet();   
        $cate = array();   
        $list = array();  
        if (count($results) > 0) {            
            foreach ($results as $key => $value) {
                $val = trim($value['b']->getproperty('name'));
                //$val = preg_replace('/[^A-Za-z0-9\-]/', '', trim($val));
                $cate[$val] = $val;                
            }            
        }


        $query_string = 'MATCH p = (a:Label)-[r:HAS_CATEGORY]->(b:Category) RETURN p ORDER BY b.name, a.description';        
        $query = new CypherQuery($client, $query_string);
        $results = $query->getResultSet();   
        //var_dump(count($results));        
        if (count($results) > 0) {                        
            foreach ($results as $key => $value) {                
                $la = $value['p']->getStartNode();
                $ca = $value['p']->getEndNode();
                $val = trim($ca->getproperty('name'));
                $valu = trim($la->getproperty('description'));
                // //$val = preg_replace('/[^A-Za-z0-9\-]/', '', trim($val));
                // $valu = trim($value['a']->getproperty('description'));
                // //$valu = preg_replace('/[^A-Za-z0-9\-]/', '', trim($valu));
                $list[$valu] = $val;
                //echo $val . ' ' . $valu;     
            }            
        }

        return view('index_cate')->with(['cate' => $cate, 'list' => $list]);

    }

    public function updatecate(Request $req){
        $clientneo = new NeoClient(env("DB_HOST_NEO", "18.158.132.112"), env("DB_PORT_NEO", "7474"));
        $clientneo->getTransport()->useHttps(null)->setAuth(env("DB_USERNAME_NEO", "neo4j"), env("DB_PASSWORD_NEO", "Vietnam99"));

        $from = $req->catefrom;
        $to = $req->cateto;

        $query_string = 'MERGE (n:Category {name: "' . $this->preStr($to) . '"})';
        $this->insertDB($query_string,$clientneo); 

        //get all nodes with catefrom
        $query_string = 'MATCH p = (a:Label)-[r:HAS_CATEGORY]->(b:Category {name: "' . $this->preStr($from) . '"}) RETURN a';                
        $results = $this->insertDB($query_string,$clientneo);                   
        if (count($results) > 0) {                        
            foreach ($results as $key => $value) {                
                $val = trim($value['a']->getproperty('description'));
                $query_string = 'MATCH (a:Category), (b:Label) WHERE a.name = "' . $this->preStr($to) . '" AND b.description = "' . $this->preStr($val) . '" MERGE (b)-[r:HAS_CATEGORY {description: ""}]->(a) RETURN r';
                $this->insertDB($query_string,$clientneo);                
            }            
        }

        // Delete catefrom all relationship
        $query_string = 'MATCH (n:Category { name: "' . $this->preStr($from) . '" }) DETACH DELETE n';
        $this->insertDB($query_string,$clientneo);   

        return Redirect::back()->with('success',"Update category successfully!");
    }

    public function showslide(Request $req){
        putenv("GOOGLE_APPLICATION_CREDENTIALS=D:\\xampp7\\htdocs\\default\\mykey.json");
        $clientneo = new NeoClient(env("DB_HOST_NEO", "18.158.132.112"), env("DB_PORT_NEO", "7474"));
        $clientneo->getTransport()->useHttps(null)->setAuth(env("DB_USERNAME_NEO", "neo4j"), env("DB_PASSWORD_NEO", "Vietnam99"));
        $category = $req->category;
        $state = $req->state;

        $strcategory = explode(',', $category);
        $category = '';
        foreach ($strcategory as $key => $value) {
            if($category == '') $category = '"' . trim($value) . '"';
            else $category .= ',"' . trim($value) . '"';
        }

        $strstate = explode(',', $state);
        $state = '';
        foreach ($strstate as $key => $value) {
            if($state == '') $state = '"' . trim($value) . '"';
            else $state .= ',"' . trim($value) . '"';
        }

        $category = '[' . $category . ']';
        $state = '[' . $state . ']';

        $query_string = 'Match(a:State),(b:City) Where a.name IN '. $state . ' AND (a)-->(b) Return b';        
        $results = $this->insertDB($query_string,$clientneo);
        $city = "";
        $citycheck = array();
        if (count($results) > 0) {            
            foreach ($results as $key => $value) {
                $val = trim($value['b']->getproperty('name'));
                //$val = preg_replace('/[^A-Za-z0-9\-]/', '', trim($val));
                if(!in_array($val, $citycheck)){
                    if($city == '') $city = '"' . $val . '"';
                    else $city .= ',"' . $val . '"';
                    array_push($citycheck, $val);
                }
            }            
        }
        $city = '[' . $city . ']';

        $query_string = 'Match(b:Label),(a:Category) Where a.name IN '. $category . ' AND (b)-->(a) Return b';
        $results = $this->insertDB($query_string,$clientneo);
        $label = "";
        if (count($results) > 0) {            
            foreach ($results as $key => $value) {
                $val = trim($value['b']->getproperty('description'));
                //$val = preg_replace('/[^A-Za-z0-9\-]/', '', trim($val));
                if($label == '') $label = '"' . $val . '"';
                else $label .= ',"' . $val . '"';
            }            
        }
        $label = '[' . $label . ']';

        $query_string = 'Match(a:City), (b:Image), (c:Label) Where a.name IN '. $city . ' AND c.description IN '. $label . ' AND (a)-->(b)-->(c) Return b';

        $results = $this->insertDB($query_string,$clientneo);
        $numimg = count($results);
        $output = array();
        if ($numimg > 0) {
            $out = array();
            $mark = array();    
            $j=0;        
            foreach ($results as $key => $value) {                
                $basepath = asset('uploads/exp') . '/';
                $props = $value['b']->getproperties();
                $props["link"] = $basepath . $props["name"];
                $info = 'title:' . $props['title'] .', name:' . $props['name'] . ', author:' . $props['author'];       
                $out[$j] = array($props['link'],$info);
                $mark[$j++] = 0;

                // array_push($out, array(
                //     'title' => $props['title'],
                //     'name' => $props['name'],
                //     'author'  => $props['author'],
                //     'lat'    => $props['lat'],
                //     'lon' => $props['lon'],
                //     'credit'      => $props['credit'],
                //     'link'      => $props['link'],                        
                // ));  
            }

            if($numimg > 60){
                while(!$this->count($mark,60)){ 
                    $j = rand() % $numimg;
                    $mark[$j] = 1;
                }
                for ($i=0; $i < $numimg; $i++) { 
                    if($mark[$i] == 1){
                        array_push($output, $out[$i]);
                    }
                }
                $numimg = 60;
            }else{
                $output = $out;
            }
        }

        // for music
        $dir = public_path() . "/uploads/music/";
        $musicpath = '';
        $music = '';
        if ($handle = opendir($dir)) {
            $mu = rand() % 7;
            $j = 0;
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    if($j == $mu){
                        $music = asset('/uploads/music/' . $entry);
                        $musicpath = $dir . $entry;
                        break;
                    }
                    $j++;
                }
            }
            closedir($handle);
        }

        $mp3file = new MP3File($musicpath);//http://www.npr.org/rss/podcast.php?id=510282
        $duration1 = $mp3file->getDurationEstimate();//(faster) for CBR only
        $duration2 = $mp3file->getDuration();//(slower) for VBR (or CBR)
        $dur = $duration1 > $duration2 ? $duration2 : $duration1;
        
        return view('slide')->with(['output' => $output, 'music' => $music, 'dur' => $dur, 'numimg' => $numimg]);
    }

    private function count($a,$num){
        $count = 0;
        foreach ($a as $key => $value) {
            if($value == 1) $count++;
        }
        return $count == $num;
    }
}

