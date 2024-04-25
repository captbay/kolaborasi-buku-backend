<?php

namespace App\Http\Controllers;

use App\Models\buku_permohonan_terbit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BukuPermohonanTerbitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the data from the database
        try {
            $data = buku_permohonan_terbit::where('user_id', Auth::user()->id);

            // search
            if ($request->search) {
                $data = $data->where('judul', 'like', '%' . $request->search . '%');
            }

            $data = $data
                ->orderBy('created_at', 'desc')
                ->paginate($request->limit);

            // Return needed data
            $data = $data->through(function ($item) {
                return [
                    'id' => $item->id,
                    'cover_buku' => $item->cover_buku,
                    'judul' => $item->judul,
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
     * Display the specified resource.
     */
    public function show($id)
    {
        // Get the data from the database
        try {
            $data = buku_permohonan_terbit::with('transaksi_paket_penerbitan')
                ->find($id);

            // return needed data
            $transaksi_paket_penerbitan = [
                'trx_id' => $data->transaksi_paket_penerbitan->id,
                'no_transaksi' => $data->transaksi_paket_penerbitan->no_transaksi,
                'total_harga' => $data->transaksi_paket_penerbitan->total_harga,
                'status' => $data->transaksi_paket_penerbitan->status,
                'date_time_exp' => $data->transaksi_paket_penerbitan->date_time_exp,
                'note' => $data->transaksi_paket_penerbitan->note,
            ];

            $data = [
                'id' => $data->id,
                'judul' => $data->judul,
                'deskripsi' => $data->deskripsi,
                'cover_buku' => $data->cover_buku,
                'file_buku' => $data->file_buku,
                'file_mou' => $data->file_mou,
                'dijual' => $data->dijual,
                'isbn' => $data->isbn,
                'created_at' => Carbon::parse($data->created_at)->locale('id')
                    ->settings(['formatFunction' => 'translatedFormat'])
                    ->format('l, j F Y'),
                'transaksi_paket_penerbitan' => $transaksi_paket_penerbitan,
                'array_status' => [
                    'REVIEW', 'TERIMA DRAFT', 'DP UPLOADED', 'DP TIDAK SAH', 'INPUT ISBN', 'DRAFT SELESAI', 'PELUNASAN UPLOADED', 'PELUNASAN TIDAK SAH', 'SIAP TERBIT', 'SUDAH TERBIT'
                ],
            ];

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
            $data = buku_permohonan_terbit::find($id);

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
