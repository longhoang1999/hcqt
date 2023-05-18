<?php

namespace App\Http\Controllers;

use App\Helpers\RoomHelper;
use App\Models\Room;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PhongHopController extends Base\BaseController
{
    public function __construct(){

    }
    public function getAll(Request $request){
       // error_log('PhongHopController - getAll');
        $lst_phonghop = Room::orderBy('room_name')->get();
        $lst_building = [];
        
        foreach ($lst_phonghop as $phonghop) {
            $phonghop->name = $phonghop->room_name .' Nhà '.$phonghop->campus_code;
            if (array_key_exists($phonghop->campus_id, $lst_building)) {
                $building = $lst_building[$phonghop->campus_id];
                $lst_phonghop_sub = $building['ds_phong_hop'];
                $lst_phonghop_sub[] = $phonghop;
                $building['ds_phong_hop'] = $lst_phonghop_sub;
            } else {
                $building = [
                    'branch_id' => $phonghop->branch_id,
                    'branch_name' => $phonghop->branch_name,
                    'campus_id' => $phonghop->campus_id,
                    'campus_name' => $phonghop->campus_name,
                    'ds_phong_hop' => [$phonghop]
                ];
            }
            $lst_building[$phonghop->campus_id] = $building;
        }
        $rst_lst_building = [];
        foreach ($lst_building as $building) {
            $rst_lst_building[] = $building;
        }
        return $this->responseJson([
            'status' => 'ok',
            'ds_phong_hop' => $lst_phonghop,
            'ds_toa_nha' => $rst_lst_building
        ]);
    }
    
    public function importRooms(Request $request) {
        Log::info("Lấy danh sách Phòng họp từ bên ngoài");
           // error_log('importRoom 0');
            $result = RoomHelper::importRooms();
           // error_log('importRoom 1');
            if ($result['status'] != 'ok') {
            return $this->responseJson($result);
            }   
            //error_log('importRoom 2');
            $lst_rooms = Room::orderBy('room_name')->get();
           // error_log('importRoom 3');
            return $this->responseJson([
                'status' => 'ok',
                'ds_phong_hop' => $lst_rooms
            ]);
        
    }
}
