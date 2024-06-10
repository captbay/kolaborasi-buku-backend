<?php

namespace App\Http\Controllers;

use App\Jobs\CheckIsDeadline;
use App\Models\bab_buku_kolaborasi;
use App\Models\transaksi_kolaborasi_buku;
use App\Models\User;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TransaksiKolaborasiBukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get all resources
        try {
            $data = transaksi_kolaborasi_buku::with('bab_buku_kolaborasi.buku_kolaborasi.kategori')
                ->where('user_id', Auth::user()->id)
                ->where('status', $request->status)
                ->orderBy('created_at', 'desc')
                ->get();

            // return needed list_transaksi_buku from map data
            $data = $data->map(function ($item) {
                $bab_buku = [
                    'id' => $item->bab_buku_kolaborasi->id,
                    'no_bab' => $item->bab_buku_kolaborasi->no_bab,
                    'judul' => $item->bab_buku_kolaborasi->judul,
                    'harga' => $item->bab_buku_kolaborasi->harga,
                    'durasi_pembuatan' => $item->bab_buku_kolaborasi->durasi_pembuatan,
                    'deskripsi' => $item->bab_buku_kolaborasi->deskripsi,
                ];

                $buku_kolaborasi = [
                    'id' => $item->bab_buku_kolaborasi->buku_kolaborasi->id,
                    'slug' => $item->bab_buku_kolaborasi->buku_kolaborasi->slug,
                    'judul' => $item->bab_buku_kolaborasi->buku_kolaborasi->judul,
                    'kategori' => $item->bab_buku_kolaborasi->buku_kolaborasi->kategori->nama,
                    'cover_buku' => $item->bab_buku_kolaborasi->buku_kolaborasi->cover_buku,
                ];

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
                    'bab_buku' => $bab_buku,
                    'buku_kolaborasi' => $buku_kolaborasi,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data Pembelian Buku Kolaborasi',
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
            // get user login
            $user = Auth::user();

            // if user role != MEMBER
            if ($user->role != 'MEMBER') {
                return response()->json([
                    'success' => false,
                    'message' => 'Kolaborasi Hanya Bisa Dilakukan Oleh Member, Silahkan Mendaftar Terlebih Dahulu Di Menu Akun!',
                ], 404);
            }

            // validate request
            $validatedData = Validator::make($request->all(), [
                'bab_buku_kolaborasi_id' => 'required',
            ]);

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            //  find bab buku kolaborasi id
            $bab = bab_buku_kolaborasi::find($request->bab_buku_kolaborasi_id);

            // if not found
            if (!$bab) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bab Buku Kolaborasi tidak ditemukan',
                ], 404);
            }

            // generate no_transaksi
            $no_transaksi = 'TRX-KOLABORASI-' . date('YmdHis') . '-' . rand(1000, 9999);

            // create new transaksi
            $data = transaksi_kolaborasi_buku::create([
                'user_id' => $user->id,
                'bab_buku_kolaborasi_id' => $bab->id,
                'no_transaksi' => $no_transaksi,
                'total_harga' => $bab->harga,
                'status' => 'PROGRESS',
                'date_time_exp' => Carbon::now()->addHours(2),
            ]);

            if ($data) {
                CheckIsDeadline::dispatch($data->id, 'kolaborasi')->delay($data->date_time_exp);

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
            $data = transaksi_kolaborasi_buku::with('bab_buku_kolaborasi.buku_kolaborasi.kategori')->find($id);
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            $bab_buku = [
                'id' => $data->bab_buku_kolaborasi->id,
                'no_bab' => $data->bab_buku_kolaborasi->no_bab,
                'judul' => $data->bab_buku_kolaborasi->judul,
                'harga' => $data->bab_buku_kolaborasi->harga,
                'durasi_pembuatan' => $data->bab_buku_kolaborasi->durasi_pembuatan,
                'deskripsi' => $data->bab_buku_kolaborasi->deskripsi,
            ];

            $buku_kolaborasi = [
                'id' => $data->bab_buku_kolaborasi->buku_kolaborasi->id,
                'slug' => $data->bab_buku_kolaborasi->buku_kolaborasi->slug,
                'judul' => $data->bab_buku_kolaborasi->buku_kolaborasi->judul,
                'kategori' => $data->bab_buku_kolaborasi->buku_kolaborasi->kategori->nama,
                'cover_buku' => $data->bab_buku_kolaborasi->buku_kolaborasi->cover_buku,
            ];

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
                'bab_buku' => $bab_buku,
                'buku_kolaborasi' => $buku_kolaborasi,
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
     * Update Status the specified resource from storage.
     */
    public function gagal(Request $request)
    {
        // Delete the resource
        try {
            $data = transaksi_kolaborasi_buku::find($request->transaksi_kolaborasi_buku_id);
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
            $trx = transaksi_kolaborasi_buku::with('user')->find($id);

            // validate request
            $validatedData = Validator::make($request->all(), [
                'foto_bukti_bayar' => 'required|file|mimes:jpeg,png,jpg|max:2048',
            ], [
                'foto_bukti_bayar.required' => 'Foto bukti bayar harus diisi',
                'foto_bukti_bayar.file' => 'Foto bukti bayar harus berupa file',
                'foto_bukti_bayar.mimes' => 'Foto bukti bayar harus berupa file gambar dengan format jpeg, png, jpg',
                'foto_bukti_bayar.max' => 'Foto bukti bayar Maksimal 2MB',
            ]);

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            if ($request->file('foto_bukti_bayar')) {
                $uploadedFile = $request->file('foto_bukti_bayar');

                if ($uploadedFile) {
                    // delete old file
                    $oldFile = $trx->foto_bukti_bayar;
                    if ($oldFile) {
                        $filesystem = Storage::disk('public');
                        $filesystem->delete($oldFile);
                    }
                    $filename = Str::uuid() . '.' . $uploadedFile->getClientOriginalExtension();
                    $filesystem = Storage::disk('public');
                    $filesystem->putFileAs('foto_bukti_bayar_kolaborasi/', $uploadedFile, $filename);
                }
            }

            $trx->update([
                'foto_bukti_bayar' => 'foto_bukti_bayar_kolaborasi/' . $filename,
                'date_time_exp' => null,
                'status' => 'UPLOADED',
            ]);

            // send notification to admin
            $recipientAdmin = User::where('role', 'admin')->first();

            Notification::make()
                ->success()
                ->title('Terdapat pembelian kolaborasi oleh user ' . $trx->user->nama_lengkap . ' dan sudah upload bukti pembayaran!')
                ->sendToDatabase($recipientAdmin)
                ->send();
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
