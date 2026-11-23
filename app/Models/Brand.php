<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $table = "brands";
    protected $guarded = ['id'];
    protected $fillable = ['name', 'slug', 'image', 'status'];

    //Product =============================
     public function products(){
        return $this->hasMany(Product::class);
    }
}
