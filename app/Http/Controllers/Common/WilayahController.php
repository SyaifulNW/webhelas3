<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class WilayahController extends Controller
{
   public function getProvinces()
    {
        $response = Http::get('https://emsifa.github.io/api-wilayah-indonesia/api/provinces.json');
        return response()->json($response->json());
    }

    public function getCities($provinceId)
    {
        $response = Http::get("https://emsifa.github.io/api-wilayah-indonesia/api/regencies/{$provinceId}.json");
        return response()->json($response->json());
    }
}
