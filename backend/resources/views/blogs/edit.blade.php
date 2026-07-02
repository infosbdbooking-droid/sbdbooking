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
                            <span class="text-gray-900 font-medium">Edit Blog</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-bold text-gray-900">Edit Blog Post</h1>
        </div>

        <form id="blogForm" action="{{ route('blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
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
                                <input type="text" name="title" id="title" value="{{ $blog->title }}" placeholder="Enter blog title"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Slug</label>
                                <input type="text" name="slug" id="slug" value="{{ $blog->slug }}" placeholder="blog-post-slug"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm bg-gray-50 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Short Description</label>
                                <textarea name="short_description" rows="3" placeholder="Summary..."
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">{{ $blog->short_description }}</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Full Blog Content <span class="text-red-500">*</span></label>
                                <textarea name="content" id="mytextarea" class="w-full border border-gray-300 rounded">{{ $blog->content }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Travel Information -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Travel Details (Optional)</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Travel Type</label>
                                <input type="text" name="travel_type" value="{{ $blog->travel_type }}" placeholder="e.g. Adventure"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Destination</label>
                                <input type="text" name="destination" value="{{ $blog->destination }}" placeholder="e.g. Goa"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">State</label>
                                <input type="text" name="state" value="{{ $blog->state }}" placeholder="State"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">City</label>
                                <input type="text" name="city" value="{{ $blog->city }}" placeholder="City"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Best Time to Visit</label>
                                <input type="text" name="best_time" value="{{ $blog->best_time }}" placeholder="e.g. Nov to Feb"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Estimated Budget (₹)</label>
                                <input type="number" step="0.01" name="estimated_budget" value="{{ $blog->estimated_budget }}" placeholder="e.g. 5000"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Trip Duration</label>
                                <input type="text" name="trip_duration" value="{{ $blog->trip_duration }}" placeholder="e.g. 2 Days"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Distance (km)</label>
                                <input type="text" name="distance" value="{{ $blog->distance }}" placeholder="Distance"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div class="md:col-span-3">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Google Map Embed Link</label>
                                <input type="url" name="google_map" value="{{ $blog->google_map }}" placeholder="https://maps.google.com/..."
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Sidebar Settings -->
                <div class="space-y-6">
                    <!-- Media -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Blog Media</h2>
                        <div class="space-y-4">
                            <!-- Featured Image -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Featured Image</label>
                                <input type="file" name="featured_image" id="featuredImageInput" accept="image/*"
                                    class="w-full border border-gray-300 rounded px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                
                                <div id="featuredPreviewContainer" class="mt-3 border border-gray-100 rounded-lg overflow-hidden h-40 bg-gray-50 flex items-center justify-center relative {{ $blog->featured_image ? '' : 'hidden' }}">
                                    <img id="featuredPreview" class="w-full h-full object-cover" 
                                         src="{{ $blog->featured_image ? asset('images/blogs/featured/' . $blog->featured_image) : '' }}">
                                    <button type="button" id="removeFeaturedBtn" class="absolute top-2 right-2 w-7 h-7 bg-red-600 text-white rounded-full flex items-center justify-center shadow-md hover:bg-red-700 transition">
                                        &times;
                                    </button>
                                </div>
                            </div>

                            <hr class="border-gray-100">

                            <!-- Gallery Images -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Upload New Gallery Images</label>
                                <input type="file" name="gallery_images[]" id="galleryImagesInput" accept="image/*" multiple
                                    class="w-full border border-gray-300 rounded px-3 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mt-4 mb-2">Existing Gallery</label>
                                <div class="grid grid-cols-3 gap-2" id="existingGalleryGrid">
                                    @if ($blog->gallery_images)
                                        @foreach ($blog->gallery_images as $image)
                                            <div class="relative border rounded overflow-hidden h-16 bg-gray-50 flex items-center justify-center gallery-item">
                                                <img src="{{ asset('images/blogs/gallery/' . $image) }}" class="w-full h-full object-cover">
                                                <button type="button" class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center text-white text-xs hover:bg-opacity-65 transition delete-existing-image-btn" data-name="{{ $image }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div id="deletedImagesWrapper"></div>
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
                                        <option value="{{ $cat->id }}" {{ $blog->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Tags</label>
                                <select name="tags[]" class="w-full border select2 border-gray-300 rounded px-3 py-2 text-xs" multiple>
                                    @foreach ($tags as $tag)
                                        <option value="{{ $tag->id }}" {{ in_array($tag->id, $selectedTags) ? 'selected' : '' }}>{{ $tag->tag_name }}</option>
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
                                    <span id="seoTitleCount" class="text-[10px] text-gray-400">{{ strlen($blog->seo_title) }}/70</span>
                                </div>
                                <input type="text" name="seo_title" id="seoTitle" value="{{ $blog->seo_title }}" maxlength="70"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <label class="block text-xs font-semibold text-gray-700">Meta Description</label>
                                    <span id="metaDescCount" class="text-[10px] text-gray-400">{{ strlen($blog->meta_description) }}/160</span>
                                </div>
                                <textarea name="meta_description" id="metaDesc" rows="3" maxlength="160"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">{{ $blog->meta_description }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Meta Keywords</label>
                                <input type="text" name="meta_keywords" value="{{ $blog->meta_keywords }}"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Publishing -->
                    <div class="bg-white shadow-sm border border-gray-100 rounded-xl p-6">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider border-b pb-2 mb-4">Publish & Status</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Author</label>
                                <input type="text" name="author" value="{{ $blog->author }}"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                                <select name="status" id="blogStatus" class="w-full border border-gray-300 rounded px-3 py-2 text-xs focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    <option value="Draft" {{ $blog->status === 'Draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="Published" {{ $blog->status === 'Published' ? 'selected' : '' }}>Published</option>
                                    <option value="Scheduled" {{ $blog->status === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                </select>
                            </div>
                            <div id="publishDateWrapper" class="{{ $blog->status === 'Scheduled' ? '' : 'hidden' }}">
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Scheduled Publish Date</label>
                                <input type="datetime-local" name="publish_date" id="publish_date" 
                                    value="{{ $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : '' }}"
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
                                    <input type="checkbox" name="featured" class="sr-only peer" {{ $blog->featured ? 'checked' : '' }}>
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
                                    <input type="checkbox" name="allow_comments" class="sr-only peer" {{ $blog->allow_comments ? 'checked' : '' }}>
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
                <button type="submit" id="submitBtn" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-xs font-bold transition shadow">
                    Update Blog
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
                    $('#submitBtn').text('Update Blog');
                }
            });

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

            // Delete existing gallery images dynamically
            $('.delete-existing-image-btn').on('click', function(e) {
                e.preventDefault();
                const imageName = $(this).data('name');
                $('#deletedImagesWrapper').append(`<input type="hidden" name="delete_gallery_images[]" value="${imageName}">`);
                $(this).closest('.gallery-item').remove();
            });

            // Gallery Previews for new files
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

            // AJAX Form Submit (simulating PUT request using POST method spoofing)
            $('#blogForm').on('submit', function(e) {
                e.preventDefault();

                if (typeof tinymce !== 'undefined') {
                    tinymce.triggerSave();
                }

                const form = this;
                const formData = new FormData(form);
                formData.append('_method', 'PUT');

                $.ajax({
                    url: $(form).attr('action'),
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $("#submitBtn").prop('disabled', true);
                        $("#submitBtn").html('<span class="animate-spin inline-block w-4 h-4 border-t-2 border-white rounded-full mr-2"></span> Updating...');
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
                                const fieldKey = key.replace(/\./g, '\\.');
                                const input = $(`[name="${key}"], [name="${key}[]"], #${fieldKey}`);
                                if (input.length) {
                                    input.addClass('border-red-500');
                                    input.after(`<span class="text-red-500 text-xs error-text mt-1 block">${val[0]}</span>`);
                                }
                                $.toastr.error(val[0]);
                            });
                            const firstErr = $('.border-red-500').first();
                            if (firstErr.length) {
                                $('html, body').animate({ scrollTop: firstErr.offset().top - 120 }, 400);
                            }
                        } else {
                            $.toastr.error("Something went wrong!");
                        }
                    },
                    complete: function () {
                        $("#submitBtn").prop('disabled', false).text('Update Blog');
                    }
                });
            });
        });
    </script>
@endsection
