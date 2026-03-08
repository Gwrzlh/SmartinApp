@extends('layouts.kasir')

@section('content')
<div class="h-screen bg-gray-100 p-4">

<div class="grid grid-cols-[300px_1fr_350px] gap-4 h-full">
    {{-- siswa bagian --}}
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
            <button 
                onclick="selectStudent({{ $student->id }})" 
                class="bg-cyan-500 hover:bg-cyan-600 text-white text-[10px] font-bold px-3 py-1.5 rounded-lg shadow-sm transition-colors uppercase tracking-tight">
                Pilih
            </button>

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
            <button
            class="w-full border border-red-400 text-red-500 rounded-lg py-2 text-sm">
                 {{$countInactive}} Tagihan SPP
            </button>
        </div>

    </div>


    {{-- ============================= --}}
    {{-- KATALOG PRODUK --}}
    {{-- ============================= --}}
    <div class="bg-white rounded-xl shadow p-4 flex flex-col">

        {{-- Menu --}}
        <div class="flex gap-2 mb-4">

            <button class="bg-cyan-500 text-white px-4 py-2 rounded-lg text-sm">
                Beli Paket
            </button>

            <button class="bg-gray-200 px-4 py-2 rounded-lg text-sm">
                Bayar SPP Bulanan
            </button>

            <button class="bg-gray-200 px-4 py-2 rounded-lg text-sm">
                Katalog Mapel
            </button>

        </div>

        {{-- Header --}}
        <div class="flex justify-between items-center mb-4">
            
            <form method="GET" action="{{ route('kasir.transaction') }}" class="flex items-center gap-2">
                <select name="category_id" onchange="this.form.submit()" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="">Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->category_name }}
                        </option>
                    @endforeach
                </select>

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
            </form>

                <div class="absolute right-3 top-2.5 text-gray-500">
                    <x-akar-search class="w-4"/>
                </div>

            </form>

        </div>

        {{-- Grid Paket --}}
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

                <button
                class="bg-cyan-500 text-white text-xs px-3 py-1 rounded-lg">
                + Tambah
                </button>

            </div>

        </div>

        @endforeach

        </div>

    </div>



    {{-- ============================= --}}
    {{-- DETAIL TRANSAKSI --}}
    {{-- ============================= --}}
    <div class="bg-white rounded-xl shadow p-4 flex flex-col">

        <h3 class="font-semibold mb-4">Detail Transaksi</h3>

        <div class="text-sm mb-4">
            <p class="text-gray-500">Kasir</p>
            <p class="font-medium">Argha</p>
        </div>

        <div class="border-t pt-3 mb-4">

            <p class="text-gray-500 text-sm">Siswa</p>
            <p class="font-medium">Daffa Rizqulloh</p>

            <span class="text-green-500 text-xs">Active</span>

        </div>


        {{-- Produk --}}
        <div class="space-y-2 mb-4">

            <div class="bg-cyan-100 rounded-lg p-2 text-sm flex justify-between">
                <span>Paket Music and Science</span>
                <span>1/Bln</span>
            </div>

            <div class="bg-red-100 rounded-lg p-2 text-sm flex justify-between">
                <span>Admin Pendaftaran</span>
                <span>Rp.500.000</span>
            </div>

        </div>


        {{-- Total --}}
        <div class="border-t pt-4 mt-auto">

            <div class="flex justify-between mb-2 text-sm">
                <span>Total</span>
                <span class="font-semibold">Rp.1.500.000</span>
            </div>

            <div class="flex justify-between mb-3 text-sm">
                <span>Uang Diterima</span>
                <span>Rp.2.000.000</span>
            </div>

            <div class="flex justify-between mb-4 text-sm">
                <span>Kembalian</span>
                <span>Rp.500.000</span>
            </div>
            <button
            class="w-full bg-green-500 text-white rounded-lg py-3 font-semibold">
            Checkout & Bayar
            </button>
        </div>
    </div>
</div>
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
    form.action = "{{ url('siswa/delete') }}" + id;

    const inputToken = document.createElement('input');
    inputToken.type = 'hidden';
    inputToken.name = '_token';
    inputToken.value = token;
    form.appendChild(inputToken);

    document.body.appendChild(form);
    form.submit();
}

// make students available to JS
window.students = @json($students);

</script>
@endsection
