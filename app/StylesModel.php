<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StylesModel extends Model
{
    use HasFactory;

    protected $table = 'styles';
    protected $fillable = [
        'name',
        'image',
        'image_link',
        'product_id',
        'asset_id',
        "fabric_id"
    ];
}
