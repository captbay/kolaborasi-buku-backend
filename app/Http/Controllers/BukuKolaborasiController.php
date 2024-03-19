<?php

namespace App\Http\Controllers;

use App\Models\buku_kolaborasi;
use Carbon\Carbon;
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
    public function show(Request $req)
    {
        try {
            $data = buku_kolaborasi::with('kategori')
                ->with(['bab_buku_kolaborasi' => function ($query) {
                    $query->with(['user_bab_buku_kolaborasi'
                    => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    }])
                        ->where('active_flag', 1)
                        ->orderBy('created_at', 'desc');
                }])
                ->where('slug', $req->slug)
                ->where('active_flag', 1)
                ->where('dijual', 0)
                ->first();

            // set temp count for timeline
            $count_kontributor = 0;
            $count_upload = 0;
            $count_editing = 0;
            $count_selesai = 0;

            // bab buku
            $bab_buku = $data->bab_buku_kolaborasi->map(function ($item)
            use (
                &$count_kontributor,
                &$count_upload,
                &$count_editing,
                &$count_selesai,
            ) {
                // check if user_bab_buku_kolaborasi is exist
                if ($item->user_bab_buku_kolaborasi->first()) {
                    // compere datetime_deadline to get is_terjual true or false
                    if ($item->user_bab_buku_kolaborasi->first()->datetime_deadline > Carbon::now()) {
                        $terjual = true;
                        $count_kontributor++;
                    } else {
                        $terjual = false;
                    }

                    // check if status is UPLOADED
                    if ($item->user_bab_buku_kolaborasi->first()->status == "UPLOADED") {
                        $count_upload++;
                    }

                    // check if status is REVISI
                    if ($item->user_bab_buku_kolaborasi->first()->status == "REVISI") {
                        $count_editing++;
                    }

                    // check if status is DONE
                    if ($item->user_bab_buku_kolaborasi->first()->status == "DONE") {
                        $count_selesai++;
                    }
                } else {
                    $terjual = false;
                }

                return [
                    'id' => $item->id,
                    'no_bab' => $item->no_bab,
                    'judul' => $item->judul,
                    'harga' => $item->harga,
                    'durasi_pembuatan' => $item->durasi_pembuatan,
                    'deskripsi' => $item->deskripsi,
                    'is_terjual' => $terjual,
                ];
            });

            // set status of kolaborasi
            if ($count_kontributor >= $data->jumlah_bab) {
                $status_kolaborasi = "closed";
            } else {
                $status_kolaborasi = "open";
            }

            // set data for timeline kolaborasi
            // Kontributor
            // Upload Naskah
            // Editing Oleh Editor
            // Naskah Selesai
            // Input ISBN
            // Buku Publish
            $timeline_kolaborasi = [
                [
                    'id' => 1,
                    'judul' => 'Kontributor',
                    'count' => $count_kontributor . '/' . $data->jumlah_bab,
                    'status' => $count_kontributor >= $data->jumlah_bab ? 'selesai' : 'menunggu'
                ],
                [
                    'id' => 2,
                    'judul' => 'Upload Naskah',
                    'count' => $count_upload . '/' . $data->jumlah_bab,
                    'status' => $count_upload >= $data->jumlah_bab ? 'selesai' : 'menunggu'
                ],
                [
                    'id' => 3,
                    'judul' => 'Editing Oleh Editor',
                    'count' => $count_editing . '/' . $data->jumlah_bab,
                    'status' => $count_editing >= $data->jumlah_bab ? 'selesai' : 'menunggu'
                ],
                [
                    'id' => 4,
                    'judul' => 'Naskah Selesai',
                    'count' => $count_selesai . '/' . $data->jumlah_bab,
                    'status' => $count_selesai >= $data->jumlah_bab ? 'selesai' : 'menunggu'
                ],
                [
                    'id' => 5,
                    'judul' => 'Input ISBN',
                    'status' => $count_selesai == $data->jumlah_bab ? 'proses' : 'menunggu'
                ],
                [
                    'id' => 6,
                    'judul' => 'Buku Publish',
                    'status' => $data->dijual == 1 ? 'selesai' : 'menunggu'
                ],
            ];

            // filter only needed data
            $data = [
                'slug' => $data->slug,
                'judul' => $data->judul,
                'deskripsi' => $data->deskripsi,
                'kategori' => $data->kategori->nama,
                'cover_buku' => $data->cover_buku,
                'jumlah_bab' => $data->jumlah_bab,
                'status_kolaborasi' => $status_kolaborasi,
                'bab' => $bab_buku,
                'timeline_kolaborasi' => $timeline_kolaborasi
            ];
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
            'message' => 'kolaborasi retrieved successfully.',
            'data' => $data
        ], 200);
    }
}
