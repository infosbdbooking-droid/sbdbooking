<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\settings;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
class settingsController extends Controller
{
    public function index()
    {
        try {
            $settings = settings::first();

            if ($settings) {
                return response()->json([
                    'success' => true,
                    'data' => $settings
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No settings found'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Update settings
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'firebase_key' => 'required|string|max:255',
                'currency' => 'required|string|max:10',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'copyright' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'contact' => 'required|string|max:20',
                'email' => 'required|email',
                'facebook' => 'nullable|url',
                'twitter' => 'nullable|url',
                'instagram' => 'nullable|url',
                'linkedin' => 'nullable|url',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            $settings = Settings::first();

            if (!$settings) {
                return response()->json([
                    'success' => false,
                    'message' => 'No settings found'
                ], 404);
            }
            $settings->firebase_key = $request->firebase_key;
            $settings->currency = $request->currency;
            $settings->copyright = $request->copyright;
            $settings->address = $request->address;
            $settings->contact = $request->contact;
            $settings->email = $request->email;
            $settings->facebook = $request->facebook;
            $settings->twitter = $request->twitter;
            $settings->instagram = $request->instagram;
            $settings->linkedin = $request->linkedin;
          

             if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                if ($settings->logo && Storage::exists('storage/app/public/images/category' . $settings->logo)) {
                    Storage::delete('storage/app/public/images/logo' . $settings->logo);
                }
                $logo = $request->file('logo');
                $logoName = 'logo-' . uniqid() . '.' . $logo->getClientOriginalExtension();
                $logo->move(public_path('storage/app/public/images/logo'), $logoName);
                $settings->logo = $logoName;
            }

            $settings->save();
            return response()->json([
                'success' => true,
                'message' => 'Settings updated successfully'
            ], 200);
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