<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class SoVanBan extends Model
{
    use \App\Traits\EditorsTrait;
    use SoftDeletes;
    
    protected $fillable = [
        'id',
        'FileCode',
        'OrganId',
        'FileCatalog',
        'FileNotation',
        'Title',
        'Maintenance',
        'Rights',
        'ClosedDate',
        'CloserId',
        'Language',
        'StartDate',
        'EndDate',
        'DocTotal',
        'PageTotal',
        'UseStatus',
        'FileType',
        'Description'
     ];
     protected $casts = [
        'OrganId' => 'integer',
        'FileCatalog' => 'integer',
        'CreatorId' => 'integer',
        'CloserId' => 'integer',
        'DocTotal' => 'integer',
        'PageTotal' => 'integer',
        'UseStatus' => 'integer',
        'FileType' => 'integer'
     ];
     
    protected $table = 'sovanbans';
    protected $primaryKey = 'id'; // or null
    public $timestamps = true;
    public $incrementing = true;
    protected $appends = ['doctypename'];
    

    // In Laravel 6.0+ make sure to also set $keyType
    //protected $keyType = 'biginteger';
    protected function serializeDate(\DateTimeInterface $date) {
        return $date->format('c');
    }
    public function Organization() {
        return $this->belongsTo('\App\Models\Csdt', 'OrganId');
    }
    
    public function creator() {
        return $this->belongsTo('\App\Models\user', 'created_by');
    }
    public function DsVanBan() {
        return $this->belongsToMany('\App\Models\VanBan', 'sovanban_vanban', 'sovanban_id','vanban_id')
        ->withPivot('created_at', 'created_by', 'updated_at', 'updated_by', 'vanban_id', 'sovanban_id', 'trangthai');
    }
    
    public function getDocTypeNameAttribute () {
        return $this->FileType == 1 ? 'Văn bản phát hành' : ($this->FileType == 2 ? 'Văn bản đến' : '(Không xác định)');
    }
    
}