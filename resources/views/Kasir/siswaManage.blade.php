@extends('layouts.Kasir')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen">
    
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manajemen Siswa</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola data murid, status aktif, dan kursus yang diambil.</p>
        </div>
        <button onclick="openStudentModal()" 
            class="inline-flex items-center justify-center px-4 py-2 bg-cyan-600 text-white text-sm font-semibold rounded-lg hover:bg-cyan-700 transition-all shadow-sm">
            <x-dynamic-component component="akar-plus" class="w-4 h-4 mr-2" />
            Tambah Siswa
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('kasir.siswa.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-dynamic-component component="akar-search" class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm transition-all" 
                        placeholder="Cari Nama atau NIK Siswa...">
                </div>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2 text-sm font-medium rounded-lg text-white bg-gray-800 hover:bg-black transition-colors">
                    Cari Data
                </button>
                @if(request('search'))
                    <a href="{{ route('kasir.siswa.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition-colors flex items-center">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b text-gray-600 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-bold">Identitas Siswa</th>
                        <th class="px-6 py-4 font-bold text-center">Kontak & Email</th>
                        <th class="px-6 py-4 font-bold">Program Diambil</th>
                        <th class="px-6 py-4 font-bold text-center">Status</th>
                        <th class="px-6 py-4 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($students as $siswa)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0 flex items-center justify-center rounded-full bg-cyan-50 text-cyan-600 font-bold border border-cyan-100">
                                    {{ strtoupper(substr($siswa->student_name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $siswa->student_name }}</div>
                                    <div class="text-xs text-gray-500">NIK: {{ $siswa->student_nik }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-700 font-medium">{{ $siswa->phone_number }}</div>
                            <div class="text-xs text-gray-400">{{ $siswa->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @php $enrollments = $siswa->enrollments; @endphp
                            <div class="flex flex-wrap gap-1">
                                @forelse($enrollments as $enrollment)
                                    @php
                                        $programName = '-';
                                        if($enrollment->item_type == 'bundling') {
                                            $programName = $enrollment->bundling->bundling_name ?? 'Program';
                                        } else {
                                            $programName = $enrollment->subject->mapel_name ?? 'Program';
                                        }

                                        $badgeColor = 'bg-gray-50 text-gray-700 border-gray-200';
                                        $statusText = '';
                                        
                                        if($enrollment->status_pembelajaran == 'Lulus') {
                                            $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200';
                                            $statusText = ' (Lulus)';
                                        } elseif($enrollment->status_pembelajaran == 'active') {
                                            if(\Carbon\Carbon::parse($enrollment->expired_at)->isBefore(now())) {
                                                $badgeColor = 'bg-rose-50 text-rose-700 border-rose-200';
                                                $statusText = ' (Menunggak)';
                                            } else {
                                                $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                            }
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $badgeColor }}">
                                        {{ $programName }}{{ $statusText }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400 italic">Belum ada program</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            {{-- ========================================================== --}}
                        {{-- TRAFFIC LIGHT BADGE SYSTEM                                   --}}
                        {{-- Prioritas: graduated_debt > hasDebt > active > inactive       --}}
                        {{-- ========================================================== --}}
                        @php
                            $isGraduatedDebt = $siswa->isGraduatedWithDebt();
                            $isHasDebt       = !$isGraduatedDebt && $siswa->hasDebt();
                        @endphp

                        @if($isGraduatedDebt)
                            {{-- 🔴 MERAH BERDENYUT: Sudah lulus tapi masih ada tunggakan SPP --}}
                            <span class="px-2.5 py-1 inline-flex items-center gap-1.5 text-[10px] leading-4 font-bold rounded-full border bg-red-50 text-red-700 border-red-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse shrink-0"></span>
                                LULUS — NUNGGAK
                            </span>
                        @elseif($isHasDebt)
                            {{-- 🟡 KUNING: Masih aktif tapi ada tunggakan SPP --}}
                            <span class="px-2.5 py-1 inline-flex items-center gap-1.5 text-[10px] leading-4 font-bold rounded-full border bg-amber-50 text-amber-700 border-amber-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                AKTIF — NUNGGAK
                            </span>
                        @elseif($siswa->status == 'active')
                            {{-- 🟢 HIJAU: Aktif dan sudah lunas --}}
                            <span class="px-2.5 py-1 inline-flex items-center gap-1.5 text-[10px] leading-4 font-bold rounded-full border bg-emerald-50 text-emerald-700 border-emerald-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                AKTIF — LUNAS
                            </span>
                        @else
                            {{-- ⚫ ABU: Non-aktif / belum mendaftar --}}
                            <span class="px-2.5 py-1 inline-flex items-center gap-1.5 text-[10px] leading-4 font-bold rounded-full border bg-gray-50 text-gray-600 border-gray-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 shrink-0"></span>
                                NON-AKTIF
                            </span>
                        @endif

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openProfileModal({{ $siswa->id }})" class="p-2 bg-white border border-gray-200 text-gray-600 hover:text-cyan-600 hover:border-cyan-200 rounded-lg shadow-sm transition-all" title="Detail">
                                    <x-dynamic-component component="akar-eye-open" class="w-4 h-4" />
                                </button>
                                <button onclick="openEditModal({{ $siswa->id }})" class="p-2 bg-white border border-gray-200 text-gray-600 hover:text-amber-600 hover:border-amber-200 rounded-lg shadow-sm transition-all" title="Edit">
                                    <x-dynamic-component component="akar-edit" class="w-4 h-4" />
                                </button>
                                <button onclick="confirmDelete({{ $siswa->id }})" class="p-2 bg-white border border-gray-200 text-gray-600 hover:text-rose-600 hover:border-rose-200 rounded-lg shadow-sm transition-all" title="Hapus">
                                    <x-dynamic-component component="akar-trash-can" class="w-4 h-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-100 p-3 rounded-full mb-2">
                                    <x-dynamic-component component="akar-person" class="w-6 h-6 text-gray-400" />
                                </div>
                                <p class="text-gray-500 text-sm">Data siswa tidak ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($students->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
            {{ $students->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Modals --}}
@include('Kasir.modal.createSiswa')
@include('Kasir.modal.editSiswa')
@include('Kasir.modal.detailSiswa')

<script>
    // Global data for modals
    window.studentsData = @json($students->items());

    // --- Create Student Modal ---
    function openStudentModal() {
        const modal = document.getElementById('studentModal');
        const content = document.getElementById('studentModalContent');
        modal.classList.remove('invisible');
        setTimeout(() => {
            content.classList.remove('translate-y-4', 'opacity-0');
            content.classList.add('translate-y-0', 'opacity-100');
        }, 50);
    }

    function closeStudentModal() {
        const modal = document.getElementById('studentModal');
        const content = document.getElementById('studentModalContent');
        content.classList.add('translate-y-4', 'opacity-0');
        content.classList.remove('translate-y-0', 'opacity-100');
        setTimeout(() => modal.classList.add('invisible'), 300);
    }

    // --- Edit Student Modal ---
    function openEditModal(studentId) {
        const student = window.studentsData.find(s => s.id === studentId);
        if (!student) return;

        // Fill form fields
        document.getElementById('edit_student_id').value = student.id;
        document.getElementById('edit_name').value = student.student_name;
        document.getElementById('edit_email').value = student.email || '';
        document.getElementById('edit_phone').value = student.phone_number || '';
        document.getElementById('edit_address').value = student.address || '';
        
        // Handle gender - support both full names and abbreviations
        const genderSelect = document.getElementById('edit_gender');
        if (student.gender === 'L' || student.gender === 'Laki-Laki') {
            genderSelect.value = 'Laki-Laki';
        } else if (student.gender === 'P' || student.gender === 'Perempuan') {
            genderSelect.value = 'Perempuan';
        } else {
            genderSelect.value = student.gender || '';
        }

        // Set form action (matching route 'updateSiswa')
        const form = document.getElementById('editStudentForm');
        form.action = `/siswa/${student.id}`;

        const modal = document.getElementById('editStudentModal');
        const content = document.getElementById('editStudentModalContent');
        modal.classList.remove('invisible');
        setTimeout(() => {
            content.classList.remove('translate-y-4', 'opacity-0');
            content.classList.add('translate-y-0', 'opacity-100');
        }, 50);
    }

    function closeEditModal() {
        const modal = document.getElementById('editStudentModal');
        const content = document.getElementById('editStudentModalContent');
        content.classList.add('translate-y-4', 'opacity-0');
        content.classList.remove('translate-y-0', 'opacity-100');
        setTimeout(() => modal.classList.add('invisible'), 300);
    }

    // --- Delete Confirmation ---
    function confirmDelete(studentId) {
        if (confirm('Apakah Anda yakin ingin menghapus data siswa ini?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            // Matching route('hapusSiswa'): /siswa/delete/{student}
            form.action = `/siswa/delete/${studentId}`;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            
            // Note: The route is defined as POST, so we don't need _method='DELETE'
            
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endsection