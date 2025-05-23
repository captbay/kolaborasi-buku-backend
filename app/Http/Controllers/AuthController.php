<?php

namespace App\Http\Controllers;

use App\Mail\HubungiKamiMail;
use App\Models\hubungi_kami;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RulesPassword;

class AuthController extends Controller
{
    /**
     * Login function
     */
    public function login(Request $request)
    {
        try {
            // validate request
            $validatedData = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
            ]);

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            // find user
            $user = User::where('email', $request->email)->withTrashed()->first();

            // if user not found
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password kamu salah!',
                ], 404);
            }

            // if user deleted
            if ($user->deleted_at != null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun kamu sedang diblokir oleh admin!, hubungi admin untuk info lebih lanjut',
                ], 404);
            }

            // if emails not verify
            if ($user->status_verif_email != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email Anda Belum Terverifikasi!'
                ], 404);
            }

            // create token
            $token = $user->createToken('auth_token', ['*'], now()->addDay())->plainTextToken;

            // check password
            if (Hash::check($request->password, $user->password)) {
                // return needed data
                $data = [
                    'id' => $user->id,
                    'token_type' => 'Bearer',
                    'token' => $token
                ];

                return response()->json([
                    'success' => true,
                    'message' => 'Login success',
                    'data' => $data,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password kamu salah!',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Register function
     */
    public function register(Request $request)
    {
        try {
            // validate request
            $validatedData = Validator::make($request->all(), [
                'nama_depan' => 'required|min:3',
                'nama_belakang' => 'required|min:3',
                'email' => 'required|email:rfc,dns|unique:users',
                'password' => ['required', RulesPassword::min(6)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()],
                'no_telepon' => 'required|min:9|starts_with:08,62',
            ], [
                'password.min' => 'Password minimal 6 karakter',
                'password.letters' => 'Password minimal memiliki 1 huruf',
                'password.numbers' => 'Password minimal memiliki 1 angka',
                'password.symbols' => 'Password minimal memiliki 1 simbol',
                'password.mixed' => 'Password minimal memiliki 1 huruf kecil dan besar',
                'password.uncompromised' => 'Silahkan masukkan password yang berbeda karena password ini tidak aman',
            ]);

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            // create user
            User::create([
                'nama_depan' => $request->nama_depan,
                'nama_belakang' => $request->nama_belakang,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'no_telepon' => $request->no_telepon,
                'kode_verif_email' => uniqid(),
                'status_verif_email' => 0,
                'role' => 'CUSTOMER',
            ])->sendEmailVerificationNotification();

            // return response
            return response()->json([
                'message' => 'Pendaftaran Akun Berhasil!',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Logout function
     */
    public function logout()
    {
        try {
            // revoke token
            Auth::user()->currentAccessToken()->delete();

            // return response
            return response()->json([
                'message' => 'Berhasil Keluar',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Change password function
     */
    public function changePassword(Request $request)
    {
        try {
            // find user login
            $user = User::find(Auth::user()->id);

            // check if req->password is same with user password
            if (!Hash::check($request->old_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kata Sandi Lama Anda Salah!',
                ], 404);
            }

            // validate request
            $validatedData = Validator::make(
                $request->all(),
                [
                    'old_password' => 'required',
                    'password' => ['required', 'different:old_password', RulesPassword::min(6)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                        ->uncompromised()],
                    'confirm_password' => 'required|min:6|same:password',
                ],
                [
                    'password.letters' => 'Kata Sandi Baru minimal memiliki 1 huruf',
                    'password.numbers' => 'Kata Sandi Baru minimal memiliki 1 angka',
                    'password.symbols' => 'Kata Sandi Baru minimal memiliki 1 simbol',
                    'password.mixed' => 'Kata Sandi Baru minimal memiliki 1 huruf kecil dan besar',
                    'password.uncompromised' => 'Silahkan masukkan password yang berbeda karena password ini tidak aman',
                    'password.min' => 'Kata Sandi Baru Minimal 6 Karakter!',
                    'password.different' => 'Kata Sandi Baru Harus Berbeda Dengan Kata Sandi Lama!',
                    'confirm_password.min' => 'Konfirmasi Kata Sandi Minimal 6 Karakter!',
                    'confirm_password.same' => 'Kata Sandi Baru dan Konfirmasi Kata Sandi Tidak Sama!',
                ]
            );

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            // check password
            if (Hash::check($request->old_password, $user->password)) {
                // update password
                $user->update([
                    'password' => Hash::make($request->password),
                ]);

                // revoke token
                Auth::user()->currentAccessToken()->delete();

                // return response
                return response()->json([
                    'message' => 'Berhasil Mengubah Kata Sandi',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Kata Sandi Lama Anda Salah!',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Forgot password function
     */
    public function sendEmailForgotPassword(Request $request)
    {
        try {
            // validate request
            $validatedData = Validator::make($request->all(), [
                'email' => 'required|email:rfc,dns',
            ]);

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors(), 'success' => false], 422);
            }

            if ($request->email == config('app.email_penerbitan_admin')) {
                // insert delay
                sleep(5);
                return response()->json([
                    'success' => false,
                    'message' => 'Jika Benar Email Terdaftar, Link Reset Kata Sandi Akan Dikirim Ke Email Anda!',
                ], 200);
            }

            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status) {
                return response()->json([
                    'message' => 'Jika Benar Email Terdaftar, Link Reset Kata Sandi Akan Dikirim Ke Email Anda!',
                    'success' => true,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset password function
     */
    public function resetPassword(Request $request)
    {
        try {
            // validate request
            $validatedData = Validator::make(
                $request->all(),
                [
                    'token' => 'required',
                    'email' => 'required|email',
                    'password' => ['required', RulesPassword::min(6)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols()
                        ->uncompromised()],
                    'confirm_password' => 'required|same:password',
                ],
                [
                    'password.min' => 'Password minimal 6 karakter',
                    'password.letters' => 'Password minimal memiliki 1 huruf',
                    'password.numbers' => 'Password minimal memiliki 1 angka',
                    'password.symbols' => 'Password minimal memiliki 1 simbol',
                    'password.mixed' => 'Password minimal memiliki 1 huruf kecil dan besar',
                    'password.uncompromised' => 'Silahkan masukkan password yang berbeda karena password ini tidak aman',
                    'confirm_password.same' => 'Kata Sandi dan Konfirmasi Password Tidak Sama!',
                ]
            );

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function (User $user, string $password) {
                    $user->forceFill([
                        'password' => Hash::make($password)
                    ])->setRememberToken(Str::random(60));

                    $user->save();

                    event(new PasswordReset($user));
                }
            );

            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'success' => true,
                    'message' => __($status)
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __($status)
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify email function
     */
    public function verifyEmail(Request $request, $user_id)
    {
        try {
            if (!$request->hasValidSignature()) {
                return response()->json(["message" => "Invalid/Expired url provided."], 401);
            }

            $user = User::findOrFail($user_id);

            if (!$user->hasVerifiedEmail()) {
                $user->update([
                    "status_verif_email" => 1,
                ]);
                $user->markEmailAsVerified();
            }

            // return response()->json(["message" => "Email Anda Berhasil di Verifikasi."], 200);
            // redirect another link
            return redirect(config('app.front_end_web') . '/login');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resend email verification function
     */
    public function resendEmailVerification(Request $request)
    {
        try {
            // find user with email
            $user = User::where('email', $request->email)->first();

            if ($user->hasVerifiedEmail()) {
                return response()->json(["message" => "Email Anda Sudah di Verifikasi."], 400);
            }

            $user->sendEmailVerificationNotification();

            return response()->json(["message" => "Link Verifikasi Email Dikirim Ke Email Anda!"]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hubungi kami function
     */
    public function hubungiKami(Request $request)
    {
        try {
            // validate request
            $validatedData = Validator::make($request->all(), [
                'nama' => 'required',
                'email' => 'required|email:rfc,dns',
                'subjek' => 'required',
                'pesan' => 'required',
            ]);

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            // add data
            $hubungiKami = hubungi_kami::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'subjek' => $request->subjek,
                'pesan' => $request->pesan,
            ]);

            // get admin
            $recipientAdmin = User::where('role', 'ADMIN')->first();

            Notification::make()
                ->success()
                ->title('#HUBUNGIKAMI Ada Pesan Baru Dari User : ' . $request->nama)
                ->sendToDatabase($recipientAdmin)
                ->send();

            if ($hubungiKami) {
                return response()->json([
                    'message' => 'Pesan Anda Berhasil Dikirim!',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Pesan Anda Gagal Dikirim!',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
