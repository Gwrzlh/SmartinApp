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
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-cyan-600 mb-6 transition-colors group">
            <x-akar-arrow-left class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" /> Kembali ke Manajemen User
        </a>

        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-cyan-600 to-cyan-500 px-8 py-10 text-white">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                        <x-akar-person-add class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold tracking-tight">Tambah Pengguna Baru</h3>
                        <p class="text-cyan-50/80 text-sm mt-1">Daftarkan akun administrator atau kasir baru ke sistem SmartIn.</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-10">
                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6" id="userForm" onsubmit="handleFormSubmit(event)">
                    @csrf
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
                                focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none placeholder:text-gray-300" 
                                placeholder="ex: jdoe22">
                        </div>

                        <div class="space-y-2">
                            <label for="full_name" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                            <input type="text" name="full_name" id="full_name" 
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none placeholder:text-gray-300" 
                                placeholder="Masukkan nama lengkap">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="email" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Alamat Email</label>
                        <div class="relative">
                            <input type="email" name="email" id="email" 
                                class="block w-full pl-4 pr-12 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                focus:bg-white focus:border-blue-600 focus:ring-4 focus:ring-blue-600/10 outline-none placeholder:text-gray-300" 
                                placeholder="admin@smartin.com">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                <x-ri-mail-line class="w-5 h-5" />
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="password" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Password</label>
                            <input type="password" name="password" id="password" required
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                focus:bg-white focus:border-red-500 focus:ring-4 focus:ring-red-500/10 outline-none placeholder:text-gray-300" 
                                placeholder="••••••••">
                            <p class="text-[10px] text-gray-400 ml-1 italic">Minimal 7 karakter kombinasi huruf & angka.</p>
                        </div>

                        <div class="space-y-2">
                            <label for="password_confirmation" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Konfirmasi Password</label>
                            <input type="password" 
                                name="password_confirmation"
                                id="password_confirmation" required
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none placeholder:text-gray-300" 
                                placeholder="••••••••">
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-end gap-6 pt-4">
                        <div class="flex-1 space-y-2">
                            <label for="role" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Otoritas Akses (Role)</label>
                            <select name="role" id="role" 
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                focus:bg-white focus:border-cyan-500 outline-none appearance-none cursor-pointer">
                                <option value="" disabled selected>Pilih hak akses...</option>
                                <option value="owner">Owner</option>
                                <option value="admin">Administrator</option>
                                <option value="kasir">Staff Kasir</option>
                            </select>
                        </div>

                        <div class="flex items-center pb-3 px-4 h-[52px] bg-gray-50/50 border-2 border-gray-100 rounded-xl group hover:bg-cyan-50/50 hover:border-cyan-100 transition-all">
                            <input type="checkbox" name="active" id="active" class="h-5 w-5 text-cyan-600 border-gray-300 rounded-lg focus:ring-cyan-500 cursor-pointer">
                            <label for="active" class="ml-3 text-sm font-semibold text-gray-600 cursor-pointer group-hover:text-cyan-700">Akun Aktif</label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-10 mt-6 border-t border-gray-50">
                        {{-- <a href="{{ route('admin.users.index') }}" 
                            class="px-8 py-3 text-sm font-bold text-gray-400 hover:text-red-500 transition-colors">
                            Batalkan
                        </a> --}}
                        <button type="submit" id="submitBtn"
                            class="px-10 py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-cyan-200 hover:shadow-cyan-300 hover:-translate-y-0.5 active:scale-95 flex items-center justify-center">
                            <span id="submitText">Simpan Data Pengguna</span>
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
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        // Validasi Konfirmasi Password
        if (password !== confirmPassword) {
            Swal.fire({
                icon: 'warning',
                title: 'Password Tidak Cocok!',
                text: 'Harap pastikan konfirmasi password sama dengan password yang Anda masukkan.',
                confirmButtonColor: '#06b6d4',
                customClass: {
                    popup: 'rounded-3xl',
                    confirmButton: 'rounded-xl px-6 py-2'
                }
            });
            return;
        }
        
        // Validate form
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitText.innerHTML = '<div class="loading-spinner"></div> Menyimpan...';
        
        // Show loading alert
        Swal.fire({
            title: 'Menyimpan Data...',
            text: 'Mohon tunggu, data pengguna sedang disimpan ke sistem.',
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