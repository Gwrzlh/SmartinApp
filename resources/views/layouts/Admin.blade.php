<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
    <title>Admin Panel</title>
</head>

<body class="flex h-screen bg-gray-100 overflow-hidden">

    <aside
        class="bg-white shadow-xl flex flex-col transition-all duration-500 ease-in-out group w-20 hover:w-64 rounded-r-xl">

        <div class="h-20 ml-3 flex items-center overflow-hidden">
            <div
                class="opacity-0 group-hover:opacity-100 transition-opacity duration-500 flex items-center min-w-[180px]">
                <img src="{{ asset('asset/SmartIn-removebg-preview.png') }}" alt="Logo" class="h-10 w-auto">
            </div>
        </div>

        <nav class="flex-1 ml-2 space-y-2 pr-2 overflow-x-hidden">
            @php
                $navItems = [
                    [
                        'target_route' => 'admin.dashboard',
                        'pattern' => 'admin.dashboard',
                        'icon' => 'akar-home',
                        'label' => 'Dashboard',
                    ],
                    [
                        'target_route' => 'admin.users.index',
                        'pattern' => 'admin.users.*',
                        'icon' => 'ri-user-settings-line',
                        'label' => 'Kelola User',
                    ],
                    [
                        'target_route' => 'admin.category.index',
                        'pattern' => 'admin.category.*',
                        'icon' => 'akar-folder',
                        'label' => 'Kelola Kategori',
                    ],
                    [
                        'target_route' => 'admin.subjects.index',
                        'pattern' => 'admin.subjects.*',
                        'icon' => 'bx-book',
                        'label' => 'Kelola Mapel',
                    ],
                    [
                        'target_route' => 'admin.mentor.index',
                        'pattern' => 'admin.mentor.*',
                        'icon' => 'akar-people-group',
                        'label' => 'Kelola Mentors',
                    ],
                    [
                        'target_route' => 'admin.bundling.index',
                        'pattern' => 'admin.bundling.*',
                        'icon' => 'eos-packages-o',
                        'label' => 'Kelola Bundling',
                    ],
                    [
                        'target_route' => 'admin.schedules.index',
                        'pattern' => 'admin.schedules.*',
                        'icon' => 'akar-calendar',
                        'label' => 'Kelola Schedule',
                    ],
                ];
            @endphp

            @foreach ($navItems as $nav)
                @php $isActive = request()->routeIs($nav['pattern']); @endphp
                <a href="{{ route($nav['target_route']) }}"
                    class="flex items-center p-3 rounded-xl transition-all duration-300 group/item {{ $isActive ? 'bg-cyan-50 text-cyan-600 shadow-sm' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-700' }}">
                    <div
                        class="flex-shrink-0 transition-transform group-hover/item:scale-110 {{ $isActive ? 'scale-110' : '' }}">
                        <x-dynamic-component :component="$nav['icon']" class="w-6 h-6" />
                    </div>
                    <span
                        class="ml-4 {{ $isActive ? 'font-bold' : 'font-semibold' }} whitespace-nowrap opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-x-[-10px] group-hover:translate-x-0">
                        {{ $nav['label'] }}
                    </span>
                </a>
            @endforeach
        </nav>

        <div class="p-4 border-t border-gray-100 bg-gray-300 rounded-t-xl">
            <div class="flex items-center overflow-hidden">
                <div
                    class="flex-shrink-0 w-10 h-10 bg-cyan-500 rounded-full flex items-center justify-center text-white font-bold">
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
