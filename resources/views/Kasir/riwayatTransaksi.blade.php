@extends('layouts.Kasir')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Riwayat Transaksi</h2>
        <p class="text-sm text-gray-500 mt-1">Laporan dan histori pembayaran yang telah berhasil diproses.</p>
    </div>
    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('kasir.riwayat.index') }}" class="flex flex-col md:flex-row gap-4">
            
            <div class="flex-1">
                <label for="search" class="sr-only">Cari Transaksi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-dynamic-component component="akar-search" class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors" 
                        placeholder="Cari ID Transaksi atau Nama Siswa...">
                </div>
            </div>

            <div class="w-full md:w-48">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-dynamic-component component="akar-calendar" class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="date" name="date" value="{{ request('date') }}" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-colors">
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'date']))
                    <a href="{{ route('kasir.riwayat.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            No. Transaksi & Tanggal
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Siswa
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Rincian Item
                        </th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Total Nominal
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Kasir
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-semibold text-gray-900">#TRX-{{ $trx->id }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ \Carbon\Carbon::parse($trx->tgl_bayar)->format('d M Y, H:i') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $studentName = '-';
                                foreach($trx->details as $detail) {
                                    // JALUR 1: Jika ini transaksi SPP (item_id merujuk ke ID Enrollment)
                                    if($detail->item_type == 'subject' || $detail->item_type == 'spp') {
                                        $enroll = \App\Models\enrollments::with('student')->find($detail->item_id);
                                        if($enroll && $enroll->student) {
                                            $studentName = $enroll->student->student_name;
                                            break;
                                        }
                                    } 
                                    
                                    // JALUR 2: Jika ini pendaftaran baru (menggunakan relasi hasOne)
                                    if($detail->enrollment && $detail->enrollment->student) {
                                        $studentName = $detail->enrollment->student->student_name;
                                        break;
                                    }
                                }
                            @endphp
                            <span class="text-sm font-medium text-blue-600">{{ $studentName }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-700 max-w-xs truncate">
                                @foreach($trx->details as $detail)
                                    <span class="inline-block px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs mb-1">
                                        {{ $detail->item_type == 'subject' ? 'Mapel' : ucfirst($detail->item_type) }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium text-gray-900">
                            Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($trx->status_pembayaran == 'paid')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-green-200">Lunas</span>
                            @else
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full border border-yellow-200">{{ $trx->status_pembayaran }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                            {{ $trx->user->full_name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm flex justify-center gap-2">
                            <button type="button" onclick="openDetailTrxModal({{ $trx->id }})" class="p-2 text-gray-500 bg-gray-50 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-colors border border-gray-200 shadow-sm flex items-center justify-center" title="Detail Transaksi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                            <a href="{{ route('kasir.invoice', $trx->id) }}" target="_blank" 
                               class="p-2 text-green-500 bg-green-50 hover:bg-green-100 rounded-lg transition-colors border border-green-200 shadow-sm flex items-center justify-center" title="Cetak Struk">
                                <x-akar-reciept class="w-4 h-4"/>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 whitespace-nowrap text-center text-gray-400 font-medium">
                            Belum ada riwayat transaksi.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $transactions->links() }}
        </div>
    </div>

</div>

@include('Kasir.modal.detailRiwayat')

<script>
    window.transactionsData = @json($transactions->items());
</script>
@endsection
