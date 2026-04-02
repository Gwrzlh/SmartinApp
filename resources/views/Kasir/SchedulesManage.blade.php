@extends('layouts.Kasir')
@section('content')

<div class="p-6 bg-gray-50 min-h-screen">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Program Berjalan</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau kapasitas program dan kelangsungan kelas angkatan murid.</p>
        </div>
    </div>

    {{-- Search & Filter Bar --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('kasir.schedules.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-dynamic-component component="akar-search" class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm" 
                        placeholder="Cari Nama Program / Bundling...">
                </div>
            </div>

            <div class="w-full md:w-48">
                <select name="status" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm transition-colors">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-aktif (Arsip)</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-cyan-600 hover:bg-cyan-700 transition-colors">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('kasir.schedules.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table Area --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600 text-sm">
                    <th class="p-4 font-semibold">Program / Angkatan</th>
                    <th class="p-4 font-semibold">Durasi & Mulai</th>
                    <th class="p-4 font-semibold text-center">Kapasitas</th>
                    <th class="p-4 font-semibold text-center">Terisi</th>
                    <th class="p-4 font-semibold text-center">Status Jadwal</th>
                    <th class="p-4 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($programs as $program)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4">
                            <p class="text-sm font-bold text-gray-800">{{ $program->bundling_name }}</p>
                            <p class="text-[11px] text-gray-500 mt-1 uppercase tracking-wider">HARGA: Rp{{ number_format($program->bundling_price,0,',','.') }}</p>
                        </td>
                        <td class="p-4 text-sm text-gray-600">
                            <span class="font-semibold text-gray-700">{{ $program->duration_mounths }} Bulan</span><br>
                            <span class="text-xs text-gray-500">
                                Mulai: {{ \Carbon\Carbon::parse($program->start_date)->format('d M Y') }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-1 bg-gray-100 rounded text-[11px] text-gray-600 font-medium">
                                {{ $program->capacity }} Siswa
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            @php
                                $isFull = $program->active_students_count >= $program->capacity;
                                $colorClass = $isFull ? 'bg-red-50 text-red-600 border-red-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100';
                            @endphp
                            <span class="px-3 py-1 rounded-full border text-[11px] font-bold {{ $colorClass }}">
                                {{ $program->active_students_count }} / {{ $program->capacity }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            @php
                                $statusColor = 'bg-gray-100 text-gray-600 border-gray-200';
                                if($program->program_status == 'Berjalan') $statusColor = 'bg-sky-50 text-sky-600 border-sky-100';
                                if($program->program_status == 'Selesai') $statusColor = 'bg-amber-50 text-amber-600 border-amber-100';
                                if($program->program_status == 'Belum Mulai') $statusColor = 'bg-purple-50 text-purple-600 border-purple-100';
                            @endphp
                            <span class="px-2 py-1 border rounded-md text-[10px] font-bold uppercase {{ $statusColor }} tracking-wider">
                                {{ $program->program_status }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <a href="{{ route('kasir.schedules.show', $program->id) }}" 
                               class="inline-flex items-center px-4 py-1.5 bg-white border border-cyan-200 text-cyan-600 hover:bg-cyan-50 text-xs font-bold rounded-lg transition-all shadow-sm group">
                                Kelola Kelas <x-akar-chevron-right class="w-3 ml-1 group-hover:translate-x-1 transition-transform" />
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-100 p-3 rounded-full mb-3">
                                    <x-dynamic-component component="akar-search" class="h-6 w-6 text-gray-400" />
                                </div>
                                <p class="text-gray-500 text-sm">Program tidak ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination Area --}}
        @if($programs->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                {{ $programs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection