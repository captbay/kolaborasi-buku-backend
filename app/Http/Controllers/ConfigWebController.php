<?php

namespace App\Http\Controllers;

use App\Models\config_web;
use Illuminate\Http\Request;

class ConfigWebController extends Controller
{
    /**
     * Display a listing of the getRekening.
     */
    public function getRekening()
    {
        try {
            $config = config_web::where('key', 'like', '%_rek%')
                ->where('tipe', 'TEXT')
                ->get();

            if ($config->count() == 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'data not found.',
                    'data' => [
                        "no_rek" => "92031903901923",
                        "bank_rek" => "BANK SEJAHTERA",
                        "nama_rek" => "PT PENERBITAN BUKU",
                    ],
                ], 200);
            }

            $no_rek = "";
            $bank_rek = "";
            $nama_rek = "";

            $data = $config->map(function ($data) use (&$no_rek, &$bank_rek, &$nama_rek) {
                if ($data->key == 'no_rek') {
                    $no_rek = $data->value;
                } elseif ($data->key == 'bank_rek') {
                    $bank_rek = $data->value;
                } elseif ($data->key == 'nama_rek') {
                    $nama_rek = $data->value;
                }
            });

            // return needed data
            $data = [
                "no_rek" => $no_rek,
                "bank_rek" => $bank_rek,
                "nama_rek" => $nama_rek,
            ];

            // return the resource
            return response()->json([
                'success' => true,
                'message' => 'data retrieved successfully.',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
