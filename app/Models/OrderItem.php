<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'product_id', 'quantity', 'price','size',    // Make sure this is included
    'color',   // Make sure this is included
];

   /*  public function order()
    {
        return $this->belongsTo(Order::class);
    } */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
