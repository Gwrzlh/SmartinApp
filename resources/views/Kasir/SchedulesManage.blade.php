@extends('layouts.Kasir')
@section('content')

<div class="p-6 bg-gray-50 min-h-screen">
    
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Jadwal</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau kapasitas jadwal dan lihat siswa yang terdaftar.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b text-gray-600 text-sm">
                    <th class="p-4 font-semibold">Mata Pelajaran</th>
                    <th class="p-4 font-semibold">Hari & Waktu</th>
                    <th class="p-4 font-semibold">Mentor</th>
                    <th class="p-4 font-semibold text-center">Kapasitas</th>
                    <th class="p-4 font-semibold text-center">Terisi</th>
                    <th class="p-4 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($schedules as $schedule)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-sm font-medium text-gray-800">
                            {{ $schedule->subject->mapel_name ?? '-' }}
                        </td>
                        <td class="p-4 text-sm text-gray-600">
                            {{ $schedule->hari }}<br>
                            <span class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($schedule->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->jam_selesai)->format('H:i') }}
                            </span>
                        </td>
                        <td class="p-4 text-sm text-gray-600">
                            {{ $schedule->mentor->nama ?? '-' }}
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-block px-2 py-1 bg-gray-100 rounded text-xs text-gray-600 font-medium">
                                {{ $schedule->capacity }} Siswa
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            @php
                                $isFull = $schedule->active_students_count >= $schedule->capacity;
                                $badgeClass = $isFull ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600';
                            @endphp
                            <span class="inline-block px-2 py-1 rounded text-xs font-medium {{ $badgeClass }}">
                                {{ $schedule->active_students_count }} / {{ $schedule->capacity }}
                            </span>
                        </td>
                        <td class="p-4 text-center">
                            <a href="{{ route('kasir.schedules.show', $schedule->id) }}" 
                               class="inline-block px-4 py-2 bg-cyan-50 text-cyan-600 hover:bg-cyan-100 hover:text-cyan-700 text-xs font-semibold rounded-lg transition-colors">
                                Lihat Jadwal
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">
                            Belum ada jadwal yang tersedia.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection