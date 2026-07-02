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
                            <span class="text-gray-900 font-medium">Blog Settings</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-bold text-gray-900">Blog Settings</h1>
        </div>

        <div class="bg-white shadow border border-gray-100 rounded-lg max-w-4xl">
            <form id="settingsForm" action="{{ route('blogSettings.update') }}" method="POST">
                @csrf
                <div class="p-6 space-y-6">
                    <!-- General Settings -->
                    <div>
                        <h2 class="text-base font-bold text-gray-900 border-b pb-2 mb-4">General Configuration</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Default Author</label>
                                <input type="text" name="default_author" value="{{ $settings['default_author'] ?? 'Admin' }}"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Default Blog Status</label>
                                <select name="default_blog_status" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    <option value="Draft" {{ ($settings['default_blog_status'] ?? '') === 'Draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="Published" {{ ($settings['default_blog_status'] ?? '') === 'Published' ? 'selected' : '' }}>Published</option>
                                    <option value="Scheduled" {{ ($settings['default_blog_status'] ?? '') === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Blogs Per Page</label>
                                <input type="number" name="blogs_per_page" value="{{ $settings['blogs_per_page'] ?? 10 }}"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- SEO Settings -->
                    <div>
                        <h2 class="text-base font-bold text-gray-900 border-b pb-2 mb-4">Default SEO Defaults</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Default SEO Title</label>
                                <input type="text" name="default_seo_title" value="{{ $settings['default_seo_title'] ?? '' }}"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Default Meta Description</label>
                                <textarea name="default_meta_description" rows="3"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">{{ $settings['default_meta_description'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Switches / Toggles -->
                    <div>
                        <h2 class="text-base font-bold text-gray-900 border-b pb-2 mb-4">Preferences & Features</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Toggle Comments -->
                            <div class="flex items-center justify-between border-b pb-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800">Enable Comments</label>
                                    <span class="text-xs text-gray-500">Allow users to comment on blog posts by default.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="enable_comments" class="sr-only peer" {{ ($settings['enable_comments'] ?? 1) == 1 ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Auto-generate Slug -->
                            <div class="flex items-center justify-between border-b pb-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800">Auto Generate Slug</label>
                                    <span class="text-xs text-gray-500">Automatically generate unique URLs from titles.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="auto_generate_slug" class="sr-only peer" {{ ($settings['auto_generate_slug'] ?? 1) == 1 ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Enable Featured Blogs -->
                            <div class="flex items-center justify-between border-b pb-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800">Enable Featured Blogs</label>
                                    <span class="text-xs text-gray-500">Highlight specific articles on the front page.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="enable_featured_blogs" class="sr-only peer" {{ ($settings['enable_featured_blogs'] ?? 1) == 1 ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Enable Social Share -->
                            <div class="flex items-center justify-between border-b pb-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800">Enable Social Share Buttons</label>
                                    <span class="text-xs text-gray-500">Render Facebook, Twitter, WhatsApp sharing widgets.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="enable_social_share_buttons" class="sr-only peer" {{ ($settings['enable_social_share_buttons'] ?? 1) == 1 ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button type="submit" id="saveSettingsBtn"
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-bold transition shadow">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#settingsForm').on('submit', function (e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);

                $.ajax({
                    url: $(form).attr('action'),
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $("#saveSettingsBtn").html('<span class="animate-spin inline-block w-4 h-4 border-t-2 border-white rounded-full mr-2"></span> Saving...').prop('disabled', true);
                    },
                    success: function (response) {
                        if (response.success) {
                            $.toastr.success(response.message);
                        } else {
                            $.toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422 && xhr.responseJSON.errors) {
                            $.each(xhr.responseJSON.errors, function (key, val) {
                                $.toastr.error(val[0]);
                            });
                        } else {
                            $.toastr.error("Failed to update settings!");
                        }
                    },
                    complete: function () {
                        $("#saveSettingsBtn").html('Save Settings').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
