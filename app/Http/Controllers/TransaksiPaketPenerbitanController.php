<?php

namespace App\Http\Controllers;

use App\Models\buku_permohonan_terbit;
use App\Models\jasa_tambahan;
use App\Models\paket_penerbitan;
use App\Models\transaksi_paket_penerbitan;
use App\Models\trx_jasa_penerbitan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TransaksiPaketPenerbitanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get all resources
        try {
            $data = transaksi_paket_penerbitan::with('buku_permohonan_terbit')
                ->where('user_id', Auth::user()->id)
                ->where('status', $request->status)
                ->orderBy('created_at', 'desc')
                ->get();

            // return needed transaksi_paket_penerbitan from map data
            $data = $data->map(function ($item) {
                return [
                    'trx_id' => $item->id,
                    'no_transaksi' => $item->no_transaksi,
                    'status' => $item->status,
                    'date_time_exp' => $item->date_time_exp,
                    'date_time_dp_lunas' => $item->date_time_dp_lunas,
                    'date_time_lunas' => $item->date_time_lunas,
                    'total_harga' => $item->total_harga,
                    'created_at' => Carbon::parse($item->created_at)->locale('id')
                        ->settings(['formatFunction' => 'translatedFormat'])
                        ->format('l, j F Y'),
                    'buku_permohonan_terbit' =>
                    [
                        'judul' => $item->buku_permohonan_terbit->judul,
                    ],
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data Transaksi Paket Penerbitan',
                'data' => $data,
            ]);
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
                'paket_id' => 'required',
                'judul_buku' => 'required|string',
                'deskripsi_buku' => 'required|string',
                'file_buku' => 'required|file|mimes:pdf,doc,docx|max:2048',
                'file_mou' => 'required|file|mimes:pdf|max:2048',
            ]);

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            // user login
            $user = Auth::user();

            // set temp total harga
            $total_harga = 0;

            // paket
            $paket = paket_penerbitan::find($request->paket_id);

            if (!$paket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paket tidak ditemukan',
                ], 404);
            }

            $total_harga += $paket->harga;

            // JSON.parse jasa_tambahan_id
            $jasa_tambahan_id = json_decode($request->jasa_tambahan_id);

            // jasa tambahan
            foreach ($jasa_tambahan_id as $jasa_tambahan_id) {
                $jasa_tambahan = jasa_tambahan::find($jasa_tambahan_id);
                if (!$jasa_tambahan) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jasa tambahan tidak ditemukan',
                    ], 404);
                }

                $total_harga += $jasa_tambahan->harga;
            }

            // upload file_buku
            if ($request->file('file_buku')) {
                $uploadedFile = $request->file('file_buku');
                if ($uploadedFile) {
                    $filenameBuku = Str::uuid() . '.' . $uploadedFile->getClientOriginalExtension();
                    /** @var Illuminate\Filesystem\FilesystemAdapter */
                    $filesystem = Storage::disk('public');
                    $filesystem->putFileAs('buku_permohonan_terbit/', $uploadedFile, $filenameBuku);
                }
            }

            // upload file_mou
            if ($request->file('file_mou')) {
                $uploadedFile = $request->file('file_mou');
                if ($uploadedFile) {
                    $filenameMou = Str::uuid() . '.' . $uploadedFile->getClientOriginalExtension();
                    /** @var Illuminate\Filesystem\FilesystemAdapter */
                    $filesystem = Storage::disk('public');
                    $filesystem->putFileAs('mou_paket_penerbitan/', $uploadedFile, $filenameMou);
                }
            }

            // create buku_permohonan_terbit
            $buku_permohonan_terbit = buku_permohonan_terbit::create([
                'user_id' => $user->id,
                'judul' => $request->judul_buku,
                'deskripsi' => $request->deskripsi_buku,
                'file_buku' => 'buku_permohonan_terbit/' . $filenameBuku,
                'file_mou' => 'mou_paket_penerbitan/' . $filenameMou,
                'dijual' => 0,
            ]);

            // generate no_transaksi
            $no_transaksi = 'TRX-PAKET-' . date('YmdHis') . '-' . rand(1000, 9999);

            // create transaksi_paket_penerbitan
            $transaksi_paket_penerbitan = transaksi_paket_penerbitan::create([
                'user_id' => $user->id,
                'paket_penerbitan_id' => $request->paket_id,
                'buku_permohonan_terbit_id' => $buku_permohonan_terbit->id,
                'no_transaksi' => $no_transaksi,
                'total_harga' => $total_harga,
                'status' => 'REVIEW',
                'note' => 'Buku permohonan terbit Anda sedang dalam proses review, mohon menunggu :)',
            ]);

            // JSON.parse jasa_tambahan_id
            $jasa_tambahan_id = json_decode($request->jasa_tambahan_id);

            // jasa tambahan
            foreach ($jasa_tambahan_id as $jasa_tambahan_id) {
                trx_jasa_penerbitan::create([
                    'jasa_tambahan_id' => $jasa_tambahan_id,
                    'transaksi_paket_penerbitan_id' => $transaksi_paket_penerbitan->id,
                ]);
            }

            if ($transaksi_paket_penerbitan) {
                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi paket penerbitan berhasi dibuat',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi paket penerbitan gagal dibuat',
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
            $data = transaksi_paket_penerbitan::with(
                'jasa_tambahan',
                'buku_permohonan_terbit',
                'paket_penerbitan.jasa_paket_penerbitan.jasa_tambahan'
            )
                ->find($id);

            // if not find
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi paket penerbitan tidak ditemukan',
                ], 404);
            }

            // return needed transaksi_paket_penerbitan from map data
            $data =  [
                'trx_id' => $data->id,
                'no_transaksi' => $data->no_transaksi,
                'status' => $data->status,
                'date_time_exp' => $data->date_time_exp,
                'date_time_dp_lunas' => $data->date_time_dp_lunas,
                'date_time_lunas' => $data->date_time_lunas,
                'total_harga' => $data->total_harga,
                'created_at' => Carbon::parse($data->created_at)->locale('id')
                    ->settings(['formatFunction' => 'translatedFormat'])
                    ->format('l, j F Y'),
                'jasa_tambahan' => $data->jasa_tambahan?->map(function ($data) {
                    return [
                        'nama' => $data->nama,
                        'harga' => $data->harga,
                    ];
                }),
                'buku_permohonan_terbit' =>
                [
                    'judul' => $data->buku_permohonan_terbit->judul,
                    'deskripsi' => $data->buku_permohonan_terbit->deskripsi,
                ],
                'paket_penerbitan' =>
                [
                    'nama' => $data->paket_penerbitan->nama,
                    'harga' => $data->paket_penerbitan->harga,
                    'deskripsi' => $data->paket_penerbitan->deskripsi,
                    'jasa_paket_penerbitan' => $data->paket_penerbitan->jasa_paket_penerbitan->map(function ($data) {
                        return [
                            'nama' => $data->jasa_tambahan->nama
                        ];
                    }),
                ],
            ];


            return response()->json([
                'success' => true,
                'message' => 'Data Transaksi Paket Penerbitan',
                'data' => $data,
            ]);
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
            $data = transaksi_paket_penerbitan::find($request->trx_id);

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            if ($data->date_time_dp_lunas == null) {
                $data->update([
                    'status' => 'DP TIDAK SAH',
                    'date_time_exp' => null,
                ]);
            } else if ($data->date_time_lunas == null) {
                $data->update([
                    'status' => 'PELUNASAN TIDAK SAH',
                    'date_time_exp' => null,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi Kesalahan!',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Waktu sudah habis, Transaksi dibatalkan',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    // transactionAgain
    public function transactionAgain(string $id)
    {
        // Delete the resource
        try {
            $data = transaksi_paket_penerbitan::find($id);

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan',
                ], 404);
            }

            if ($data->date_time_dp_lunas == null) {
                $data->update([
                    'status' => 'TERIMA DRAFT',
                    'date_time_exp' => Carbon::now()->addHours(24),
                ]);
            } else if ($data->date_time_lunas == null) {
                $data->update([
                    'status' => 'DRAFT SELESAI',
                    'date_time_exp' => Carbon::now()->addHours(24),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi Kesalahan!',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data Transaksi berhasil dilakukan kembali',
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
            $trx = transaksi_paket_penerbitan::find($id);

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

            if ($trx->date_time_dp_lunas == null) {
                if ($request->file('foto_bukti_bayar')) {
                    $uploadedFileCV = $request->file('foto_bukti_bayar');

                    if ($uploadedFileCV) {
                        // delete old file
                        $oldFile = $trx->dp_upload;
                        if ($oldFile) {
                            $filesystem = Storage::disk('public');
                            $filesystem->delete($oldFile);
                        }
                        $filenameDp = Str::uuid() . '.' . $uploadedFileCV->getClientOriginalExtension();
                        $filesystem = Storage::disk('public');
                        $filesystem->putFileAs('foto_bukti_bayar_dp_penerbitan/', $uploadedFileCV, $filenameDp);
                    }
                }

                $trx->update([
                    'dp_upload' => 'foto_bukti_bayar_dp_penerbitan/' . $filenameDp,
                    'date_time_exp' => null,
                    'status' => 'DP UPLOADED',
                ]);
            } else if ($trx->date_time_lunas == null) {
                if ($request->file('foto_bukti_bayar')) {
                    $uploadedFileCV = $request->file('foto_bukti_bayar');

                    if ($uploadedFileCV) {
                        // delete old file
                        $oldFile = $trx->pelunasan_upload;
                        if ($oldFile) {
                            $filesystem = Storage::disk('public');
                            $filesystem->delete($oldFile);
                        }
                        $filenameCv = Str::uuid() . '.' . $uploadedFileCV->getClientOriginalExtension();
                        $filesystem = Storage::disk('public');
                        $filesystem->putFileAs('foto_bukti_bayar_lunas_penerbitan/', $uploadedFileCV, $filenameCv);
                    }
                }

                $trx->update([
                    'pelunasan_upload' => 'foto_bukti_bayar_lunas_penerbitan/' . $filenameCv,
                    'date_time_exp' => null,
                    'status' => 'PELUNASAN UPLOADED',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi Kesalahan!',
                ], 500);
            }
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
