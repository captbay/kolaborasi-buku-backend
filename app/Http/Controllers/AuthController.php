<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


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

            // create token
            $token = $user->createToken('auth_token')->plainTextToken;

            // check password
            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Login success',
                    'user' => $user,
                    'token_type' => 'Bearer',
                    'token' => $token
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
            $user = User::create([
                'nama_depan' => $request->nama_depan,
                'nama_belakang' => $request->nama_belakang,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'no_telepon' => $request->no_telepon,
                'kode_verif_email' => uniqid(),
                'status_verif_email' => 0,
                'role' => 'CUSTOMER',
                'active_flag' => 1,
            ]);

            // if user created
            if ($user) {
                // return response
                return response()->json([
                    'message' => 'Pendaftaran Akun Berhasil!',
                ], 201);
            }
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
    public function changePassword()
    {
        try {
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
    public function forgotPassword()
    {
        try {
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
    public function resetPassword()
    {
        try {
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
    public function verifyEmail()
    {
        try {
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
    public function resendEmailVerification()
    {
        try {
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
