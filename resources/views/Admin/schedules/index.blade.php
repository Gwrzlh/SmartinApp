@extends('layouts.Admin')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .loading-spinner {
        display: inline-block; width: 20px; height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%; border-top-color: white;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
</style>

<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800 inline-block border-b-4 border-cyan-600 pb-2">Jadwal & Slot Kelas</h1>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <form action="{{ route('admin.schedules.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative">
                        <select name="filterbysubject" onchange="this.form.submit()" 
                            class="pl-3 pr-10 py-2 w-full sm:w-48 rounded-xl border-2 border-gray-100 bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all text-sm appearance-none cursor-pointer">
                            <option value="">Semua Mapel</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('filterbysubject') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->mapel_name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <x-akar-chevron-down class="w-4 h-4 text-gray-400" />
                        </div>
                    </div>

                    @if(request('filterbysubject'))
                        <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center px-3 py-2 text-sm text-red-500 hover:text-red-700 transition-colors">
                            <x-akar-circle-x class="w-4 h-4 mr-1" /> Reset
                        </a>
                    @endif
                </form>

                <a href="{{ route('admin.schedules.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95">
                    <x-akar-calendar class="w-5 h-5 mr-2" />Tambah Jadwal
                </a>
            </div>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mapel & Mentor</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ruangan</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Slot Terisi</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($schedules as $schedule)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $schedule->subject->mapel_name }}</div>
                                <div class="text-xs text-gray-500 italic">Mentor: {{ $schedule->mentor->mentor_name }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="block text-sm font-medium text-gray-900">{{ $schedule->hari }}</span>
                                <span class="text-xs text-gray-500">{{ $schedule->jam_mulai }} - {{ $schedule->jam_selesai }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900">
                                <span class="px-2 py-1 bg-gray-100 rounded-md">{{ $schedule->ruangan }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $terisi = $schedule->enrollments_count ?? 0;
                                    $persen = ($terisi / $schedule->capacity) * 100;
                                    $color = $persen >= 100 ? 'text-red-600' : ($persen >= 80 ? 'text-orange-500' : 'text-green-600');
                                @endphp
                                <div class="text-sm font-bold {{ $color }}">
                                    {{ $terisi }} / {{ $schedule->capacity }}
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1 mt-1">
                                    <div class="h-1 rounded-full bg-cyan-500" style="width: {{ $persen }}%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $schedule->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $schedule->is_active ? 'Aktif' : 'Tutup' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-medium space-x-2">
                                <button onclick="ShowScheduleDetails({{ $schedule->id }})" class="p-2 text-cyan-500 hover:bg-cyan-50 rounded-lg" title="Lihat Pendaftar">
                                    <x-akar-eye-open class="w-5 h-5 inline" />
                                </button>
                                <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <x-akar-edit class="w-4 h-4 inline" />
                                </a>
                                <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" class="inline deleteForm">
                                    @csrf @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)" class="text-red-600 hover:text-red-900">
                                        <x-akar-trash-can class="w-4 h-4 inline" />
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-400 italic">Belum ada jadwal yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('Admin.schedules.partials.details')

<script>
   function ShowScheduleDetails(id) {
        const modal = document.getElementById('schedulesModal');
        const modalContent = document.getElementById('modalContent');
        
        // Gunakan placeholder agar tidak error parameter Laravel
        let url = "{{ route('admin.schedules.show', ':id') }}";
        url = url.replace(':id', id);

        modal.classList.remove('invisible');
        document.getElementById('modalUsername').textContent = 'Loading...'; 
        document.getElementById('modalUserRole').textContent = '';
        document.getElementById('modalGridBody').innerHTML = '<div class="col-span-2 flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-cyan-600"></div></div>';

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => {
                if (!response.ok) throw new Error('Data tidak ditemukan');
                return response.json();
            })
            .then(data => {
                // Update Header Modal
                document.getElementById('modalUsername').textContent = data.subject_name;
                document.getElementById('modalUserRole').textContent = 'Mentor: ' + data.mentor_name;

                // Update Isi Grid Modal
                document.getElementById('modalGridBody').innerHTML = `
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Hari & Waktu</p>
                        <p class="text-sm font-semibold text-gray-700">${data.hari}, ${data.jam}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Ruangan / Link</p>
                        <p class="text-sm font-semibold text-gray-700">${data.ruangan}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kapasitas</p>
                        <p class="text-sm font-semibold text-gray-700">${data.capacity} Siswa</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Dibuat Pada</p>
                        <p class="text-sm font-semibold text-gray-700">${new Date(data.created_at).toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'})}</p>
                    </div>
                `;

                modalContent.classList.remove('translate-y-4', 'opacity-0');
                modalContent.classList.add('translate-y-0', 'opacity-100');
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal memuat detail jadwal.',
                    icon: 'error',
                    confirmButtonColor: '#06b6d4'
                });
            });
    }

    function confirmDelete(button) {
        Swal.fire({
            title: 'Hapus Jadwal?',
            text: "Jadwal yang memiliki pendaftar aktif tidak disarankan untuk dihapus!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        });
    }

    function closeModal() {
        const modal = document.getElementById('schedulesModal');
        const modalContent = document.getElementById('modalContent');
        
        // Animasi Keluar
        modalContent.classList.add('translate-y-4', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('invisible');
        }, 300);
    }
</script>

@endsection