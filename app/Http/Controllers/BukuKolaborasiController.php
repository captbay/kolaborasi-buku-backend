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
                ->where('active_flag', '1');

            // filter by kategori
            if ($request->has("kategori")) {
                if ($request->kategori != "semua") {
                    $data->whereHas('kategori', function ($query) use ($request) {
                        $query->where('slug', $request->kategori);
                    });
                }
            }

            // search
            if ($request->has("search")) {
                // search by penulis nama, and judul buku
                if ($request->search != "") {
                    $data->where('judul', 'like', '%' . $request->search . '%');

                    // if kategori is also filtered, sync the search with kategori filter
                    if ($request->has("kategori") && $request->kategori != "semua") {
                        $data->whereHas('kategori', function ($query) use ($request) {
                            $query->where('slug', $request->kategori);
                        });
                    }
                }
            }

            // order
            if ($request->has("order")) {
                if ($request->order == "terbaru") {
                    $data->orderBy('created_at', 'desc');
                }
                // else if ($request->order == "terlaris") {
                //     $data->orderBy('list_transaksi_buku_count', 'desc');
                // } else  if ($request->order == "termurah") {
                //     $data->orderBy('harga', 'asc');
                // } else if ($request->order == "termahal") {
                //     $data->orderBy('harga', 'desc');
                // }
            } else {
                $data->orderBy('created_at', 'desc');
            }

            $data = $data->paginate($request->limit);

            // filter only needed data
            $data = $data->through(function ($item) {
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
