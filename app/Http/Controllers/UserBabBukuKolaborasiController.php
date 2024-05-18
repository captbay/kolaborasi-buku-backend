<?php

namespace App\Http\Controllers;

use App\Models\buku_kolaborasi;
use App\Models\mou;
use App\Models\User;
use App\Models\user_bab_buku_kolaborasi;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

            $dataForTimeline = buku_kolaborasi::with('bab_buku_kolaborasi')
                ->with('bab_buku_kolaborasi.user_bab_buku_kolaborasi', function ($query) {
                    $query->orderBy('created_at', 'desc');
                })
                ->find($data->bab_buku_kolaborasi->buku_kolaborasi->id);

            // dd($dataForTimeline);

            // set temp count for timeline
            $count_kontributor = 0;
            $count_upload = 0;
            $count_editing = 0;
            $count_selesai = 0;

            foreach ($dataForTimeline->bab_buku_kolaborasi as $key => $value) {
                if (
                    $value->user_bab_buku_kolaborasi->first()
                ) {
                    if (
                        $value->user_bab_buku_kolaborasi->first()->datetime_deadline > Carbon::now()
                        || $value->user_bab_buku_kolaborasi->first()?->status != "FAILED"
                    ) {
                        $count_kontributor++;
                    }
                    // check if status is UPLOADED
                    if ($value->user_bab_buku_kolaborasi->first()?->status == "UPLOADED" || $value->user_bab_buku_kolaborasi->first()?->status == "EDITING" || $value->user_bab_buku_kolaborasi->first()?->status == "DONE") {
                        $count_upload++;
                    }

                    // check if status is EDITING
                    if ($value->user_bab_buku_kolaborasi->first()?->status == "EDITING" || $value->user_bab_buku_kolaborasi->first()?->status == "DONE") {
                        $count_editing++;
                    }

                    // check if status is DONE
                    if ($value->user_bab_buku_kolaborasi->first()?->status == "DONE") {
                        $count_selesai++;
                    }
                }
            }

            $timeline_kolaborasi = [
                [
                    'id' => 1,
                    'judul' => 'Kontributor',
                    'count' => $count_kontributor . '/' . $dataForTimeline->jumlah_bab,
                    'status' => $count_kontributor >= $dataForTimeline->jumlah_bab ? 'selesai' : 'menunggu'
                ],
                [
                    'id' => 2,
                    'judul' => 'Upload Naskah',
                    'count' => $count_upload . '/' . $dataForTimeline->jumlah_bab,
                    'status' => $count_upload >= $dataForTimeline->jumlah_bab ? 'selesai' : 'menunggu'
                ],
                [
                    'id' => 3,
                    'judul' => 'Editing Oleh Editor',
                    'count' => $count_editing . '/' . $dataForTimeline->jumlah_bab,
                    'status' => $count_editing >= $dataForTimeline->jumlah_bab ? 'selesai' : 'menunggu'
                ],
                [
                    'id' => 4,
                    'judul' => 'Naskah Selesai',
                    'count' => $count_selesai . '/' . $dataForTimeline->jumlah_bab,
                    'status' => $count_selesai >= $dataForTimeline->jumlah_bab ? 'selesai' : 'menunggu'
                ],
                [
                    'id' => 5,
                    'judul' => 'Input ISBN',
                    'status' => $dataForTimeline->dijual == 1 ? 'selesai' : 'menunggu'
                ],
                [
                    'id' => 6,
                    'judul' => 'Buku Publish',
                    'status' => $dataForTimeline->dijual == 1 ? 'selesai' : 'menunggu'
                ],
            ];

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
                'deskripsi_buku' => $data->bab_buku_kolaborasi->buku_kolaborasi->deskripsi,
                'kategori_buku' => $data->bab_buku_kolaborasi->buku_kolaborasi->kategori->nama,
                'file_mou' => $data->file_mou,
                'file_hak_cipta' => $data->bab_buku_kolaborasi->buku_kolaborasi->file_hak_cipta,
                'timeline_kolaborasi' => $timeline_kolaborasi,
                'buku_kolaborasi_id' => $data->bab_buku_kolaborasi->buku_kolaborasi->id,
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

    /**
     * Download file mou base on buku_kolaborasi_id
     *
     **/
    public function downloadMou(Request $request)
    {
        // Get the data from the database
        try {
            $data = mou::where('active_flag', 1)
                ->where('kategori', $request->filter)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mou tidak ditemukan'
                ], 404);
            }

            // Path to the PDF file
            $path = Storage::disk('public')->path($data->file_mou);

            // Check if the file exists
            if (!Storage::disk('public')->exists($data->file_mou)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File buku tidak ditemukan'
                ], 404);
            }

            return response()->download($path, 'filemou_' . $data->nama . '.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload file mou base on user_bab_buku_kolaborasi_id
     *
     * */
    public function uploadMou(Request $request, $id)
    {

        // Get the data from the database
        try {
            $data = user_bab_buku_kolaborasi::find($id);

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // validate request
            $validatedData = Validator::make($request->all(), [
                'file_mou' => 'required|file|mimes:pdf|max:2048',
            ], [
                'file_mou.max' => 'File MOU Maksimal 2 MB',
            ]);

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            if ($request->file('file_mou')) {
                $uploadedFile = $request->file('file_mou');

                if ($uploadedFile) {
                    // delete old file
                    $oldFile = $data->file_mou;
                    if ($oldFile) {
                        $filesystem = Storage::disk('public');
                        $filesystem->delete($oldFile);
                    }
                    // save new file
                    $filename = Str::uuid() . '.' . $uploadedFile->getClientOriginalExtension();
                    $filesystem = Storage::disk('public');
                    $filesystem->putFileAs('mou_buku_kolaborasi/', $uploadedFile, $filename);
                }
            }

            // update data
            $data->update([
                'file_mou' => 'mou_buku_kolaborasi/' . $filename,
            ]);

            // Path to the PDF file
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Failed bcs time exp
     *
     * */
    public function failedKolaborasi(string $id)
    {
        // Get the data from the database
        try {
            $data = user_bab_buku_kolaborasi::find($id);

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            $data->update([
                'status' => 'FAILED',
                'datetime_deadline' => null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proses Kolaborasi Gagal, Waktu Kolaborasi Sudah Habis!, Silahkan Membeli Ulang Bab ini',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload bab file
     *
     * */
    public function uploadBab(Request $request, $id)
    {

        // Get the data from the database
        try {
            $data = user_bab_buku_kolaborasi::with('user')->find($id);

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // validate request
            $validatedData = Validator::make($request->all(), [
                'file_bab' => 'required|file|mimes:pdf,doc,docx|max:20480',
            ], [
                'file_bab.max' => 'File BAB Maksimal 20 MB',
            ]);

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            if ($request->file('file_bab')) {
                $uploadedFile = $request->file('file_bab');

                if ($uploadedFile) {
                    // delete old file
                    $oldFile = $data->file_bab;
                    if ($oldFile) {
                        $filesystem = Storage::disk('public');
                        $filesystem->delete($oldFile);
                    }
                    // save new file
                    $filename = Str::uuid() . '.' . $uploadedFile->getClientOriginalExtension();
                    $filesystem = Storage::disk('public');
                    $filesystem->putFileAs('file_buku_bab_kolaborasi/', $uploadedFile, $filename);
                }
            }

            // update data
            $data->update([
                'status' => 'UPLOADED',
                'file_bab' => 'file_buku_bab_kolaborasi/' . $filename,
                'datetime_deadline' => null,
            ]);

            // send notification to admin
            $recipientAdmin = User::where('role', 'admin')->first();

            Notification::make()
                ->success()
                ->title('Terdapat upload bab kolaborasi oleh user ' . $data->user->nama_lengkap . ', silahkan cek!')
                ->sendToDatabase($recipientAdmin)
                ->send();

            // Path to the PDF file
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
