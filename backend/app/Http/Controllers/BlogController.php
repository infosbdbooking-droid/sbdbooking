<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\BlogComment;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class BlogController extends Controller
{
    // Dashboard page statistics
    public function dashboard()
    {
        $stats = [
            'total_blogs' => Blog::count(),
            'published_blogs' => Blog::where('status', 'Published')->count(),
            'draft_blogs' => Blog::where('status', 'Draft')->count(),
            'scheduled_blogs' => Blog::where('status', 'Scheduled')->count(),
            'featured_blogs' => Blog::where('featured', 1)->count(),
            'total_categories' => BlogCategory::count(),
            'total_tags' => BlogTag::count(),
            'pending_comments' => BlogComment::where('status', 'Pending')->count(),
            'approved_comments' => BlogComment::where('status', 'Approved')->count(),
            'total_views' => Blog::sum('view_count'),
        ];

        $recentBlogs = Blog::with('category')->orderBy('id', 'desc')->limit(5)->get();
        $recentComments = BlogComment::with('blog')->orderBy('id', 'desc')->limit(5)->get();
        $mostViewedBlogs = Blog::with('category')->orderBy('view_count', 'desc')->limit(5)->get();
        $latestPublished = Blog::with('category')->where('status', 'Published')->orderBy('published_at', 'desc')->limit(5)->get();

        return view('blogs.dashboard', compact('stats', 'recentBlogs', 'recentComments', 'mostViewedBlogs', 'latestPublished'));
    }

    // Return all blogs view
    public function index()
    {
        $categories = BlogCategory::where('status', 1)->get();
        $authors = Blog::select('author')->whereNotNull('author')->distinct()->pluck('author');
        return view('blogs.index', compact('categories', 'authors'));
    }

    // Ajax data for Datatables (with filters)
    public function getData(Request $request)
    {
        try {
            $query = Blog::with('category')->select('blogs.*');

            // Apply Filters
            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            if ($request->filled('destination')) {
                $query->where('destination', 'like', '%' . $request->destination . '%');
            }
            if ($request->filled('author')) {
                $query->where('author', $request->author);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('category_name', function ($row) {
                    return $row->category ? $row->category->category_name : 'N/A';
                })
                ->addColumn('formatted_publish_date', function ($row) {
                    return $row->published_at ? $row->published_at->format('Y-m-d H:i') : '-';
                })
                ->make(true);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Show create form
    public function create()
    {
        $categories = BlogCategory::where('status', 1)->get();
        $tags = BlogTag::all();

        // Get default author / SEO from settings if exists
        $settingsFile = 'blog_settings.json';
        $defaults = [
            'default_author' => 'Admin',
            'default_blog_status' => 'Draft',
            'default_seo_title' => '',
            'default_meta_description' => '',
            'auto_generate_slug' => 1,
            'enable_comments' => 1,
            'enable_featured_blogs' => 0
        ];
        if (\Illuminate\Support\Facades\Storage::disk('local')->exists($settingsFile)) {
            $defaults = array_merge($defaults, json_decode(\Illuminate\Support\Facades\Storage::disk('local')->get($settingsFile), true));
        }

        return view('blogs.create', compact('categories', 'tags', 'defaults'));
    }

    // Store new blog post
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:blogs,slug',
                'category_id' => 'required|exists:blog_categories,id',
                'content' => 'required|string',
                'featured_image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
                'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
                'status' => 'required|in:Draft,Published,Scheduled',
                'publish_date' => 'nullable|date',
                'estimated_budget' => 'nullable|numeric|min:0',
                'seo_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'meta_keywords' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $blog = new Blog();
            $blog->title = $request->title;
            $blog->slug = Str::slug($request->slug);
            $blog->short_description = $request->short_description;
            $blog->content = $request->content;
            $blog->category_id = $request->category_id;
            
            // Format tags
            $blog->tags = $request->tags ? implode(',', $request->tags) : null;

            // Travel information
            $blog->travel_type = $request->travel_type;
            $blog->destination = $request->destination;
            $blog->state = $request->state;
            $blog->city = $request->city;
            $blog->best_time = $request->best_time;
            $blog->estimated_budget = $request->estimated_budget;
            $blog->trip_duration = $request->trip_duration;
            $blog->distance = $request->distance;
            $blog->google_map = $request->google_map;

            // SEO Info
            $blog->seo_title = $request->seo_title;
            $blog->meta_description = $request->meta_description;
            $blog->meta_keywords = $request->meta_keywords;

            // Publishing
            $blog->author = $request->author ?? 'Admin';
            $blog->status = $request->status;
            $blog->featured = $request->has('featured') ? 1 : 0;
            $blog->allow_comments = $request->has('allow_comments') ? 1 : 0;
            $blog->published_at = $request->status === 'Published' ? now() : ($request->publish_date ?? null);

            // Save Featured Image
            if ($request->hasFile('featured_image')) {
                $file = $request->file('featured_image');
                $name = 'blog-feat-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/blogs/featured'), $name);
                $blog->featured_image = $name;
            }

            // Save Gallery Images
            $gallery = [];
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $name = 'blog-gal-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('images/blogs/gallery'), $name);
                    $gallery[] = $name;
                }
            }
            $blog->gallery_images = $gallery;

            $blog->save();

            return response()->json([
                'success' => true,
                'message' => 'Blog post created successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Show details (read-only)
    public function show($id)
    {
        $blog = Blog::with('category')->findOrFail($id);
        $tags = BlogTag::all();
        return view('blogs.show', compact('blog', 'tags'));
    }

    // Edit form
    public function edit($id)
    {
        $blog = Blog::findOrFail($id);
        $categories = BlogCategory::where('status', 1)->get();
        $tags = BlogTag::all();
        $selectedTags = $blog->tag_ids;

        return view('blogs.edit', compact('blog', 'categories', 'tags', 'selectedTags'));
    }

    // Update blog post
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:blogs,slug,' . $id,
                'category_id' => 'required|exists:blog_categories,id',
                'content' => 'required|string',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
                'gallery_images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
                'status' => 'required|in:Draft,Published,Scheduled',
                'publish_date' => 'nullable|date',
                'estimated_budget' => 'nullable|numeric|min:0',
                'seo_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:500',
                'meta_keywords' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $blog = Blog::findOrFail($id);
            $blog->title = $request->title;
            $blog->slug = Str::slug($request->slug);
            $blog->short_description = $request->short_description;
            $blog->content = $request->content;
            $blog->category_id = $request->category_id;
            
            // Format tags
            $blog->tags = $request->tags ? implode(',', $request->tags) : null;

            // Travel information
            $blog->travel_type = $request->travel_type;
            $blog->destination = $request->destination;
            $blog->state = $request->state;
            $blog->city = $request->city;
            $blog->best_time = $request->best_time;
            $blog->estimated_budget = $request->estimated_budget;
            $blog->trip_duration = $request->trip_duration;
            $blog->distance = $request->distance;
            $blog->google_map = $request->google_map;

            // SEO Info
            $blog->seo_title = $request->seo_title;
            $blog->meta_description = $request->meta_description;
            $blog->meta_keywords = $request->meta_keywords;

            // Publishing
            $blog->author = $request->author ?? 'Admin';
            $blog->status = $request->status;
            $blog->featured = $request->has('featured') ? 1 : 0;
            $blog->allow_comments = $request->has('allow_comments') ? 1 : 0;

            if ($request->status === 'Published' && $blog->status !== 'Published') {
                $blog->published_at = now();
            } elseif ($request->filled('publish_date')) {
                $blog->published_at = $request->publish_date;
            }

            // Update Featured Image
            if ($request->hasFile('featured_image')) {
                // Delete old
                if ($blog->featured_image && file_exists(public_path('images/blogs/featured/' . $blog->featured_image))) {
                    unlink(public_path('images/blogs/featured/' . $blog->featured_image));
                }

                $file = $request->file('featured_image');
                $name = 'blog-feat-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/blogs/featured'), $name);
                $blog->featured_image = $name;
            }

            // Handle deleted gallery images
            $existingGallery = $blog->gallery_images ?? [];
            if ($request->filled('delete_gallery_images')) {
                $toDelete = $request->delete_gallery_images;
                foreach ($toDelete as $delImage) {
                    if (in_array($delImage, $existingGallery)) {
                        if (file_exists(public_path('images/blogs/gallery/' . $delImage))) {
                            unlink(public_path('images/blogs/gallery/' . $delImage));
                        }
                    }
                }
                $existingGallery = array_diff($existingGallery, $toDelete);
            }

            // Upload new gallery images
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $name = 'blog-gal-' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('images/blogs/gallery'), $name);
                    $existingGallery[] = $name;
                }
            }
            $blog->gallery_images = array_values($existingGallery);

            $blog->save();

            return response()->json([
                'success' => true,
                'message' => 'Blog post updated successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Duplicate blog post
    public function duplicate($id)
    {
        try {
            $original = Blog::findOrFail($id);
            $clone = $original->replicate();
            $clone->title = $original->title . ' - Copy';
            
            // Unique slug
            $slug = Str::slug($clone->title);
            $slugCount = Blog::where('slug', 'like', $slug . '%')->count();
            if ($slugCount > 0) {
                $slug .= '-' . ($slugCount + 1);
            }
            $clone->slug = $slug;
            $clone->status = 'Draft';
            $clone->view_count = 0;
            $clone->like_count = 0;
            $clone->share_count = 0;
            $clone->featured = 0;
            $clone->save();

            return response()->json([
                'success' => true,
                'message' => 'Blog post duplicated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate blog.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Toggle featured
    public function changeFeatured(Request $request, $id)
    {
        try {
            $blog = Blog::findOrFail($id);
            $blog->featured = (int)$request->featured;
            $blog->save();

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
            $blog = Blog::findOrFail($id);
            $blog->status = $request->status;
            if ($request->status === 'Published') {
                $blog->published_at = now();
            }
            $blog->save();

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

    // Delete blog post
    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            
            // Delete images
            if ($blog->featured_image && file_exists(public_path('images/blogs/featured/' . $blog->featured_image))) {
                unlink(public_path('images/blogs/featured/' . $blog->featured_image));
            }
            if ($blog->gallery_images) {
                foreach ($blog->gallery_images as $image) {
                    if (file_exists(public_path('images/blogs/gallery/' . $image))) {
                        unlink(public_path('images/blogs/gallery/' . $image));
                    }
                }
            }

            $blog->delete();

            return response()->json([
                'success' => true,
                'message' => 'Blog post deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete blog post.',
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
                    'message' => 'No blogs selected.'
                ], 422);
            }

            if ($action === 'delete') {
                $blogs = Blog::whereIn('id', $ids)->get();
                foreach ($blogs as $blog) {
                    if ($blog->featured_image && file_exists(public_path('images/blogs/featured/' . $blog->featured_image))) {
                        unlink(public_path('images/blogs/featured/' . $blog->featured_image));
                    }
                    if ($blog->gallery_images) {
                        foreach ($blog->gallery_images as $image) {
                            if (file_exists(public_path('images/blogs/gallery/' . $image))) {
                                unlink(public_path('images/blogs/gallery/' . $image));
                            }
                        }
                    }
                    $blog->delete();
                }
                $msg = 'Selected blogs deleted successfully.';
            } elseif ($action === 'publish') {
                Blog::whereIn('id', $ids)->update(['status' => 'Published', 'published_at' => now()]);
                $msg = 'Selected blogs published.';
            } elseif ($action === 'draft') {
                Blog::whereIn('id', $ids)->update(['status' => 'Draft']);
                $msg = 'Selected blogs moved to draft.';
            } elseif ($action === 'feature') {
                Blog::whereIn('id', $ids)->update(['featured' => 1]);
                $msg = 'Selected blogs set as featured.';
            } elseif ($action === 'unfeature') {
                Blog::whereIn('id', $ids)->update(['featured' => 0]);
                $msg = 'Selected blogs removed from featured.';
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid bulk action.'
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
