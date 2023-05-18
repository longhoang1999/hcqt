<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Job extends Model
{

    public $table = 'jobs';
    


    public $fillable = [
        'jb_sc_id_index',
        'conpany',
        'nr',
        'padmin',
        'dateopenm',
        'dateopen',
        'description',
        'jpaytype',
        'jstatus'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'jb_sc_id_index' => 'integer',
        'conpany' => 'string',
        'nr' => 'string',
        'padmin' => 'string',
        'dateopen' => 'string',
        'description' => 'string',
        'jpaytype' => 'integer',
        'jstatus' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'jb_sc_id_index' => 'required',
        'dateopenm' => 'required',
        'jpaytype' => 'required',
        'jstatus' => 'required'
    ];
}
