@extends('layouts.Kasir')
@section('content')

<div class="p-6 bg-gray-50 min-h-screen">
    
    <div class="mb-4">
        <a href="{{ route('kasir.schedules.index') }}" class="text-cyan-600 hover:underline text-sm font-medium">
            &larr; Kembali ke Daftar Program
        </a>
    </div>

    {{-- Header Info Card --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6 border-l-4 border-cyan-500 flex flex-col md:flex-row gap-6 justify-between items-start md:items-center">
        <div>
            <span class="px-2 py-1 bg-cyan-50 text-cyan-700 text-[10px] font-bold tracking-wider uppercase rounded">Program Angkatan</span>
            <h1 class="text-2xl font-bold text-gray-800 mt-2">{{ $program->bundling_name }}</h1>
            <p class="text-sm text-gray-600 mt-1">{{ $program->description }}</p>
            <div class="flex gap-4 mt-3 text-[11px] font-medium text-gray-500">
                <span class="flex items-center gap-1"><x-akar-calendar class="w-3"/> Mulai: {{ \Carbon\Carbon::parse($program->start_date)->format('d M Y') }}</span>
                <span class="flex items-center gap-1"><x-akar-clock class="w-3"/> Durasi: {{ $program->duration_mounths }} Bulan</span>
            </div>
        </div>
        <div class="flex gap-4 shrink-0">
            <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 text-center min-w-[100px]">
                <p class="text-xs text-gray-500 mb-1">Kapasitas</p>
                <p class="text-lg font-bold text-gray-800">{{ $program->capacity }}</p>
            </div>
            <div class="bg-cyan-50 border border-cyan-100 rounded-lg p-3 text-center min-w-[100px]">
                <p class="text-xs text-cyan-600 mb-1">Siswa Terdaftar</p>
                <p class="text-lg font-bold text-cyan-700">{{ $enrollments->where('status_pembelajaran', '!=', 'inactive')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Jadwal Kelas (Sidebar Kiri/Kanan) --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-100">
                <div class="p-4 border-b border-gray-50 bg-gray-50/50">
                    <h2 class="font-semibold text-sm text-gray-800 uppercase tracking-widest">Jadwal Kelas</h2>
                </div>
                <div class="p-4 space-y-4">
                    @forelse($schedules as $sch)
                        <div class="p-3 border border-gray-100 rounded-lg bg-white relative overflow-hidden group hover:border-cyan-200 transition-colors">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-cyan-400"></div>
                            <p class="text-[13px] font-bold text-gray-800 mb-0.5 ml-2">{{ $sch->subject->mapel_name ?? '-' }}</p>
                            <p class="text-[11px] font-medium text-gray-600 mb-2 ml-2">{{ $sch->hari }}, {{ \Carbon\Carbon::parse($sch->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($sch->jam_selesai)->format('H:i') }}</p>
                            
                            <div class="flex items-center justify-between text-[10px] text-gray-500 mt-2 pt-2 border-t border-gray-50 ml-2">
                                <span>Mentor: <span class="font-semibold text-gray-700">{{ $sch->mentor->mentor_name ?? '-' }}</span></span>
                                <span>R: {{ $sch->ruangan ?? 'Online' }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 italic text-center py-4">Belum ada jadwal yang dikaitkan ke program ini.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Daftar Siswa (Main Area) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow overflow-hidden border border-gray-100">
                <div class="p-4 border-b border-gray-50 flex justify-between items-center">
                    <h2 class="font-semibold text-lg text-gray-800">Siswa Angkatan Ini</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b text-gray-500 text-[11px] uppercase tracking-wider">
                                <th class="p-4 font-semibold">Nama Siswa</th>
                                <th class="p-4 font-semibold text-center">Status</th>
                                <th class="p-4 font-semibold text-center">Tgl Daftar</th>
                                <th class="p-4 font-semibold text-center">Masa Aktif SPP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($enrollments as $en)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="p-4">
                                        <p class="text-[13px] font-bold text-gray-800">{{ $en->student->student_name ?? '-' }}</p>
                                        <p class="text-[10px] text-gray-400 font-mono tracking-tighter">{{ $en->student->student_nik ?? '-' }}</p>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($en->status_pembelajaran == 'Lulus')
                                            <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-700 border border-blue-200 rounded text-[10px] font-bold uppercase tracking-wider">LULUS</span>
                                        @elseif($en->status_pembelajaran == 'active')
                                            @if(\Carbon\Carbon::parse($en->expired_at)->isBefore(now()))
                                                <span class="inline-block px-2 py-0.5 bg-rose-100 text-rose-700 border border-rose-200 rounded text-[10px] font-bold uppercase tracking-wider">MENUNGGAK</span>
                                            @else
                                                <span class="inline-block px-2 py-0.5 bg-emerald-100 text-emerald-700 border border-emerald-200 rounded text-[10px] font-bold uppercase tracking-wider">AKTIF</span>
                                            @endif
                                        @else
                                            <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 border border-gray-200 rounded text-[10px] font-bold uppercase tracking-wider">{{ $en->status_pembelajaran }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-center text-xs text-gray-600 font-medium">
                                        {{ \Carbon\Carbon::parse($en->tgl_daftar)->format('d M Y') }}
                                    </td>
                                    <td class="p-4 text-center text-xs text-gray-600">
                                        <span class="font-medium {{ \Carbon\Carbon::parse($en->expired_at)->isBefore(now()) ? 'text-rose-600' : 'text-gray-800' }}">
                                            {{ \Carbon\Carbon::parse($en->expired_at)->format('d M Y') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-10 text-center">
                                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3 text-gray-400">
                                            <x-akar-people-group class="w-6 h-6"/>
                                        </div>
                                        <p class="text-sm text-gray-500 font-medium font-medium">Belum ada siswa di program ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
