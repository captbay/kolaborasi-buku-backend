<?php

namespace App\Http\Controllers;

use App\Models\buku_dijual;
use App\Models\keranjang;
use App\Models\list_transaksi_buku;
use App\Models\transaksi_penjualan_buku;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TransaksiPenjualanBukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get all resources
        try {
            $data = transaksi_penjualan_buku::with('list_transaksi_buku')
                ->where('user_id', Auth::user()->id)
                ->where('status', $request->status)
                ->orderBy('created_at', 'desc')
                ->get();

            // return needed list_transaksi_buku from map data
            $data = $data->map(function ($item) {
                return [
                    'trx_id' => $item->id,
                    'no_transaksi' => $item->no_transaksi,
                    'status' => $item->status,
                    'date_time_exp' => $item->date_time_exp,
                    'date_time_lunas' => $item->date_time_lunas,
                    'total_harga' => $item->total_harga,
                    'created_at' => Carbon::parse($item->created_at)->locale('id')
                        ->settings(['formatFunction' => 'translatedFormat'])
                        ->format('l, j F Y'),
                    'jumlah_buku' => $item->list_transaksi_buku->count(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data Pembelian Buku',
                'data' => $data,
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
        // Create a new resource
        try {
            // validate request
            $validatedData = Validator::make($request->all(), [
                'buku_dijual' => 'required|array',
            ]);

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            // get buku_dijual
            $buku_dijual_array = $request->buku_dijual;

            // set temp total harga
            $total_harga = 0;

            // check id buku_dijual from array in $req->buku_dijual if available
            foreach ($buku_dijual_array as $key => $value) {
                $buku_dijual = buku_dijual::find($value);
                if (!$buku_dijual) {
                    return response()->json([
                        'success' => false,
                        'message' => 'id Buku dijual tidak ditemukan',
                    ], 404);
                }

                // get price
                $price = $buku_dijual->harga;

                //  count total harga
                $total_harga += $price;
            }

            // get user login
            $user = Auth::user();

            // generate no_transaksi
            $no_transaksi = 'TRX-BUKU-' . date('YmdHis') . '-' . rand(1000, 9999);

            // create new transaksi
            $data = transaksi_penjualan_buku::create([
                'user_id' => $user->id,
                'no_transaksi' => $no_transaksi,
                'total_harga' => $total_harga,
                'status' => 'PROGRESS',
                // make exp is 2 hours from created
                'date_time_exp' => Carbon::now()->addHours(2),
            ]);

            foreach ($buku_dijual_array as $key => $value) {
                $buku_dijual = buku_dijual::find($value);
                if (!$buku_dijual) {
                    return response()->json([
                        'success' => false,
                        'message' => 'id Buku dijual tidak ditemukan',
                    ], 404);
                }

                // create list_transaksi_penjualan_buku
                $list_buku = list_transaksi_buku::create([
                    'transaksi_penjualan_buku_id' => $data->id,
                    'buku_dijual_id' => $buku_dijual->id,
                ]);
            }

            // delete keranjang if $buku_dijual_array is more than 1
            if (
                count($buku_dijual_array) > 1
            ) {
                // delete keranjang
                foreach ($buku_dijual_array as $key => $value) {
                    $keranjang = keranjang::where('buku_dijual_id', $value);
                    $keranjang->delete();
                }
            }

            if ($data && $list_buku) {
                return response()->json([
                    'success' => true,
                    'message' => 'Melanjutkan ke halaman pembayaran',
                    'data' => $data->id,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaski gagal',
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
    public function show(string $id)
    {
        // Get the resource
        try {
            $data = transaksi_penjualan_buku::with('list_transaksi_buku.buku_dijual.kategori')->find($id);
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            // return needed list_transaksi_buku from map data
            $data->list_transaksi_buku = $data->list_transaksi_buku->map(function ($item) {
                return [
                    'buku_dijual_id' => $item->buku_dijual->id,
                    'kategori' => $item->buku_dijual->kategori->nama,
                    'judul' => $item->buku_dijual->judul,
                    'harga' => $item->buku_dijual->harga,
                    'cover_buku' => $item->buku_dijual->cover_buku,
                    'jumlah_halaman' => $item->buku_dijual->jumlah_halaman,
                    'bahasa' => $item->buku_dijual->bahasa,
                    'isbn' => $item->buku_dijual->isbn
                ];
            });

            // return needed data
            $data = [
                'trx_id' => $data->id,
                'no_transaksi' => $data->no_transaksi,
                'status' => $data->status,
                'date_time_exp' => $data->date_time_exp,
                'date_time_lunas' => $data->date_time_lunas != null ? Carbon::parse($data->date_time_lunas)->locale('id')
                    ->settings(['formatFunction' => 'translatedFormat'])
                    ->format('l, j F Y') : $data->date_time_lunas,
                'total_harga' => $data->total_harga,
                'created_at' => Carbon::parse($data->created_at)->locale('id')
                    ->settings(['formatFunction' => 'translatedFormat'])
                    ->format('l, j F Y'),
                'jumlah_buku' => $data->list_transaksi_buku->count(),
                'list_transaksi_buku' => $data->list_transaksi_buku,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil ditemukan',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update status the specified resource from storage.
     */
    public function gagal(Request $request)
    {
        // Delete the resource
        try {
            $data = transaksi_penjualan_buku::find($request->transaksi_buku_id);
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            $data->update([
                'date_time_exp' => null,
                'status' => 'FAILED',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Gagal',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // upload bukti pembayaran
    public function uploadBuktiPembayaran(string $id, Request $request)
    {
        // Upload bukti pembayaran
        try {
            $trx = transaksi_penjualan_buku::find($id);

            // validate request
            $validatedData = Validator::make($request->all(), [
                'foto_bukti_bayar' => 'required|file|mimes:jpeg,png,jpg|max:2048',
            ], [
                'foto_bukti_bayar.required' => 'Foto bukti bayar harus diisi',
                'foto_bukti_bayar.file' => 'Foto bukti bayar harus berupa file',
                'foto_bukti_bayar.mimes' => 'Foto bukti bayar harus berupa file gambar dengan format jpeg, png, jpg',
                'foto_bukti_bayar.max' => 'Foto bukti bayar maksimal 2MB',
            ]);

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            if ($request->file('foto_bukti_bayar')) {
                $uploadedFileCV = $request->file('foto_bukti_bayar');

                if ($uploadedFileCV) {
                    // delete old file
                    $oldFile = $trx->foto_bukti_bayar;
                    if ($oldFile) {
                        $filesystem = Storage::disk('public');
                        $filesystem->delete($oldFile);
                    }
                    $filenameCv = Str::uuid() . '.' . $uploadedFileCV->getClientOriginalExtension();
                    $filesystem = Storage::disk('public');
                    $filesystem->putFileAs('foto_bukti_bayar_pembelian_buku/', $uploadedFileCV, $filenameCv);
                }
            }

            $trx->update([
                'foto_bukti_bayar' => 'foto_bukti_bayar_pembelian_buku/' . $filenameCv,
                'date_time_exp' => null,
                'status' => 'UPLOADED',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        // Return the user
        return response()->json([
            'success' => true,
            'message' => 'Berhasil upload foto bukti bayar',
        ], 200);
    }
}
