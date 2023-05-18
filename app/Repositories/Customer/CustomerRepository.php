<?php

namespace App\Repositories\Customer;

use App\Models\Customer\Customer;
use InfyOm\Generator\Common\BaseRepository;

class CustomerRepository extends BaseRepository
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
        return Customer::class;
    }
}
