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
                       
                       <div class="flex items-center pb-3 px-4 h-[52px] bg-gray-50/50 border-2 border-gray-100 rounded-xl group hover:bg-cyan-50/50 hover:border-cyan-100 transition-all">
                            <input type="checkbox" name="isActive" id="isActive" class="h-5 w-5 text-cyan-600 border-gray-300 rounded-lg focus:ring-cyan-500 cursor-pointer">
                            <label for="isActive" class="ml-3 text-sm font-semibold text-gray-600 cursor-pointer group-hover:text-cyan-700">Status Mentor</label>
                        </div>
                    </div>
                    <div class="space-y-3 pt-4 border-t border-gray-50">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1 text-cyan-600">
                                Keahlian / Spesialisasi Mapel
                            </label>
                            
                            <div class="relative">
                                <select id="subjectSelector" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-700 transition-all focus:bg-white focus:border-cyan-500 outline-none appearance-none cursor-pointer">
                                    <option value="" disabled selected>-- Klik untuk memilih keahlian mentor --</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" data-name="{{ $subject->mapel_name }}">
                                            {{ $subject->mapel_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                    <x-akar-chevron-down class="w-5 h-5" />
                                </div>
                            </div>

                            <div id="tagsContainer" class="flex flex-wrap gap-2 p-4 min-h-[100px] rounded-2xl border-2 border-dashed border-gray-100 bg-gray-50/30 transition-all">
                                <div id="placeholderText" class="w-full flex flex-col items-center justify-center text-gray-300 py-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-xs font-medium">Belum ada spesialisasi yang dipilih</p>
                                </div>
                            </div>

                            <div id="hiddenInputsContainer"></div>
                        </div>
                    <div class="flex items-center justify-end space-x-4 pt-10 mt-6 border-t border-gray-50">
                        <button type="submit" id="submitBtn"
                            class="px-10 py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-cyan-200 hover:shadow-cyan-300 hover:-translate-y-0.5 isActive:scale-95 flex items-center justify-center">
                            <span id="submitText">Simpan Data mentor</span>
                        </button>   
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

    const selector = document.getElementById('subjectSelector');
    const container = document.getElementById('tagsContainer');
    const hiddenContainer = document.getElementById('hiddenInputsContainer');
    const placeholder = document.getElementById('placeholderText');
    let selectedSubjects = [];

    selector.addEventListener('change', function() {
        const id = this.value;
        const name = this.options[this.selectedIndex].getAttribute('data-name');

        // Cek jika sudah ada
        if (selectedSubjects.includes(id)) {
            Swal.fire({ 
                icon: 'info', 
                title: 'Sudah Ada', 
                text: 'Mata pelajaran ini sudah ditambahkan ke keahlian mentor.', 
                confirmButtonColor: '#06b6d4' 
            });
            this.value = "";
            return;
        }

        selectedSubjects.push(id);
        placeholder.style.display = 'none';

        // 1. Buat Tampilan Tag (Chip)
        const tag = document.createElement('div');
        tag.className = "group flex items-center bg-white border-2 border-cyan-100 text-cyan-700 pl-4 pr-2 py-2 rounded-xl shadow-sm hover:border-cyan-300 transition-all animate-in fade-in zoom-in duration-300";
        tag.setAttribute('data-id', id);
        tag.innerHTML = `
            <span class="text-sm font-bold mr-2">${name}</span>
            <button type="button" onclick="removeTag('${id}')" class="p-1 rounded-lg hover:bg-red-50 hover:text-red-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;
        container.appendChild(tag);

        // 2. Buat Hidden Input untuk dikirim ke Controller
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'spesialization_id[]'; // SESUAI DENGAN CONTROLLER MENTOR
        hiddenInput.value = id;
        hiddenInput.id = `input-${id}`;
        hiddenContainer.appendChild(hiddenInput);

        this.value = ""; // Reset dropdown agar bisa pilih lagi
    });

    window.removeTag = function(id) {
        selectedSubjects = selectedSubjects.filter(item => item !== id);
        const tagElement = document.querySelector(`[data-id="${id}"]`);
        const inputElement = document.getElementById(`input-${id}`);
        
        if (tagElement) tagElement.remove();
        if (inputElement) inputElement.remove();

        if (selectedSubjects.length === 0) {
            placeholder.style.display = 'flex';
        }
    };
    
    
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