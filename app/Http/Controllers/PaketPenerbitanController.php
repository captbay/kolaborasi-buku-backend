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
                    $query->with("jasa_tambahan");
                })
                ->where('waktu_mulai', '<=', Carbon::now())
                ->where('waktu_selesai', '>=', Carbon::now())
                ->orderBy('created_at', 'desc')
                ->get();

            // return needed data
            $data = $data->map(function ($item) {
                // return needed data jasa_paket_penerbitan
                $jasa_paket_penerbitan = $item->jasa_paket_penerbitan->map(function ($item) {
                    return [
                        'paket_penerbitan_id' => $item->id,
                        'nama' => $item->jasa_tambahan->nama
                    ];
                });

                return [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'harga' => $item->harga,
                    'deskripsi' => $item->deskripsi,
                    'jasa_paket_penerbitan' => $jasa_paket_penerbitan
                ];
            });
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
            'message' => 'paket retrieved successfully.',
            'data' => $data
        ], 200);
    }

    /**
     * Show detail by id
     **/
    public function show($id)
    {
        // get the resource
        try {
            $data = paket_penerbitan::with('jasa_paket_penerbitan.jasa_tambahan')->find($id);

            // return needed data jasa_paket_penerbitan
            $jasa_paket_penerbitan = $data->jasa_paket_penerbitan->map(function ($data) {
                return [
                    'paket_penerbitan_id' => $data->id,
                    'nama' => $data->jasa_tambahan->nama
                ];
            });

            // return needed data
            $data = [
                'id' => $data->id,
                'nama' => $data->nama,
                'harga' => $data->harga,
                'deskripsi' => $data->deskripsi,
                'jasa_paket_penerbitan' => $jasa_paket_penerbitan
            ];

            // return needed data
            return response()->json([
                'success' => true,
                'message' => 'detail paket',
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
