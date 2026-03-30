@extends('layouts.Kasir')
@section('content')

<div class="p-6 bg-gray-50 min-h-screen">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Jadwal</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau kapasitas jadwal dan lihat siswa yang terdaftar.</p>
        </div>
    </div>

    {{-- Search & Filter Bar (Konsisten dengan Riwayat) --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('kasir.schedules.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-dynamic-component component="akar-search" class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm" 
                        placeholder="Cari Mata Pelajaran atau Mentor...">
                </div>
            </div>

            <div class="w-full md:w-48">
                <select name="hari" class="block w-full pl-3 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm transition-colors">
                    <option value="">Semua Hari</option>
                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                        <option value="{{ $hari }}" {{ request('hari') == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-cyan-600 hover:bg-cyan-700 transition-colors">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'hari']))
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
                    <th class="p-4 font-semibold">Mata Pelajaran</th>
                    <th class="p-4 font-semibold">Hari & Waktu</th>
                    <th class="p-4 font-semibold">Mentor</th>
                    <th class="p-4 font-semibold text-center">Kapasitas</th>
                    <th class="p-4 font-semibold text-center">Terisi</th>
                    <th class="p-4 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($schedules as $schedule)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 text-sm font-medium text-gray-800">
                            {{ $schedule->subject->mapel_name ?? '-' }}
                        </td>
                        <td class="p-4 text-sm text-gray-600">
                            <span class="font-semibold text-gray-700">{{ $schedule->hari }}</span><br>
                            <span class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($schedule->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->jam_selesai)->format('H:i') }}
                            </span>
                        </td>
                        <td class="p-4 text-sm text-gray-600">
                            {{ $schedule->mentor->nama ?? '-' }}
                        </td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-1 bg-gray-100 rounded text-[11px] text-gray-600 font-medium">
                                {{ $schedule->capacity }} Siswa
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            @php
                                $isFull = $schedule->active_students_count >= $schedule->capacity;
                                $colorClass = $isFull ? 'bg-red-50 text-red-600 border-red-100' : 'bg-emerald-50 text-emerald-600 border-emerald-100';
                            @endphp
                            <span class="px-2 py-1 rounded-full border text-[11px] font-bold {{ $colorClass }}">
                                {{ $schedule->active_students_count }} / {{ $schedule->capacity }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <a href="{{ route('kasir.schedules.show', $schedule->id) }}" 
                               class="inline-flex items-center px-3 py-1.5 bg-white border border-cyan-200 text-cyan-600 hover:bg-cyan-50 text-xs font-bold rounded-lg transition-all shadow-sm">
                                Detail
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
                                <p class="text-gray-500 text-sm">Jadwal tidak ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination Area --}}
        @if($schedules->hasPages())
            <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                {{ $schedules->links() }}
            </div>
        @endif
    </div>
</div>
@endsection