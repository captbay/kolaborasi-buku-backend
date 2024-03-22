<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
                'message' => 'error',
                'data' => $e->getMessage()
            ], 500);
        }

        // If the user was not found, return a 404 response
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'error',
                'data' => 'user not found'
            ], 404);
        }

        // Return the user
        return response()->json([
            'success' => true,
            'message' => 'user successfully.',
            'data' => $data
        ], 200);
    }
}
