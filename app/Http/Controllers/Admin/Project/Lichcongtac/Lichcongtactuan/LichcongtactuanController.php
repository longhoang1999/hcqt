<?php namespace App\Http\Controllers\Admin\Project\Lichcongtac\Lichcongtactuan;

use App\Http\Controllers\Admin\DefinedController;
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
// export excel
use App\Exports\StandardExport;
use App\Exports\ListStandardExport;
use App\Exports\MinimunExport;
use App\Exports\GyhdExport;




class LichcongtactuanController extends DefinedController
{
    public $baseView = "admin.project.Lichcongtac.Lichcongtactuan.";

    
    public function getCsdt() {
        if(Sentinel::check()){
            return Sentinel::getUser()->csdt_id;
        }
    }

    public function index(Request $req){
        if($this->checkPermissions(1)){
            return view($this->baseView . 'index')->with([]); 
        }
    }
    


   
}