@extends('layouts.Admin')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .loading-spinner {
        display: inline-block;
        width: 18px;
        height: 18px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.8s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .btn-loading {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<div class="min-h-screen bg-gray-50/50 p-4 sm:p-8">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-yellow-600 mb-6 transition-colors group">
            <x-akar-arrow-left class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" /> Kembali ke Manajemen User
        </a>

        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-amber-600 to-amber-500 px-8 py-10 text-white">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                        <x-akar-person-add class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold tracking-tight">Edit Pengguna</h3>
                        <p class="text-amber-50/80 text-sm mt-1">Perbarui informasi akun administrator atau kasir.</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-10">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6" id="userForm" onsubmit="handleFormSubmit(event)">
                    @csrf
                    @method('PUT')
                    @if ($errors->any())
                        <div id="errorContainer" class="p-4 mb-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-100 hidden">
                            <ul class="list-disc pl-5" id="errorList">
                            </ul>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="username" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Username</label>
                            <input type="text" name="username" id="username" 
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none placeholder:text-gray-300" 
                                placeholder="ex: jdoe22" value="{{ old('username', $user->username) }}" required>
                        </div>

                        <div class="space-y-2">
                            <label for="full_name" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                            <input type="text" name="full_name" id="full_name" 
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none placeholder:text-gray-300" 
                                placeholder="Masukkan nama lengkap" value="{{ old('full_name', $user->full_name) }}" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Alamat Email</label>
                        <div class="relative">
                            <input type="email" name="email" id="email" 
                                class="block w-full pl-4 pr-12 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 outline-none placeholder:text-gray-300" 
                                placeholder="admin@smartin.com" value="{{ old('email', $user->email) }}" required>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                <x-ri-mail-line class="w-5 h-5" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="password" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Password</label>
                            <input type="password" name="password" id="password" 
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 outline-none placeholder:text-gray-300" 
                                placeholder="••••••••" value="{{ old('password') }}">
                            <p class="text-[10px] text-gray-400 ml-1 italic">Minimal 7 karakter kombinasi huruf & angka.</p>
                        </div>

                        <div class="space-y-2">
                            <label for="confirm_password" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Konfirmasi Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" 
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none placeholder:text-gray-300" 
                                placeholder="••••••••" value="{{ old('password') }}">
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-end gap-6 pt-4">
                        <div class="flex-1 space-y-2">
                            <label for="role" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Otoritas Akses (Role)</label>
                                <select name="role" id="role" 
                                    class="w-full mt-2 px-4 py-2 text-xs font-semibold bg-gray-100 border border-gray-200 text-gray-400 rounded-xl cursor-not-allowed appearance-none opacity-75" 
                                    disabled>
                                    <option value="" disabled {{ old('role', $user->role ?? '') == '' ? 'selected' : '' }}>Pilih hak akses...</option>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                                    <option value="kasir" {{ old('role', $user->role) == 'kasir' ? 'selected' : '' }}>Staff Kasir</option>
                                    <option value="owner" {{ old('role', $user->role) == 'owner' ? 'selected' : '' }}>Owner</option>
                                </select>
                        </div>

                        <div class="flex items-center pb-3 px-4 h-[52px] bg-gray-50/50 border-2 border-gray-100 rounded-xl group hover:bg-amber-50/50 hover:border-amber-100 transition-all">
                            <input type="checkbox" name="active" id="active" class="h-5 w-5 text-amber-600 border-gray-300 rounded-lg focus:ring-amber-500 cursor-pointer" {{ old('active', $user->is_active) ? 'checked' : '' }}>
                            <label for="active" class="ml-3 text-sm font-semibold text-gray-600 cursor-pointer group-hover:text-amber-700">Akun Aktif</label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-10 mt-6 border-t border-gray-50">
                        {{-- <a href="{{ route('admin.users.index') }}" 
                            class="px-8 py-3 text-sm font-bold text-gray-400 hover:text-red-500 transition-colors">
                            Batalkan
                        </a> --}}
                        <button type="submit" id="submitBtn"
                            class="px-10 py-3 bg-amber-300 hover:bg-amber-400 text-white font-bold rounded-xl transition-all shadow-lg shadow-amber-100 hover:shadow-amber-200 hover:-translate-y-0.5 active:scale-95 flex items-center justify-center">
                            <span id="submitText">Update Data Pengguna</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Display validation errors as SweetAlert if any
    @if ($errors->any())
        const errors = [
            @foreach ($errors->all() as $error)
                "{{ $error }}",
            @endforeach
        ];
        const errorHtml = errors.map(err => `<li class="text-left">${err}</li>`).join('');
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal!',
            html: `<ul class="list-disc pl-6 text-gray-700">${errorHtml}</ul>`,
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl px-6 py-2'
            }
        });
    @endif

    // Handle form submission with loading
    function handleFormSubmit(event) {
        event.preventDefault();
        
        const form = document.getElementById('userForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitText.innerHTML = '<div class="loading-spinner"></div> Memperbarui...';
        
        // Show loading alert
        Swal.fire({
            title: 'Memperbarui Data Pengguna',
            text: 'Mohon tunggu, data pengguna sedang diperbarui ke sistem.',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: (modal) => {
                Swal.showLoading();
            }
        });
        
        // Submit form
        setTimeout(() => {
            form.submit();
        }, 500);
    }

    // Alert untuk Pesan Sukses
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#06b6d4',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl px-6 py-2'
            }
        }).then(() => {
            window.location.href = "{{ route('admin.users.index') }}";
        });
    @endif

    // Alert untuk Pesan Error Global
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('error') }}",
            confirmButtonColor: '#ef4444',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-xl px-6 py-2'
            }
        });
    @endif
</script>

@endsection