@extends('layouts.app')

@section('title', $student->first_name . ' ' . $student->last_name . ' — Profile')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow p-8">

        <div class="flex items-center gap-6 mb-8">
            {{-- Profile picture --}}
            @if ($student->profile_picture && Storage::disk('public')->exists($student->profile_picture))
                <img src="{{ asset('storage/' . $student->profile_picture) }}"
                     alt="Profile picture of {{ $student->first_name }}"
                     class="w-28 h-28 rounded-full object-cover border-4 border-blue-200">
            @else
                <img src="{{ asset('images/placeholder.png') }}"
                     alt="No profile picture"
                     class="w-28 h-28 rounded-full object-cover border-4 border-gray-200">
            @endif

            <div>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ $student->first_name }}
                    {{ $student->middle_name ?: 'N/A' }}
                    {{ $student->last_name }}
                </h1>
                <p class="text-gray-500 text-sm">{{ $student->student_id }}</p>
            </div>
        </div>

        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">

            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Student ID</dt>
                <dd class="mt-1 text-gray-800">{{ $student->student_id }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">First Name</dt>
                <dd class="mt-1 text-gray-800">{{ $student->first_name }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Middle Name</dt>
                <dd class="mt-1 text-gray-800">
                    {{ $student->middle_name ?: 'N/A' }}
                </dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Last Name</dt>
                <dd class="mt-1 text-gray-800">{{ $student->last_name }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Email Address</dt>
                <dd class="mt-1 text-gray-800">{{ $student->email }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Mobile Number</dt>
                <dd class="mt-1 text-gray-800">{{ $student->mobile_number }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Date of Birth</dt>
                <dd class="mt-1 text-gray-800">{{ $student->date_of_birth }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Gender</dt>
                <dd class="mt-1 text-gray-800 capitalize">{{ $student->gender }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Program</dt>
                <dd class="mt-1 text-gray-800">{{ $student->program }}</dd>
            </div>

            <div>
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Year Level</dt>
                <dd class="mt-1 text-gray-800">Year {{ $student->year_level }}</dd>
            </div>

            <div class="md:col-span-2">
                <dt class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Address</dt>
                <dd class="mt-1 text-gray-800">{{ $student->address }}</dd>
            </div>

        </dl>

        <div class="mt-8 flex gap-4">
            <a href="{{ route('students.index') }}"
               class="text-sm text-blue-600 hover:underline">&larr; Back to students</a>
        </div>

    </div>
</div>
@endsection
