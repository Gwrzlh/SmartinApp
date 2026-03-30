@extends('layouts.Owner')

@section('content')
<div class="px-2 sm:px-0">
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Laporan Keuangan</h1>
            <p class="text-sm text-slate-500 mt-1 font-medium">Monitoring pergerakan kas dan riwayat transaksi Smartin</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('owner.exportExcel', request()->query()) }}" class="flex items-center justify-center text-sm font-semibold text-white bg-emerald-600 px-5 py-2.5 rounded-xl shadow-md shadow-emerald-100 hover:bg-emerald-700 hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 bg-indigo-50/50 rounded-full h-24 w-24 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="flex items-center justify-between mb-3 relative z-10">
                <h3 class="text-slate-500 font-semibold text-xs uppercase tracking-wider">Total Omzet</h3>
                <div class="bg-indigo-50 text-indigo-600 p-2 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 relative z-10 tracking-tight">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-2 relative z-10 font-medium italic">Berdasarkan filter</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 bg-emerald-50/50 rounded-full h-24 w-24 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="flex items-center justify-between mb-3 relative z-10">
                <h3 class="text-slate-500 font-semibold text-xs uppercase tracking-wider">Volume Transaksi</h3>
                <div class="bg-emerald-50 text-emerald-600 p-2 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 relative z-10 tracking-tight">{{ $totalTransactions }} Transaksi</p>
            <p class="text-xs text-slate-400 mt-2 relative z-10 font-medium">Transaksi sukses</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 relative overflow-hidden group">
            <div class="absolute -right-6 -top-6 bg-amber-50/50 rounded-full h-24 w-24 transition-transform duration-500 group-hover:scale-150"></div>
            <div class="flex items-center justify-between mb-3 relative z-10">
                <h3 class="text-slate-500 font-semibold text-xs uppercase tracking-wider">Rata-rata</h3>
                <div class="bg-amber-50 text-amber-600 p-2 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800 relative z-10 tracking-tight">Rp {{ number_format($avgTransaction, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-2 relative z-10 font-medium">Ticket size rata-rata</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-8">
        <form action="{{ route('owner.laporanKeuangan') }}" method="GET" class="flex flex-col lg:flex-row gap-4 items-end">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full lg:flex-1">
                <div class="w-full">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                </div>
                <div class="w-full">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Tanggal Selesai</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm px-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                </div>
                <div class="w-full">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Pencarian</label>
                    <div class="relative flex items-center">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / #TRX-ID..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm pl-10 pr-3 py-2.5 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all">
                    </div>
                </div>
            </div>

            <div class="flex gap-2 w-full lg:w-auto shrink-0 mt-4 lg:mt-0">
                <button type="submit" class="flex-1 lg:flex-none justify-center items-center bg-indigo-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-indigo-700 shadow-md shadow-indigo-100 transition-all">
                    Terapkan
                </button>
                <a href="{{ route('owner.laporanKeuangan') }}" class="flex-1 lg:flex-none justify-center items-center text-center bg-slate-100 text-slate-600 px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-slate-200 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100">
                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-[10px] whitespace-nowrap">No</th>
                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-[10px] whitespace-nowrap">ID</th>
                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-[10px] whitespace-nowrap">Waktu</th>
                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-[10px] whitespace-nowrap">Siswa</th>
                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-[10px] whitespace-nowrap">Items</th>
                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-[10px] whitespace-nowrap">Total</th>
                        <th class="px-6 py-4 font-bold text-slate-700 uppercase tracking-wider text-[10px] whitespace-nowrap">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($transactions as $index => $transaction)
                        <tr class="hover:bg-slate-50/30 transition-colors group align-middle">
                            <td class="px-6 py-4 font-medium text-slate-400 text-xs">
                                {{ $transactions->firstItem() + $index }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-bold text-indigo-600 bg-indigo-50/50 px-2 py-1.5 rounded-lg text-xs tracking-wide">
                                    #TRX-{{ $transaction->id }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-600 whitespace-nowrap">
                                <div class="font-medium text-xs">{{ $transaction->tgl_bayar ? $transaction->tgl_bayar->translatedFormat('d M Y') : $transaction->created_at->translatedFormat('d M Y') }}</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">{{ $transaction->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $studentName = 'N/A';
                                    foreach($transaction->details as $detail) {
                                        if (strtolower($detail->item_type) === 'spp') {
                                            // Bypass isu Eager Loading Laravel: cari manual karena item_id = enrollment_id
                                            $enrollment = \App\Models\enrollments::find($detail->item_id);
                                            if ($enrollment && $enrollment->student) {
                                                $studentName = $enrollment->student->student_name;
                                                break;
                                            }
                                        } else {
                                            // Berjalan normal untuk registrasi & bundling
                                            if ($detail->enrollment && $detail->enrollment->student) {
                                                $studentName = $detail->enrollment->student->student_name;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <div class="font-bold text-slate-700 text-xs uppercase">{{ $studentName }}</div>
                            </td>
                            <td class="px-6 py-4 max-w-[200px]">
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($transaction->details as $detail)
                                        <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-1 rounded-md italic font-medium">
                                            {{ $detail->item_type }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">
                                Rp {{ number_format($transaction->total_bayar, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-6 h-6 bg-slate-100 rounded-full flex items-center justify-center mr-2 text-[10px] font-bold text-slate-500 border border-slate-200 uppercase">
                                        {{ substr($transaction->user->full_name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-xs font-medium text-slate-600">{{ $transaction->user->full_name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center opacity-60">
                                    <svg class="w-12 h-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest">Data Tidak Ditemukan</h3>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-5 bg-slate-50/50 border-t border-slate-100">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
                    Records: {{ $transactions->firstItem() ?? 0 }} - {{ $transactions->lastItem() ?? 0 }} / {{ $transactions->total() }}
                </p>
                <div class="pagination-premium">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pagination-premium nav > div:first-child { display: none; }
    .pagination-premium nav span, .pagination-premium nav a {
        @apply px-3 py-1.5 text-[11px] font-bold rounded-xl transition-all border-none shadow-none;
    }
    /* Memastikan height icon calendar bawaan browser presisi */
    input[type="date"]::-webkit-calendar-picker-indicator {
        filter: opacity(0.4);
        margin-top: 1px;
    }
</style>
@endsection