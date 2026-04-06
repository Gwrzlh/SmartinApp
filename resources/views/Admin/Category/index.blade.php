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
                <h1 class="text-2xl font-semibold text-gray-800 inline-block border-b-4 border-cyan-600 pb-2">Kelola Category</h1>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <form action="{{ route('admin.category.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">

                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama Kategori..." 
                            class="pl-10 pr-4 py-2 w-full sm:w-64 rounded-xl border-2 border-gray-100 bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none transition-all text-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-akar-search class="w-4 h-4 text-gray-400 group-focus-within:text-cyan-500" />
                        </div>
                        <button type="submit" class="hidden"></button>
                    </div>
                    
                    @if(request('search') || request('filterbycategory'))
                        <a href="{{ route('admin.category.index') }}" class="inline-flex items-center px-3 py-2 text-sm text-red-500 hover:text-red-700 transition-colors">
                            <x-akar-circle-x class="w-4 h-4 mr-1" /> Reset
                        </a>
                    @endif
                </form>

                <a href="{{ route('admin.category.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl shadow-lg shadow-blue-200 transition-all active:scale-95">
                    <x-akar-person-add class="w-5 h-5 mr-2" />Tambah Kategori
                </a>
            </div>
        </div>

        <div class="overflow-x-auto bg-white shadow rounded-lg justify-items-center">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-center text-sm text-gray-900">
                                {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900">{{ $category->category_name }}</td>
                            <td class="px-6 py-4 text-center text-sm font-medium space-x-2">
                                <a href="{{ route('admin.category.edit', $category->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                    <x-akar-edit class="w-4 h-4 inline" /> Edit
                                </a>
                                <form action="{{ route('admin.category.destroy', $category->id) }}" method="POST" class="inline deleteForm">
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
                        Menampilkan {{ $categories->firstItem() }} sampai {{ $categories->lastItem() }} dari {{ $categories->total() }} kategori
                    </p>
                    <div class="pagination-custom">
                        {{ $categories->links() }}
                    </div>
            </div>
    </div>
</div>


<script>
    function confirmDelete(button) {
        Swal.fire({
            title: 'Konfirmasi Penghapusan Permanen',
            text: 'Mohon perhatikan kembali: Menghapus data ini akan menghapus seluruh data lain yang saling berkaitan secara permanen. Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin ingin melanjutkan?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Permanen',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = button.closest('.deleteForm');
                
                // Show loading
                Swal.fire({
                    title: 'Menghapus Data...',
                    text: 'Mohon tunggu sebentar, data sedang diproses.',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: new FormData(form)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Data berhasil dihapus secara permanen.',
                            icon: 'success',
                            confirmButtonColor: '#06b6d4'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Gagal menghapus data.');
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Gagal!',
                        text: error.message || 'Terjadi kesalahan saat menghapus data.',
                        icon: 'error',
                        confirmButtonColor: '#06b6d4'
                    });
                });
            }
        });
    }
</script>

@endsection