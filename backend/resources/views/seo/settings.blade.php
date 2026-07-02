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
                            <span class="text-gray-900 font-medium">SEO Dashboard Settings</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-xl font-bold text-gray-900">SEO Landing Page Settings</h1>
        </div>

        <div class="bg-white shadow border border-gray-100 rounded-lg max-w-4xl">
            <form id="settingsForm" action="{{ route('seoSettings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="p-6 space-y-6">
                    <!-- General Settings -->
                    <div>
                        <h2 class="text-base font-bold text-gray-900 border-b pb-2 mb-4">Defaults Configuration</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Default Author</label>
                                <input type="text" name="default_author" value="{{ $settings['default_author'] ?? 'SEO Admin' }}"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Default Page Status</label>
                                <select name="default_page_status" class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                                    <option value="Draft" {{ ($settings['default_page_status'] ?? '') === 'Draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="Published" {{ ($settings['default_page_status'] ?? '') === 'Published' ? 'selected' : '' }}>Published</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Pages Per Page</label>
                                <input type="number" name="pages_per_page" value="{{ $settings['pages_per_page'] ?? 15 }}"
                                    class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Banner Settings -->
                    <div>
                        <h2 class="text-base font-bold text-gray-900 border-b pb-2 mb-4">Default Banner</h2>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Upload Default Banner</label>
                                <input type="file" name="default_banner" id="defaultBannerInput" accept="image/*"
                                    class="w-full border border-gray-300 rounded px-3 py-1.5 text-xs focus:outline-none">
                                
                                <div id="bannerPreviewContainer" class="mt-3 border rounded overflow-hidden h-40 bg-gray-50 flex items-center justify-center relative {{ ($settings['default_banner'] ?? null) ? '' : 'hidden' }}">
                                    <img id="bannerPreview" class="w-full h-full object-cover" 
                                         src="{{ ($settings['default_banner'] ?? null) ? asset('images/seo/defaults/' . $settings['default_banner']) : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Switches / Toggles -->
                    <div>
                        <h2 class="text-base font-bold text-gray-900 border-b pb-2 mb-4">Preferences & Features</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Toggle Featured -->
                            <div class="flex items-center justify-between border-b pb-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800">Enable Featured Pages</label>
                                    <span class="text-xs text-gray-500">Allow spotlighting premium locations.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="enable_featured_pages" class="sr-only peer" {{ ($settings['enable_featured_pages'] ?? 1) == 1 ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Toggle View Counter -->
                            <div class="flex items-center justify-between border-b pb-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800">Enable View Counter</label>
                                    <span class="text-xs text-gray-500">Track and log client visits per page.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="enable_view_counter" class="sr-only peer" {{ ($settings['enable_view_counter'] ?? 1) == 1 ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Toggle Related Pages -->
                            <div class="flex items-center justify-between border-b pb-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800">Enable Related Pages</label>
                                    <span class="text-xs text-gray-500">Display context links under SEO landing pages.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="enable_related_pages" class="sr-only peer" {{ ($settings['enable_related_pages'] ?? 1) == 1 ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Toggle WhatsApp -->
                            <div class="flex items-center justify-between border-b pb-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800">Enable WhatsApp Button</label>
                                    <span class="text-xs text-gray-500">Render floating widget to prompt booking inquiries.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="enable_whatsapp_button" class="sr-only peer" {{ ($settings['enable_whatsapp_button'] ?? 1) == 1 ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>

                            <!-- Toggle Call -->
                            <div class="flex items-center justify-between border-b pb-3">
                                <div>
                                    <label class="block text-sm font-bold text-gray-800">Enable Call Button</label>
                                    <span class="text-xs text-gray-500">Enable call-to-action click-to-dial prompts.</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="enable_call_button" class="sr-only peer" {{ ($settings['enable_call_button'] ?? 1) == 1 ? 'checked' : '' }}>
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
            // Default Banner Preview
            $('#defaultBannerInput').on('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $('#bannerPreview').attr('src', e.target.result);
                        $('#bannerPreviewContainer').removeClass('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });

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
