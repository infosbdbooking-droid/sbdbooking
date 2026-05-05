@include('layouts.header')

<div class="min-h-screen flex bg-gradient-to-r from-teal-500 to-blue-600">
  <!-- Left Side Banner (60%) - Hidden on Mobile -->
  <div class="w-3/5 bg-cover bg-center flex items-center justify-center p-10 sm:block hidden"
    style="background-image: url('https://www.travelteacher.online/img/2205europe_banner.jpg');">
    {{-- <div class="text-white mt-60 text-center">
      <h1 class="text-5xl font-bold mb-4">Welcome to SBD Booking</h1>
      <p class="text-lg mb-4">
        Reliable and affordable car rentals for your city and outstation journeys.
      </p>
      <p class="text-xl font-semibold">
        Experience comfort, safety, and convenience with every ride.
      </p>
    </div> --}}
  </div>


  <div class="w-full sm:w-2/5 flex items-center justify-center bg-white p-6">
    <div class="max-w-sm w-full bg-white shadow-lg rounded-lg p-8">
      <!-- Hommlie Logo at the top -->
      <div class="flex  mb-6">
        <img src="{{ asset('storage/app/public/images/logo/logo-69501cbfb88fc.png') }}" alt="SBD Logo" width="20%"
          height="10%">
      </div>

      <!-- Login Form -->
      <form id="signInForm" action="{{ route('signin.verify') }}" method="POST">
        @csrf
        <div class="space-y-4">
          <!-- Email -->
          <div>
            <input id="email" name="email" type="text"
              class="w-full h-12 px-4 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-600"
              placeholder="Email address">
          </div>
          <!-- Password -->
          <div>
            <input id="password" name="password" type="password"
              class="w-full h-12 px-4 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-600"
              placeholder="Password">
          </div>
          <div id="otpSection" class="hidden">
            <div class="flex space-x-2">
              <input id="otp" name="otp" type="text" maxlength="6"
                class="w-full h-12 px-4 border border-gray-300 rounded-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-teal-600"
                placeholder="Enter OTP" />
              <button id="sendOtpBtn" type="button" class="bg-teal-600 text-white px-4 rounded-lg hover:bg-teal-700">
                Send OTP
              </button>
            </div>
          </div>

          <!-- Toggle -->
          <div class="text-right">
            <button id="toggleLoginType" type="button" class="text-teal-600 hover:underline">
              Login by OTP
            </button>
          </div>
          <!-- Submit Button -->
          <div class="mt-6">
            <button type="submit" class="signinBtn w-full bg-teal-600 text-white rounded-lg py-3 text-lg">Login</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@include('layouts.footer')
<script>
  $(document).ready(function () {
    let useOtp = false;

    const toggleBtn = $("#toggleLoginType");
    const passwordField = $("#password").closest("div");
    const otpSection = $("#otpSection");
    const sendOtpBtn = $("#sendOtpBtn");

    // Toggle Login Type
    toggleBtn.on("click", function () {
      useOtp = !useOtp;
      passwordField.toggleClass("hidden", useOtp);
      otpSection.toggleClass("hidden", !useOtp);
      toggleBtn.text(useOtp ? "Login by Password" : "Login by OTP");
    });

    // Send OTP
    sendOtpBtn.on("click", function () {
      const email = $("#email").val();
      if (!email) {
        $.toastr.error("Please enter your email");
        return;
      }
      $.ajax({
        url: "{{-- route('send.otp') --}}",
        type: "POST",
        data: {
          _token: "{{ csrf_token() }}",
          email: email
        },
        beforeSend: function () {
          sendOtpBtn.prop("disabled", true).text("Sending...");
        },
        success: function (response) {
          $.toastr.success(response.message);
        },
        error: function (xhr) {
          $.toastr.error(xhr.responseJSON?.message || "Failed to send OTP");
        },
        complete: function () {
          sendOtpBtn.prop("disabled", false).text("Send OTP");
        }
      });
    });

    // Form Validation & Submit
    $("#signInForm").on("submit", function (e) {
      e.preventDefault(); // always stop default

      let form = $(this);
      let submitBtn = form.find('.signinBtn');
      let originalBtnText = submitBtn.text();

      // Basic client-side validation
      if (!$("#email").val() || (!useOtp && !$("#password").val()) || (useOtp && !$("#otp").val())) {
          if (typeof $.toastr !== 'undefined') $.toastr.error("Please fill in all required fields."); 
          else alert("Please fill in all required fields.");
          return;
      }

      let ajaxUrl = useOtp ? "/login-otp" : form.attr("action");

      $.ajax({
        type: "POST",
        url: ajaxUrl,
        data: form.serialize(),
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        beforeSend: function () {
          submitBtn.html('<span class="animate-spin inline-block w-4 h-4 border-t-2 border-white rounded-full mr-2"></span> Processing...').prop('disabled', true);
        },
        success: function (res) {
          if (res.success) {
            if (typeof $.toastr !== 'undefined') $.toastr.success(res.message || "Authentication successful");
            window.location.href = res.redirect;
          } else {
            if (typeof $.toastr !== 'undefined') $.toastr.error(res.message); else alert(res.message);
            submitBtn.html(originalBtnText).prop('disabled', false);
          }
        },
        error: function (xhr) {
          submitBtn.html(originalBtnText).prop('disabled', false);
          
          let msg = "Login failed. Please try again.";
          if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
          }
          if (typeof $.toastr !== 'undefined') $.toastr.error(msg); else alert(msg);
        }
      });

    });
  });
</script>