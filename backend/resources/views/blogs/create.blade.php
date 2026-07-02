@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="mb-6">
            <nav class="flex text-xs text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}" class="hover:text-gray-700">Dashboard</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-[10px] mx-2 text-gray-400"></i>
                            <a href="{{ route('blogs.index') }}" class="hover:text-gray-700">All Blogs</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="fas fa-chevron-right text-[10px] mx-2 text-gray-400"></i>
                            <span class="text-gray-900 font-medium">Add New Blog</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-bold text-gray-900">Create New Blog Post</h1>
        </div>

        <form id="blogForm" action="{{ route('blogs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Columns: Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Basic Information</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Blog Title <span class="text-red-500">*</span></label>
                                <input type="text" name="title" id="title" placeholder="Enter an engaging blog title"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Slug (Auto-generated)</label>
                                <input type="text" name="slug" id="slug" placeholder="blog-post-slug"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-gray-50 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Short Description</label>
                                <textarea name="short_description" rows="3" placeholder="Provide a brief summary of the blog post..."
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:outline-none"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Full Blog Content <span class="text-red-500">*</span></label>
                                <textarea name="content" id="mytextarea" class="w-full border border-gray-300 rounded"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Travel Information -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Travel Details (Optional)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Travel Type</label>
                                <input type="text" name="travel_type" placeholder="e.g. Adventure, Family"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Destination</label>
                                <input type="text" name="destination" placeholder="e.g. Manali"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">State</label>
                                <input type="text" name="state" placeholder="e.g. Himachal Pradesh"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">City</label>
                                <input type="text" name="city" placeholder="e.g. Manali"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Best Time to Visit</label>
                                <input type="text" name="best_time" placeholder="e.g. Oct to Mar"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Estimated Budget (₹)</label>
                                <input type="number" step="0.01" name="estimated_budget" placeholder="e.g. 5000"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Trip Duration</label>
                                <input type="text" name="trip_duration" placeholder="e.g. 3 Days / 2 Nights"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Distance (km)</label>
                                <input type="text" name="distance" placeholder="e.g. 250 km"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Google Map Embed Link</label>
                                <input type="url" name="google_map" placeholder="https://maps.google.com/..."
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Sidebar settings & uploads -->
                <div class="space-y-6">
                    <!-- Images -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Blog Media</h2>
                        <div class="space-y-4">
                            <!-- Featured Image -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Featured Image <span class="text-red-500">*</span></label>
                                <input type="file" name="featured_image" id="featuredImageInput" accept="image/*"
                                    class="w-full border border-gray-300 rounded px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <p class="text-[10px] text-gray-400 mt-1">Image size max 2MB. Format: JPG, PNG, WEBP, GIF.</p>
                                <div id="featuredPreviewContainer" class="hidden mt-3 border border-gray-100 rounded-lg overflow-hidden h-40 bg-gray-50 flex items-center justify-center relative">
                                    <img id="featuredPreview" class="w-full h-full object-cover" src="">
                                    <button type="button" id="removeFeaturedBtn" class="absolute top-2 right-2 w-7 h-7 bg-red-600 text-white rounded-full flex items-center justify-center shadow-md hover:bg-red-700 transition">
                                        &times;
                                    </button>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            <!-- Gallery Images -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Gallery Images</label>
                                <input type="file" name="gallery_images[]" id="galleryImagesInput" accept="image/*" multiple
                                    class="w-full border border-gray-300 rounded px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <div id="galleryPreviews" class="grid grid-cols-3 gap-2 mt-3"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Category & Tags -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Taxonomy</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                                <select name="category_id" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Tags</label>
                                <select name="tags[]" class="w-full border select2 border-gray-300 rounded px-3 py-2 text-xs" multiple>
                                    @foreach ($tags as $tag)
                                        <option value="{{ $tag->id }}">{{ $tag->tag_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- SEO settings -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">SEO Configuration</h2>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-semibold text-gray-700">SEO Title</label>
                                    <span id="seoTitleCount" class="text-[10px] text-gray-400">0/70</span>
                                </div>
                                <input type="text" name="seo_title" id="seoTitle" placeholder="SEO optimized title" maxlength="70"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-semibold text-gray-700">Meta Description</label>
                                    <span id="metaDescCount" class="text-[10px] text-gray-400">0/160</span>
                                </div>
                                <textarea name="meta_description" id="metaDesc" rows="3" placeholder="SEO optimized description" maxlength="160"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Meta Keywords</label>
                                <input type="text" name="meta_keywords" placeholder="e.g. traveling, cab booking, luxury"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Publishing details -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Publish & Status</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Author</label>
                                <input type="text" name="author" value="{{ $defaults['default_author'] }}"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                                <select name="status" id="blogStatus" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    <option value="Draft" {{ $defaults['default_blog_status'] === 'Draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="Published" {{ $defaults['default_blog_status'] === 'Published' ? 'selected' : '' }}>Published</option>
                                    <option value="Scheduled" {{ $defaults['default_blog_status'] === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                </select>
                            </div>
                            <div id="publishDateWrapper" class="{{ $defaults['default_blog_status'] === 'Scheduled' ? '' : 'hidden' }}">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Scheduled Publish Date</label>
                                <input type="datetime-local" name="publish_date" id="publish_date"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <hr class="border-gray-100">

                            <!-- Feature Toggle -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="block text-xs font-bold text-gray-800">Featured Blog</label>
                                    <span class="text-[10px] text-gray-400">Display this post as featured.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="featured" class="sr-only peer" {{ $defaults['enable_featured_blogs'] == 1 ? 'checked' : '' }}>
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Comment Toggle -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <label class="block text-xs font-bold text-gray-800">Allow Comments</label>
                                    <span class="text-[10px] text-gray-400">Enable comment submissions.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="allow_comments" class="sr-only peer" {{ $defaults['enable_comments'] == 1 ? 'checked' : '' }}>
                                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Footer -->
            <div class="bg-white border border-gray-100 shadow-sm rounded-xl p-5 mt-6 flex justify-end gap-3">
                <a href="{{ route('blogs.index') }}" class="px-5 py-2.5 border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-md text-xs font-bold transition">
                    Cancel
                </a>
                <button type="button" id="saveDraftBtn" class="px-5 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-md text-xs font-bold transition shadow">
                    Save Draft
                </button>
                <button type="submit" id="submitBtn" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-bold transition shadow">
                    Publish Blog
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Select2 Tag init
            $('.select2').select2({
                placeholder: "Select tags"
            });

            // Toggle scheduled date picker
            $('#blogStatus').on('change', function() {
                const val = $(this).val();
                if (val === 'Scheduled') {
                    $('#publishDateWrapper').removeClass('hidden');
                } else {
                    $('#publishDateWrapper').addClass('hidden');
                }

                if (val === 'Draft') {
                    $('#submitBtn').text('Save Draft');
                } else if (val === 'Scheduled') {
                    $('#submitBtn').text('Schedule Post');
                } else {
                    $('#submitBtn').text('Publish Blog');
                }
            });

            // Auto slug generator (optional setting)
            const autoGenerateSlug = {{ $defaults['auto_generate_slug'] }};
            if (autoGenerateSlug) {
                $('#title').on('input', function() {
                    let val = $(this).val().toLowerCase()
                        .replace(/[^a-z0-9 -]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-');
                    $('#slug').val(val);
                });
            }

            // SEO Counters
            $('#seoTitle').on('input', function() {
                $('#seoTitleCount').text($(this).val().length + '/70');
            });
            $('#metaDesc').on('input', function() {
                $('#metaDescCount').text($(this).val().length + '/160');
            });

            // Featured Image Preview
            $('#featuredImageInput').on('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    // Check file size (2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        $.toastr.error('Featured image must not exceed 2MB.');
                        $(this).val('');
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $('#featuredPreview').attr('src', e.target.result);
                        $('#featuredPreviewContainer').removeClass('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

            $('#removeFeaturedBtn').on('click', function () {
                $('#featuredImageInput').val('');
                $('#featuredPreviewContainer').addClass('hidden');
                $('#featuredPreview').attr('src', '');
            });

            // Gallery Previews
            $('#galleryImagesInput').on('change', function (e) {
                const files = e.target.files;
                $('#galleryPreviews').html('');
                if (files) {
                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];
                        if (file.size > 2 * 1024 * 1024) {
                            $.toastr.error(`Gallery image "${file.name}" exceeds 2MB limit.`);
                            continue;
                        }
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#galleryPreviews').append(`
                                <div class="relative border rounded overflow-hidden h-20 bg-gray-50 flex items-center justify-center">
                                    <img src="${e.target.result}" class="w-full h-full object-cover">
                                </div>
                            `);
                        }
                        reader.readAsDataURL(file);
                    }
                }
            });

            // Save Draft button shortcut
            $('#saveDraftBtn').on('click', function() {
                $('#blogStatus').val('Draft').trigger('change');
                $('#blogForm').submit();
            });

            // AJAX Form Submit
            $('#blogForm').on('submit', function(e) {
                e.preventDefault();
                
                // Sync TinyMCE content
                if (typeof tinymce !== 'undefined') {
                    tinymce.triggerSave();
                }

                const form = this;
                const formData = new FormData(form);

                $.ajax({
                    url: $(form).attr('action'),
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $("#submitBtn, #saveDraftBtn").prop('disabled', true);
                        $("#submitBtn").html('<span class="animate-spin inline-block w-4 h-4 border-t-2 border-white rounded-full mr-2"></span> Processing...');
                    },
                    success: function (response) {
                        if (response.success) {
                            $.toastr.success(response.message);
                            window.location.href = "{{ route('blogs.index') }}";
                        } else {
                            $.toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        $('.error-text').remove();
                        $('input, select, textarea').removeClass('border-red-500');
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function (key, val) {
                                // Match array formats e.g. gallery_images.0
                                const fieldKey = key.replace(/\./g, '\\.');
                                const input = $(`[name="${key}"], [name="${key}[]"], #${fieldKey}`);
                                if (input.length) {
                                    input.addClass('border-red-500');
                                    input.after(`<span class="text-red-500 text-xs error-text mt-1 block">${val[0]}</span>`);
                                }
                                $.toastr.error(val[0]);
                            });
                            // Scroll to first error
                            const firstErr = $('.border-red-500').first();
                            if (firstErr.length) {
                                $('html, body').animate({ scrollTop: firstErr.offset().top - 120 }, 400);
                            }
                        } else {
                            $.toastr.error("Something went wrong!");
                        }
                    },
                    complete: function () {
                        $("#submitBtn, #saveDraftBtn").prop('disabled', false);
                        const status = $('#blogStatus').val();
                        if (status === 'Draft') {
                            $('#submitBtn').text('Save Draft');
                        } else if (status === 'Scheduled') {
                            $('#submitBtn').text('Schedule Post');
                        } else {
                            $('#submitBtn').text('Publish Blog');
                        }
                    }
                });
            });
        });
    </script>
@endsection
