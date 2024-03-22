<?php

namespace App\Http\Controllers;

use App\Models\konten_event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KontenEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get the resource
        try {
            $konten_event = konten_event::where('active_flag', 1)
                ->where('waktu_mulai', '<=', Carbon::now())
                ->where('waktu_selesai', '>=', Carbon::now())
                ->where('tipe', 'IMAGE')
                ->get();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }

        if ($konten_event->isEmpty()) {
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
            'data' => $konten_event
        ], 200);
    }
}
