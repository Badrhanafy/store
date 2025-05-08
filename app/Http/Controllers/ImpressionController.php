<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Impression;
class ImpressionController extends Controller
{
   public function saveImpression(Request $req){
      $impression = Impression::create([
        'clientName'=>$req->clientName,
        'clientImpression'=>$req->clientImpression
      ]);
      return response()->json(['message' => 'impression created successfully!']);
   }
}
