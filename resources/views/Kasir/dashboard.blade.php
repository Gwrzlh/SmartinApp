@extends('layouts.Kasir')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen rounded-xl">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-cyan-500">Dashboard Kasir</h1>
        <p class="text-sm text-gray-500">Halaman untuk melihat ringkasan transaksi dan jadwal hari ini</p>
    </div>

    <div class="bg-white rounded-[24px] shadow-lg overflow-hidden flex flex-col md:flex-row items-center p-6 md:p-8 mb-6 border border-gray-50">
        <div class="flex items-center gap-6 flex-1">
            <div class="relative">
                <div class="absolute -inset-1 bg-gradient-to-r from-cyan-300 to-yellow-300 rounded-full blur opacity-25"></div>
                <img src="{{ asset('asset/220d832249670a51c3f560fcba0fd0eb-removebg-preview.png') }}" alt="avatar" class="relative w-36 h-36 object-contain drop-shadow-2xl">
            </div>

            <div>
                <h2 class="text-2xl font-bold text-cyan-600">{{ $today ?? now()->format('d F Y') }}</h2>
                <div class="mt-2 text-gray-600">
                    <p>Nama Kasir: <span class="font-semibold text-cyan-500">{{ $cashierName ?? auth()->user()->full_name ?? auth()->user()->name }}</span></p>
                    <p class="mt-1">Status Office: <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">{{ $officeStatus ?? 'Open' }}</span></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6 md:mt-0 md:pl-8 w-full md:w-auto">
            <div class="flex items-center gap-4 p-3 bg-yellow-50 rounded-lg">
                <x-ri-user-settings-line class="w-6 h-6 text-yellow-500" />
                <div>
                    <div class="text-2xl font-bold text-cyan-500">{{ $unpaidCount ?? 0 }}</div>
                    <div class="text-xs text-gray-400 uppercase font-semibold">Transaksi Belum Bayar</div>
                </div>
            </div>

            <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-lg">
                <x-ri-user-settings-line class="w-6 h-6 text-cyan-500" />
                <div>
                    <div class="text-2xl font-bold text-cyan-500">{{ $inactiveCount ?? 0 }}</div>
                    <div class="text-xs text-gray-400 uppercase font-semibold">Siswa Tidak Aktif</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-[20px] shadow-sm border border-gray-50">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Shortcut</h3>
            <div class="space-y-3">
                <a href="{{ route('kasir.transaction') ?? '#' }}" class="block p-3 rounded-lg hover:bg-gray-50">Transaksi Baru</a>
                <a href="{{ route('kasir.riwayat.index') ?? '#' }}" class="block p-3 rounded-lg hover:bg-gray-50">Riwayat Transaksi</a>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[20px] shadow-sm border border-gray-50 lg:col-span-2">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Jadwal Hari Ini</h3>

            <div class="space-y-4">
                @if($schedules->count())
                    @foreach($schedules as $sch)
                        <div class="p-4 rounded-lg border border-gray-100 hover:shadow-md transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-sm text-gray-500">{{ $sch->hari ?? '' }} • {{ \Carbon\Carbon::parse($sch->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($sch->jam_selesai)->format('H:i') }}</div>
                                    <h4 class="text-lg font-bold text-cyan-600">{{ $sch->subject_name ?? '—' }}</h4>
                                    <div class="text-sm text-gray-500">Pengajar: {{ $sch->mentor_name ?? '—' }} • Ruangan: {{ $sch->ruangan ?? '-' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-black text-cyan-500">{{ $sch->student_count ?? 0 }}</div>
                                    <div class="text-xs text-gray-400">Siswa Terdaftar</div>
                                </div>
                            </div>

                            @if(!empty($sch->students) && count($sch->students))
                                <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-2 text-sm text-gray-600">
                                    @foreach($sch->students as $st)
                                        <div class="p-2 bg-gray-50 rounded">{{ $st->student_name }}</div>
                                    @endforeach
                                </div>
                            @else
                                <div class="mt-3 text-sm text-gray-400">Belum ada siswa terdaftar pada jadwal ini.</div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="p-4 text-gray-500">Tidak ada jadwal untuk hari ini.</div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection