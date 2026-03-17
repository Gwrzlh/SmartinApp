@extends('layouts.Kasir')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Manage Siswa</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar siswa dan manajemen status pembelajaran.</p>
        </div>
        <div>
            <form action="{{ route('kasir.siswa.index') }}" method="GET">
                <div class="relative group">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari Nama atau NIK..." 
                        class="pl-10 pr-4 py-2 w-full sm:w-64 rounded-xl border-2 border-gray-100 bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-dynamic-component component="akar-search" class="w-4 h-4 text-gray-400 group-focus-within:text-cyan-500" />
                    </div>
                </div>
            </form>
        </div>
        <div>
            <button onclick="openStudentModal()" 
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Tambah Siswa
            </button>   
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Siswa
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Kontak
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Program Aktif
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Status Akun
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $siswa)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-cyan-100 text-cyan-600 font-bold">
                                    {{ substr($siswa->student_name, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $siswa->student_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $siswa->student_nik }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-900">{{ $siswa->phone_number }}</div>
                            <div class="text-xs text-gray-500">{{ $siswa->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $enrollments = $siswa->enrollments->where('status_pembelajaran', 'active');
                            @endphp
                            @if($enrollments->count() > 0)
                                @foreach($enrollments as $enrollment)
                                    <span class="inline-block px-2 py-0.5 bg-blue-50 text-blue-600 rounded text-xs">
                                        {{ $enrollment->subject->mapel_name ?? 'Product' }}
                                    </span>
                                @endforeach
                            @else
                                <span class="text-xs text-gray-400 italic font-medium">Belum terdaftar kursus</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $siswa->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($siswa->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center space-x-2">
                                <button type="button" onclick="openProfileModal({{ $siswa->id }})" class="p-2 text-gray-400 bg-gray-50 hover:bg-cyan-50 hover:text-cyan-600 rounded-lg transition-colors border border-gray-200 shadow-sm flex items-center justify-center" title="Lihat Profil">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                                {{-- <a href="https://wa.me/{{ $siswa->phone_number }}" target="_blank" class="p-2 text-green-600 bg-green-50 hover:bg-green-100 rounded-lg transition-colors border border-green-200 shadow-sm" title="WhatsApp">
                                    <x-akar-whatsapp-fill class="w-4 h-4" />
                                </a> --}}
                                <button onclick="openEditModal({{ $siswa->id }})">
                                    <x-akar-edit class="w-4 h-4" />
                                </button>
                                <button onclick="confirmDelete({{ $siswa->id }})">
                                    <x-akar-trash-can class="w-4 h-4" />    
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 whitespace-nowrap text-center text-gray-400 font-medium">
                            Tidak ada data siswa ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $students->links() }}
        </div>
    </div>
</div>

@include('Kasir.modal.createSiswa')
@include('Kasir.modal.editSiswa')
@include('Kasir.modal.detailSiswa')
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
window.studentsData = @json($students->items() ?? []);
// Keep original student list for edit modal
window.students = window.studentsData;
</script>
@endsection