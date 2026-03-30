@extends('layouts.Admin')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-extrabold text-cyan-600">Smartin<span class="text-yellow-500">Dashboard</span></h1>
            <p class="text-gray-500 font-medium">Monitoring operasional kursus hari ini.</p>
        </div>
        <div class="text-right hidden md:block">
            <h2 class="text-lg font-bold text-gray-700">{{ now()->format('l, d F Y') }}</h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                Office: Open
            </span>
        </div>
    </div>

    <div class="bg-white rounded-[30px] shadow-xl shadow-gray-200/50 p-6 md:p-8 mb-8 border border-gray-50">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="flex items-center gap-4 p-4 rounded-2xl hover:bg-yellow-50 transition duration-300 border border-transparent hover:border-yellow-100 group">
                <div class="p-3 bg-yellow-100 rounded-xl text-yellow-600 group-hover:scale-110 transition-transform">
                    <x-akar-person class="w-7 h-7" />
                </div>
                <div>
                    <h3 class="text-2xl font-black text-cyan-600">{{ $jumlahSiswa }}</h3>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Total Siswa</p>
                </div>
            </div>

            <div class="flex items-center gap-4 p-4 rounded-2xl hover:bg-cyan-50 transition duration-300 border border-transparent hover:border-cyan-100 group">
                <div class="p-3 bg-cyan-100 rounded-xl text-cyan-600 group-hover:scale-110 transition-transform">
                    <x-akar-edit class="w-7 h-7" />
                </div>
                <div>
                    <h3 class="text-2xl font-black text-cyan-600">{{ $jumlahMentor }}</h3>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Mentor</p>
                </div>
            </div>

            <div class="flex items-center gap-4 p-4 rounded-2xl hover:bg-yellow-50 transition duration-300 border border-transparent hover:border-yellow-100 group">
                <div class="p-3 bg-yellow-100 rounded-xl text-yellow-600 group-hover:scale-110 transition-transform">
                    <x-akar-book class="w-7 h-7" />
                </div>
                <div>
                    <h3 class="text-2xl font-black text-cyan-600">{{ $jumlahMapel }}</h3>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Mata Pelajaran</p>
                </div>
            </div>

            <div class="flex items-center gap-4 p-4 rounded-2xl hover:bg-cyan-50 transition duration-300 border border-transparent hover:border-cyan-100 group">
                <div class="p-3 bg-cyan-100 rounded-xl text-cyan-600 group-hover:scale-110 transition-transform">
                    <x-eos-packages-o class="w-7 h-7" />
                </div>
                <div>
                    <h3 class="text-2xl font-black text-cyan-600">{{ $jumlahPaket }}</h3>
                    <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Bundling</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-[30px] shadow-sm p-6 border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="w-1 h-6 bg-cyan-500 rounded-full"></span>
                        Jadwal Kursus hari ini
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <tbody class="text-gray-600 text-sm">
                        @forelse($schedules as $s)
                        @php
                            $isNow = now()->format('H:i:s') >= $s->jam_mulai && now()->format('H:i:s') <= $s->jam_selesai;
                        @endphp
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                            <td class="py-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-700">{{ $s->student_count }} Siswa Terdaftar</span>
                                </div>
                            </td>
                            <td class="py-4">
                                <p class="text-cyan-600 font-bold">{{ $s->subject_name }}</p>
                                <p class="text-[10px] text-gray-400 uppercase">{{ $s->mentor_name }}</p>
                            </td>
                            <td class="py-4 font-medium">
                                {{ \Carbon\Carbon::parse($s->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->jam_selesai)->format('H:i') }}
                            </td>
                            <td class="py-4">
                                @if($isNow)
                                    <span class="px-2 py-1 bg-green-100 text-green-600 rounded-lg text-[10px] font-black animate-pulse">
                                        ONGOING
                                    </span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-lg text-[10px] font-bold">
                                        UPCOMING
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-400 italic">
                                Tidak ada jadwal untuk hari ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-[30px] p-6 text-white shadow-lg shadow-cyan-200">
                <div class="flex items-center gap-4 mb-4">
                    <img src="{{ asset('asset/220d832249670a51c3f560fcba0fd0eb-removebg-preview.png') }}" class="w-16 h-16 bg-white/20 rounded-2xl p-1" alt="avatar">
                    <div>
                        <p class="text-cyan-100 text-xs">Login sebagai Admin</p>
                        <h4 class="font-bold text-lg leading-tight">{{ auth()->user()->full_name }}</h4>
                    </div>
                </div>
                <div class="pt-4 border-t border-white/20">
                    <p class="text-xs text-cyan-100 mb-1">Email Terdaftar:</p>
                    <p class="text-sm font-medium truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>

            {{-- <div class="bg-white rounded-[30px] p-6 shadow-sm border border-gray-100">
                <h3 class="text-gray-800 font-bold mb-4 text-sm uppercase tracking-widest">Aksi Cepat</h3>
                <div class="grid grid-cols-2 gap-3">
                    <button class="p-3 text-xs font-bold bg-gray-50 hover:bg-cyan-500 hover:text-white rounded-2xl transition-all border border-gray-100 flex flex-col items-center gap-2 group">
                      <x-akar-person class="w-5 h-5" />  <a href="{{ route('admin.users.index') }}">+ Siswa</a> 
                    </button>
                    <button class="p-3 text-xs font-bold bg-gray-50 hover:bg-yellow-500 hover:text-white rounded-2xl transition-all border border-gray-100 flex flex-col items-center gap-2 group">
                       <x-eos-packages-o class="w-5 h-5" /><a href="{{ 'admin.bundling.index' }}">Tambah Bundling +</a> 
                    </button>
                </div>
            </div> --}}
        </div>

    </div>
</div>
@endsection