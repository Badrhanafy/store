<?php

namespace App\Models;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'price','sizes','colors', 'qte', 'image', 'category'];
    protected $casts = [
    'sizes' => 'array',
    'colors' => 'array',
];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
     public function images()
{
    return $this->hasMany(ProductImage::class);
}
public function Impressions(){
    return $this->hasMany(Impression::class);
}
}
