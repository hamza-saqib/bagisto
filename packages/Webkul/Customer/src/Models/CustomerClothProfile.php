<?php

namespace Webkul\Customer\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Customer\Contracts\CustomerClothProfile as CustomerClothProfileContract;

class CustomerClothProfile extends Model implements CustomerClothProfileContract
{
    protected $table = 'customer_cloth_profile';

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'profile_data' => 'array',
    ];

    protected $fillable = [
        'product_id',
        'customer_id',
        'profile_data',
    ];

    
}