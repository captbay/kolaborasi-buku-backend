<?php

namespace App\Http\Controllers;

use App\Models\buku_kolaborasi;
use Illuminate\Http\Request;

class BukuKolaborasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // get the most count buku_dijual in list_transasksi_buku
            $data = buku_kolaborasi::with('kategori')
                ->where('active_flag', '1')
                ->orderBy('created_at', 'desc');

            if ($request->has("limit")) {
                $data->limit($request->limit);
            }
            $data = $data->get();

            // filter only needed data
            $data = $data->map(function ($item) {
                return [
                    'id' => $item->id,
                    'slug' => $item->slug,
                    'judul' => $item->judul,
                    'kategori' => $item->kategori->nama,
                    'cover_buku' => $item->cover_buku,
                    'jumlah_bab' => $item->jumlah_bab,
                ];
            });
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }

        if (!$data) {
            return response()->json([
                'message' => 'error',
                'data' => 'kolaborasi not found'
            ], 404);
        }

        // return the resource
        return response()->json([
            'success' => true,
            'message' => 'kolaborasi all retrieved successfully.',
            'data' => $data
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(buku_kolaborasi $buku_kolaborasi)
    {
        //
    }
}
