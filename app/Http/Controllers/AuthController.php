<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

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
            $user = User::where('email', $request->email)->first();

            // if user not found
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password kamu salah!',
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
            $token = $user->createToken('auth_token')->plainTextToken;

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
                'password' => 'required|min:8',
                'no_telepon' => 'required|max:12|min:9',
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
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kata Sandi Lama Anda Salah!',
                ], 404);
            }

            // validate request
            $validatedData = Validator::make(
                $request->all(),
                [
                    'password' => 'required|min:8',
                    'new_password' => 'required|min:8|different:password',
                    'confirm_password' => 'required|min:8|same:new_password',
                ],
                [
                    'new_password.min' => 'Kata Sandi Baru Minimal 8 Karakter!',
                    'confirm_password.min' => 'Konfirmasi Kata Sandi Minimal 8 Karakter!',
                    'new_password.different' => 'Kata Sandi Baru Harus Berbeda Dengan Kata Sandi Lama!',
                    'confirm_password.same' => 'Kata Sandi Baru dan Konfirmasi Kata Sandi Tidak Sama!',
                ]
            );

            // if validation fails
            if ($validatedData->fails()) {
                return response()->json(['message' => $validatedData->errors()], 422);
            }

            // check password
            if (Hash::check($request->password, $user->password)) {
                // update password
                $user->update([
                    'password' => Hash::make($request->new_password),
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
                    'password' => 'required|min:8',
                    'confirm_password' => 'required|same:password',
                ],
                [
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
            return redirect('http://localhost:3000/login');
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
}