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
        'created_at' => 'datetime:Y-m-d',
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
/**
     * Scope for new arrivals (products added in the last 7 days)
     */
    public function scopeNewArrivals($query)
    {
        return $query->where('created_at', '>=', now()->subDays(7))
                    ->orderBy('created_at', 'desc')
                    ->take(12);
    }


    /**
     * Get the first image URL
     */
   /*  public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image);
    } */
}
