<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SeoPage;
use App\Models\SeoServiceCategory;
use App\Models\Blog;
use App\Models\SeoRoute;

class SeoPageController extends Controller
{
    public function getSeoPageDetail(Request $request, $slug)
    {
        try {
            $page = SeoPage::with(['category', 'state', 'city', 'route'])
                ->where('slug', $slug)
                ->where('status', 'Published')
                ->first();

            if (!$page) {
                return response()->json([
                    'status' => 0,
                    'message' => 'SEO page not found.'
                ], 404);
            }

            // Increment views
            $page->increment('view_count');

            // Format images to full URLs
            if ($page->banner_image) {
                $page->banner_image_url = asset('images/seo/banners/' . $page->banner_image);
            } else {
                $page->banner_image_url = null;
            }

            if ($page->featured_image) {
                $page->featured_image_url = asset('images/seo/featured/' . $page->featured_image);
            } else {
                $page->featured_image_url = null;
            }

            $galleryUrls = [];
            if ($page->gallery_images) {
                foreach ($page->gallery_images as $img) {
                    $galleryUrls[] = asset('images/seo/gallery/' . $img);
                }
            }
            $page->gallery_images_urls = $galleryUrls;

            // Fetch related models if mapped in JSON metadata
            $ext = $page->extended_data;
            
            $relatedBlogs = [];
            if (!empty($ext['related_blogs'])) {
                $relatedBlogs = Blog::whereIn('id', $ext['related_blogs'])
                    ->where('status', 'Published')
                    ->get();
                $relatedBlogs->transform(function ($item) {
                    if ($item->featured_image) {
                        $item->featured_image_url = asset('images/blogs/featured/' . $item->featured_image);
                    }
                    return $item;
                });
            }

            $relatedPages = [];
            if (!empty($ext['related_pages'])) {
                $relatedPages = SeoPage::whereIn('id', $ext['related_pages'])
                    ->where('status', 'Published')
                    ->get();
                $relatedPages->transform(function ($item) {
                    if ($item->featured_image) {
                        $item->featured_image_url = asset('images/seo/featured/' . $item->featured_image);
                    }
                    return $item;
                });
            }

            $relatedRoutes = [];
            if (!empty($ext['related_routes'])) {
                $relatedRoutes = SeoRoute::with(['fromCity', 'toCity'])
                    ->whereIn('id', $ext['related_routes'])
                    ->where('status', 1)
                    ->get();
            }

            // Categories list for top slider bar
            $categories = SeoServiceCategory::where('status', 1)->get();

            return response()->json([
                'status' => 1,
                'message' => 'SEO landing page details fetched successfully',
                'data' => [
                    'page' => $page,
                    'extended_data' => $ext,
                    'related_blogs' => $relatedBlogs,
                    'related_pages' => $relatedPages,
                    'related_routes' => $relatedRoutes,
                    'categories' => $categories
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
