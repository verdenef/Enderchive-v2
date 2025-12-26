<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::orderBy('name')->get();

        return response()->json([
            'data' => $stores,
            'message' => 'Stores retrieved successfully'
        ], 200);
    }
}

