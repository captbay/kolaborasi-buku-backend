<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Get the user from the database
        try {
            $data = User::find($id);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }

        // If the user was not found, return a 404 response
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'error',
                'data' => 'User tidak ditemukan'
            ], 404);
        }

        // Return the user
        return response()->json([
            'success' => true,
            'message' => 'User ditemukan',
            'data' => $data
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Get the user from the database
        try {
            $user = User::find(Auth::user()->id);

            // If the user was not found, return a 404 response
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'user not found',
                ], 404);
            }

            // email old
            $emailOld = $user->email;

            // validate request
            $validatedData = Validator::make($request->all(), [
                'email' => [
                    'required',
                    'email:rfc,dns',
                    Rule::unique('users')->ignore($user->id),
                ],
                'no_telepon' => 'required|max:12|min:9|starts_with:08,+62',
                'nama_depan' => 'required|min:3',
                'nama_belakang' => 'required|min:3',
                'tgl_lahir' => 'required|date|before:today',
                'gender' => 'required',
                'alamat' => 'required|min:10',
                'provinsi' => 'required',
                'kota' => 'required',
                'kecamatan' => 'required',
                'kode_pos' => 'required|numeric',
            ]);

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            // update user
            $user->update([
                'email' => $request->email,
                'no_telepon' => $request->no_telepon,
                'nama_depan' => $request->nama_depan,
                'nama_belakang' => $request->nama_belakang,
                'tgl_lahir' => $request->tgl_lahir,
                'gender' => $request->gender,
                'alamat' => $request->alamat,
                'provinsi' => $request->provinsi,
                'kota' => $request->kota,
                'kecamatan' => $request->kecamatan,
                'kode_pos' => $request->kode_pos,
            ]);

            // if email changed
            if ($emailOld != $request->email) {
                $user->update([
                    'email_verified_at' => null,
                    'status_verif_email' => null,
                ]);
                $user->sendEmailVerificationNotification();

                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil update data user. Silahkan verifikasi email anda kembali.',
                ], 200);
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
            'message' => 'Berhasil update data user.',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function uploadFileMember(Request $request)
    {
        // Get the user from the database
        try {
            // Get the authenticated user
            $user = User::find(Auth::user()->id);

            // If the user was not found, return a 404 response
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'user not found',
                ], 404);
            }

            // validate request
            $validatedData = Validator::make($request->all(), [
                'file_cv' => 'required|file|mimes:pdf,doc,docx|max:2048',
                'file_ktp' => 'required|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048',
                'file_ttd' => 'required|image|mimes:png|max:2048',
            ]);

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            if ($request->file('file_cv')) {
                $uploadedFileCV = $request->file('file_cv');

                if ($uploadedFileCV) {
                    // delete old file
                    $oldFile = $user->file_cv;
                    if ($oldFile) {
                        $filesystem = Storage::disk('public');
                        $filesystem->delete($oldFile);
                    }
                    $filenameCv = Str::uuid() . '.' . $uploadedFileCV->getClientOriginalExtension();
                    $filesystem = Storage::disk('public');
                    $filesystem->putFileAs('file_cv/', $uploadedFileCV, $filenameCv);
                }
            }

            if ($request->file('file_ktp')) {
                $uploadedFileKtp = $request->file('file_ktp');

                if ($uploadedFileKtp) {
                    // delete old file
                    $oldFile = $user->file_ktp;
                    if ($oldFile) {
                        $filesystem = Storage::disk('public');
                        $filesystem->delete($oldFile);
                    }
                    $filenameKtp = Str::uuid() . '.' . $uploadedFileKtp->getClientOriginalExtension();
                    $filesystem = Storage::disk('public');
                    $filesystem->putFileAs('file_ktp/', $uploadedFileKtp, $filenameKtp);
                }
            }

            if ($request->file('file_ttd')) {
                $uploadedFileTtd = $request->file('file_ttd');

                if ($uploadedFileTtd) {
                    // delete old file
                    $oldFile = $user->file_ttd;
                    if ($oldFile) {
                        $filesystem = Storage::disk('public');
                        $filesystem->delete($oldFile);
                    }
                    $filenameTtd = Str::uuid() . '.' . $uploadedFileTtd->getClientOriginalExtension();
                    $filesystem = Storage::disk('public');
                    $filesystem->putFileAs('file_ttd/', $uploadedFileTtd, $filenameTtd);
                }
            }

            // update user
            $user->update([
                'file_cv' => 'file_cv/' . $filenameCv,
                'file_ktp' => 'file_ktp/' . $filenameKtp,
                'file_ttd' => 'file_ttd/' . $filenameTtd,
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
            'message' => 'Berhasil upload file member.',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function uploadFotoProfil(Request $request)
    {
        // Get the user from the database
        try {
            // Get the authenticated user
            $user = User::find(Auth::user()->id);

            // If the user was not found, return a 404 response
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'user not found',
                ], 404);
            }

            // validate request
            $validatedData = Validator::make($request->all(), [
                'foto_profil' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);


            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            if ($request->file('foto_profil')) {
                $uploadedFile = $request->file('foto_profil');

                if ($uploadedFile) {
                    // delete old file
                    $oldFile = $user->foto_profil;
                    if ($oldFile) {
                        $filesystem = Storage::disk('public');
                        $filesystem->delete($oldFile);
                    }
                    // save new file
                    $filename = Str::uuid() . '.' . $uploadedFile->getClientOriginalExtension();
                    $filesystem = Storage::disk('public');
                    $filesystem->putFileAs('foto_profil/', $uploadedFile, $filename);
                }
            }

            // update user
            $user->update([
                'foto_profil' => 'foto_profil/' . $filename,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }

        // Return the user
        return response()->json([
            'success' => true,
            'message' => 'Berhasil upload photo.',
        ], 200);
    }

    /**
     * Display a listing of notifikasi
     */
    public function notifikasi()
    {
        // Get the user from the database
        try {
            $data = User::find(Auth::user()->id);

            // If the user was not found, return a 404 response
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'error',
                    'data' => 'User tidak ditemukan'
                ], 404);
            }

            // return needed data
            $notif = $data->notifications->map(function ($item) {
                return [
                    'notif_id' => $item->id,
                    'actions' => $item->data['actions'],
                    'title' => $item->data['title'],
                    'body' => $item->data['body'],
                    'is_read' => $item->read_at ? true : false,
                    'created_at' => Carbon::parse($item->created_at)->diffForHumans(),
                ];
            });

            // count not read notifications
            $countNotRead = $data->unreadNotifications->count();

            // final data
            $finalData = [
                'count_not_read' => $countNotRead,
                'data' => $notif,
            ];

            // Return the user
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi User',
                'data' => $finalData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark the all notification as read.
     */
    public function readNotifikasi()
    {
        // Get the user from the database
        try {
            $data = User::find(Auth::user()->id);

            // If the user was not found, return a 404 response
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'error',
                    'data' => 'User tidak ditemukan'
                ], 404);
            }

            // mark all notification as read
            $data->unreadNotifications->markAsRead();

            // Return the user
            return response()->json([
                'success' => true,
                'message' => 'Notifikasi telah dibaca',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove all notification from storage.
     */
    public function deleteNotifikasi()
    {
        // Get the user from the database
        try {
            $data = User::find(Auth::user()->id);

            // If the user was not found, return a 404 response
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'error',
                    'data' => 'User tidak ditemukan'
                ], 404);
            }

            // delete all notification
            $data->notifications()->delete();

            // Return the user
            return response()->json([
                'success' => true,
                'message' => 'Seluruh Notifikasi telah dihapus',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
