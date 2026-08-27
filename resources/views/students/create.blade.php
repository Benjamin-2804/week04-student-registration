@extends('layouts.app')

@section('title', 'Student Registration Form')

@section('content')

{{-- Page heading --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Student Registration Form</h1>
    <p class="text-sm text-gray-500 mt-1">Fill in all required fields. Fields marked <span class="text-red-500 font-semibold">*</span> are required.</p>
</div>

@if (session('picture_notice'))
    <div class="mb-5 flex items-start gap-3 rounded-xl bg-yellow-50 border border-yellow-300 text-yellow-800 px-4 py-3">
        <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
        </svg>
        <span class="text-sm font-medium">{{ session('picture_notice') }} Please re-select your profile picture before resubmitting.</span>
    </div>
@endif

<form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    {{-- ----------------------------------------------------------------- --}}
    {{-- Section 1 — Personal Information                                   --}}
    {{-- ----------------------------------------------------------------- --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-5 overflow-hidden">
        <div class="bg-blue-600 px-6 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <h2 class="text-sm font-semibold text-white uppercase tracking-wide">Personal Information</h2>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Student ID --}}
            <div>
                <label for="student_id" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Student ID <span class="text-red-500">*</span>
                </label>
                <input type="text" id="student_id" name="student_id"
                       value="{{ old('student_id') }}"
                       placeholder="e.g. 2024-00001"
                       class="w-full rounded-lg border px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition
                              @error('student_id') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror">
                @error('student_id')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- First Name --}}
            <div>
                <label for="first_name" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="first_name" name="first_name"
                       value="{{ old('first_name') }}"
                       placeholder="Juan"
                       class="w-full rounded-lg border px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition
                              @error('first_name') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror">
                @error('first')
                    <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                @enderror
                @error('first_name')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Middle Name --}}
            <div>
                <label for="middle_name" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Middle Name <span class="text-gray-400 normal-case font-normal text-xs">(optional)</span>
                </label>
                <input type="text" id="middle_name" name="middle_name"
                       value="{{ old('middle_name') }}"
                       placeholder="Dela"
                       class="w-full rounded-lg border px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition
                              @error('middle_name') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror">
                @error('middle_name')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Last Name --}}
            <div>
                <label for="last_name" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <input type="text" id="last_name" name="last_name"
                       value="{{ old('last_name') }}"
                       placeholder="Cruz"
                       class="w-full rounded-lg border px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition
                              @error('last_name') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror">
                @error('last_name')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Date of Birth --}}
            <div>
                <label for="date_of_birth" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Date of Birth <span class="text-red-500">*</span>
                </label>
                <input type="date" id="date_of_birth" name="date_of_birth"
                       value="{{ old('date_of_birth') }}"
                       class="w-full rounded-lg border px-3 py-2.5 text-sm text-gray-800
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition
                              @error('date_of_birth') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror">
                @error('date_of_birth')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Gender --}}
            <div>
                <label for="gender" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Gender <span class="text-red-500">*</span>
                </label>
                <select id="gender" name="gender"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm text-gray-800
                               focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition
                               @error('gender') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror">
                    <option value="">— Select gender —</option>
                    <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other"  {{ old('gender') === 'other'  ? 'selected' : '' }}>Other</option>
                </select>
                @error('gender')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>
    </div>

    {{-- ----------------------------------------------------------------- --}}
    {{-- Section 2 — Contact Information                                    --}}
    {{-- ----------------------------------------------------------------- --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-5 overflow-hidden">
        <div class="bg-blue-600 px-6 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <h2 class="text-sm font-semibold text-white uppercase tracking-wide">Contact Information</h2>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="juan@example.com"
                       class="w-full rounded-lg border px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition
                              @error('email') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror">
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Mobile Number --}}
            <div>
                <label for="mobile_number" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Mobile Number <span class="text-red-500">*</span>
                </label>
                <input type="text" id="mobile_number" name="mobile_number"
                       value="{{ old('mobile_number') }}"
                       placeholder="09171234567"
                       maxlength="15"
                       class="w-full rounded-lg border px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition
                              @error('mobile_number') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror">
                <p class="mt-1 text-xs text-gray-400">Digits only, 10–15 characters</p>
                @error('mobile_number')
                    <p class="mt-0.5 text-xs text-red-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Address --}}
            <div class="md:col-span-2">
                <label for="address" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Address <span class="text-red-500">*</span>
                </label>
                <textarea id="address" name="address" rows="3"
                          placeholder="House/Unit No., Street, Barangay, City/Municipality, Province"
                          class="w-full rounded-lg border px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400 resize-none
                                 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition
                                 @error('address') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror">{{ old('address') }}</textarea>
                <p class="mt-1 text-xs text-gray-400">10–500 characters</p>
                @error('address')
                    <p class="mt-0.5 text-xs text-red-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>
    </div>

    {{-- ----------------------------------------------------------------- --}}
    {{-- Section 3 — Academic Information                                   --}}
    {{-- ----------------------------------------------------------------- --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-5 overflow-hidden">
        <div class="bg-blue-600 px-6 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h2 class="text-sm font-semibold text-white uppercase tracking-wide">Academic Information</h2>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Program --}}
            <div>
                <label for="program" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Program <span class="text-red-500">*</span>
                </label>
                <input type="text" id="program" name="program"
                       value="{{ old('program') }}"
                       placeholder="e.g. BSIT, BSCS, BSIS"
                       class="w-full rounded-lg border px-3 py-2.5 text-sm text-gray-800 placeholder-gray-400
                              focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition
                              @error('program') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror">
                @error('program')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Year Level --}}
            <div>
                <label for="year_level" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-1">
                    Year Level <span class="text-red-500">*</span>
                </label>
                <select id="year_level" name="year_level"
                        class="w-full rounded-lg border px-3 py-2.5 text-sm text-gray-800
                               focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition
                               @error('year_level') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror">
                    <option value="">— Select year level —</option>
                    @foreach (range(1, 4) as $yr)
                        <option value="{{ $yr }}" {{ old('year_level') == $yr ? 'selected' : '' }}>
                            {{ $yr }}{{ match($yr) { 1=>'st', 2=>'nd', 3=>'rd', default=>'th' } }} Year
                        </option>
                    @endforeach
                </select>
                @error('year_level')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>
    </div>

    {{-- ----------------------------------------------------------------- --}}
    {{-- Section 4 — Profile Picture                                        --}}
    {{-- ----------------------------------------------------------------- --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden">
        <div class="bg-blue-600 px-6 py-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <h2 class="text-sm font-semibold text-white uppercase tracking-wide">Profile Picture</h2>
        </div>

        <div class="p-6">
            <label for="profile_picture" class="block text-xs font-semibold text-gray-600 uppercase tracking-wide mb-2">
                Upload Photo <span class="text-red-500">*</span>
            </label>

            {{-- Drop-zone styled file input --}}
            <label for="profile_picture"
                   class="flex flex-col items-center justify-center w-full h-36 rounded-xl border-2 border-dashed cursor-pointer transition
                          hover:bg-blue-50 hover:border-blue-400
                          @error('profile_picture') border-red-300 bg-red-50 @else border-gray-200 bg-gray-50 @enderror">
                <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor"
                     stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                </svg>
                <span class="text-sm text-gray-500">Click to choose a photo</span>
                <span class="text-xs text-gray-400 mt-1">JPG, JPEG, PNG — max 2 MB</span>
                <input type="file" id="profile_picture" name="profile_picture"
                       accept=".jpg,.jpeg,.png" class="hidden">
            </label>

            {{-- Preview (JS-powered) --}}
            <div id="preview-wrap" class="hidden mt-3 flex items-center gap-3">
                <img id="preview-img" src="" alt="Preview"
                     class="w-16 h-16 rounded-full object-cover border-2 border-blue-200">
                <span id="preview-name" class="text-sm text-gray-600 truncate max-w-xs"></span>
            </div>

            @error('profile_picture')
                <p class="mt-2 text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>
    </div>

    {{-- Submit bar --}}
    <div class="flex items-center justify-between bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-4">
        <p class="text-xs text-gray-400">
            <span class="text-red-500">*</span> Required fields
        </p>
        <button type="submit"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 active:bg-blue-800
                       text-white font-semibold text-sm px-7 py-2.5 rounded-xl shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Register Student
        </button>
    </div>

</form>

<script>
    document.getElementById('profile_picture').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const wrap = document.getElementById('preview-wrap');
        const img  = document.getElementById('preview-img');
        const name = document.getElementById('preview-name');
        img.src = URL.createObjectURL(file);
        name.textContent = file.name;
        wrap.classList.remove('hidden');
    });
</script>

@endsection
