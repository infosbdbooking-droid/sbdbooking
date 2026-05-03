@extends('layouts.app')
@section('content')
    <div class="bg-white shadow rounded-lg p-6">
        <form id="settingsForm" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <!-- 🔧 Basic Info -->
            <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">🔧 Basic Info</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Firebase key -->
                <div>
                    <label for="firebase_key" class="block text-sm font-medium text-gray-700 mb-1">Firebase Key</label>
                    <input type="text" name="firebase_key" placeholder="Enter Firebase Key"
                        class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500" value="">
                </div>
                <!-- Currency -->
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                    <input type="text" name="currency" placeholder="e.g. USD, INR"
                        class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500" value="">
                </div>
                <!-- Logo -->
                <div class="md:col-span-2">
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Logo</label>
                    <input type="file" name="logo" id="logo-input"
                        class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500">
                    <img id="settings-logo-preview" src="" class="h-12 mt-3 rounded hidden" alt="Current Logo">
                </div>

                <!-- Copyright -->
                <div>
                    <label for="copyright" class="block text-sm font-medium text-gray-700 mb-1">Copyright</label>
                    <input type="text" name="copyright" placeholder="© 2025 Your Company"
                        class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500" value="">
                </div>
                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="address" placeholder="Enter office address"
                        class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500" value="">
                </div>
                <!-- Contact -->
                <div>
                    <label for="contact" class="block text-sm font-medium text-gray-700 mb-1">Contact</label>
                    <input type="text" name="contact" placeholder="+1 234 567 890"
                        class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500" value="">
                </div>
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" placeholder="your@email.com"
                        class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500" value="">
                </div>
            </div>

            <!-- 🌐 Social Accounts -->
            <h2 class="text-lg font-semibold text-gray-700 border-b pb-2 mt-10">🌐 Social Accounts</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Facebook -->
                <div>
                    <label for="facebook" class="block text-sm font-medium text-gray-700 mb-1">Facebook</label>
                    <input type="text" name="facebook" placeholder="https://facebook.com/yourpage"
                        class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500" value="">
                </div>
                <!-- Twitter -->
                <div>
                    <label for="twitter" class="block text-sm font-medium text-gray-700 mb-1">Twitter</label>
                    <input type="text" name="twitter" placeholder="https://twitter.com/yourhandle"
                        class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500" value="">
                </div>
                <!-- YouTube -->
                <div>
                    <label for="instagram" class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
                    <input type="text" name="instagram" placeholder="https://instagram.com/yourprofile"
                        class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500" value="">
                </div>
                <!-- LinkedIn -->
                <div>
                    <label for="linkedin" class="block text-sm font-medium text-gray-700 mb-1">LinkedIn</label>
                    <input type="text" name="linkedin" placeholder="https://linkedin.com/in/yourprofile"
                        class="w-full px-3 py-2 border-b border-gray-300 focus:outline-none focus:border-blue-500" value="">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-right">
                <button type="submit"
                    class="inline-flex  updateBtn items-center px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    <i class="fa fa-check mr-2"></i> Update
                </button>
            </div>
        </form>
    </div>

@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            // GET SETTINGS DATA
            $.ajax({
                url: "{{ route('settings.data') }}",
                method: 'GET',
                success: function (response) {
                    if (response.success) {
                        const data = response.data;
                        $('input[name="firebase_key"]').val(data.firebase_key);
                        $('input[name="currency"]').val(data.currency);
                        $('input[name="copyright"]').val(data.copyright);
                        $('input[name="address"]').val(data.address);
                        $('input[name="contact"]').val(data.contact);
                        $('input[name="email"]').val(data.email);
                        $('input[name="facebook"]').val(data.facebook);
                        $('input[name="twitter"]').val(data.twitter);
                        $('input[name="instagram"]').val(data.instagram);
                        $('input[name="linkedin"]').val(data.linkedin);
                        $('input[name="youtube"]').val(data.youtube);
                        if (data.logo) {
                            let imgPath = basePath + "storage/app/public/images/logo" + data.logo;
                            $('#settings-logo-preview').attr('src', imgPath).removeClass('hidden');
                        }
                    }
                },
                error: function (xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        $.each(xhr.responseJSON.errors, function (key, val) {
                            $.toastr.error(val[0]);
                        });
                    } else {
                        $.toastr.error("Something went wrong!");
                    }
                }
            });
            $('#logo-input').on('change', function (event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $('#settings-logo-preview').attr('src', e.target.result).removeClass('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
            // FORM SUBMISSION
            $('#settingsForm').on('submit', function (e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);
                $('.error-text').remove();
                $('input, select, textarea').removeClass('border-red-500');
                $.ajax({
                    url: $(form).attr('action'),
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $(".updateBtn").html('<span class="animate-spin inline-block w-4 h-4 border-t-2 border-white rounded-full mr-2"></span> Processing...').prop('disabled', true);
                    },
                    success: function (response) {
                        if (response.success) {
                            $.toastr.success(response.message);
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                        } else {
                            $.toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        handleValidationErrors(xhr);
                    },
                    complete: function () {
                        $(".updateBtn").html('<i class="fa fa-check mr-2"></i> Update').prop('disabled', false);
                    }
                });
            });
            // handle Validation Errors
            function handleValidationErrors(xhr) {
                $('.error-text').remove();
                $('input, select, textarea').removeClass('border-red-500');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let firstErrorField = null;
                    $.each(xhr.responseJSON.errors, function (key, val) {
                        const inputField = $('[name="' + key + '"]');
                        inputField.addClass('border-red-500');
                        if (inputField.next('.error-text').length === 0) {
                            inputField.after('<span class="text-red-500 text-sm error-text">' + val[0] + '</span>');
                        }
                        if (!firstErrorField && inputField.is(':visible')) {
                            firstErrorField = inputField;
                        }
                        $.toastr.error(val[0]);
                    });
                    if (firstErrorField) {
                        const offset = firstErrorField.offset().top - 100;
                        $('html, body').animate({
                            scrollTop: offset
                        }, 200, function () {
                            firstErrorField.focus();
                        });
                    }
                } else {
                    $.toastr.error("Something went wrong!");
                }
            }
        });
    </script>
@endsection