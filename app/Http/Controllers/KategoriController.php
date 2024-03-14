<?php

namespace App\Http\Controllers;

use App\Models\kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get the resource
        try {
            $data = kategori::select('id', 'nama', 'slug')
                ->get();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }

        if ($data->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'data is empty',
                'data' => []
            ], 200);
        }

        // return the resource
        return response()->json([
            'success' => true,
            'message' => 'data retrieved successfully.',
            'data' => $data
        ], 200);
    }
}
