<?php

namespace App\Repositories;

use App\Models\Job;
use InfyOm\Generator\Common\BaseRepository;

class JobRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Job::class;
    }
}
