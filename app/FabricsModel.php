<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FabricsModel extends Model
{
    use HasFactory;

    protected $table = 'fabric';
    protected $fillable = [
        'name',
        'color',
        'image',
        'image_link',
        'product_id'
    ];
}
