@extends('layouts.Kasir')

@section('content')
<div class="min-h-screen bg-gray-100 p-4 flex flex-col gap-4">

@if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative flex justify-between items-center" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        @if(request('print_invoice'))
            <a href="{{ route('kasir.invoice', request('print_invoice')) }}" target="_blank" 
               class="bg-green-600 text-white px-4 py-1 rounded-lg text-sm font-bold shadow-sm hover:bg-green-700 flex items-center gap-2">
               <x-akar-reciept class="w-4 h-4"/> Cetak Struk
            </a>
        @endif
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-[300px_1fr_350px] gap-4">
                            {{-- SISWA AREA --}}
    <div class="bg-white rounded-xl shadow p-4 flex flex-col" x-data="{ openStudentModal:false }">

        <div class="flex justify-between items-center mb-4">
            <h2 class="font-semibold text-lg">Daftar Siswa</h2>
            <x-akar-three-line-horizontal class="w-5"/>
        </div>

        <form method="GET" action="{{ route('kasir.transaction') }}" class="relative mb-3">
            <input type="text"
                name="q_student"
                value="{{ request('q_student') }}"
                placeholder="Cari Siswa"
                class="w-full border rounded-lg px-3 py-2 text-sm">

            <div class="absolute right-3 top-2.5 text-gray-500">
                <x-akar-search class="w-4"/>
            </div>
        </form>

        <button onclick="openStudentModal()" 
        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        + Tambah Siswa
        </button>

        <div class="space-y-3 overflow-y-auto">

        @foreach ($students as $student)

     <div class="bg-gray-50 rounded-lg p-3 flex justify-between items-center group hover:bg-white hover:shadow-md transition-all border border-transparent hover:border-cyan-100 relative">
    
        <div class="text-sm flex-1">
            <div class="flex items-center gap-2 mb-1">
                <span class="bg-cyan-100 text-cyan-700 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">
                    {{ $student->student_nik }}
                </span>
                <span class="text-[10px] {{ $student->status == 'active' ? 'text-green-500' : 'text-gray-400' }}">
                    ● {{ ucfirst($student->status) }}
                </span>
            </div>
            <p class="font-bold text-gray-800 leading-tight">{{ $student->student_name }}</p>
            <p class="text-gray-500 text-[11px] mt-0.5 italic">{{ $student->phone_number }}</p>
        </div>

        <div class="flex flex-col gap-2 items-end">
            <form action="{{ route('kasir.selectStudent') }}" method="POST">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
               <button class="w-full text-left p-2 hover:bg-gray-100 rounded">
                   Pilih
                </button>
            </form>

            <div class="flex gap-3 mt-1 opacity-0 group-hover:opacity-100 transition-opacity">
                <button onclick="openEditModal({{ $student->id }})" class="text-gray-400 hover:text-amber-500 tooltip" title="Edit Data">
                    <x-akar-edit class="w-3.5 h-3.5"/>
                </button>
                <button onclick="confirmDelete({{ $student->id }})" class="text-gray-400 hover:text-red-500 tooltip" title="Hapus Siswa">
                    <x-akar-trash-can class="w-3.5 h-3.5"/>
                </button>
            </div>
        </div>
    </div>
        @endforeach

        </div>

        <div class="mt-auto pt-4">
            <a href="{{ route('kasir.transaction',['mode'=>'spp']) }}"
            class="w-full border border-red-400 text-red-500 rounded-lg py-2 text-sm block text-center">
            {{$countInactive}} Tagihan SPP
            </a>
        </div>

    </div>


                {{-- ++++++++++++++++++++ PRODUK AREA====================== --}}
    <div class="bg-white rounded-xl shadow p-4 flex flex-col">

        {{-- Menu --}}
        <div class="flex gap-2 mb-4">

            <a href="{{ route('kasir.transaction',['mode'=>'paket']) }}"
                class="px-4 py-2 rounded-lg text-sm {{ $mode=='paket' ? 'bg-cyan-500 text-white' : 'bg-gray-200' }}">
                    Beli Paket
            </a>

            <a href="{{ route('kasir.transaction',['mode'=>'mapel']) }}"
                class="px-4 py-2 rounded-lg text-sm {{ $mode=='mapel' ? 'bg-cyan-500 text-white' : 'bg-gray-200' }}">
                    Katalog Mapel
            </a>

            <a href="{{ route('kasir.transaction',['mode'=>'spp']) }}"
                class="px-4 py-2 rounded-lg text-sm {{ $mode=='spp' ? 'bg-cyan-500 text-white' : 'bg-gray-200' }}">
                    Bayar SPP Bulanan
            </a>

        </div>

        {{-- Header --}}
        <div class="flex justify-between items-center mb-4">
            
            <form method="GET" action="{{ route('kasir.transaction') }}" class="flex items-center gap-2">
                {{-- keep mode across requests --}}
                <input type="hidden" name="mode" value="{{ $mode }}">
                {{-- also preserve spp search term when switching modes --}}
                @if($mode=='spp')
                    <input type="hidden" name="q_spp" value="{{ request('q_spp') }}">
                @endif

                @if($mode == 'paket' || $mode == 'mapel')
                <select name="category_id" onchange="this.form.submit()" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="">Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->category_name }}
                        </option>
                    @endforeach
                </select>
                @endif

                @if($mode == 'paket')
                <div class="relative w-60">
                    <input type="text"
                        name="q_bundling"
                        value="{{ request('q_bundling') }}"
                        placeholder="Cari Bundling"
                        class="border rounded-lg px-3 py-2 w-full text-sm">

                    <div class="absolute right-3 top-2.5 text-gray-500">
                        <x-akar-search class="w-4"/>
                    </div>
                </div>
                @elseif($mode == 'mapel')
                <div class="relative w-60">
                    <input type="text"
                        name="q_mapel"
                        value="{{ request('q_mapel') }}"
                        placeholder="Cari Mapel"
                        class="border rounded-lg px-3 py-2 w-full text-sm">

                    <div class="absolute right-3 top-2.5 text-gray-500">
                        <x-akar-search class="w-4"/>
                    </div>
                </div>
                @elseif($mode == 'spp')
                <div class="relative w-60">
                    <input type="text"
                        name="q_spp"
                        value="{{ request('q_spp') }}"
                        placeholder="Cari Tagihan"
                        class="border rounded-lg px-3 py-2 w-full text-sm">

                    <div class="absolute right-3 top-2.5 text-gray-500">
                        <x-akar-search class="w-4"/>
                    </div>
                </div>
                @endif
            </form>
        </div>

        @if($mode == 'paket')
            <div class="grid grid-cols-2 gap-4 overflow-y-auto">
            @foreach ($bundlings as $bundling)
            <div class="bg-gray-50 rounded-xl p-4 shadow-sm">
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-yellow-400 text-white rounded-lg p-2">
                        <x-eos-packages-o class="w-5"/>
                    </div>
                    <div>
                        <p class="font-semibold text-sm">{{$bundling->bundling_name}}</p>
                        @if(!empty($bundling->description))
                            <p class="text-xs text-gray-500 italic">{{ \Illuminate\Support\Str::limit($bundling->description, 60) }}</p>
                        @endif
                        @if($bundling->details->count())
                            <ul class="text-xs text-gray-500 mt-1">
                                @foreach($bundling->details as $detail)
                                    <li>{{ $detail->subject->mapel_name ?? '-' }}</li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-xs text-gray-400 mt-1">(tidak ada mata pelajaran)</p>
                        @endif
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex flex-col items-end">
                        <span class="text-sm font-semibold">
                            Rp.{{$bundling->bundling_price}}/Bln
                        </span>
                        @if(isset($bundling->is_active))
                            <span class="text-[10px] {{ $bundling->is_active ? 'text-green-500' : 'text-gray-400' }}">
                                {{ $bundling->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        @endif
                    </div>
                    <form action="{{ route('kasir.cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="bundling">
                        <input type="hidden" name="id" value="{{ $bundling->id }}">
                        <input type="hidden" name="name" value="{{ $bundling->bundling_name }}">
                        <input type="hidden" name="price" value="{{ $bundling->bundling_price }}">
                        <button class="bg-cyan-500 text-white text-xs px-3 py-1 rounded-lg">
                            + Tambah
                        </button>
                    </form>

                </div>

            </div>

            @endforeach

            </div>
        @elseif($mode == 'mapel')
            <div class="grid grid-cols-2 gap-4 overflow-y-auto">
                @foreach($subjects as $subject)
                    <div class="bg-gray-50 rounded-xl p-4 shadow-sm flex flex-col">
                        <p class="font-semibold text-sm">{{ $subject->mapel_name }}</p>
                        <p class="text-xs text-gray-500">Rp.{{ $subject->monthly_price }}/Bln</p>
                        <p class="text-xs text-gray-400">{{ $subject->categories->category_name ?? '-' }}</p>
                        <form action="{{ route('kasir.cart.add') }}" method="POST" class="mt-auto">
                            @csrf
                            <input type="hidden" name="type" value="subject">
                            <input type="hidden" name="id" value="{{ $subject->id }}">
                            <input type="hidden" name="name" value="{{ $subject->mapel_name }}">
                            <input type="hidden" name="price" value="{{ $subject->monthly_price }}">
                            <button class="bg-cyan-500 text-white text-xs px-3 py-1 rounded-lg">
                                + Tambah
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

        @elseif($mode == 'spp')
            <div class="space-y-3 overflow-y-auto">
                @foreach($inactiveStudents as $student)
                    <div class="bg-gray-50 rounded-lg p-3 flex justify-between items-center group hover:bg-white hover:shadow-md transition-all border border-transparent hover:border-cyan-100 relative">
                        <div class="text-sm flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="bg-cyan-100 text-cyan-700 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">
                                    {{ $student->student_nik }}
                                </span>
                                <span class="text-[10px] text-gray-400">
                                    ● {{ ucfirst($student->status) }}
                                </span>
                            </div>
                            <p class="font-bold text-gray-800 leading-tight">{{ $student->student_name }}</p>
                            <p class="text-gray-500 text-[11px] mt-0.5 italic">{{ $student->phone_number }}</p>
                        </div>
                        <div class="flex flex-col gap-2 items-end">
                            <form action="{{ route('kasir.selectStudent') }}" method="POST">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                <input type="hidden" name="mode" value="{{ $mode }}">
                                <button class="bg-cyan-500 hover:bg-cyan-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm transition-colors uppercase tracking-tight">
                                    Pilih
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

                {{-- DETAIL TRANSAKSI  --}}
    <div class="bg-white rounded-xl shadow p-4 flex flex-col">

        <h3 class="font-semibold mb-4">Detail Transaksi</h3>

        <div class="text-sm mb-4">
            <p class="text-gray-500">Kasir</p>
            <p class="font-medium">{{ $cashierName }}</p>
        </div>

        <div class="border-t pt-3 mb-4">
            <p class="text-gray-500 text-sm">Siswa</p>
            @if($selectedStudent)
                <p class="font-medium">{{ $selectedStudent->student_name }}</p>
              <span class="text-xs px-2 py-1 rounded
                {{ $selectedStudent->status == 'active' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500' }}">
            @else
                <p class="font-medium text-gray-400">Belum ada</p>
            @endif
        </div>


        {{-- Produk --}}
        <div class="space-y-2 mb-4">
            @if(count($cart))
                @foreach($cart as $i => $item)
                   <div class="bg-cyan-100 rounded-lg p-2 text-sm flex justify-between items-center">
                    <span>
                        {{ $item['name'] }}
                        {{ isset($item['quantity']) ? ' x'.$item['quantity'] : '' }}
                    </span>
                    <span class="text-xs text-gray-500">
                        ({{ $item['type'] }})
                    </span>
                    <div class="flex items-center gap-2">
                        <span>
                            Rp.{{ number_format($item['price'] * ($item['quantity'] ?? 1), 0, ',', '.') }}
                        </span>
                        @if($item['type'] !== 'registration')
                        <form action="{{ route('kasir.cart.remove',$i) }}" method="POST">
                            @csrf
                            <button class="text-red-500 text-xs">✕</button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <p class="text-gray-400 italic">Belum ada produk yang dipilih</p>
            @endif
        </div>

        {{-- Total --}}
        <div class="border-t pt-4 mt-auto">
        <form action="{{ route('kasir.checkout') }}" method="POST">
        @csrf
            <div class="flex justify-between mb-2 text-sm">
                <span>Total</span>
                <span class="font-semibold">Rp.{{ number_format($totalAmount,0,',','.') }}</span>
            </div>

            {{-- additional payment inputs can go here later --}}
            <div class="flex justify-between mb-3 text-sm">
                <span>Uang Diterima</span>
              <input 
                type="number"
                name="paid_amount"
                id="paid_amount"
                min="0"
                placeholder="0"
                class="border rounded px-2 py-1 w-24 text-sm"
                >
            </div>

           <div class="flex justify-between mb-4 text-sm">
                <span>Kembalian</span>
                <input 
                    type="text"
                    id="change"
                    readonly
                    class="border rounded px-2 py-1 w-28 text-sm bg-gray-100"
                >
            </div>
           <button id="checkoutBtn" type="submit"
                class="w-full bg-green-500 text-white rounded-lg py-3 font-semibold
                {{ !$selectedStudent || count($cart)==0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                {{ !$selectedStudent || count($cart)==0 ? 'disabled' : '' }}
                >
                Checkout & Bayar
            </button>
        </form>
        </div>
    </div>
</div>

{{-- SCHEDULE PLACEMENT PANEL --}}
@if(isset($transaction_id) && $enrollmentsToSchedule->isNotEmpty())
<div class="bg-white rounded-xl shadow p-6 mt-4">
    <div class="border-b pb-4 mb-6">
        <h2 class="text-xl font-bold text-gray-800">Penempatan Jadwal Belajar</h2>
        <p class="text-gray-500 text-sm mt-1">Siswa: <span class="font-semibold">{{ $studentForSchedule->student_name ?? 'Siswa' }}</span></p>
        <p class="text-gray-500 text-sm">ID Transaksi: #{{ $transaction_id }}</p>
    </div>

    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Pilih jadwal yang tersedia untuk setiap mata pelajaran di bawah ini. Pastikan Anda menyimpannya agar pendaftaran selesai.
                </p>
            </div>
        </div>
    </div>

    <form action="{{ route('kasir.transaction.saveSchedules', $transaction_id) }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($enrollmentsToSchedule as $i => $enrollment)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-col">
                    
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold">
                            {{ $i + 1 }}
                        </div>
                        <h3 class="font-semibold text-lg text-gray-800">
                            {{ $enrollment->subject->mapel_name ?? 'Mata Pelajaran' }}
                        </h3>
                    </div>

                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Waktu & Hari</label>
                        <select name="schedules[{{ $enrollment->id }}]" required class="w-full border-gray-300 rounded-lg shadow-sm focus:border-cyan-500 focus:ring-cyan-500 p-2.5 bg-white border text-sm">
                            <option value="">-- Pilih Jadwal --</option>
                            
                            @if($enrollment->subject && $enrollment->subject->schedules->count() > 0)
                                @foreach($enrollment->subject->schedules as $schedule)
                                    @php
                                        $mentorName = $schedule->mentor->nama ?? 'Tidak Ada Mentor';
                                    @endphp
                                    @if($schedule->remaining_capacity > 0)
                                        <option value="{{ $schedule->id }}">
                                            {{ $schedule->hari }} | {{ \Carbon\Carbon::parse($schedule->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->jam_selesai)->format('H:i') }} | {{ $mentorName }} (Sisa Kuota: {{ $schedule->remaining_capacity }})
                                        </option>
                                    @else
                                        <option value="" disabled class="text-red-500 bg-red-50">
                                            {{ $schedule->hari }} | {{ \Carbon\Carbon::parse($schedule->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->jam_selesai)->format('H:i') }} | {{ $mentorName }} (Penuh)
                                        </option>
                                    @endif
                                @endforeach
                            @else
                                <option value="" disabled>Belum ada jadwal untuk mata pelajaran ini</option>
                            @endif
                            
                        </select>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-end gap-3 border-t pt-4">
            <a href="{{ route('kasir.transaction') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Nanti Saja
            </a>
            <button type="submit" class="px-6 py-2.5 bg-cyan-600 text-white font-semibold rounded-lg hover:bg-cyan-700 shadow-sm transition-colors">
                Simpan Semua Jadwal
            </button>
        </div>
        
    </form>
</div>
@endif

</div>
@include('Kasir.modal.createSiswa')
@include('Kasir.modal.editSiswa')

<script>

function openStudentModal(){

    const modal = document.getElementById('studentModal')
    const modalContent = document.getElementById('studentModalContent')

    modal.classList.remove('invisible')

    setTimeout(() => {
        modalContent.classList.remove('translate-y-4','opacity-0')
        modalContent.classList.add('translate-y-0','opacity-100')
    },100)
}

function closeStudentModal(){

    const modal = document.getElementById('studentModal')
    const modalContent = document.getElementById('studentModalContent')

    modalContent.classList.add('translate-y-4','opacity-0')

    setTimeout(()=>{
        modal.classList.add('invisible')
    },300)

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

    modal.classList.remove('invisible');

    setTimeout(() => {
        modalContent.classList.remove('translate-y-4','opacity-0');
        modalContent.classList.add('translate-y-0','opacity-100');
    }, 100);
}

function closeEditModal(){
    const modal = document.getElementById('editStudentModal');
    const modalContent = document.getElementById('editStudentModalContent');

    modalContent.classList.add('translate-y-4','opacity-0');

    setTimeout(()=>{
        modal.classList.add('invisible');
    },300);
}

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

function formatRupiah(angka){
    return new Intl.NumberFormat('id-ID').format(angka);
}

const total = {{ $totalAmount ?? 0 }};
const inputBayar = document.getElementById('paid_amount');
const changeInput = document.getElementById('change');
const checkoutBtn = document.getElementById('checkoutBtn');

function updatePaymentFields(){
    if(!inputBayar || !changeInput || !checkoutBtn) return;
    
    let bayar = parseFloat(inputBayar.value) || 0;
    let kembalian = bayar - total;
    changeInput.value = formatRupiah(kembalian > 0 ? kembalian : 0);
    // enable button only when paid amount is enough
    checkoutBtn.disabled = bayar < total;
}

if(inputBayar){
    inputBayar.addEventListener('input', updatePaymentFields);
    updatePaymentFields();
}



window.students = @json($students ?? []);

</script>
@endsection
