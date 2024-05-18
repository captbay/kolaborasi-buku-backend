<?php

namespace App\Http\Controllers;

use App\Models\buku_dijual;
use App\Models\buku_lunas_user;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BukuLunasUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the data from the database
        try {
            $data = buku_lunas_user::with('buku_dijual.kategori')
                ->where('user_id', Auth::user()->id);

            // search
            if ($request->search) {
                $data = $data->whereHas('buku_dijual', function ($query) use ($request) {
                    $query->where('judul', 'like', '%' . $request->search . '%');
                });
            }

            $data = $data
                ->orderBy('created_at', 'desc')
                ->paginate($request->limit);

            // Return needed data
            $data = $data->through(function ($item) {
                return [
                    'buku_dijual_id' => $item->buku_dijual->id,
                    'slug' => $item->buku_dijual->slug,
                    'cover_buku' => $item->buku_dijual->cover_buku,
                    'judul' => $item->buku_dijual->judul,
                    'kategori' => $item->buku_dijual->kategori->nama,
                    'active_flag' => $item->buku_dijual->active_flag
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
     * Download buku pdf base on buku_dijual id
     */
    public function download(string $id)
    {
        // Get the data from the database
        try {
            $data = buku_dijual::find($id);

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku tidak ditemukan'
                ], 404);
            }

            // Path to the PDF file
            $path = Storage::disk('public')->path($data->file_buku);

            // Check if the file exists
            if (!Storage::disk('public')->exists($data->file_buku)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File buku tidak ditemukan'
                ], 404);
            }

            return response()->download($path, $data->judul . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
