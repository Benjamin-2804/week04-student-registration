<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Registration Form')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50:'#eff6ff', 100:'#dbeafe', 500:'#3b82f6', 600:'#2563eb', 700:'#1d4ed8' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gradient-to-br from-slate-100 to-blue-50 min-h-screen font-sans antialiased">

    {{-- Header --}}
    <header class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-10">
        <div class="max-w-5xl mx-auto px-6 h-14 flex items-center justify-between">
            <a href="{{ route('students.create') }}"
               class="flex items-center gap-2 text-blue-700 font-bold text-lg tracking-tight hover:text-blue-800 transition">
                {{-- Graduation cap icon --}}
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 14l9-5-9-5-9 5 9 5zm0 0v6m-4-3.5l4 2 4-2"/>
                </svg>
                Student Registration Form
            </a>
            <a href="{{ route('students.index') }}"
               class="text-sm text-gray-500 hover:text-blue-600 transition">
                View All Students
            </a>
        </div>
    </header>

    {{-- Flash banners --}}
    @if (session('success') || session('error'))
        <div class="max-w-5xl mx-auto px-6 pt-5">
            @if (session('success'))
                <div role="alert"
                     class="flex items-start gap-3 rounded-xl bg-green-50 border border-green-300 text-green-800 px-4 py-3 shadow-sm">
                    <svg class="w-5 h-5 mt-0.5 shrink-0 text-green-500" fill="none" stroke="currentColor"
                         stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div role="alert"
                     class="flex items-start gap-3 rounded-xl bg-red-50 border border-red-300 text-red-800 px-4 py-3 shadow-sm">
                    <svg class="w-5 h-5 mt-0.5 shrink-0 text-red-500" fill="none" stroke="currentColor"
                         stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                </div>
            @endif
        </div>
    @endif

    <main class="max-w-5xl mx-auto px-6 py-8">
        @yield('content')
    </main>

</body>
</html>
