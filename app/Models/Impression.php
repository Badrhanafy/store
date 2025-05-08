<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Impression extends Model
{
    use HasFactory;
    protected $fillable = ['clientImpression','clientName','rating'];
    public function product(){
        return $this->belongsTo(Product::class);
    }
}
