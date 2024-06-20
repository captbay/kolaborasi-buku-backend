<?php

namespace App\Http\Controllers;

use App\Models\buku_kolaborasi;
use App\Models\transaksi_kolaborasi_buku;
use App\Models\user_bab_buku_kolaborasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
                ->where('dijual', 0);

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
                //     $data->orderBy('buku_lunas_user_count', 'desc');
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
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        if (!$data) {
            return response()->json([
                'success' => false,
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
    public function show(String $slug)
    {
        try {
            $data = buku_kolaborasi::with('kategori')
                ->with(['bab_buku_kolaborasi' => function ($query) {
                    $query->with(['user_bab_buku_kolaborasi'
                    => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    }])
                        ->with(['transaksi_kolaborasi_buku' => function ($query) {
                            $query->orderBy('created_at', 'desc');
                        }])
                        ->where('active_flag', 1)
                        ->orderBy('no_bab', 'asc');
                }])
                ->where('slug', $slug)
                ->where('active_flag', 1)
                ->where('dijual', 0)
                ->first();

            // set temp count for timeline
            $count_kontributor = 0;

            // bab buku
            $bab_buku = $data->bab_buku_kolaborasi->map(function ($item)
            use (
                &$count_kontributor,
            ) {
                // check if user_bab_buku_kolaborasi is exist
                if ($item->user_bab_buku_kolaborasi->first()) {
                    // compere datetime_deadline to get is_terjual true or false
                    if (
                        $item->user_bab_buku_kolaborasi->first()->datetime_deadline > Carbon::now()
                        || $item->user_bab_buku_kolaborasi->first()->status != "FAILED"
                    ) {
                        $terjual = true;
                        $count_kontributor++;
                    } else {
                        $terjual = false;
                    }
                } else {
                    if ($item->transaksi_kolaborasi_buku->first()) {
                        if (
                            $item->transaksi_kolaborasi_buku->first()->datetime_deadline > Carbon::now()
                            || $item->transaksi_kolaborasi_buku->first()->status != "FAILED"
                        ) {
                            $terjual = true;
                            $count_kontributor++;
                        } else {
                            $terjual = false;
                        }
                    } else {
                        $terjual = false;
                    }
                }

                if (auth('sanctum')->check()) {
                    $alreadyBuy = user_bab_buku_kolaborasi::where('bab_buku_kolaborasi_id', $item->id)
                        ->where('user_id', auth('sanctum')->user()->id)
                        ->orderBy('created_at', 'desc')
                        ->first();

                    $alreadyTransaksi = transaksi_kolaborasi_buku::where('bab_buku_kolaborasi_id', $item->id)
                        ->where('user_id', auth('sanctum')->user()->id)
                        ->where('status', '!=', 'FAILED')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($alreadyBuy?->status == "FAILED" && $alreadyTransaksi?->status == "DONE") {
                        $isDibeli = false;
                        $isTransaksi = false;
                    } else {
                        if ($alreadyBuy) {
                            $isDibeli = true;
                        } else {
                            $isDibeli = false;
                        }

                        if ($alreadyTransaksi) {
                            $isTransaksi = true;
                        } else {
                            $isTransaksi = false;
                        }
                    }

                    return [
                        'id' => $item->id,
                        'no_bab' => $item->no_bab,
                        'judul' => $item->judul,
                        'harga' => $item->harga,
                        'durasi_pembuatan' => $item->durasi_pembuatan,
                        'deskripsi' => $item->deskripsi,
                        'is_terjual' => $terjual,
                        'isDibeli' => $isDibeli,
                        'isTransaksi' => $isTransaksi,
                    ];
                } else {
                    return [
                        'id' => $item->id,
                        'no_bab' => $item->no_bab,
                        'judul' => $item->judul,
                        'harga' => $item->harga,
                        'durasi_pembuatan' => $item->durasi_pembuatan,
                        'deskripsi' => $item->deskripsi,
                        'is_terjual' => $terjual,
                        'isDibeli' => false,
                        'isTransaksi' => false,
                    ];
                }
            });

            // set status of kolaborasi
            if ($count_kontributor >= $data->jumlah_bab) {
                $status_kolaborasi = "closed";
            } else {
                $status_kolaborasi = "open";
            }

            // filter only needed data
            $data = [
                'id' => $data->id,
                'slug' => $data->slug,
                'judul' => $data->judul,
                'deskripsi' => $data->deskripsi,
                'kategori' => $data->kategori->nama,
                'cover_buku' => $data->cover_buku,
                'jumlah_bab' => $data->jumlah_bab,
                'status_kolaborasi' => $status_kolaborasi,
                'bab' => $bab_buku,
                // 'timeline_kolaborasi' => $timeline_kolaborasi
            ];
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'error',
                'data' => 'kolaborasi not found'
            ], 404);
        }

        // return the resource
        return response()->json([
            'success' => true,
            'message' => 'kolaborasi retrieved successfully.',
            'data' => $data
        ], 200);
    }

    // downloadFileHakCipta
    public function downloadFileHakCipta($id)
    {
        try {
            $data = buku_kolaborasi::find($id);

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku Kolaborasi not found',
                ], 404);
            }

            // Path to the PDF file
            $path = Storage::disk('public')->path($data->file_hak_cipta);

            // Check if the file exists
            if (!Storage::disk('public')->exists($data->file_hak_cipta)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File buku tidak ditemukan'
                ], 404);
            }

            return response()->download($path, $data->judul . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
