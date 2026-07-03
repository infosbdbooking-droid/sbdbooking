@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center p-6">
    <div class="text-center max-w-lg mx-auto">

        {{-- Big 403 Number --}}
        <div class="relative inline-block mb-8">
            <div class="text-[120px] md:text-[160px] font-extrabold text-slate-100 leading-none select-none">
                403
            </div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-20 h-20 bg-red-500/10 rounded-full flex items-center justify-center border-2 border-red-300/30">
                    <i class="fas fa-shield-alt text-3xl text-[#d84e55]"></i>
                </div>
            </div>
        </div>

        {{-- Message --}}
        <h1 class="text-2xl font-extrabold text-slate-800 mb-2">Access Denied</h1>
        <p class="text-sm text-slate-500 mb-2">
            You do not have permission to access this module.
        </p>
        <p class="text-xs text-slate-400 mb-8">
            Contact your administrator to request access to this section.
        </p>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#d84e55] hover:bg-[#c04349] text-white rounded-xl text-sm font-bold shadow transition-all duration-150">
                <i class="fas fa-home"></i>
                Go to Dashboard
            </a>
            <a href="javascript:history.back()"
               class="inline-flex items-center gap-2 px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition-all duration-150">
                <i class="fas fa-arrow-left"></i>
                Go Back
            </a>
        </div>

        {{-- Permission Note --}}
        <div class="mt-10 p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-left">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                <div>
                    <p class="text-xs font-bold text-yellow-800 mb-0.5">Permission Required</p>
                    <p class="text-xs text-yellow-700">
                        Your current role does not include access to this module. 
                        Please ask the administrator to update your role permissions.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
