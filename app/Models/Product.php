<?php

namespace App\Models;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'price', 'qte', 'image'];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
     public function images()
{
    return $this->hasMany(ProductImage::class);
}
}
