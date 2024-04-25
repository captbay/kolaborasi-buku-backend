<?php

namespace App\Http\Controllers;

use App\Models\jasa_tambahan;
use App\Models\paket_penerbitan;
use Illuminate\Http\Request;

class JasaTambahanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        try {
            $data = paket_penerbitan::with('jasa_paket_penerbitan.jasa_tambahan')
                ->orderBy('created_at', 'desc')
                ->find($id);

            $arrayNameJasaTambahan = $data->jasa_paket_penerbitan->map(function ($data) {
                return [
                    $data->jasa_tambahan->nama
                ];
            });

            $data = jasa_tambahan::select('id', 'nama', 'harga')->whereNotIn('nama', $arrayNameJasaTambahan)->get();

            return response()->json([
                'success' => true,
                'message' => 'semua data jasa tamabahan',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}