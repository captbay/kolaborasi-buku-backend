<?php

namespace App\Http\Controllers;

use App\Models\buku_dijual;
use Illuminate\Http\Request;

class BukuDijualController extends Controller
{
    /**
     * TODO: implement filter, search, dll ini belum selesai
     */
    public function index(Request $request)
    {
        try {
            // get the most count buku_dijual in list_transasksi_buku
            $data = buku_dijual::with('kategori', 'testimoni_pembeli', 'penulis')
                ->withCount('list_transaksi_buku')
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
                    $data->where(function ($query) use ($request) {
                        $query->WhereHas('penulis', function ($query) use ($request) {
                            $query->where('nama', 'like', '%' . $request->search . '%');
                        })
                            ->orWhere('judul', 'like', '%' . $request->search . '%');
                    });

                    // if kategori is also filtered, sync the search with kategori filter
                    if ($request->has("kategori") && $request->kategori != "semua") {
                        $data->whereHas('kategori', function ($query) use ($request) {
                            $query->where('slug', $request->kategori);
                        });
                    }
                }
            }

            // filter by range harga
            if ($request->has("hargaMin") && $request->has("hargaMax")) {
                if ($request->hargaMin > $request->hargaMax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'hargaMin must be less than hargaMax',
                        'data' => $data->paginate($request->limit)
                    ], 400);
                } else if ($request->hargaMin != 0 || $request->hargaMax != 0 || $request->hargaMin != null || $request->hargaMax != null) {
                    $data->whereBetween('harga', [$request->hargaMin, $request->hargaMax]);
                }
            }

            // ads
            if ($request->has("bookAds")) {
                if ($request->bookAds == "true") {
                    $data->orderBy('list_transaksi_buku_count', 'asc');
                }
            }

            // order
            if ($request->has("order")) {
                if ($request->order == "terlaris") {
                    $data->orderBy('list_transaksi_buku_count', 'desc');
                } else if ($request->order == "terbaru") {
                    $data->orderBy('created_at', 'desc');
                } else if ($request->order == "termurah") {
                    $data->orderBy('harga', 'asc');
                } else if ($request->order == "termahal") {
                    $data->orderBy('harga', 'desc');
                }
            } else {
                $data->orderBy('created_at', 'desc');
            }

            $data = $data->paginate($request->limit);

            if ($request->bookAds == "true") {
                $data = $data->through(function ($item) use ($request) {
                    return [
                        'id' => $item->id,
                        'slug' => $item->slug,
                        'judul' => $item->judul,
                        'kategori' => $item->kategori->nama,
                        'deskripsi' => $item->deskripsi,
                        'cover_buku' => $item->cover_buku,
                    ];
                });
            } else {
                // filter only needed data
                $data = $data->through(function ($item) {
                    // count rating from testimoni_pembeli
                    $rating = $item->testimoni_pembeli->avg('rating');

                    return [
                        'id' => $item->id,
                        'slug' => $item->slug,
                        'judul' => $item->judul,
                        'harga' => $item->harga,
                        'kategori' => $item->kategori->nama,
                        'cover_buku' => $item->cover_buku,
                        'pembeli' => $item->list_transaksi_buku_count == 0 ? 0 : $item->list_transaksi_buku_count,
                        'rating' => $rating
                    ];
                });
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }

        if (!$data) {
            return response()->json([
                'message' => 'error',
                'data' => 'buku_dijual not found'
            ], 404);
        }

        // return the resource
        return response()->json([
            'success' => true,
            'message' => 'buku all retrieved successfully.',
            'data' => $data
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(buku_dijual $buku_dijual)
    {
        //
    }

    // toptenterlaris
    public function bestseller()
    {
        try {
            // get the most count buku_dijual in list_transasksi_buku
            $data = buku_dijual::with('kategori', 'testimoni_pembeli')
                ->withCount('list_transaksi_buku')
                ->whereHas('list_transaksi_buku', function ($query) {
                    $query->orderBy('created_at', 'desc');
                })
                ->where('active_flag', '1')
                ->orderBy('list_transaksi_buku_count', 'desc')
                ->limit(10)
                ->get();

            // filter only needed data
            $data = $data->map(function ($item) {
                // count avarage rating of all tesimoni_pembeli data
                $rating = $item->testimoni_pembeli->avg('rating');

                return [
                    'id' => $item->id,
                    'slug' => $item->slug,
                    'judul' => $item->judul,
                    'harga' => $item->harga,
                    'kategori' => $item->kategori->nama,
                    'cover_buku' => $item->cover_buku,
                    'pembeli' => $item->list_transaksi_buku_count,
                    'rating' => $rating,
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
                'data' => 'buku_dijual not found'
            ], 404);
        }

        // return the resource
        return response()->json([
            'success' => true,
            'message' => 'buku best seller retrieved successfully.',
            'data' => $data
        ], 200);
    }
}
