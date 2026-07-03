<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\BlogCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    // Fetch active/published blogs
    public function getActiveBlogs(Request $request)
    {
        try {
            $query = Blog::with('category')->where('status', 'Published');

            // Apply search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('short_description', 'like', "%{$search}%");
                });
            }

            // Apply category filter
            if ($request->filled('category')) {
                $categorySlug = $request->category;
                $query->whereHas('category', function($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            } elseif ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            // Apply tag filter
            if ($request->filled('tag_id')) {
                $tagId = $request->tag_id;
                $query->whereRaw("FIND_IN_SET(?, tags)", [$tagId]);
            }

            // Paginate results based on settings or default
            $settingsFile = 'blog_settings.json';
            $perPage = 10;
            if (Storage::disk('local')->exists($settingsFile)) {
                $settings = json_decode(Storage::disk('local')->get($settingsFile), true);
                $perPage = $settings['blogs_per_page'] ?? 10;
            }
            if ($request->filled('per_page')) {
                $perPage = (int)$request->per_page;
            }

            $blogs = $query->orderBy('id', 'desc')->paginate($perPage);

            // Transform images to full URLs
            $blogs->getCollection()->transform(function ($blog) {
                if ($blog->featured_image) {
                    $blog->featured_image_url = asset('images/blogs/featured/' . $blog->featured_image);
                } else {
                    $blog->featured_image_url = null;
                }

                $galleryUrls = [];
                if ($blog->gallery_images) {
                    foreach ($blog->gallery_images as $img) {
                        $galleryUrls[] = asset('images/blogs/gallery/' . $img);
                    }
                }
                $blog->gallery_images_urls = $galleryUrls;
                return $blog;
            });

            return response()->json([
                'status' => 1,
                'message' => $blogs->isEmpty() ? 'No blogs found' : 'Blogs fetched successfully',
                'data' => $blogs
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Fetch single blog detail by slug
    public function getBlogDetail(Request $request, $slug)
    {
        try {
            $blog = Blog::with('category')->where('slug', $slug)->where('status', 'Published')->first();

            if (!$blog) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Blog post not found.'
                ], 404);
            }

            // Increment views
            $blog->increment('view_count');

            // Format images
            if ($blog->featured_image) {
                $blog->featured_image_url = asset('images/blogs/featured/' . $blog->featured_image);
            } else {
                $blog->featured_image_url = null;
            }

            $galleryUrls = [];
            if ($blog->gallery_images) {
                foreach ($blog->gallery_images as $img) {
                    $galleryUrls[] = asset('images/blogs/gallery/' . $img);
                }
            }
            $blog->gallery_images_urls = $galleryUrls;

            // Load approved comments
            $comments = BlogComment::where('blog_id', $blog->id)
                ->where('status', 'Approved')
                ->orderBy('id', 'desc')
                ->get();

            // Load tags
            $tags = $blog->tags_models;

            // Related blogs
            $related = Blog::where('category_id', $blog->category_id)
                ->where('id', '!=', $blog->id)
                ->where('status', 'Published')
                ->orderBy('id', 'desc')
                ->limit(3)
                ->get();

            $related->transform(function ($item) {
                if ($item->featured_image) {
                    $item->featured_image_url = asset('images/blogs/featured/' . $item->featured_image);
                }
                return $item;
            });

            return response()->json([
                'status' => 1,
                'message' => 'Blog details fetched successfully',
                'data' => [
                    'blog' => $blog,
                    'comments' => $comments,
                    'tags' => $tags,
                    'related' => $related
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

    // Submit a blog comment
    public function addComment(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:150',
                'email' => 'required|email|max:150',
                'comment' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors()
                ], 422);
            }

            $blog = Blog::find($id);
            if (!$blog) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Blog post not found.'
                ], 404);
            }

            // Check if commenting is allowed in settings
            $settingsFile = 'blog_settings.json';
            $enableComments = 1;
            if (Storage::disk('local')->exists($settingsFile)) {
                $settings = json_decode(Storage::disk('local')->get($settingsFile), true);
                $enableComments = $settings['enable_comments'] ?? 1;
            }

            if (!$enableComments || !$blog->allow_comments) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Comments are disabled for this post.'
                ], 400);
            }

            $comment = new BlogComment();
            $comment->blog_id = $id;
            $comment->name = $request->name;
            $comment->email = $request->email;
            $comment->comment = $request->comment;
            $comment->status = 'Pending'; // pending admin moderation
            $comment->save();

            return response()->json([
                'status' => 1,
                'message' => 'Comment submitted successfully and is awaiting moderation.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to submit comment.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get categories listing for filter/widget
    public function getCategories()
    {
        try {
            $categories = BlogCategory::where('status', 1)->get();
            return response()->json([
                'status' => 1,
                'message' => 'Categories fetched',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to fetch categories.'
            ], 500);
        }
    }
}
