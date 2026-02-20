<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite('resources/css/app.css')
    <title>SmartIn Navbar</title>
</head>
<body class="bg-gray-50">
    <div class="flex flex-col h-screen">
        <header class="bg-white shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20"> <div class="flex-shrink-0">
                        <img src="{{ asset('asset/SmartIn-removebg-preview.png') }}" alt="Logo" class="h-10 w-auto">
                    </div>

                    <nav class="flex items-center space-x-2"> 
                        @php
                            $items = [
                                [
                                    'icon' => 'akar-home', 
                                    'label' => 'Dashboard', 
                                    'href' => '#', 
                                    'color' => 'hover:bg-blue-50 text-blue-600' // Biru
                                ],
                                [
                                    'icon' => 'akar-clipboard', 
                                    'label' => 'Transaksi', 
                                    'href' => '#', 
                                    'color' => 'hover:bg-emerald-50 text-emerald-600' // Hijau
                                ],
                                [
                                    'icon' => 'akar-history', 
                                    'label' => 'Riwayat', 
                                    'href' => '#', 
                                    'color' => 'hover:bg-amber-50 text-amber-600' // Kuning/Oranye
                                ],
                            ];
                        @endphp

                        @foreach ($items as $item)
                            <a href="{{ $item['href'] }}" 
                            class="group flex items-center bg-transparent {{ $item['color'] }} px-3 py-2 rounded-xl transition-all duration-600 ease-in-out max-w-[50px] hover:max-w-[200px] overflow-hidden">
                                
                                <div class="flex-shrink-0 text-gray-500 group-hover:text-inherit transition-colors duration-300">
                                    <x-dynamic-component :component="$item['icon']" class="h-6 w-6" />
                                </div>

                                <span class="ml-0 group-hover:ml-3 font-semibold opacity-0 group-hover:opacity-100 transition-all duration-500 whitespace-nowrap tracking-tight">
                                    {{ $item['label'] }}
                                </span>
                            </a>
@endforeach
                    </nav>

                   <div class="flex items-center">
                        <form method="POST" action="{{ route('logout') }}" id="logout-form">
                            @csrf
                            <button type="submit" 
                            class="group flex items-center bg-transparent hover:bg-red-50 px-3 py-2 rounded-xl transition-all duration-500 ease-in-out max-w-[50px] hover:max-w-[200px] overflow-hidden focus:outline-none">
                                
                                <div class="flex-shrink-0 text-gray-500 group-hover:text-red-600 transition-colors duration-300">
                                    <x-akar-sign-out class="h-6 w-6" />
                                </div>

                                <span class="ml-0 group-hover:ml-3 text-red-600 font-semibold opacity-0 group-hover:opacity-100 transition-all duration-500 whitespace-nowrap tracking-tight">
                                    Logout
                                </span>
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