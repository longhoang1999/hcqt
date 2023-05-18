<?php

namespace App\Http\Controllers;

use App\Common\Constant\UploadFileConstants;
use App\Helpers\UploadHelper;
use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use App\Utils\NumUtils;
use Exception;
use Illuminate\Http\Request;

class BannerController extends Base\ResourceBaseController
{
    public function __construct(){

    }

    public function insertOrUpdate(BannerRequest $request){
        $banner = Banner::first();
        if (!isset($banner)) 
        {
            $banner = new Banner();
            $banner->save();
        }
        
        if ($request->hasFile('banner')) {
            
            $uploadFiles = $request->file('banner');
            
            if (!empty($uploadFiles)) {
                if (is_array($uploadFiles)) {

                } else {   
                    $upload_file = UploadHelper::storeFile(
                        $uploadFiles,
                        $banner->uploadfile_id,
                        $banner->id,
                        UploadFileConstants::SOURCE_DOCUMENT,
                        'banner'
                    );
                    if ($upload_file) {
                        $banner->update(
                            array(
                            'uploadfile_id' => $upload_file->id
                        )
                        );
                    }
                     
                }
            }
        }
        $banner->load('image');
        return $this->responseJson(array(
            'status'=>'ok',
            'banner' => $banner
        ));
    }


    public function getBanner(){
        $banner = Banner::first();
       
        if (isset($banner)) {
            $banner->load('image');
        }
        return $this->responseJson([
            'banner'=>$banner
        ]);
    }
    
    public function getBanners(){
        $banners = Banner::all()->load('image');
      
        return $this->responseJson([
            'banners'=>$banners
        ]);
    }
}
