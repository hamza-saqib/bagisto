<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetsModel extends Model
{
    use HasFactory;

    protected $table = 'assets';
    protected $fillable = [
        'name',
        'color',
        'image',
        'image_link',
        'product_id',
        'parent_id'
    ];
}
