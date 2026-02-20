<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>SmartIn Dashboard</title>
</head>
<body class="flex h-screen bg-gray-100 overflow-hidden">

    <aside class="bg-white shadow-xl flex flex-col transition-all duration-500 ease-in-out group w-20 hover:w-64 rounded-r-xl">
        
        <div class="h-20 ml-3 flex items-center overflow-hidden">
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center min-w-[180px]">
                <img src="{{ asset('asset/SmartIn-removebg-preview.png') }}" alt="Logo" class="h-10 w-auto">
            </div>
        </div>

        <nav class="flex-1 ml-2 space-y-2">
            
            <a href="{{ route('admin.dashboard') }}" class="flex items-center p-3 text-gray-600 hover:bg-cyan-50 hover:text-cyan-600 rounded-xl transition-colors group/item">
                <div class="flex-shrink-0">
                    <x-akar-home class="w-7 h-7" />
                </div>
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-x-[-10px] group-hover:translate-x-0">
                    Dashboard
                </span>
            </a>

            <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 text-gray-600 hover:bg-cyan-50 hover:text-cyan-600 rounded-xl transition-colors group/item">
                <div class="flex-shrink-0">
                    <x-ri-user-settings-line class="w-7 h-7" />
                </div>
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-x-[-10px] group-hover:translate-x-0">
                    Kelola User
                </span>
            </a>

            <a href="#" class="flex items-center p-3 text-gray-600 hover:bg-cyan-50 hover:text-cyan-600 rounded-xl transition-colors group/item">
                <div class="flex-shrink-0">
                    <x-akar-folder class="w-7 h-7" />
                </div>
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-x-[-10px] group-hover:translate-x-0">
                    Kelola Kategori
                </span>
            </a>

            <a href="#" class="flex items-center p-3 text-gray-600 hover:bg-cyan-50 hover:text-cyan-600 rounded-xl transition-colors group/item">
                <div class="flex-shrink-0">
                    <x-bx-book class="w-7 h-7" />
                </div>
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-x-[-10px] group-hover:translate-x-0">
                    Kelola Mapel
                </span>
            </a>

            <a href="#" class="flex items-center p-3 text-gray-600 hover:bg-cyan-50 hover:text-cyan-600 rounded-xl transition-colors group/item">
                <div class="flex-shrink-0">
                    <x-akar-people-group class="w-7 h-7" />
                </div>
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-x-[-10px] group-hover:translate-x-0">
                    Kelola Mentors
                </span>
            </a>

            <a href="#" class="flex items-center p-3 text-gray-600 hover:bg-cyan-50 hover:text-cyan-600 rounded-xl transition-colors group/item">
                <div class="flex-shrink-0">
                    <x-eos-packages-o class="w-7 h-7" />
                </div>
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-x-[-10px] group-hover:translate-x-0">
                    Kelola Bundling
                </span>
            </a>

            <a href="#" class="flex items-center p-3 text-gray-600 hover:bg-cyan-50 hover:text-cyan-600 rounded-xl transition-colors group/item">
                <div class="flex-shrink-0">
                    <x-akar-calendar class="w-7 h-7" />
                </div>
                <span class="ml-4 font-medium whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-x-[-10px] group-hover:translate-x-0">
                    Kelola Schedule
                </span>
            </a>

        </nav>

        <div class="p-4 border-t border-gray-100 bg-gray-300 rounded-t-xl">
            <div class="flex items-center overflow-hidden">
                <div class="flex-shrink-0 w-10 h-10 bg-cyan-500 rounded-full flex items-center justify-center text-white font-bold">
                    {{ substr(auth()->user()->full_name, 0, 1) }}
                </div>
                <div class="ml-3 opacity-0 group-hover:opacity-100 transition-opacity duration-500 min-w-[120px]">
                    <p class="text-sm font-semibold text-gray-700 truncate">{{ auth()->user()->full_name }}</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs text-red-500 hover:underline">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col overflow-hidden">
        <section class="flex-1 overflow-y-auto p-8">
            @yield('content')
        </section>
    </main>

</body>
</html>