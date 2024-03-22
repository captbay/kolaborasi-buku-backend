<?php

namespace App\Http\Controllers;

use App\Models\testimoni_pembeli;
use Illuminate\Http\Request;

class TestimoniPembeliController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // get the most count buku_dijual in list_transasksi_buku
            $data = testimoni_pembeli::with('user')
                ->where('active_flag', '1')
                ->where('ulasan', '!=', null)
                ->orderBy('created_at', 'desc');

            if ($request->has("limit")) {
                $data->limit($request->limit);
            }
            $data = $data->get();

            // filter only needed data
            $data = $data->map(function ($item) {
                return [
                    'ulasan' => $item->ulasan,
                    'rating' => $item->rating,
                    'nama_lengkap' => $item->user->nama_lengkap,
                    'foto_profil' => $item->user->foto_profil,
                ];
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'error',
                'data' => 'testimoni not found'
            ], 404);
        }

        // return the resource
        return response()->json([
            'success' => true,
            'message' => 'testimoni retrieved successfully.',
            'data' => $data
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function testimoniBukuDijual(Request $req)
    {
        try {
            // get testimoni_pembeli where buku_dijual slug is $slug
            $data = testimoni_pembeli::with('user')
                ->where('active_flag', '1')
                ->where('ulasan', '!=', null)
                ->whereHas('buku_dijual', function ($query) use ($req) {
                    $query->where('slug', $req->slug);
                })
                ->orderBy('created_at', 'desc');

            $data = $data->paginate(10);

            // filter only needed data
            $data = $data->through(function ($item) {
                return [
                    'ulasan' => $item->ulasan,
                    'rating' => $item->rating,
                    'nama_lengkap' => $item->user->nama_lengkap,
                    'foto_profil' => $item->user->foto_profil,
                ];
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'error',
                'data' => 'testimoni not found'
            ], 404);
        }

        // return the resource
        return response()->json([
            'success' => true,
            'message' => 'testimoni retrieved successfully.',
            'data' => $data
        ], 200);
    }
}
