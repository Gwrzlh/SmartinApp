<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>SmartIn Navbar</title>
</head>
<body class="bg-gray-50">
    <div class="flex flex-col h-screen">
        <header class="bg-white shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20"> 
                    
                    <div class="flex-shrink-0">
                        <img src="{{ asset('asset/SmartIn-removebg-preview.png') }}" alt="Logo" class="h-10 w-auto">
                    </div>

                    <nav class="flex items-center space-x-2"> 
                        @php
                        // Style Active dan Hover disamakan persis agar konsisten 100%
                        $items = [
                            [
                                'icon' => 'akar-home', 
                                'label' => 'Dashboard', 
                                'href' => route('kasir.dashboard'), 
                                'active_pattern' => 'kasir.dashboard',
                                'style_active' => 'bg-blue-50 text-blue-600',
                                'style_hover' => 'hover:bg-blue-50 group-hover:text-blue-600'
                            ],
                            [
                                'icon' => 'akar-clipboard', 
                                'label' => 'Transaksi', 
                                'href' => route('kasir.transaction'), 
                                'active_pattern' => 'kasir.transaction',
                                'style_active' => 'bg-emerald-50 text-emerald-600',
                                'style_hover' => 'hover:bg-emerald-50 group-hover:text-emerald-600'
                            ],
                            [
                                'icon' => 'akar-history', 
                                'label' => 'Riwayat', 
                                'href' => route('kasir.riwayat.index'), 
                                'active_pattern' => 'kasir.riwayat.*',
                                'style_active' => 'bg-violet-50 text-violet-600',
                                'style_hover' => 'hover:bg-violet-50 group-hover:text-violet-600'
                            ],
                            [
                                'icon' => 'akar-calendar', 
                                'label' => 'Schedules', 
                                'href' => route('kasir.schedules.index'), 
                                'active_pattern' => 'kasir.schedules.*',
                                'style_active' => 'bg-amber-50 text-amber-600',
                                'style_hover' => 'hover:bg-amber-50 group-hover:text-amber-600'
                            ],
                            [
                                'icon' => 'akar-people-group', 
                                'label' => 'Siswa', 
                                'href' => route('kasir.siswa.index'), 
                                'active_pattern' => 'kasir.siswa.*',
                                'style_active' => 'bg-rose-50 text-rose-600',
                                'style_hover' => 'hover:bg-rose-50 group-hover:text-rose-600'
                            ],
                        ];
                        @endphp

                        @foreach ($items as $item)
                            @php
                                $isActive = request()->routeIs($item['active_pattern']);
                            @endphp

                            <a href="{{ $item['href'] }}" 
                               class="group flex items-center h-11 px-3 rounded-xl transition-colors duration-300 ease-out
                               {{ $isActive ? $item['style_active'] : 'text-gray-400 ' . $item['style_hover'] }}">
                                
                                <div class="flex-shrink-0 transition-transform duration-300 group-hover:scale-110">
                                    <x-dynamic-component :component="$item['icon']" class="h-6 w-6" />
                                </div>

                                <div class="grid transition-[grid-template-columns,margin,opacity] duration-300 ease-in-out 
                                    {{ $isActive ? 'grid-cols-[1fr] ml-3 opacity-100' : 'grid-cols-[0fr] ml-0 opacity-0 group-hover:grid-cols-[1fr] group-hover:ml-3 group-hover:opacity-100' }}">
                                    <div class="overflow-hidden">
                                        <span class="font-bold whitespace-nowrap tracking-tight block">
                                            {{ $item['label'] }}
                                        </span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </nav>

                    <div class="flex items-center">
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="submit" 
                                    class="group flex items-center h-11 px-3 rounded-xl text-gray-400 hover:bg-red-50 hover:text-red-600 transition-colors duration-300 ease-out focus:outline-none">
                                
                                <div class="flex-shrink-0 transition-transform duration-300 group-hover:scale-110 group-hover:text-red-600">
                                    <x-akar-sign-out class="h-6 w-6" />
                                </div>

                                <div class="grid transition-[grid-template-columns,margin,opacity] duration-300 ease-in-out grid-cols-[0fr] ml-0 opacity-0 group-hover:grid-cols-[1fr] group-hover:ml-3 group-hover:opacity-100">
                                    <div class="overflow-hidden">
                                        <span class="font-bold whitespace-nowrap tracking-tight block group-hover:text-red-600">
                                            Logout
                                        </span>
                                    </div>
                                </div>
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </header>

        <main class="flex-1 overflow-auto">
            @yield('content')
        </main>
    </div>
</body>
</html>