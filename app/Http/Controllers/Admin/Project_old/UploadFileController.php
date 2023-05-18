<?php

namespace App\Http\Controllers;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Base\ResourceBaseController;
use App\Models\UploadFile;
use Exception;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class UploadFileController extends ResourceBaseController
{
    public function __construct()
    {

        // $this->authorizeResource(UploadFile::class, 'uploadFile');//, ['except' => ['index', 'create', 'store']]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $ctl_filename = (isset($request->ctl_filename) && !empty($request->ctl_filename)) ? $request->ctl_filename : 'image_file';
        
        $upload_file_id = (isset($request->upload_file_id) && is_numeric($request->upload_file_id)) ? $request->upload_file_id : 0;
        $fk_id = (isset($request->fk_id) && is_numeric($request->fk_id)) ? $request->fk_id : 0;
        $source = (isset($request->source) && !empty($request->source)) ? $request->source : 'uploads';
        $cat = (isset($request->category) && !empty($request->category)) ? $request->category : 'tmp';
        $ret_type = (isset($request->ret_type) && is_numeric($request->ret_type)) ? $request->ret_type : 0; //0: show/down load file; 1: UploadFile Object
        
        try {
            if ($request->hasFile($ctl_filename)) {
               
                $uploadFiles = $request->file($ctl_filename);
                if (!empty($uploadFiles)) {
                    if (is_array($uploadFiles)) {
                        $upload_file = null;
                        $upload_files = array();
                        //for multifiles
                        foreach ($uploadFiles as $file) {
                            $upload_file = UploadHelper::storeFile(
                                $file,
                                $upload_file_id,
                                $fk_id,
                                $source,
                                $cat
                            );
                            $upload_files[] = $upload_file;
                        }
                        if ($ret_type == 0 && count($uploadFiles) == 1) {
                            if ($upload_file != null) {
                                return $this->show($upload_file);
                            } else {
                                $this->responseJson(array(
                                    'status' => 'ng',
                                    'uploadFile' => null
                                ));
                            }
                       } else {
                           $this->responseJson(array(
                               'status' => 'ok',
                               'uploadFiles' => $upload_files
                           ));
                       }  
                    } else {
                       
                        // assume the upload is file

                        $upload_file = UploadHelper::storeFile(
                            $uploadFiles,
                            $upload_file_id,
                            $fk_id,
                            $source,
                            $cat
                        );
                        if ($ret_type == 0) {
                             return $this->show($upload_file);
                        } else {
                            $this->responseJson(array(
                                'status' => 'ok',
                                'uploadFile' => $upload_file
                            ));
                        }   
                    }
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        $this->responseJson(array(
            'status' => 'error'
        ));
    }
    
    public function uploadFiles(Request $request, $fk_id, $source, $cat)
    {
        error_log('Upload file - store: '.$request->ctl_filename);
        $ctl_filename = (isset($request->ctl_filename) && !empty($request->ctl_filename)) ? $request->ctl_filename : 'image_file';
        error_log('Upload file - store: '.$request->ctl_filename);
        
        try {
            /* foreach ($request->all() as $key) {
               
            } */
            if ($request->hasFile($ctl_filename)) {
                $uploadFiles = $request->file($ctl_filename);
              

                if (!empty($uploadFiles)) {
                    if (is_array($uploadFiles)) {
                       
                        //for multifiles
                    } else {
                        
                        // assume the upload is file

                        $upload_file = UploadHelper::storeFile(
                            $uploadFiles,
                            0,
                            $fk_id,
                            $source,
                            $cat
                        );
                        $this->responseJson(array(
                            'status' => 'ok',
                            'uploadFile' => $upload_file
                        ));
                    }
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        $this->responseJson(array(
            'status' => 'error'
        ));
    }
    public function storeTiny(Request $request)
    {
        error_log('------------storeTiny-------------loi tạo file -0--------------');
        try {
            if ($request->hasFile('file')) {
                error_log('------------storeTiny-------------loi tạo file -1--------------');
                $uploadFiles = $request->file('file');
                error_log('------------storeTiny-------------loi tạo file -2--------------');
                if (!empty($uploadFiles)) {
                    if (is_array($uploadFiles)) {
                       
                        //for multifiles
                    } else {
                       
                        $upload_file = UploadHelper::storeFile(
                            $uploadFiles,
                            0,
                            0,
                            $request->upload_for,
                            $request->upload_for
                        );

                        return $this->responseJson(array(
                            'status' => 'ok',
                            'uploadFile' => $upload_file
                        ));
                    }
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            return $this->responseJson(array(
                'status' => 'ng',
                'message' => $e->getMessage()
            ));
        }
        return $this->responseJson(array(
            'status' => 'ng'
        ));
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\UploadFile  $UploadFile
     * @return \Illuminate\Http\Response
     */
    public function show(UploadFile $uploadFile)
    {
        // TODO: Make downloadName of files save!!!
        error_log('-----show--------');
        $downloadName = $uploadFile->original_name;
        $filePath = storage_path() . '/uploads/' . $uploadFile->local_path;
        error_log('------show UploadFile---------:'.$uploadFile->local_path.'-------'.$filePath);
        if ($uploadFile->local_path == '' || !file_exists($filePath)) {
            //abort(404, 'Sorry, but we could not find this file in our system, even though it is supposed to exist. Please contact our support team.');
            $disposition = ('inline');
            $filePath = storage_path() . '/uploads/default/notfound.jpg';

            return response()->download(
                $filePath,
                'notfound.jpg',
                [],
                $disposition
            );
        }
        $disposition = (
            ($uploadFile->mime_type_guess
                && ($uploadFile->mime_type_guess == $uploadFile->mime_type_client)
                && in_array($uploadFile->mime_type_guess, ['application/pdf', 'image/jpeg', 'image/png']))
            ? 'inline'
            : 'attachment');
          $disposition = ('attachment');
        return response()->download(
            $filePath,
            $downloadName,
            ['Content-Type' => $uploadFile->mime_type_client],
            $disposition
        );
        //->deleteFileAfterSend(true)
        //// mime-type nur uebernehmen, wenn client und system sich einig waren:
        //if ( $projectUpload->mime_type_guess && $projectUpload->mime_type_guess == $projectUpload->mime_type_client ) {
        //   // nur bestimmte mime-types senden wir als "inline", den rest als attachment-download:
        //   if ( in_array($projectUpload->mime_type_guess, ['application/pdf', 'image/jpeg', 'image/png']) ) {
        //      $disposition = 'inline';
        //   } else {
        //      $disposition = 'attachment';
        //   }
        //   
        //   header('Content-type:'.$projectUpload->mime_type_guess);
        //   header('Content-disposition: '.$disposition.';filename='.$downloadName);
        //} else {
        //   // TODO: It might be better to send no Content-Type at all in theses cases:
        //   // TODO: http://stackoverflow.com/questions/1176022/unknown-file-type-mime
        //   // TODO: We still send them, because the server might default to text/html otherwise
        //   header('Content-type:application/octet-stream');
        //   header('Content-disposition: attachment;filename='.$downloadName);
        //}
        //
        //
        //readfile($filePath);
        //
        //return;
    }

    public function showInline(UploadFile $uploadFile)
    {
        // TODO: Make downloadName of files save!!!
        
        error_log('-----showInline--------');
        return $this->showFile($uploadFile, 'inline');

    }
    public function downloadFile(UploadFile $uploadFile)
    {
        error_log('-----downloadFile--------');
        // TODO: Make downloadName of files save!!!
        return $this->showFile($uploadFile, 'attachment');
    }
    private function showFile($uploadFile, $disposition = 'attachment') {
        
        $downloadName = $uploadFile->original_name;
        $filePath = storage_path() . '/uploads/' . $uploadFile->local_path;
        error_log('------show UploadFile---------:'.$uploadFile->local_path.'-------'.$filePath);
        if ($uploadFile->local_path == '' || !file_exists($filePath)) {
            //abort(404, 'Sorry, but we could not find this file in our system, even though it is supposed to exist. Please contact our support team.');
            $disposition = ('inline');
            $filePath = storage_path() . '/uploads/default/notfound.jpg';
            error_log('-----disposition---0-----'.$disposition);
            return response()->download(
                $filePath,
                'notfound.jpg',
                [],
                $disposition
            );
        }
        if ($uploadFile->mime_type_guess
            && ($uploadFile->mime_type_guess == $uploadFile->mime_type_client)
            && (in_array($uploadFile->mime_type_guess, ['application/pdf', 'image/jpeg', 'image/png']))
            || strpos($uploadFile->mime_type_client, 'application/vnd') !== false
            || strpos($uploadFile->mime_type_client, 'text/plain') !== false){
                
                error_log('-----disposition---1-----'.$disposition);
                
                return response()->download(
                    $filePath,
                    $downloadName,
                    ['Content-Type' => $uploadFile->mime_type_client,
                    ],
                    $disposition
                );
        } else {
            error_log('-----disposition---2-----'.$disposition);
            return response()->download(
                $filePath,
                $downloadName,
                ['Content-Type' => 'application/octet-stream',
                ],
                $disposition
            );
        }
    }
    private function endsWith($myStr, $mySearchStrings)
    {
        foreach ($mySearchStrings as $mySearchString) {
            if (substr_compare($myStr, $mySearchString, -strlen($mySearchString)) === 0) {
                return true;
            }
        }
        return false;
    }

    private function getDefaultThumbnail(UploadFile $uploadFile)
    {
        $fileTypes = array();
        $fileTypes[] = array(
            'mime_types' => ['application/pdf'],
            'endings' => ['.pdf'],
            'image_name' => 'iconfinder_pdf_272699.png'
        );
        $fileTypes[] = array(
            'mime_types' => ['application/msword'],
            'endings' => ['.doc', '.docx'],
            'image_name' => 'iconfinder_word_272702.png'
        );
        $fileTypes[] = array(
            'mime_types' => ['application/vnd.ms-excel'],
            'endings' => ['.xls', '.xlsx'],
            'image_name' => 'iconfinder_excel_272697.png'
        );
        $fileTypes[] = array(
            'mime_types' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
            'endings' => ['.ppt', '.pptx'],
            'image_name' => 'iconfinder_powerpoint_272700.png'
        );

        foreach ($fileTypes as $fileType) {
            if (
                in_array($uploadFile->mime_type_client, $fileType['mime_types'])
                || $this->endsWith($uploadFile->local_path, $fileType['endings'])
            ) {
                return resource_path() . '/views/_svg/file_logos/' . $fileType['image_name'];
            }
        }

        // if no thing found, return a default
        return resource_path() . '/views/_svg/file_logos/iconfinder_text_272701.png';
    }

    public function showDefaultThumbnail(Request $request, UploadFile $uploadFile)
    {
        return $this->showThumbnail($request, $uploadFile, 'default');
    }

    public function showThumbnail(Request $request, UploadFile $uploadFile, $size)
    {

        if ($size == 'stiny') {
            $requestedHeight = 75;
            $requestedWidth  = 75;
            $givenSize = true;
        } else if ($size == 'tiny') {
            $requestedHeight = 100;
            $requestedWidth  = 100;
            $givenSize = true;
        } else if ($size == 'small') {
            $requestedHeight = 200;
            $requestedWidth  = 200;
            $givenSize = true;
        } else if ($size == 'default') {
            //            $givenSize = false;
            $requestedHeight = 600;
            $requestedWidth  = 600;
            $givenSize = true;
        } else {
            $requestedHeight = 200;
            $requestedWidth  = 200;
            $givenSize = true;
            //            abort(404, 'Invalid thumbnail size');
        }
        try {
            $filePath = storage_path() . '/uploads/' . $uploadFile->local_path;
            // if file does not exists, return a dummy;
            if ($uploadFile->local_path == '' || !file_exists($filePath)) {
                $img = Image::canvas(100, 100, '#dddddd');
                return $img->response('jpg', 50);
            }

            // if file is not an image return file type icon;
            if (!$uploadFile->isImage) {
                $imagePath = $this->getDefaultThumbnail($uploadFile);
                $imageType = 'png';
            } else {
                $imagePath = $filePath;
                $imageType = 'jpg';
            }

            // if file is an image try to find the thumpnail, if not find create one
            $thumbPath = $givenSize ? $filePath . '_' . $requestedHeight . 'x' . $requestedWidth . '.' . $imageType
            : $filePath . '_600.' . $imageType;

            if (file_exists($thumbPath)) {
                $img = Image::make($thumbPath);
                return $img->response($imageType, 50);
            } else {
                $img = Image::make($imagePath);
                $img->orientate();
                if ($givenSize) {
                    $img->fit($requestedWidth, $requestedHeight);
                } else {
                    // get the width and height of the image
                    $width = $img->width();
                    $height = $img->height();
                    if ($width > $height) {
                        $img->resize(600, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    } else {
                        $img->resize(null, 600, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }
                }
                $img->save($thumbPath);
                return $img->response($imageType, 50);
            }
        }catch(Exception $e){
            
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\uploadFile  $uploadFile
     * @return \Illuminate\Http\Response
     */
    public function edit(UploadFile $uploadFile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ProjectUpload  $projectUpload
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UploadFile $uploadFile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\uploadFile  $uploadFile
     * @return \Illuminate\Http\Response
     */
    public function destroy(UploadFile $uploadFile)
    {
        //
    }
    
    public function deletes(Request $request)
    {
        error_log('delete upload file');
        $upload_ids = $request->upload_ids;
        if (!isset($upload_ids)) {
            return $this->responseJson(array(
                'status' => 'ok'
            ));
        }
        if (!is_array($upload_ids)) {
            error_log('delete upload file: '.$upload_ids);
            $upload_ids = explode(',', $upload_ids);
        }
        foreach ($upload_ids as $upload_id) {
            error_log('delete upload file: '.$upload_id);
            UploadHelper::deleteFile($upload_id);
        }
        return $this->responseJson(array(
            'status' => 'ok'
        ));
    }
    public function deletesByFkId(Request $request)
    {
        $fk_id = $request->fk_id;
        if (!isset($fk_id)) {
            return;
        }
        $lstUploadFiles = UploadFile::where('fk_id', $fk_id)->get();
        foreach ($lstUploadFiles as $uploadFile) {
            UploadHelper::deleteFile($uploadFile->id);
        }
        return;
    }
}
