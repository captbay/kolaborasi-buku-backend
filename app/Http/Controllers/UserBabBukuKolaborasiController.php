<?php

namespace App\Http\Controllers;

use App\Models\user_bab_buku_kolaborasi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBabBukuKolaborasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the data from the database
        try {
            // Get the data from the database
            $data = user_bab_buku_kolaborasi::with('bab_buku_kolaborasi.buku_kolaborasi.kategori')
                ->where('user_id', Auth::user()->id);

            // search
            if ($request->search) {
                $data = $data->where(function ($query) use ($request) {
                    $query->orwhereHas('bab_buku_kolaborasi', function ($query) use ($request) {
                        $query->whereHas('buku_kolaborasi', function ($query) use ($request) {
                            $query->where('judul', 'like', '%' . $request->search . '%');
                        })
                            ->orWhere('judul', 'like', '%' . $request->search . '%');
                    });
                });
            }

            $data = $data
                ->orderBy('created_at', 'desc')
                ->paginate($request->limit);

            $data = $data->through(function ($item) {
                return [
                    'id' => $item->id,
                    'status' => $item->status,
                    'datetime_deadline' => $item->datetime_deadline,
                    'no_bab' => $item->bab_buku_kolaborasi->no_bab,
                    'judul_bab' => $item->bab_buku_kolaborasi->judul,
                    'judul_buku' => $item->bab_buku_kolaborasi->buku_kolaborasi->judul,
                    'cover_buku' => $item->bab_buku_kolaborasi->buku_kolaborasi->cover_buku,
                    'kategori_buku' => $item->bab_buku_kolaborasi->buku_kolaborasi->kategori->nama,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Buku user',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * show detail data
     * @param  int  $id
     * @return \Illuminate\Http\Response
     **/
    public function show($id)
    {
        try {
            $data = user_bab_buku_kolaborasi::with('bab_buku_kolaborasi.buku_kolaborasi.kategori')
                ->find($id);

            $data = [
                'id' => $data->id,
                'bab_buku_kolaborasi_id' => $data->bab_buku_kolaborasi_id,
                'status' => $data->status,
                'note' => $data->note,
                'file_bab' => $data->file_bab,
                'datetime_deadline' => $data->datetime_deadline,
                'created_at' => Carbon::parse($data->created_at)->locale('id')
                    ->settings(['formatFunction' => 'translatedFormat'])
                    ->format('l, j F Y'),
                'no_bab' => $data->bab_buku_kolaborasi->no_bab,
                'judul_bab' => $data->bab_buku_kolaborasi->judul,
                'deskripsi_bab' => $data->bab_buku_kolaborasi->deskripsi,
                'judul_buku' => $data->bab_buku_kolaborasi->buku_kolaborasi->judul,
                'cover_buku' => $data->bab_buku_kolaborasi->buku_kolaborasi->cover_buku,
                'kategori_buku' => $data->bab_buku_kolaborasi->buku_kolaborasi->kategori->nama,
                'file_mou' => $data->bab_buku_kolaborasi->buku_kolaborasi->file_mou
            ];
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Data',
            'data' => $data
        ]);
    }
}
