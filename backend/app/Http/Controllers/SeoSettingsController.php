<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SeoSettingsController extends Controller
{
    private $settingsFile = 'seo_settings.json';

    public function getSettings()
    {
        if (Storage::disk('local')->exists($this->settingsFile)) {
            $content = Storage::disk('local')->get($this->settingsFile);
            return json_decode($content, true) ?: [];
        }

        return [
            'default_author' => 'SEO Admin',
            'default_page_status' => 'Draft',
            'default_banner' => null,
            'pages_per_page' => 15,
            'enable_featured_pages' => 1,
            'enable_view_counter' => 1,
            'enable_related_pages' => 1,
            'enable_whatsapp_button' => 1,
            'enable_call_button' => 1,
        ];
    }

    public function index()
    {
        $settings = $this->getSettings();
        return view('seo.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'default_author' => 'nullable|string|max:150',
                'default_page_status' => 'required|in:Draft,Published',
                'default_banner' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
                'pages_per_page' => 'required|integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $current = $this->getSettings();

            $bannerName = $current['default_banner'] ?? null;
            if ($request->hasFile('default_banner')) {
                // Delete old default banner
                if ($bannerName && file_exists(public_path('images/seo/defaults/' . $bannerName))) {
                    unlink(public_path('images/seo/defaults/' . $bannerName));
                }

                $file = $request->file('default_banner');
                $bannerName = 'default-banner-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/seo/defaults'), $bannerName);
            }

            $settings = [
                'default_author' => $request->default_author ?? 'SEO Admin',
                'default_page_status' => $request->default_page_status,
                'default_banner' => $bannerName,
                'pages_per_page' => (int)$request->pages_per_page,
                'enable_featured_pages' => $request->has('enable_featured_pages') ? 1 : 0,
                'enable_view_counter' => $request->has('enable_view_counter') ? 1 : 0,
                'enable_related_pages' => $request->has('enable_related_pages') ? 1 : 0,
                'enable_whatsapp_button' => $request->has('enable_whatsapp_button') ? 1 : 0,
                'enable_call_button' => $request->has('enable_call_button') ? 1 : 0,
            ];

            Storage::disk('local')->put($this->settingsFile, json_encode($settings, JSON_PRETTY_PRINT));

            return response()->json([
                'success' => true,
                'message' => 'SEO Settings updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save settings.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
