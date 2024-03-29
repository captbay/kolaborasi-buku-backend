<?php

namespace App\Http\Controllers;

use App\Models\keranjang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // get data by user login
        try {
            $user = Auth::user();
            $keranjang = keranjang::with('buku_dijual.kategori')
                ->where('user_id', $user->id)
                ->get();

            // return needed keranjang data
            $keranjang = $keranjang->map(function ($item) {
                return [
                    'keranjang_id' => $item->id,
                    'buku_dijual_id' => $item->buku_dijual_id,
                    'judul' => $item->buku_dijual->judul,
                    'harga' => $item->buku_dijual->harga,
                    'kategori' => $item->buku_dijual->kategori->nama,
                    'cover_buku' => $item->buku_dijual->cover_buku,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'List keranjang',
                'data' => $keranjang,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // get user login
            $user = Auth::user();


            // find if already in keranjang
            $keranjang = keranjang::where('user_id', $user->id)
                ->where('buku_dijual_id', $request->buku_dijual_id)
                ->first();

            if ($keranjang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku ' . $keranjang->buku_dijual->judul . ' sudah ada di keranjang',
                ], 500);
            }

            // make keranjang
            $keranjang = keranjang::create([
                'user_id' => $user->id,
                'buku_dijual_id' => $request->buku_dijual_id,
            ]);

            // get nama buku
            $buku_nama = $keranjang->buku_dijual->judul;

            if ($keranjang) {
                return response()->json([
                    'success' => true,
                    'message' => 'Buku ' . $buku_nama . ' berhasil dimasukkan ke keranjang',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku gagal dimasukkan ke keranjang',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // delete the resource by keranjang id
        try {
            $keranjang = keranjang::find($id);

            if ($keranjang) {
                $keranjang->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Buku ' . $keranjang->buku_dijual->judul . ' berhasil dihapus dari keranjang',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku gagal dihapus dari keranjang',
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
