<?php

namespace App\Http\Controllers;

use App\Models\paket_penerbitan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaketPenerbitanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get the resource
        try {
            $data = paket_penerbitan::select('id', 'nama', 'harga', 'deskripsi')
                ->with('jasa_paket_penerbitan', function ($query) {
                    $query->select('paket_penerbitan_id', 'nama');
                })
                ->where('waktu_mulai', '<=', Carbon::now())
                ->where('waktu_selesai', '>=', Carbon::now())
                ->orderBy('created_at', 'desc')
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
            'message' => 'paket retrieved successfully.',
            'data' => $data
        ], 200);
    }
}
