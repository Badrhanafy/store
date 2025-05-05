<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
class ProductController extends Controller
{
    public function postsListe(){
        return Product::all();
    }
    public function store( Request $req){
           $validated = $req->validate([
             'title'=>'required|min:8|max:50',
             'qte'=>'required|integer',
             
             'price'=>'required|integer',
           ]);
           //dd($validated);
           $product = Product::create($validated);
           return response()->json($product,200);
    }
}
