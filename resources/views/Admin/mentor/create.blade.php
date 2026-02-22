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
        <a href="{{ route('admin.mentor.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-cyan-600 mb-6 transition-colors group">
            <x-akar-arrow-left class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" /> Kembali ke Manajemen Mentor
        </a>

        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-cyan-600 to-cyan-500 px-8 py-10 text-white">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                        <x-akar-person-add class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold tracking-tight">Tambah Mentor Baru</h3>
                        <p class="text-cyan-50/80 text-sm mt-1">Daftarkan akun mentor baru ke sistem SmartIn.</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-10">
                <form action="{{ route('admin.mentor.store') }}" method="POST" class="space-y-6" id="mentorForm" onsubmit="handleFormSubmit(event)">
                    @csrf
                    @if ($errors->any())
                        <div id="errorContainer" class="p-4 mb-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-100 hidden">
                            <ul class="list-disc pl-5" id="errorList">
                            </ul>
                        </div>
                    @endif
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="mentor_name" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Langkap Mentor</label>
                            <input type="text" name="mentor_name" id="mentor_name" 
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none placeholder:text-gray-300" 
                                placeholder="ex: Johnathan S.kom" required>
                        </div>

                        <div class="space-y-2">
                            <label for="phone_number" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Nomor Telepon</label>
                            <input type="text" name="phone_number" id="phone_number" 
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none placeholder:text-gray-300" 
                                placeholder="Masukkan nomor telepon mentor" required>
                        </div>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-end gap-6 pt-4">
                        <div class="flex-1 space-y-2">
                            <label for="gender" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Jenis Kelamin</label>
                            <select name="gender" id="gender" 
                                class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                focus:bg-white focus:border-cyan-500 outline-none appearance-none cursor-pointer">
                                <option value="" disabled selected>Pilih gender...</option>
                                <option value="Laki-Laki">Laki Laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                                <label for="spesialization_id" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Spesialisasi Mapel</label>
                                <select name="spesialization_id" id="spesialization_id" 
                                    class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                    focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none placeholder:text-gray-300" required>
                                    <option value="" disabled selected>Pilih Spesialisasi</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->mapel_name }}</option>
                                    @endforeach
                                </select>
                        </div>
 
                        <div class="flex items-center pb-3 px-4 h-[52px] bg-gray-50/50 border-2 border-gray-100 rounded-xl group hover:bg-cyan-50/50 hover:border-cyan-100 transition-all">
                            <input type="checkbox" name="active" id="isActive" class="h-5 w-5 text-cyan-600 border-gray-300 rounded-lg focus:ring-cyan-500 cursor-pointer">
                            <label for="isActive" class="ml-3 text-sm font-semibold text-gray-600 cursor-pointer group-hover:text-cyan-700">Status Mentor</label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-10 mt-6 border-t border-gray-50">
                        <button type="submit" id="submitBtn"
                            class="px-10 py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-cyan-200 hover:shadow-cyan-300 hover:-translate-y-0.5 active:scale-95 flex items-center justify-center">
                            <span id="submitText">Simpan Data mentor</span>
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
        
        const form = document.getElementById('mentorForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        
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
            window.location.href = "{{ route('admin.mentor.index') }}";
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