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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // get user login
            $user = Auth::user();

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
     * Display the specified resource.
     */
    public function show(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        //
    }
}
