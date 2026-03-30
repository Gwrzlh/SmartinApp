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
                <h1 class="text-2xl font-semibold text-gray-800 inline-block border-b-4 border-cyan-600 pb-2">Kelola Bundling</h1>
            </div>
            
           <div class="flex flex-col sm:flex-row gap-3">
                <form action="{{ route('admin.bundling.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama Bundling..." 
                            class="pl-10 pr-4 py-2 w-full sm:w-64 rounded-xl border-2 border-gray-100 bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-akar-search class="w-4 h-4 text-gray-400 group-focus-within:text-cyan-500" />
                        </div>
                        <button type="submit" class="hidden"></button>
                    </div>
                    
                    @if(request('search') || request('filterbysubject'))
                        <a href="{{ route('admin.bundling.index') }}" class="inline-flex items-center px-3 py-2 text-sm text-red-500 hover:text-red-700 transition-colors">
                            <x-akar-circle-x class="w-4 h-4 mr-1" /> Reset
                        </a>
                    @endif
                </form>

                <a href="{{ route('admin.bundling.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95">
                    <x-akar-person-add class="w-5 h-5 mr-2" />Tambah Bundling
                </a>
            </div>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded-lg justify-items-center">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Bundling</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Bundling</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($bundlings as $bundling)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-center text-sm text-gray-900">
                                {{ ($bundlings->currentPage() - 1) * $bundlings->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $bundling->bundling_name }}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900">Rp {{ number_format($bundling->bundling_price, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $bundling->description }}</td>
                             <td class="px-6 py-4 text-center text-sm">
                                @if($bundling->is_active)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-medium space-x-2">
                                <button onclick="ShowBundlingDetails({{ $bundling->id }})" class="p-2 text-cyan-500 hover:bg-cyan-50 rounded-lg">
                                    <x-akar-eye-open class="w-5 h-5 inline" />
                                </button>
                                <a href="{{ route('admin.bundling.edit', $bundling->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <x-akar-edit class="w-4 h-4 inline" /> Edit
                                </a>
                                <form action="{{ route('admin.bundling.destroy', $bundling->id) }}" method="POST" class="inline deleteForm">
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
                        Menampilkan {{ $bundlings->firstItem() }} sampai {{ $bundlings->lastItem() }} dari {{ $bundlings->total() }} Bundling
                    </p>
                    <div class="pagination-custom">
                        {{ $bundlings->links() }}
                    </div>
            </div>
    </div>
</div>

@include('Admin.bundlings.partials.detail')


<script>
    function ShowBundlingDetails(id) {
        const modal = document.getElementById('bundlingModal');
        const modalContent = document.getElementById('modalContent');
        const url = "{{ route('admin.bundling.index', '') }}" + '/' + id;

        if (!modal || !modalContent) {
            Swal.fire({
                title: 'Error!',
                text: 'Modal element tidak ditemukan.',
                icon: 'error',
                confirmButtonColor: '#06b6d4'
            });
            return;
        }

        // Reset & Show loading
        modal.classList.remove('invisible');
        document.getElementById('modalBundlingName').textContent = 'Loading...';
        document.getElementById('modalBundlingPrice').textContent = '';
        document.getElementById('modalGridBody').innerHTML = '<div class="col-span-2 flex justify-center items-center py-8"><div class="loading-spinner"></div></div>';
        
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                document.getElementById('modalBundlingName').textContent = data.bundling_name || 'N/A';
                document.getElementById('modalBundlingPrice').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.price || 0);
                
                // Render mapel list
                const mapelList = (Array.isArray(data.subjects) && data.subjects.length > 0) 
                    ? data.subjects.join(', ') 
                    : '-';
                
                document.getElementById('modalGridBody').innerHTML = `
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Deskripsi</p>
                        <p class="text-sm font-semibold text-gray-700">${data.description || '-'}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</p>
                        <span class="inline-block px-3 py-1 text-xs font-bold rounded-full ${data.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${data.is_active ? 'Aktif' : 'Tidak Aktif'}</span>
                    </div>
                    <div class="col-span-2 space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Mata Pelajaran</p>
                        <p class="text-sm font-semibold text-gray-700">${mapelList}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Dibuat Pada</p>
                        <p class="text-sm font-semibold text-gray-700">${new Date(data.created_at).toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'})}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Diperbarui</p>
                        <p class="text-sm font-semibold text-gray-700">${new Date(data.updated_at).toLocaleDateString('id-ID', {day:'2-digit', month:'long', year:'numeric'})}</p>
                    </div>
                `;

                // Trigger Animasi Masuk
                modalContent.classList.remove('translate-y-4', 'opacity-0');
                modalContent.classList.add('translate-y-0', 'opacity-100');
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal memuat data bundling. Silakan coba lagi.',
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
                        text: 'Bundling berhasil dihapus.',
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
        const modal = document.getElementById('bundlingModal');
        const modalContent = document.getElementById('modalContent');
        
        if (!modal || !modalContent) return;
        
        // Animasi Keluar
        modalContent.classList.add('translate-y-4', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('invisible');
        }, 300);
    }
</script>

@endsection