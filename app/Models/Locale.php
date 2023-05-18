<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Locale extends Model
{
    use \App\Traits\EditorsTrait;
    
    protected $table = 'locales';
    protected $fillable = [
        'id',
        'name',
        'native_name',
        'code',
        'is_system_languag',
     ];
     
     protected $casts = [
        'is_system_language' => 'boolean',
    ];

    public $timestamps = true;

    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }

    public static function selectOptions() {
        $locales = self::where('is_system_language', 1)
        ->orderBy('native_name', 'ASC')
        ->get();
        
        return $locales->map(function($l) {
            return [
                'value' => $l->id,
                'label' => $l->native_name.' ('.$l->name.')',
                'code' => $l->code,
            ];
        });
    }
}
