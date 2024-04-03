<?php

namespace App\Http\Controllers;

use App\Models\buku_dijual;
use App\Models\buku_lunas_user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
                ->withCount('buku_lunas_user')
                ->where('active_flag', 1);

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
                    $data->orderBy('buku_lunas_user_count', 'asc');
                }
            }

            // order
            if ($request->has("order")) {
                if ($request->order == "terlaris") {
                    $data->orderBy('buku_lunas_user_count', 'desc');
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
                    // count avg rating from testimoni_pembeli and dibulatkan
                    $rating = round($item->testimoni_pembeli->avg('rating'));

                    return [
                        'id' => $item->id,
                        'slug' => $item->slug,
                        'judul' => $item->judul,
                        'harga' => $item->harga,
                        'kategori' => $item->kategori->nama,
                        'cover_buku' => $item->cover_buku,
                        'pembeli' => $item->buku_lunas_user_count == 0 ? 0 : $item->buku_lunas_user_count,
                        'rating' => $rating
                    ];
                });
            }
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
    public function show(String $slug)
    {
        try {
            // get buku dijual detail by slug
            $data = buku_dijual::with('kategori', 'penulis', 'storage_buku_dijual')
                // with  'testimoni_pembeli' where active_flag is 1
                ->with(['testimoni_pembeli' => function ($query) {
                    $query->with('user')->where('active_flag', 1);
                }])
                ->where('slug', $slug)
                ->where('active_flag', 1)
                ->first();

            // get only name and make list of penulis to string and dvide by ,
            $list_penulis = $data->penulis->map(function ($item) {
                return $item->nama;
            })->implode(', ');

            // get only needed data testimoni pembeli
            $testimoni_pembeli = $data->testimoni_pembeli->map(function ($item) {
                return [
                    'nama' => $item->user->nama_lengkap,
                    'foto_profil' => $item->user->foto_profil,
                    'rating' => $item->rating,
                    'ulasan' => $item->ulasan,
                    // created date to diff human
                    'created_at' => $item->created_at->diffForHumans()
                ];
            });

            // get only needed data gallery foto and add cover_buku at first map
            $gallery_foto = $data->storage_buku_dijual->map(function ($item) {
                return [
                    'foto' => $item->nama_generate
                ];
            })->prepend([
                'foto' => $data->cover_buku
            ]);

            // if have $request->user('sanctum')->id
            if (auth('sanctum')->check()) {
                // check if buku already dibeli oleh user login
                $alreadyBuy = buku_lunas_user::where('buku_dijual_id', $data->id)
                    ->where('user_id', auth('sanctum')->user()->id)
                    ->first();

                if ($alreadyBuy) {
                    $isDibeli = true;
                } else {
                    $isDibeli = false;
                }

                // get needed data

                $data = [
                    'id' => $data->id,
                    'isbn' => $data->isbn,
                    'slug' => $data->slug,
                    'judul' => $data->judul,
                    'harga' => $data->harga,
                    'kategori' => $data->kategori->nama,
                    'deskripsi' => $data->deskripsi,
                    'tanggal_terbit' => $data->tanggal_terbit,
                    'jumlah_halaman' => $data->jumlah_halaman,
                    'bahasa' => $data->bahasa,
                    'penerbit' => $data->penerbit,
                    'list_penulis' => $list_penulis,
                    'testimoni_pembeli' => $testimoni_pembeli,
                    'gallery_foto' => $gallery_foto,
                    'isDibeli' => $isDibeli
                ];
            } else {
                $data = [
                    'id' => $data->id,
                    'isbn' => $data->isbn,
                    'slug' => $data->slug,
                    'judul' => $data->judul,
                    'harga' => $data->harga,
                    'kategori' => $data->kategori->nama,
                    'deskripsi' => $data->deskripsi,
                    'tanggal_terbit' => $data->tanggal_terbit,
                    'jumlah_halaman' => $data->jumlah_halaman,
                    'bahasa' => $data->bahasa,
                    'penerbit' => $data->penerbit,
                    'list_penulis' => $list_penulis,
                    'testimoni_pembeli' => $testimoni_pembeli,
                    'gallery_foto' => $gallery_foto,
                    'isDibeli' => false,
                ];
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'error',
                'data' => 'buku_dijual not found'
            ], 404);
        }

        // return the resource
        return response()->json([
            'success' => true,
            'message' => 'buku retrieved successfully.',
            'data' => $data
        ], 200);
    }

    // toptenterlaris
    public function bestseller()
    {
        try {
            // get the most count buku_dijual in list_transasksi_buku
            $data = buku_dijual::with('kategori', 'testimoni_pembeli')
                ->withCount('buku_lunas_user')
                ->whereHas('buku_lunas_user', function ($query) {
                    $query->orderBy('created_at', 'desc');
                })
                ->where('active_flag', '1')
                ->orderBy('buku_lunas_user_count', 'desc')
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
                    'pembeli' => $item->buku_lunas_user_count,
                    'rating' => $rating,
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
