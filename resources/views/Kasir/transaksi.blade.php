@extends('layouts.Kasir')

@section('content')
<div class="h-screen bg-[#F8F9FA] overflow-hidden flex flex-col p-4 font-sans text-gray-700">
    
    {{-- Alerts Area --}}
    @if(session('success') || session('error') || session('debt_warning') || $errors->any())
        <div class="mb-4 shrink-0">
            @if(session('success'))
                <div class="mb-4 bg-white border-l-4 border-emerald-400 p-4 shadow-sm flex justify-between items-center rounded-r-lg">
                    <span class="text-sm font-medium text-emerald-800">{{ session('success') }}</span>
                    <span class="text-[10px] text-emerald-600 italic">Mencetak struk otomatis...</span>
                </div>

                @if(request('print_invoice'))
                    <iframe id="printFrame" src="{{ route('kasir.invoice', request('print_invoice')) }}" style="display:none;"></iframe>

                   <script>
                    (function() {
                        const frame = document.getElementById('printFrame');
                        if (frame) {
                            frame.onload = function() {
                                try {
                                    frame.contentWindow.focus();
                                    frame.contentWindow.print();
                                    
                                    // Bersihkan URL tanpa reload halaman agar tidak print ulang saat F5
                                    const url = new URL(window.location);
                                    url.searchParams.delete('print_invoice');
                                    window.history.replaceState({}, '', url);
                                } catch (e) {
                                    console.error("Gagal mencetak otomatis:", e);
                                }
                            };
                        }
                    })();
                </script>
                @endif
            @endif

            {{-- ============================================================ --}}
            {{-- ALERT TUNGGAKAN SPP: Banner merah prioritas tinggi             --}}
            {{-- Muncul ketika kasir mencoba checkout untuk siswa yang menunggak --}}
            {{-- ============================================================ --}}
            @if(session('debt_warning'))
                <div class="mb-3 bg-red-50 border-l-4 border-red-500 p-4 shadow-sm rounded-r-lg flex items-start gap-3">
                    <div class="shrink-0 mt-0.5">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold text-red-700 mb-1">🚫 Pendaftaran Diblokir — Siswa Menunggak</p>
                        <p class="text-xs text-red-600 leading-relaxed">{!! session('debt_warning') !!}</p>
                        <p class="text-[10px] text-red-400 mt-2 italic">Arahkan siswa untuk menyelesaikan pembayaran SPP tunggakan terlebih dahulu melalui tab "Bayar SPP".</p>
                    </div>
                </div>
            @endif

            @if(session('error') || $errors->any())
                <div class="bg-white border-l-4 border-rose-400 p-4 shadow-sm rounded-r-lg">
                    <ul class="text-xs text-rose-500 list-disc list-inside">
                        @if(session('error')) <li>{{ session('error') }}</li> @endif
                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif
        </div>
    @endif

    {{-- Main 3-Column Layout --}}
    <div class="flex-1 flex gap-4 overflow-hidden min-h-0">
        
        <div class="w-[300px] bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
            <div class="p-4 border-b border-gray-50 flex justify-between items-center shrink-0">
                <h2 class="text-sm font-semibold text-gray-800">Daftar Siswa</h2>
                <x-akar-three-line-horizontal class="w-4 text-gray-400"/>
            </div>

            <div class="p-4 flex flex-col flex-1 overflow-hidden">
                <form method="GET" action="{{ route('kasir.transaction') }}" class="relative mb-3 shrink-0">
                    <input type="text" name="q_student" value="{{ request('q_student') }}" 
                           class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-lg text-sm focus:bg-white outline-none transition-all" 
                           placeholder="Cari siswa...">
                    <x-akar-search class="absolute left-3 top-2.5 w-4 text-gray-400"/>
                </form>

                <button onclick="openStudentModal()" class="w-full py-2 mb-4 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm transition-colors flex items-center justify-center gap-2 shrink-0">
                    <x-akar-plus class="w-4"/> Tambah Siswa
                </button>

                <div class="flex-1 overflow-y-auto space-y-2 pr-1 custom-scroll">
                    @foreach ($students as $student)
                    <div class="p-3 border rounded-lg transition-all {{ $selectedStudent && $selectedStudent->id == $student->id ? 'border-blue-300 bg-blue-50/30' : 'border-gray-50 bg-gray-50/50 hover:bg-white hover:border-gray-200 group' }}">
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-[10px] text-gray-400 font-mono">{{ $student->student_nik }}</span>
                            <span class="text-[9px] {{ $student->status == 'active' ? 'text-emerald-500' : 'text-gray-300' }}">● {{ strtoupper($student->status) }}</span>
                        </div>
                        <p class="text-[13px] font-medium text-gray-800">{{ $student->student_name }}</p>
                        
                        <div class="mt-3 flex gap-2">
                            <form action="{{ route('kasir.selectStudent') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                <button class="w-full py-1 text-[10px] border border-gray-200 rounded hover:bg-white hover:border-blue-400 transition-colors uppercase tracking-wider">Pilih</button>
                            </form>
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="openEditModal({{ $student->id }})" class="p-1 text-gray-400 hover:text-amber-500"><x-akar-edit class="w-3"/></button>
                                {{-- <button onclick="confirmDelete({{ $student->id }})" class="p-1 text-gray-400 hover:text-rose-500"><x-akar-trash-can class="w-3"/></button> --}}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="p-4 border-t border-gray-50 shrink-0 bg-gray-50/20">
                <a href="{{ route('kasir.transaction',['mode'=>'spp']) }}" class="block w-full py-2 border border-dashed border-rose-200 text-rose-500 text-[11px] text-center rounded-lg hover:bg-rose-50 font-medium">
                    {{$countInactive}} Tagihan SPP Terdeteksi
                </a>
            </div>
        </div>

        <div class="flex-1 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden">
            <div class="flex border-b border-gray-50 shrink-0">
                @foreach(['paket' => 'Beli Paket', 'spp' => 'Bayar SPP'] as $m => $label)
                <a href="{{ route('kasir.transaction',['mode'=>$m]) }}" 
                   class="px-8 py-4 text-sm transition-all relative {{ $mode == $m ? 'text-blue-600 font-medium' : 'text-gray-400 hover:text-gray-600' }}">
                    {{ $label }}
                    @if($mode == $m) <div class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600"></div> @endif
                </a>
                @endforeach
            </div>

            <div class="p-5 flex flex-col flex-1 overflow-hidden">
                <form method="GET" action="{{ route('kasir.transaction') }}" class="flex gap-3 mb-5 shrink-0">
                    <input type="hidden" name="mode" value="{{ $mode }}">
                    @if($mode=='spp')
                        <input type="hidden" name="q_spp" value="{{ request('q_spp') }}">
                    @endif

                    @if($mode == 'paket' || $mode == 'mapel')
                    <select name="category_id" onchange="this.form.submit()" class="text-xs border border-gray-100 bg-gray-50 rounded-lg px-3 outline-none focus:bg-white transition-all">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                        @endforeach
                    </select>
                    @endif
                    <div class="relative flex-1">
                        @if($mode == 'paket')
                            <input type="text" name="q_bundling" value="{{ request('q_bundling') }}" placeholder="Cari bundling..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-lg text-sm outline-none focus:bg-white transition-all">
                        @elseif($mode == 'spp')
                            <input type="text" name="q_spp" value="{{ request('q_spp') }}" placeholder="Cari tagihan SPP..." class="w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-lg text-sm outline-none focus:bg-white transition-all">
                        @endif
                        <x-akar-search class="absolute left-3 top-2.5 w-4 text-gray-400"/>
                        <button type="submit" class="hidden"></button>
                    </div>
                </form>

                <div class="flex-1 overflow-y-auto custom-scroll pr-1">
                    @if($mode == 'paket')
                        <div class="grid grid-cols-2 gap-4">
                            @foreach ($bundlings as $bundling)
                            <div class="p-4 border border-gray-50 bg-gray-50/30 rounded-xl flex flex-col hover:border-blue-100 hover:bg-white transition-all group relative">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="text-amber-500 bg-amber-50 p-2 rounded-lg shrink-0"><x-eos-packages-o class="w-5"/></div>
                                    <div class="text-right">
                                        <p class="text-xs font-semibold text-gray-900 leading-none">Rp{{ number_format($bundling->bundling_price,0,',','.') }}</p>
                                        <span class="text-[9px] text-gray-400 uppercase tracking-tighter">per bulan</span>
                                    </div>
                                </div>
                                <h4 class="text-[13px] font-medium text-gray-800 mb-1 leading-tight">{{$bundling->bundling_name}}</h4>
                                <p class="text-[11px] text-gray-400 mb-4 line-clamp-2 italic leading-relaxed">{{ $bundling->description }}</p>
                                <p class="text-[11px] text-gray-400 mb-4 line-clamp-2 italic leading-relaxed">Dimulai Pada : {{ \Carbon\Carbon::parse($bundling->start_date)->format('d F Y') }}</p>
                                
                                <form action="{{ route('kasir.cart.add') }}" method="POST" class="mt-auto">
                                    @csrf
                                    <input type="hidden" name="type" value="bundling">
                                    <input type="hidden" name="id" value="{{ $bundling->id }}"><input type="hidden" name="name" value="{{ $bundling->bundling_name }}"><input type="hidden" name="price" value="{{ $bundling->bundling_price }}">
                                    <button class="w-full py-2 bg-white border border-gray-200 text-gray-600 rounded-lg text-[10px] font-semibold uppercase tracking-wider hover:bg-blue-500 hover:text-white hover:border-transparent transition-all">Tambah Ke Keranjang</button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                   @elseif($mode == 'spp')
                    @if($selectedStudent)
                        <div class="space-y-3">
                            @forelse($studentEnrollments as $enrollment)
                                @php
                                    $expiredDate = $enrollment->expired_at ? \Carbon\Carbon::parse($enrollment->expired_at) : null;
                                    $isExpired = !$expiredDate || $expiredDate->isBefore(now());

                                    // Logic Cek Pelunasan Akhir (Atap Pembayaran)
                                    $startDate = \Carbon\Carbon::parse($enrollment->bundling->start_date);
                                    $duration = $enrollment->bundling->duration_mounths;
                                    $maxExpired = $startDate->copy()->addMonths($duration);
                                    
                                    // Siswa dianggap lunas jika masa aktif sudah mencapai atau melebihi durasi program
                                    $isFullyPaid = $expiredDate && $expiredDate->greaterThanOrEqualTo($maxExpired);
                                @endphp

                                <div class="p-4 bg-white border {{ $isFullyPaid ? 'border-emerald-100 bg-emerald-50/20' : 'border-gray-100' }} rounded-xl flex flex-col hover:border-blue-100 transition-all shadow-sm">
                                    <div class="flex justify-between items-start mb-2">
                                        <p class="text-[13px] font-medium text-gray-800">{{ $enrollment->bundling->bundling_name ?? 'Program Bundling' }}</p>
                                        
                                        @if($isFullyPaid)
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-600 text-[9px] font-bold border border-emerald-200">LUNAS</span>
                                        @elseif($isExpired)
                                            <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-500 text-[9px] font-semibold border border-rose-100">Menunggak</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-500 text-[9px] font-semibold border border-emerald-100">Aktif</span>
                                        @endif
                                    </div>

                                    <div class="text-[11px] text-gray-500 mb-4 flex items-center gap-1.5">
                                        <x-akar-calendar class="w-3" />
                                        <span>Masa Aktif: <span class="font-medium text-gray-700">{{ $expiredDate ? $expiredDate->format('d F Y') : '-' }}</span></span>
                                    </div>

                                    @if($isFullyPaid)
                                        {{-- Tampilan ketika sudah lunas sampai akhir durasi --}}
                                        <div class="w-full py-2 bg-gray-50 text-gray-400 border border-gray-100 rounded-lg text-[10px] font-bold text-center uppercase tracking-wider cursor-not-allowed">
                                            Pembayaran Selesai
                                        </div>
                                    @else
                                        <form action="{{ route('kasir.cart.add') }}" method="POST" class="mt-auto">
                                            @csrf
                                            <input type="hidden" name="type" value="spp">
                                            <input type="hidden" name="id" value="{{ $enrollment->id }}">
                                            <input type="hidden" name="name" value="SPP - {{ $enrollment->bundling->bundling_name ?? '' }} (S.d: {{ ($expiredDate ?? now())->copy()->addMonth()->format('d M Y') }})">
                                            <input type="hidden" name="price" value="{{ $enrollment->bundling->bundling_price ?? 0 }}">
                                            <button class="w-full py-2 bg-blue-50 hover:bg-blue-500 hover:text-white text-blue-600 rounded-lg text-[10px] font-semibold uppercase tracking-wider transition-all">Bayar SPP (+1 Bulan)</button>
                                        </form>
                                    @endif
                                </div>
                            @empty
                                <div class="p-4 text-center text-sm text-gray-500 italic bg-gray-50 rounded-lg border border-gray-100">
                                    Siswa ini belum mengambil mata pelajaran apapun.
                                </div>
                            @endforelse
                        </div>
                    @else
                        <div class="mb-4">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Daftar Siswa dengan Tunggakan SPP</h4>
                            <div class="grid grid-cols-1 gap-2">
                                @forelse($inactiveStudents as $student)
                                    <div class="p-3 bg-white border border-rose-100 rounded-xl flex justify-between items-center group hover:border-rose-300 transition-all shadow-sm">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 leading-none mb-1">{{ $student->student_name }}</p>
                                            <span class="text-[10px] text-gray-400 font-mono tracking-tighter">{{ $student->student_nik }}</span>
                                        </div>
                                        <form action="{{ route('kasir.selectStudent') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="student_id" value="{{ $student->id }}">
                                            <input type="hidden" name="mode" value="spp">
                                            <button class="px-4 py-1.5 bg-rose-500 text-white text-[10px] font-bold rounded-md hover:bg-rose-600 transition-colors uppercase">Pilih & Bayar</button>
                                        </form>
                                    </div>
                                @empty
                                    <div class="flex flex-col items-center justify-center py-10 text-center bg-emerald-50/30 rounded-xl border border-dashed border-emerald-100">
                                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mb-3 text-emerald-400">
                                            <x-akar-check class="w-6" />
                                        </div>
                                        <h4 class="text-[13px] font-medium text-emerald-800 mb-1">Semua Siswa Terbayar!</h4>
                                        <p class="text-[11px] text-emerald-600/70">Tidak ada tunggakan SPP yang terdeteksi saat ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                @endif
                </div>
            </div>
        </div>

        <div class="w-[350px] bg-white rounded-xl shadow-md border border-gray-100 flex flex-col overflow-hidden">
            <div class="p-4 border-b border-gray-50 bg-gray-50/30 shrink-0">
                <h3 class="text-[11px] font-semibold text-gray-400 uppercase tracking-[0.2em] text-center">Ringkasan Pesanan</h3>
            </div>

            <div class="p-5 flex flex-col flex-1 overflow-hidden">
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-400">Kasir</span>
                        <span class="font-medium text-gray-700">{{ $cashierName }}</span>
                    </div>
                    <div class="p-3 bg-blue-50/50 rounded-lg border border-blue-100/50">
                        <span class="text-[10px] text-blue-400 uppercase block mb-1">Siswa Pelanggan</span>
                        <p class="text-sm font-medium text-blue-800">{{ $selectedStudent->student_name ?? 'Pilih Siswa Terlebih Dahulu' }}</p>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto custom-scroll mb-6 pr-1">
                    @forelse($cart as $i => $item)
                        <div class="flex justify-between items-center py-2.5 border-b border-gray-50">
                            <div class="flex-1 pr-4">
                                <p class="text-[13px] text-gray-800 leading-tight mb-0.5">{{ $item['name'] }}</p>
                                <span class="text-[9px] text-gray-400 font-medium bg-gray-100 px-1.5 py-0.5 rounded uppercase tracking-wider">{{ $item['type'] }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-medium text-gray-900">Rp{{ number_format($item['price'],0,',','.') }}</span>
                                @if($item['type'] !== 'registration')
                                <form action="{{ route('kasir.cart.remove',$i) }}" method="POST">
                                    @csrf
                                    <button class="text-gray-300 hover:text-rose-500 transition-colors"><x-akar-cross class="w-3"/></button>
                                </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex flex-col items-center justify-center text-gray-300 grayscale scale-90 opacity-40">
                            <x-akar-shopping-bag class="w-10 mb-2"/>
                            <p class="text-[11px] italic">Keranjang belanja kosong</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-auto pt-4 border-t border-gray-100 space-y-4">
                    <div class="flex justify-between items-baseline">
                        <span class="text-xs text-gray-400">Total Pembayaran</span>
                        <span class="text-2xl font-light text-gray-900 tracking-tighter">Rp{{ number_format($totalAmount,0,',','.') }}</span>
                    </div>

                    <div class="space-y-3 bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <div class="flex items-center justify-between">
                            <label class="text-[11px] text-gray-500 font-medium">Uang Diterima</label>
                            <input type="number" id="paid_amount" placeholder="0" 
                                   class="w-32 bg-transparent border-b border-gray-300 focus:border-blue-500 outline-none py-0.5 text-right text-sm transition-all font-medium">
                        </div>
                        <div class="flex items-center justify-between text-[11px]">
                            <span class="text-gray-400">Uang Kembalian</span>
                            <span id="change_display" class="text-emerald-500 font-semibold italic">Rp 0</span>
                        </div>
                    </div>

                    <form action="{{ route('kasir.checkout') }}" method="POST">
                        @csrf
                        <input type="hidden" name="paid_amount" id="final_paid_amount">
                        <button id="checkoutBtn" type="submit" 
                                class="w-full py-4 bg-gray-900 hover:bg-black text-white rounded-xl text-xs font-semibold uppercase tracking-[0.2em] transition-all shadow-md active:scale-95 disabled:opacity-30 disabled:grayscale disabled:cursor-not-allowed"
                                {{ !$selectedStudent || count($cart)==0 ? 'disabled' : '' }}>
                            Checkout & Bayar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================================= --}}
    {{-- SCHEDULE PLACEMENT PANEL (LOGIKA ANDA KEMBALI DI SINI) --}}
    {{-- ================================================================= --}}
    @if(isset($transaction_id) && $enrollmentsToSchedule->isNotEmpty())
    <div class="shrink-0 mt-4 bg-white rounded-xl shadow-lg border border-blue-100 p-6 overflow-hidden animate-in slide-in-from-bottom duration-500">
        <div class="flex justify-between items-start border-b border-gray-50 pb-4 mb-4">
            <div>
                <h2 class="text-lg font-medium text-gray-800">Penempatan Jadwal Belajar</h2>
                <p class="text-[11px] text-gray-500 mt-0.5">Siswa: <span class="font-semibold text-blue-600">{{ $studentForSchedule->student_name ?? 'Siswa' }}</span> | ID Transaksi: #{{ $transaction_id }}</p>
            </div>
            <div class="bg-amber-50 px-3 py-1 rounded-full border border-amber-100 text-[10px] text-amber-600 font-medium italic">Pilih jadwal sebelum pendaftaran selesai</div>
        </div>

        <form action="{{ route('kasir.transaction.saveSchedules', $transaction_id) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($enrollmentsToSchedule as $i => $enrollment)
                    <div class="bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-[10px] font-bold">{{ $i + 1 }}</span>
                            <h3 class="font-medium text-[13px] text-gray-800 line-clamp-1">{{ $enrollment->subject->mapel_name ?? 'Mata Pelajaran' }}</h3>
                        </div>

                        <select name="schedules[{{ $enrollment->id }}]" required class="w-full border-gray-200 rounded-lg bg-white p-2 text-[11px] outline-none focus:ring-1 focus:ring-blue-400 transition-all border">
                            <option value="">-- Pilih Hari & Waktu --</option>
                            @if($enrollment->subject && $enrollment->subject->schedules->count() > 0)
                                @foreach($enrollment->subject->schedules as $schedule)
                                    @php $mentorName = $schedule->mentor->nama ?? 'No Mentor'; @endphp
                                    @if($schedule->remaining_capacity > 0)
                                        <option value="{{ $schedule->id }}">
                                            {{ $schedule->hari }} | {{ \Carbon\Carbon::parse($schedule->jam_mulai)->format('H:i') }} - {{ $mentorName }} (Sisa: {{ $schedule->remaining_capacity }})
                                        </option>
                                    @else
                                        <option value="" disabled class="text-rose-400">{{ $schedule->hari }} | Penuh</option>
                                    @endif
                                @endforeach
                            @else
                                <option value="" disabled>Jadwal belum tersedia</option>
                            @endif
                        </select>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 flex justify-end gap-3 pt-4 border-t border-gray-50">
                <a href="{{ route('kasir.transaction') }}" class="px-5 py-2 text-[11px] font-medium text-gray-400 hover:text-gray-600 transition-colors uppercase tracking-widest">Abaikan</a>
                <button type="submit" class="px-8 py-2 bg-blue-600 text-white text-[11px] font-semibold rounded-lg hover:bg-blue-700 shadow-md transition-all uppercase tracking-widest active:scale-95">Simpan Semua Jadwal</button>
            </div>
        </form>
    </div>
    @endif
</div>

@include('Kasir.modal.createSiswa')
@include('Kasir.modal.editSiswa')

<style>
    .custom-scroll::-webkit-scrollbar { width: 3px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
</style>

<script>
    const totalAmount = {{ $totalAmount ?? 0 }};
    const inputPaid = document.getElementById('paid_amount');
    const displayChange = document.getElementById('change_display');
    const finalPaid = document.getElementById('final_paid_amount');
    const btnCheckout = document.getElementById('checkoutBtn');

    inputPaid.addEventListener('input', (e) => {
        const value = parseFloat(e.target.value) || 0;
        const change = value - totalAmount;
        displayChange.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(change > 0 ? change : 0);
        finalPaid.value = value;
        btnCheckout.disabled = (value < totalAmount) || (totalAmount === 0);
    });

    window.students = @json($students ?? []);
    
    function confirmDelete(id){
        if (!confirm('Yakin ingin menghapus siswa ini?')) return;

        const token = '{{ csrf_token() }}';
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ url('siswa/delete') }}/" + id;

        const inputToken = document.createElement('input');
        inputToken.type = 'hidden';
        inputToken.name = '_token';
        inputToken.value = token;
        form.appendChild(inputToken);

        document.body.appendChild(form);
        form.submit();
    }

    function openStudentModal(){
        const modal = document.getElementById('studentModal')
        const modalContent = document.getElementById('studentModalContent')
        if(modal && modalContent) {
            modal.classList.remove('invisible')
            setTimeout(() => {
                modalContent.classList.remove('translate-y-4','opacity-0')
                modalContent.classList.add('translate-y-0','opacity-100')
            },100)
        }
    }

    function closeStudentModal(){
        const modal = document.getElementById('studentModal')
        const modalContent = document.getElementById('studentModalContent')
        if(modal && modalContent) {
            modalContent.classList.add('translate-y-4','opacity-0')
            setTimeout(()=>{
                modal.classList.add('invisible')
            },300)
        }
    }

    function openEditModal(id){
        const student = window.students.find(s => s.id === id);
        if (!student) return;
        document.getElementById('edit_student_id').value = student.id;
        document.getElementById('edit_name').value = student.student_name || '';
        document.getElementById('edit_email').value = student.email || '';
        document.getElementById('edit_phone').value = student.phone_number || '';
        document.getElementById('edit_address').value = student.address || '';
        document.getElementById('edit_gender').value = student.gender || '';
        document.getElementById('editStudentForm').action = "{{ url('siswa') }}" + '/' + student.id;

        const modal = document.getElementById('editStudentModal');
        const modalContent = document.getElementById('editStudentModalContent');
        if(modal && modalContent) {
            modal.classList.remove('invisible');
            setTimeout(() => {
                modalContent.classList.remove('translate-y-4','opacity-0');
                modalContent.classList.add('translate-y-0','opacity-100');
            }, 100);
        }
    }

    function closeEditModal(){
        const modal = document.getElementById('editStudentModal');
        const modalContent = document.getElementById('editStudentModalContent');
        if(modal && modalContent) {
            modalContent.classList.add('translate-y-4','opacity-0');
            setTimeout(()=>{
                modal.classList.add('invisible');
            },300);
        }
    }
</script>
@endsection