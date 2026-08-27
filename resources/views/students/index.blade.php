@extends('layouts.app')

@section('title', 'Registered Students')

@section('content')
<div class="max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Registered Students</h1>
        <a href="{{ route('students.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
            + Register New Student
        </a>
    </div>

    @if ($students->isEmpty())
        <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">
            No students registered yet.
        </div>
    @else
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Student ID</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Full Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Program</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Year</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($students as $student)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-mono text-gray-700">{{ $student->student_id }}</td>
                            <td class="px-6 py-4 text-gray-800">
                                {{ $student->first_name }}
                                {{ $student->middle_name ? $student->middle_name.' ' : '' }}{{ $student->last_name }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $student->program }}</td>
                            <td class="px-6 py-4 text-gray-600">Year {{ $student->year_level }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('students.show', $student) }}"
                                   class="text-blue-600 hover:underline text-xs font-medium">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $students->links() }}
        </div>
    @endif

</div>
@endsection
