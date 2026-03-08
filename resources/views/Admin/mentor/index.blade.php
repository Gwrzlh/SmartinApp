@extends('layouts.Admin')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800 inline-block border-b-4 border-cyan-600 pb-2">Kelola Mentor</h1>
            </div>
            
           <div class="flex flex-col sm:flex-row gap-3">
                <form action="{{ route('admin.mentor.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
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

                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama Mentor..." 
                            class="pl-10 pr-4 py-2 w-full sm:w-64 rounded-xl border-2 border-gray-100 bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-akar-search class="w-4 h-4 text-gray-400 group-focus-within:text-cyan-500" />
                        </div>
                        <button type="submit" class="hidden"></button>
                    </div>
                    
                    @if(request('search') || request('filterbysubject'))
                        <a href="{{ route('admin.mentor.index') }}" class="inline-flex items-center px-3 py-2 text-sm text-red-500 hover:text-red-700 transition-colors">
                            <x-akar-circle-x class="w-4 h-4 mr-1" /> Reset
                        </a>
                    @endif
                </form>

                <a href="{{ route('admin.mentor.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95">
                    <x-akar-person-add class="w-5 h-5 mr-2" />Tambah Mentor
                </a>
            </div>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded-lg justify-items-center">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Mentor</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No Telepon</th>
                        {{-- <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Spesialisasi</th> --}}
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($mentors as $mentor)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-center text-sm text-gray-900">
                                {{ ($mentors->currentPage() - 1) * $mentors->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $mentor->mentor_name }}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $mentor->phone_number }}</td>
                            {{-- <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $mentor->subjects->mapel_name ?? 'Tidak ada spesialisasi' }}</td> --}}
                             <td class="px-6 py-4 text-center text-sm">
                                @if($mentor->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-medium space-x-2">
                                <button onclick="ShowMentorDetails({{ $mentor->id }})" class="p-2 text-cyan-500 hover:bg-cyan-50 rounded-lg">
                                    <x-akar-eye-open class="w-5 h-5 inline" />
                                </button>
                                <a href="{{ route('admin.mentor.edit', $mentor->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <x-akar-edit class="w-4 h-4 inline" /> Edit
                                </a>
                                <form action="{{ route('admin.mentor.destroy', $mentor->id) }}" method="POST" class="inline deleteForm">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this)" class="text-red-600 hover:text-red-900">
                                        <x-akar-trash-can class="w-4 h-4 inline" /> Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">
                                Data tidak ditemukan...
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
            <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-between">
                    <p class="text-xs text-gray-500 font-medium">
                        Menampilkan {{ $mentors->firstItem() }} sampai {{ $mentors->lastItem() }} dari {{ $mentors->total() }} Mentor
                    </p>
                    <div class="pagination-custom">
                        {{ $mentors->links() }}
                    </div>
            </div>
    </div>
</div>

@include('Admin.mentor.partials.detail')


<script>
    function ShowMentorDetails(id) {
        const modal = document.getElementById('mentorModal');
        const modalContent = document.getElementById('modalContent');
        const url = "{{ route('admin.mentor.index') }}/" + id;
        const mapelList = (Array.isArray(data.specialization) && data.specialization.length > 0) 
                    ? data.specialization.join(', ') 
                    : '-';


        modal.classList.remove('invisible');
        document.getElementById('modalMentorName').textContent = 'Loading...';
        document.getElementById('modalMapelName').textContent = '';

        document.getElementById('modalGridBody').innerHTML = '<div class="col-span-2 flex justify-center items-center py-8"><div class="loading-spinner"></div></div>';
        
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => response.json())
            .then(data => {
             
                document.getElementById('modalMentorName').textContent = data.mentor_name;
                document.getElementById('modalMapelName').textContent = data.specialization;
                document.getElementById('modalGridBody').innerHTML = `
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nomor Telepon</p>
                        <p class="text-sm font-semibold text-gray-700">${data.phone_number}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Gender</p>
                        <p class="text-sm font-semibold text-gray-700">${data.gender}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">status</p>
                        <p class="text-sm font-semibold text-gray-700">${data.is_active ? 'Aktif' : 'Tidak Aktif'}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Mapel</p>
                        <p class="text-sm font-semibold text-gray-700">${mapelList}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Member Since</p>
                        <p class="text-sm font-semibold text-gray-700">${new Date(data.created_at).toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'})}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Last Updated</p>
                        <p class="text-sm font-semibold text-gray-700">${new Date(data.updated_at).toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'})}</p>
                    </div>
                `;

                // Trigger Animasi Masuk
                modalContent.classList.remove('translate-y-4', 'opacity-0');
                modalContent.classList.add('translate-y-0', 'opacity-100');
            })
            .catch(err => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal memuat data Mapel. Silakan coba lagi.',
                    icon: 'error',
                    confirmButtonColor: '#06b6d4',
                    confirmButtonText: 'OK'
                });
            });
    }
    function confirmDelete(button) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus Mapel ini? Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = button.closest('.deleteForm');
                
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu, data sedang dihapus.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: (modal) => {
                        Swal.showLoading();
                    }
                });

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new FormData(form)
                })
                .then(() => {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Mentor berhasil dihapus.',
                        icon: 'success',
                        confirmButtonColor: '#06b6d4',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                })
                .catch(() => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal menghapus Mapel.',
                        icon: 'error',
                        confirmButtonColor: '#06b6d4',
                        confirmButtonText: 'OK'
                    });
                });
            }
        });
    }
    function closeModal() {
        const modal = document.getElementById('mentorModal');
        const modalContent = document.getElementById('modalContent');
        
        // Animasi Keluar
        modalContent.classList.add('translate-y-4', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('invisible');
        }, 300);
    }
</script>

@endsection