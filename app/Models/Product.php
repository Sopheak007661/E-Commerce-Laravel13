<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    protected $fillable = [
            'name',
            'slug',
            'short_description',
            'description',
            'information',
            'regular_price',
            'sale_price',
            'SKU',
            'stock_status',
            'quantity',
            'featured',
            'status',
            'category_id',
            'brand_id',
            'image',
            'images',
        ];

    public function brand(){
        return $this->belongsTo(Brand::class);
    }    

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
