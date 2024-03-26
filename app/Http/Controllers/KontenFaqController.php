<?php

namespace App\Http\Controllers;

use App\Models\konten_faq;
use Illuminate\Http\Request;

class KontenFaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get the resource
        try {
            $data = konten_faq::where('active_flag', 1)->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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
            'message' => 'konten_event retrieved successfully.',
            'data' => $data
        ], 200);
    }
}
