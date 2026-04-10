@extends('layouts.kasir')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    
    {{-- Top Header Section --}}
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <div class="flex items-center gap-2 mb-2">
                <span class="px-3 py-1 bg-cyan-100 text-cyan-600 rounded-lg text-[10px] font-bold tracking-widest uppercase">Finance Overview</span>
            </div>
            <h1 class="text-4xl font-bold text-gray-800 tracking-tight">Dashboard <span class="text-cyan-600">Kasir</span></h1>
            <p class="text-gray-500 font-medium mt-1">Monitoring pendapatan dan status pembayaran siswa SmartIn.</p>
        </div>
        <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4 group hover:shadow-md transition-all">
            <div class="text-right">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Hari Ini</p>
                <h2 class="text-lg font-bold text-gray-700 leading-none">{{ $today }}</h2>
            </div>
            <div class="w-10 h-10 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-500 group-hover:rotate-12 transition-transform">
                <x-akar-calendar class="w-6 h-6" />
            </div>
        </div>
    </div>

    {{-- 4 Big Metric Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        {{-- Card: Month Income --}}
        <div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-100 group hover:border-cyan-200 transition-all relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:scale-110 transition-transform">
                <x-akar-money class="w-32 h-32" />
            </div>
            <div class="flex flex-col gap-4 relative z-10">
                <div class="w-12 h-12 bg-cyan-100 rounded-2xl flex items-center justify-center text-cyan-600">
                    <x-akar-money class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Rp {{ number_format($incomeMonth, 0, ',', '.') }}</h3>
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mt-1">Income Bulan Ini</p>
                </div>
                <div class="pt-4 border-t border-gray-50 flex items-center justify-between">
                    <span class="text-[10px] text-gray-400">Hari ini:</span>
                    <span class="text-xs font-bold text-emerald-500">Rp {{ number_format($incomeToday, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Card: Month Transactions --}}
        <div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-100 group hover:border-amber-200 transition-all relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:scale-110 transition-transform">
                <x-akar-shopping-bag class="w-32 h-32" />
            </div>
            <div class="flex flex-col gap-4 relative z-10">
                <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center text-amber-600">
                    <x-akar-shopping-bag class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 tracking-tight">{{ $monthTransactions }} <span class="text-sm font-semibold text-gray-400">Trx</span></h3>
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mt-1">Volume Penjualan</p>
                </div>
                <div class="pt-4 border-t border-gray-50 flex items-center justify-between">
                    <span class="text-[10px] text-gray-400">Total Transaksi</span>
                    <span class="text-xs font-bold text-amber-500">Bulan Ini</span>
                </div>
            </div>
        </div>

        {{-- Card: Active Students --}}
        <div class="bg-white p-6 rounded-[32px] shadow-sm border border-gray-100 group hover:border-emerald-200 transition-all relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:scale-110 transition-transform">
                <x-akar-people-group class="w-32 h-32" />
            </div>
            <div class="flex flex-col gap-4 relative z-10">
                <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center text-emerald-600">
                    <x-akar-person class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 tracking-tight">{{ $activeCount }} <span class="text-sm font-semibold text-gray-400">Siswa</span></h3>
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mt-1">Status Belajar Aktif</p>
                </div>
                <div class="pt-4 border-t border-gray-50 flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-600">Terdaftar</span>
                    <x-akar-check class="w-4 h-4 text-emerald-500" />
                </div>
            </div>
        </div>

        {{-- Card: Debt Count (Nunggak) --}}
        <div class="bg-white p-6 rounded-[32px] shadow-sm border border-red-50 group hover:border-red-200 transition-all relative overflow-hidden bg-gradient-to-br from-white to-red-50/20">
            <div class="absolute -right-4 -bottom-4 opacity-[0.05] group-hover:scale-110 transition-transform text-red-500">
                <x-akar-triangle-alert class="w-32 h-32" />
            </div>
            <div class="flex flex-col gap-4 relative z-10">
                <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center text-red-600">
                    <x-akar-triangle-alert class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-red-600 tracking-tight">{{ $debtCount }} <span class="text-sm font-semibold text-red-400">Siswa</span></h3>
                    <p class="text-red-400 text-xs font-semibold uppercase tracking-wider mt-1">Nunggak SPP</p>
                </div>
                <div class="pt-4 border-t border-red-100 flex items-center justify-between">
                    <span class="text-[10px] font-bold text-red-500 uppercase italic leading-none">Harap Follow Up</span>
                    <div class="w-2 h-2 bg-red-500 rounded-full animate-ping"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Left Column (2/3) --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Section: Recent Transactions --}}
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                        <span class="w-1.5 h-8 bg-cyan-600 rounded-full"></span>
                        Transaksi Terakhir
                    </h3>
                    <a href="{{ route('kasir.riwayat.index') }}" class="text-xs font-semibold text-cyan-600 hover:text-cyan-700 underline underline-offset-4 tracking-wider">Lihat Semua</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-gray-400 text-[10px] uppercase tracking-[0.2em] border-b border-gray-50 font-semibold">
                                <th class="pb-5 text-left pl-2">Siswa / Transaksi</th>
                                <th class="pb-5 text-center">Nominal</th>
                                <th class="pb-5 text-center">Tgl Bayar</th>
                                <th class="pb-5 text-right pr-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($recentTransactions as $trx)
                            @php
                                $student = null;
                                foreach($trx->details as $d) {
                                    if($d->enrollment && $d->enrollment->student) {
                                        $student = $d->enrollment->student;
                                        break;
                                    }
                                }
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="py-5 pl-2">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-500 group-hover:bg-cyan-50 group-hover:text-cyan-600 transition-colors font-semibold text-sm">
                                            {{ strtoupper(substr($student->student_name ?? '?', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 leading-tight">{{ $student->student_name ?? 'Unknown Student' }}</p>
                                            <p class="text-[10px] text-gray-400 font-mono mt-0.5">ID: #{{ $trx->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-5 text-center">
                                    <span class="text-sm font-bold text-emerald-600">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</span>
                                </td>
                                <td class="py-5 text-center">
                                    <p class="text-[11px] font-bold text-gray-500 leading-none">{{ \Carbon\Carbon::parse($trx->tgl_bayar)->format('d M Y') }}</p>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase">{{ \Carbon\Carbon::parse($trx->tgl_bayar)->format('H:i') }}</p>
                                </td>
                                <td class="py-5 text-right pr-2">
                                    <a href="{{ route('kasir.invoice', $trx->id) }}" target="_blank" class="p-2 bg-gray-50 text-gray-400 hover:bg-cyan-600 hover:text-white rounded-xl transition-all shadow-sm">
                                        <x-akar-file class="w-4 h-4" />
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center">
                                    <div class="flex flex-col items-center gap-2 grayscale opacity-40">
                                        <x-akar-shopping-bag class="w-12 h-12 text-gray-300" />
                                        <p class="text-sm italic text-gray-400">Belum ada transaksi hari ini.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Section: Daily Schedule --}}
            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                        <span class="w-1.5 h-8 bg-amber-500 rounded-full"></span>
                        Jadwal Belajar Hari Ini
                    </h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($schedules as $s)
                    @php
                        $isNow = now()->format('H:i:s') >= $s->jam_mulai && now()->format('H:i:s') <= $s->jam_selesai;
                    @endphp
                    <div class="p-5 border border-gray-50 bg-gray-50/30 rounded-3xl flex flex-col hover:border-amber-100 hover:bg-white transition-all group relative overflow-hidden">
                        @if($isNow)
                        <div class="absolute -right-1 -top-1 px-3 py-1 bg-emerald-500 text-white text-[8px] font-black uppercase rounded-bl-xl animate-pulse tracking-widest">Ongoing</div>
                        @endif
                        
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-white rounded-2xl shadow-sm text-amber-500 group-hover:scale-110 transition-transform">
                                <x-akar-calendar class="w-6 h-6" />
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Pukul</p>
                                <p class="text-sm font-bold text-gray-700 leading-none">
                                    {{ \Carbon\Carbon::parse($s->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($s->jam_selesai)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                        
                        <h4 class="text-lg font-bold text-gray-800 leading-tight mb-1">{{ $s->subject_name }}</h4>
                        <p class="text-xs text-cyan-600 font-bold uppercase tracking-tight">{{ $s->mentor_name }}</p>
                        
                        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                            <span class="text-[10px] font-semibold text-gray-400 uppercase">Siswa Terdaftar</span>
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-bold">{{ $s->student_count }} Siswa</span>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-2 py-10 text-center bg-gray-50/50 rounded-[32px] border border-dashed border-gray-200">
                        <p class="text-sm text-gray-400 italic">Tidak ada jadwal pengajaran terdaftar hari ini.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right Column (1/3) --}}
        <div class="space-y-8">
            {{-- Widget: Profile & Quick Links --}}
            <div class="space-y-4">
                <div class="bg-gradient-to-br from-cyan-600 to-cyan-700 rounded-[32px] p-8 text-white shadow-xl shadow-cyan-100 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
                    <div class="flex items-center gap-5 relative z-10">
                        <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl p-0.5 flex items-center justify-center">
                            <img src="{{ asset('asset/220d832249670a51c3f560fcba0fd0eb-removebg-preview.png') }}" class="w-full h-full object-contain" alt="avatar">
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-cyan-200 uppercase tracking-widest mb-1">Sesi Kasir Aktif</p>
                        <h4 class="text-xl font-bold tracking-tight leading-none">{{ $cashierName }}</h4>
                            <div class="flex items-center gap-1.5 mt-2">
                                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-widest">Online</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-white/10 relative z-10">
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('kasir.transaction') }}" class="flex flex-col gap-2 p-3 bg-white/10 hover:bg-white/20 rounded-2xl transition-all border border-white/5">
                                <x-akar-cart class="w-5 h-5" />
                                <span class="text-[10px] font-bold uppercase tracking-widest">Transaksi</span>
                            </a>
                            <a href="{{ route('kasir.riwayat.index') }}" class="flex flex-col gap-2 p-3 bg-white/10 hover:bg-white/20 rounded-2xl transition-all border border-white/5">
                                <x-akar-history class="w-5 h-5" />
                                <span class="text-[10px] font-bold uppercase tracking-widest">Riwayat</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Widget: Arrears Monitoring (Debt) --}}
            <div class="bg-white rounded-[32px] shadow-sm border border-red-50 p-8 overflow-hidden relative">
                @if($totalPiutangLulusan > 0)
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center text-red-600 shadow-sm shadow-red-100">
                        <x-akar-triangle-alert class="w-6 h-6" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-red-400 uppercase tracking-widest mb-1 leading-none">Piutang Tertanggung</p>
                        <h3 class="text-2xl font-bold text-red-600 leading-none tracking-tight">Rp {{ number_format($totalPiutangLulusan, 0, ',', '.') }}</h3>
                    </div>
                </div>

                <div class="space-y-4">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-4">Top Penunggak Lulusan</p>
                    @foreach($topDebtors as $i => $debtor)
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs
                                {{ $i === 0 ? 'bg-red-100 text-red-600' : ($i === 1 ? 'bg-orange-100 text-orange-600' : 'bg-gray-50 text-gray-400') }}">
                                #{{ $i + 1 }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-800 leading-tight truncate max-w-[120px]">{{ $debtor['student']->student_name }}</p>
                                <p class="text-[9px] text-gray-400 font-mono tracking-tighter">{{ $debtor['student']->student_nik }}</p>
                            </div>
                        </div>
                        <span class="text-[12px] font-bold text-red-500">Rp{{ number_format($debtor['total'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 pt-6 border-t border-red-50">
                    <a href="{{ route('kasir.riwayat.index') }}" class="w-full py-3 px-4 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all text-center block shadow-sm shadow-red-100">
                        Detail Penunggakan
                    </a>
                </div>
                @else
                <div class="py-6 text-center">
                    <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center text-emerald-500 mx-auto mb-4">
                        <x-akar-circle-check class="w-8 h-8" />
                    </div>
                    <h4 class="text-lg font-black text-emerald-800">Status Aman!</h4>
                    <p class="text-xs text-emerald-600/60 leading-relaxed font-medium mt-1 uppercase tracking-tight px-4">Tidak ada tunggakan siswa <br> lulus terdeteksi.</p>
                </div>
                @endif
            </div>

            {{-- Widget: System Summary Tips --}}
            <div class="bg-amber-50 rounded-[32px] p-6 border border-amber-100/50">
                <div class="flex items-start gap-4">
                    <div class="text-amber-600 shrink-0">
                        <x-akar-info class="w-5 h-5" />
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-amber-800 uppercase tracking-widest mb-1">Catatan Kasir</h4>
                        <p class="text-[11px] text-amber-600 leading-relaxed font-medium italic">
                            Siswa yang belum menyelesaikan SPP selama program berlangsung akan otomatis masuk ke daftar penunggak ("Debt") saat program ditutup.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    @keyframes pulse-custom {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .animate-pulse {
        animation: pulse-custom 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endsection