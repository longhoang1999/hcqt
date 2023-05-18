<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UploadFile extends Model
{
    //
    use \App\Traits\EditorsTrait;

    protected $table = 'upload_files';

    protected $fillable = [
        'id',
        'fk_id',
        'slug',
        'original_name',
        'client_name',
        'local_path',
        'source',
        'category',
        'mime_type_guess',
        'mime_type_client',
        'size',
        'ext'
    ];

    protected $casts = [];

    protected $hidden = [
        'local_path',
        'source'
    ];

    protected $appends = ['url', 'urlinline', 'urldownload', 'thumbnail'];
    public static function boot()
    {
        parent::boot();

        static::creating(function ($node) {
            if (empty($node->slug)) {
                $slug = null;
                do {
                    $slug = UploadFile::randomSlug();
                } while (self::slugExists($slug));

                $node->slug = $slug;
            }
        });
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('c');
    }

    public static function slugExists($slug)
    {
        $x = DB::table('upload_files')->select('slug')->where('slug', '=', $slug)->first();

        return $x;
    }

    //public static function getUploadDirectory() {
    //   $uploadStoragePath = storage_path().'/uploads/';
    //}

    public static function randomSlug($length = 6)
    {
        return \App\Utils\Slug::random($length);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getIsImageAttribute()
    {
        return ($this->mime_type_guess
            && ($this->mime_type_guess == $this->mime_type_client)
            && in_array($this->mime_type_guess, array('image/jpeg', 'image/png', 'image/gif')));
    }

    public function getThumbnailAttribute()
    {
        return $this->isImage ? $this->url . '/thumb' : null;
    }
    public function getUrlAttribute()
    {
        try {
            $url_path = route('upload-files.show', $this);            
            return self::convertRootHttps($url_path);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }
    public function getUrlInlineAttribute()
    {
        try {
            
            $url_path = route('upload-files.showinline', $this);      
            //error_log("getUrlInlineAttribute: ".$url_path )     ;
            return self::convertRootHttps($url_path);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }
    public function getUrlDownloadAttribute()
    {
        try {
            $url_path = route('upload-files.download_file', $this);      
            return self::convertRootHttps($url_path);
        } catch (Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }
    public function getDisplayNameAttribute()
    {
        return $this->client_name ? $this->client_name : $this->original_name;
    }

    public function getCreateUserNameAttribute()
    {
        return $this->createUser->displayName;
    }
    public function getUpdateUserNameAttribute()
    {
        return $this->updateUser->displayName;
    }

    public function toArrayForInspection()
    {

        return array(
            'id' => $this->id,
            'original_name' => $this->original_name,
            'mime' => $this->mime_type_guess,
            'url' => $this->url,
            'slug' => $this->slug,
            'thumbnail' => $this->thumbnail
        );
    }

    public function toArrayForUserUploads()
    {
        $arr = $this->toArray();
        $arr['display_name'] = $this->displayName;
        $arr['create_user_name'] = $this->create_user_name;
        $arr['update_user_name'] = $this->update_user_name;

        return $arr;
    }
    private static function convertRootHttps($root_path) {
        $app_url = config('app.url');
        if (strpos($app_url, 'https') !== false) {
            return str_replace('http:', 'https:', $root_path);
        }

        //error_log('convertRootHttps:'.$root_path);
        return $root_path;
    }
}
