@extends('layouts.Admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen rounded-xl">
    
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-cyan-500">Selamat Datang!</h1>
        <p class="text-yellow-500 font-medium">Selamat Bekerja!</p>
    </div>

    <div class="bg-white rounded-[40px] shadow-2xl shadow-gray-200/50 overflow-hidden flex flex-col md:flex-row items-center p-8 md:p-12 mb-8 relative border border-gray-50">
        
        <div class="flex flex-1 items-center gap-8 border-r-0 md:border-r border-gray-100 pr-0 md:pr-12">
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-cyan-400 to-yellow-400 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000"></div>
                <img src="{{ asset('asset/220d832249670a51c3f560fcba0fd0eb-removebg-preview.png') }}" 
                     alt="avatar" 
                     class="relative w-48 h-48 object-contain drop-shadow-2xl">
            </div>

            <div class="space-y-2">
                <div class="mb-4">
                    <h2 class="text-2xl font-bold text-cyan-600 tracking-tight">{{ now()->format('d F Y') }}</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                        Status Office: Open
                    </span>
                </div>
                
                <div class="text-gray-600 space-y-1">
                    <p class="text-sm">Nama Admin: <span class="font-bold text-cyan-500">{{ session('username', 'Daffa Rizqullah') }}</span></p>
                    <p class="text-sm">Email: <span class="text-gray-400">{{ session('email', 'admin@smartin.com') }}</span></p>
                </div>
            </div>
        </div>

        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-6 pl-0 md:pl-12 mt-8 md:mt-0">
            <div class="flex items-center gap-4 group hover:scale-105 transition-transform">
                <div class="p-3 bg-yellow-50 rounded-2xl text-yellow-500">
                    <x-akar-person class="w-8 h-8" />
                </div>
                <div>
                    <h3 class="text-2xl font-black text-cyan-500">250</h3>
                    <p class="text-gray-400 text-sm font-semibold uppercase tracking-wider">Siswa</p>
                </div>
            </div>

            <div class="flex items-center gap-4 group hover:scale-105 transition-transform">
                <div class="p-3 bg-cyan-50 rounded-2xl text-cyan-500">
                    <x-akar-book class="w-8 h-8" />
                </div>
                <div>
                    <h3 class="text-2xl font-black text-cyan-500">50</h3>
                    <p class="text-gray-400 text-sm font-semibold uppercase tracking-wider">Mata Pelajaran</p>
                </div>
            </div>

            <div class="flex items-center gap-4 group hover:scale-105 transition-transform">
                <div class="p-3 bg-yellow-50 rounded-2xl text-yellow-500">
                    <x-akar-edit class="w-8 h-8" />
                </div>
                <div>
                    <h3 class="text-2xl font-black text-cyan-500">40</h3>
                    <p class="text-gray-400 text-sm font-semibold uppercase tracking-wider">Mentor Pengajar</p>
                </div>
            </div>

            <div class="flex items-center gap-4 group hover:scale-105 transition-transform">
                <div class="p-3 bg-cyan-50 rounded-2xl text-cyan-500">
                    <x-eos-packages-o class="w-8 h-8" />
                </div>
                <div>
                    <h3 class="text-2xl font-black text-cyan-500">25</h3>
                    <p class="text-gray-400 text-sm font-semibold uppercase tracking-wider">Paket Bundling</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-[40px] shadow-xl shadow-gray-200/40 border border-gray-50 max-w-sm">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Status Pembayaran Siswa</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center p-4 bg-cyan-50 rounded-2xl">
                    <span class="text-cyan-600 font-bold">Spp sudah:</span>
                    <span class="text-2xl font-black text-cyan-600">50</span>
                </div>
                
                <div class="flex justify-between items-center p-4 bg-gray-50 rounded-2xl">
                    <span class="text-gray-500 font-bold">Spp Belum:</span>
                    <span class="text-2xl font-black text-gray-500">200</span>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection