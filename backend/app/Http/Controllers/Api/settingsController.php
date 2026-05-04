<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\settings;

class SettingsController extends Controller
{
    public function index()
    {
        try {
            $settings = Settings::first();
            if ($settings) {
                $settings->logo = $settings->logo
                    ? asset('storage/app/public/images/logo/' . $settings->logo)
                    : null;
                return response()->json([
                    'success' => true,
                    'data' => $settings
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'No settings found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
?>