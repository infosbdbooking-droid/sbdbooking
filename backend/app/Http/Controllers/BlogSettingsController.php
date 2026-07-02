<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BlogSettingsController extends Controller
{
    private $settingsFile = 'blog_settings.json';

    // Get current settings
    public function getSettings()
    {
        if (Storage::disk('local')->exists($this->settingsFile)) {
            $content = Storage::disk('local')->get($this->settingsFile);
            return json_decode($content, true);
        }

        return [
            'default_author' => 'Admin',
            'default_blog_status' => 'Draft',
            'default_seo_title' => 'SBD Booking - Travel & Cab Services',
            'default_meta_description' => 'Explore professional travel and cab booking services.',
            'blogs_per_page' => 10,
            'enable_comments' => 1,
            'auto_generate_slug' => 1,
            'enable_featured_blogs' => 1,
            'enable_social_share_buttons' => 1,
        ];
    }

    // Return view
    public function index()
    {
        $settings = $this->getSettings();
        return view('blogs.settings', compact('settings'));
    }

    // Update settings
    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'default_author' => 'nullable|string|max:150',
                'default_blog_status' => 'required|in:Draft,Published,Scheduled',
                'default_seo_title' => 'nullable|string|max:255',
                'default_meta_description' => 'nullable|string',
                'blogs_per_page' => 'required|integer|min:1|max:100',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $settings = [
                'default_author' => $request->default_author ?? 'Admin',
                'default_blog_status' => $request->default_blog_status,
                'default_seo_title' => $request->default_seo_title ?? '',
                'default_meta_description' => $request->default_meta_description ?? '',
                'blogs_per_page' => (int)$request->blogs_per_page,
                'enable_comments' => $request->has('enable_comments') ? 1 : 0,
                'auto_generate_slug' => $request->has('auto_generate_slug') ? 1 : 0,
                'enable_featured_blogs' => $request->has('enable_featured_blogs') ? 1 : 0,
                'enable_social_share_buttons' => $request->has('enable_social_share_buttons') ? 1 : 0,
            ];

            Storage::disk('local')->put($this->settingsFile, json_encode($settings, JSON_PRETTY_PRINT));

            return response()->json([
                'success' => true,
                'message' => 'Blog Settings updated successfully.'
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
