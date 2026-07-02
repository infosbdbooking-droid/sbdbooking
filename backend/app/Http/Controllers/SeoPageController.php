<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SeoPage;
use App\Models\SeoServiceCategory;
use App\Models\SeoState;
use App\Models\SeoCity;
use App\Models\SeoRoute;
use App\Models\SeoFaq;
use App\Models\Blog;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class SeoPageController extends Controller
{
    // Dashboard analytics
    public function dashboard()
    {
        $stats = [
            'total_pages' => SeoPage::count(),
            'published_pages' => SeoPage::where('status', 'Published')->count(),
            'draft_pages' => SeoPage::where('status', 'Draft')->count(),
            'featured_pages' => SeoPage::where('featured', 1)->count(),
            'total_views' => SeoPage::sum('view_count'),
            'total_categories' => SeoServiceCategory::count(),
            'total_cities' => SeoCity::count(),
            'total_routes' => SeoRoute::count(),
            'total_faqs' => SeoFaq::count(),
        ];

        $latestPages = SeoPage::with(['category', 'state', 'city'])->orderBy('id', 'desc')->limit(5)->get();
        $mostViewedPages = SeoPage::with(['category', 'state', 'city'])->orderBy('view_count', 'desc')->limit(5)->get();
        $latestPublished = SeoPage::with(['category', 'state', 'city'])->where('status', 'Published')->orderBy('published_at', 'desc')->limit(5)->get();

        return view('seo.dashboard', compact('stats', 'latestPages', 'mostViewedPages', 'latestPublished'));
    }

    // List view
    public function index()
    {
        $categories = SeoServiceCategory::where('status', 1)->get();
        $states = SeoState::where('status', 1)->get();
        $cities = SeoCity::where('status', 1)->get();
        return view('seo.index', compact('categories', 'states', 'cities'));
    }

    // DataTable provider
    public function getData(Request $request)
    {
        try {
            $query = SeoPage::with(['category', 'state', 'city', 'route']);

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            if ($request->filled('state_id')) {
                $query->where('state_id', $request->state_id);
            }
            if ($request->filled('city_id')) {
                $query->where('city_id', $request->city_id);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    return $row->category ? $row->category->category_name : 'N/A';
                })
                ->addColumn('state_name', function ($row) {
                    return $row->state ? $row->state->state_name : 'N/A';
                })
                ->addColumn('city_name', function ($row) {
                    return $row->city ? $row->city->city_name : 'N/A';
                })
                ->addColumn('route_name', function ($row) {
                    return $row->route ? $row->route->route_name : 'N/A';
                })
                ->addColumn('formatted_publish_date', function ($row) {
                    return $row->published_at ? $row->published_at->format('Y-m-d H:i') : '-';
                })
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch pages.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Create page form
    public function create()
    {
        $categories = SeoServiceCategory::where('status', 1)->get();
        $states = SeoState::where('status', 1)->get();
        $routes = SeoRoute::with(['fromCity', 'toCity'])->where('status', 1)->get();
        $blogs = Blog::where('status', 'Published')->get();
        $seoPages = SeoPage::where('status', 'Published')->get();

        // Get settings defaults
        $settingsController = new SeoSettingsController();
        $defaults = $settingsController->getSettings();

        return view('seo.create', compact('categories', 'states', 'routes', 'blogs', 'seoPages', 'defaults'));
    }

    // Store new landing page
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:seo_pages,slug',
                'content' => 'required|string',
                'category_id' => 'required|exists:seo_service_categories,id',
                'state_id' => 'required|exists:seo_states,id',
                'city_id' => 'required|exists:seo_cities,id',
                'route_id' => 'nullable|exists:seo_routes,id',
                'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
                'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
                'starting_price' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $page = new SeoPage();
            $page->title = $request->title;
            $page->slug = Str::slug($request->slug);
            $page->short_description = $request->short_description;
            $page->content = $request->content;
            $page->category_id = $request->category_id;
            $page->state_id = $request->state_id;
            $page->city_id = $request->city_id;
            $page->route_id = $request->route_id;
            $page->pickup_location = $request->pickup_location;
            $page->destination_location = $request->destination_location;
            $page->best_time_to_visit = $request->best_time_to_visit;
            $page->starting_price = $request->starting_price ?? 0.00;

            // SEO Metadata
            $page->meta_title = $request->meta_title;
            $page->meta_description = $request->meta_description;
            $page->meta_keywords = $request->meta_keywords;
            $page->canonical_url = $request->canonical_url;
            $page->schema_type = $request->schema_type ?? 'LocalBusiness';

            // Publish status
            $page->author = $request->author ?? 'SEO Admin';
            $page->status = $request->status;
            $page->featured = $request->has('featured') ? 1 : 0;
            $page->published_at = $request->status === 'Published' ? now() : null;

            // Upload Banner Image
            if ($request->hasFile('banner_image')) {
                $file = $request->file('banner_image');
                $name = 'seo-banner-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/seo/banners'), $name);
                $page->banner_image = $name;
            }

            // Upload Featured Image
            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $name = 'seo-feat-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/seo/featured'), $name);
                $page->featured_image = $name;
            }

            // Upload Gallery Images
            $gallery = [];
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $name = 'seo-gal-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('images/seo/gallery'), $name);
                    $gallery[] = $name;
                }
            }
            $page->gallery_images = $gallery;

            $page->save();

            // Save Extended Data
            $extendedData = [
                'distance' => $request->distance,
                'estimated_travel_time' => $request->estimated_travel_time,
                'nearby_attractions' => $request->nearby_attractions,
                'nearby_railway_station' => $request->nearby_railway_station,
                'nearby_airport' => $request->nearby_airport,
                'nearby_bus_stand' => $request->nearby_bus_stand,
                'why_choose_us' => $request->why_choose_us,
                'services_included' => $request->services_included,
                'services_excluded' => $request->services_excluded,
                'faqs' => $request->faqs ?? [],
                'related_blogs' => $request->related_blogs ?? [],
                'related_pages' => $request->related_pages ?? [],
                'related_routes' => $request->related_routes ?? [],
                'cta_book_cab' => $request->has('cta_book_cab') ? 1 : 0,
                'cta_join_shared_trip' => $request->has('cta_join_shared_trip') ? 1 : 0,
                'cta_whatsapp' => $request->has('cta_whatsapp') ? 1 : 0,
                'cta_call_now' => $request->has('cta_call_now') ? 1 : 0,
            ];
            $page->saveExtendedData($extendedData);

            return response()->json([
                'success' => true,
                'message' => 'SEO Landing Page created successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create page.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Edit form
    public function edit($id)
    {
        $page = SeoPage::findOrFail($id);
        $categories = SeoServiceCategory::where('status', 1)->get();
        $states = SeoState::where('status', 1)->get();
        $cities = SeoCity::where('state_id', $page->state_id)->where('status', 1)->get();
        $routes = SeoRoute::with(['fromCity', 'toCity'])->where('status', 1)->get();
        $blogs = Blog::where('status', 'Published')->get();
        $seoPages = SeoPage::where('status', 'Published')->where('id', '!=', $id)->get();

        return view('seo.edit', compact('page', 'categories', 'states', 'cities', 'routes', 'blogs', 'seoPages'));
    }

    // Update landing page
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:seo_pages,slug,' . $id,
                'content' => 'required|string',
                'category_id' => 'required|exists:seo_service_categories,id',
                'state_id' => 'required|exists:seo_states,id',
                'city_id' => 'required|exists:seo_cities,id',
                'route_id' => 'nullable|exists:seo_routes,id',
                'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
                'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
                'starting_price' => 'nullable|numeric|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $page = SeoPage::findOrFail($id);
            $page->title = $request->title;
            $page->slug = Str::slug($request->slug);
            $page->short_description = $request->short_description;
            $page->content = $request->content;
            $page->category_id = $request->category_id;
            $page->state_id = $request->state_id;
            $page->city_id = $request->city_id;
            $page->route_id = $request->route_id;
            $page->pickup_location = $request->pickup_location;
            $page->destination_location = $request->destination_location;
            $page->best_time_to_visit = $request->best_time_to_visit;
            $page->starting_price = $request->starting_price ?? 0.00;

            // SEO Metadata
            $page->meta_title = $request->meta_title;
            $page->meta_description = $request->meta_description;
            $page->meta_keywords = $request->meta_keywords;
            $page->canonical_url = $request->canonical_url;
            $page->schema_type = $request->schema_type ?? 'LocalBusiness';

            // Publish status
            $page->author = $request->author ?? 'SEO Admin';
            if ($request->status === 'Published' && $page->status !== 'Published') {
                $page->published_at = now();
            }
            $page->status = $request->status;
            $page->featured = $request->has('featured') ? 1 : 0;

            // Upload Banner Image
            if ($request->hasFile('banner_image')) {
                if ($page->banner_image && file_exists(public_path('images/seo/banners/' . $page->banner_image))) {
                    unlink(public_path('images/seo/banners/' . $page->banner_image));
                }
                $file = $request->file('banner_image');
                $name = 'seo-banner-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/seo/banners'), $name);
                $page->banner_image = $name;
            }

            // Upload Featured Image
            if ($request->hasFile('featured_image')) {
                if ($page->featured_image && file_exists(public_path('images/seo/featured/' . $page->featured_image))) {
                    unlink(public_path('images/seo/featured/' . $page->featured_image));
                }
                $file = $request->file('featured_image');
                $name = 'seo-feat-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/seo/featured'), $name);
                $page->featured_image = $name;
            }

            // Handle deleted gallery images
            $existingGallery = $page->gallery_images ?? [];
            if ($request->filled('delete_gallery_images')) {
                $toDelete = $request->delete_gallery_images;
                foreach ($toDelete as $delImage) {
                    if (in_array($delImage, $existingGallery)) {
                        if (file_exists(public_path('images/seo/gallery/' . $delImage))) {
                            unlink(public_path('images/seo/gallery/' . $delImage));
                        }
                    }
                }
                $existingGallery = array_diff($existingGallery, $toDelete);
            }

            // Upload new gallery images
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $name = 'seo-gal-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('images/seo/gallery'), $name);
                    $existingGallery[] = $name;
                }
            }
            $page->gallery_images = array_values($existingGallery);

            $page->save();

            // Save Extended Data
            $extendedData = [
                'distance' => $request->distance,
                'estimated_travel_time' => $request->estimated_travel_time,
                'nearby_attractions' => $request->nearby_attractions,
                'nearby_railway_station' => $request->nearby_railway_station,
                'nearby_airport' => $request->nearby_airport,
                'nearby_bus_stand' => $request->nearby_bus_stand,
                'why_choose_us' => $request->why_choose_us,
                'services_included' => $request->services_included,
                'services_excluded' => $request->services_excluded,
                'faqs' => $request->faqs ?? [],
                'related_blogs' => $request->related_blogs ?? [],
                'related_pages' => $request->related_pages ?? [],
                'related_routes' => $request->related_routes ?? [],
                'cta_book_cab' => $request->has('cta_book_cab') ? 1 : 0,
                'cta_join_shared_trip' => $request->has('cta_join_shared_trip') ? 1 : 0,
                'cta_whatsapp' => $request->has('cta_whatsapp') ? 1 : 0,
                'cta_call_now' => $request->has('cta_call_now') ? 1 : 0,
            ];
            $page->saveExtendedData($extendedData);

            return response()->json([
                'success' => true,
                'message' => 'SEO Landing Page updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update page.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Read-only detail show
    public function show($id)
    {
        $page = SeoPage::with(['category', 'state', 'city', 'route'])->findOrFail($id);

        // Fetch related models if mapped
        $ext = $page->extended_data;
        $relatedBlogs = [];
        if (!empty($ext['related_blogs'])) {
            $relatedBlogs = Blog::whereIn('id', $ext['related_blogs'])->get();
        }
        $relatedPages = [];
        if (!empty($ext['related_pages'])) {
            $relatedPages = SeoPage::whereIn('id', $ext['related_pages'])->get();
        }
        $relatedRoutes = [];
        if (!empty($ext['related_routes'])) {
            $relatedRoutes = SeoRoute::with(['fromCity', 'toCity'])->whereIn('id', $ext['related_routes'])->get();
        }

        // Increment views if view tracker settings enable it
        $settingsController = new SeoSettingsController();
        $settings = $settingsController->getSettings();
        if ($settings['enable_view_counter'] ?? 1) {
            $page->increment('view_count');
        }

        return view('seo.show', compact('page', 'relatedBlogs', 'relatedPages', 'relatedRoutes'));
    }

    // Toggle featured status
    public function changeFeatured(Request $request, $id)
    {
        try {
            $page = SeoPage::findOrFail($id);
            $page->featured = (int)$request->featured;
            $page->save();

            return response()->json([
                'success' => true,
                'message' => 'Featured status updated.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update featured status.'
            ], 500);
        }
    }

    // Toggle status
    public function changeStatus(Request $request, $id)
    {
        try {
            $page = SeoPage::findOrFail($id);
            $page->status = $request->status;
            if ($request->status === 'Published') {
                $page->published_at = now();
            }
            $page->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status.'
            ], 500);
        }
    }

    // Duplicate page
    public function duplicate($id)
    {
        try {
            $original = SeoPage::findOrFail($id);
            $clone = $original->replicate();
            $clone->title = $original->title . ' - Copy';
            
            // Unique slug check
            $slug = Str::slug($clone->title);
            $slugCount = SeoPage::where('slug', 'like', $slug . '%')->count();
            if ($slugCount > 0) {
                $slug .= '-' . ($slugCount + 1);
            }
            $clone->slug = $slug;
            $clone->status = 'Draft';
            $clone->view_count = 0;
            $clone->featured = 0;
            $clone->save();

            // Duplicate JSON metadata file
            $metaData = $original->extended_data;
            $clone->saveExtendedData($metaData);

            return response()->json([
                'success' => true,
                'message' => 'SEO Page duplicated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate page.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Delete single page
    public function destroy($id)
    {
        try {
            $page = SeoPage::findOrFail($id);

            // Delete physical images
            if ($page->banner_image && file_exists(public_path('images/seo/banners/' . $page->banner_image))) {
                unlink(public_path('images/seo/banners/' . $page->banner_image));
            }
            if ($page->featured_image && file_exists(public_path('images/seo/featured/' . $page->featured_image))) {
                unlink(public_path('images/seo/featured/' . $page->featured_image));
            }
            if ($page->gallery_images) {
                foreach ($page->gallery_images as $image) {
                    if (file_exists(public_path('images/seo/gallery/' . $image))) {
                        unlink(public_path('images/seo/gallery/' . $image));
                    }
                }
            }

            // Delete metadata file
            $page->deleteExtendedData();

            $page->delete();

            return response()->json([
                'success' => true,
                'message' => 'SEO Page deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete SEO Page.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Bulk actions
    public function bulkAction(Request $request)
    {
        try {
            $ids = $request->ids;
            $action = $request->action;

            if (empty($ids) || !is_array($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No pages selected.'
                ], 422);
            }

            if ($action === 'delete') {
                $pages = SeoPage::whereIn('id', $ids)->get();
                foreach ($pages as $p) {
                    if ($p->banner_image && file_exists(public_path('images/seo/banners/' . $p->banner_image))) {
                        unlink(public_path('images/seo/banners/' . $p->banner_image));
                    }
                    if ($p->featured_image && file_exists(public_path('images/seo/featured/' . $p->featured_image))) {
                        unlink(public_path('images/seo/featured/' . $p->featured_image));
                    }
                    if ($p->gallery_images) {
                        foreach ($p->gallery_images as $img) {
                            if (file_exists(public_path('images/seo/gallery/' . $img))) {
                                unlink(public_path('images/seo/gallery/' . $img));
                            }
                        }
                    }
                    $p->deleteExtendedData();
                    $p->delete();
                }
                $msg = 'Selected SEO pages deleted.';
            } elseif ($action === 'publish') {
                SeoPage::whereIn('id', $ids)->update(['status' => 'Published', 'published_at' => now()]);
                $msg = 'Selected pages published.';
            } elseif ($action === 'draft') {
                SeoPage::whereIn('id', $ids)->update(['status' => 'Draft']);
                $msg = 'Selected pages moved to Draft.';
            } elseif ($action === 'feature') {
                SeoPage::whereIn('id', $ids)->update(['featured' => 1]);
                $msg = 'Selected pages featured.';
            } elseif ($action === 'unfeature') {
                SeoPage::whereIn('id', $ids)->update(['featured' => 0]);
                $msg = 'Selected pages unfeatured.';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid action.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => $msg
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to execute bulk action.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
