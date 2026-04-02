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

    @keyframes fade-in { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
    .animate-in { animation: fade-in 0.2s ease-out forwards; }
</style>

<div class="min-h-screen bg-gray-50/50 p-4 sm:p-8">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('admin.bundling.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-cyan-600 mb-6 transition-colors group">
            <x-akar-arrow-left class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" /> Kembali ke Manajemen Bundling    
        </a>

        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-cyan-600 to-cyan-500 px-8 py-10 text-white">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                        <x-akar-person-add class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold tracking-tight">Tambah Paket Bundling Baru</h3>
                        <p class="text-cyan-50/80 text-sm mt-1">Daftarkan Paket Bundling baru ke sistem SmartIn.</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-10">
                <form action="{{ route('admin.bundling.store') }}" method="POST" id="bundlingForm" onsubmit="handleFormSubmit(event)">
                    @csrf
                    
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 space-y-6">
                            
                            <div class="space-y-2">
                                <label for="bundling_name" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Paket Bundling</label>
                                <input type="text" name="bundling_name" id="bundling_name" 
                                    class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none placeholder:text-gray-300"
                                    placeholder="Contoh: Paket Intensif UN" required value="{{ old('bundling_name') }}">
                            </div>

                            <div class="space-y-2">
                                <label for="description" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Deskripsi Paket</label>
                                <textarea name="description" id="description" rows="3"
                                    class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none placeholder:text-gray-300"
                                    placeholder="Jelaskan keuntungan paket ini..." required>{{ old('description') }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="bundling_price" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Harga Total Paket (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 font-bold">Rp</span>
                                        <input type="text" name="bundling_price" id="bundling_price" oninput="formatRupiah(this)"
                                            class="block w-full pl-12 px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all focus:bg-white focus:border-cyan-500 focus:ring-4 focus:ring-cyan-500/10 outline-none placeholder:text-gray-300"
                                            placeholder="1.000.000" required value="{{ old('bundling_price') }}">
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Status Keaktifan</label>
                                    <div class="flex items-center h-[52px]">
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="isActive" id="isActive" class="sr-only peer">
                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-cyan-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-cyan-600"></div>
                                            <span class="ml-3 text-sm font-medium text-gray-700">Aktifkan Paket</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-3 pt-4 border-t border-gray-50">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1 text-cyan-600">Pilih Anggota Mata Pelajaran</label>
                                
                                <div class="relative">
                                    <select id="subjectSelector" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-700 transition-all focus:bg-white focus:border-cyan-500 outline-none appearance-none cursor-pointer">
                                        <option value="" disabled selected>-- Klik untuk mencari/memilih mapel --</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" data-name="{{ $subject->mapel_name }}">
                                                {{ $subject->mapel_name }} (Rp {{ number_format($subject->monthly_price, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>

                                <div id="tagsContainer" class="flex flex-wrap gap-2 p-4 min-h-[100px] rounded-2xl border-2 border-dashed border-gray-100 bg-gray-50/30 transition-all">
                                    <div id="placeholderText" class="w-full flex flex-col items-center justify-center text-gray-300 py-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-xs font-medium">Belum ada mata pelajaran yang terpilih</p>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-gray-50">
                                <div class="space-y-2">
                                    <label for="duration_mounths" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Durasi (Bulan)</label>
                                    <div class="relative">
                                        <input type="number" name="duration_mounths" id="duration_mounths" min="1"
                                            class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all focus:bg-white focus:border-cyan-500 outline-none"
                                            placeholder="Misal: 3" required value="{{ old('duration_month') }}">
                                        <span class="absolute inset-y-0 right-4 flex items-center text-gray-400 text-sm italic">Bulan</span>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label for="start_date" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Tanggal Mulai</label>
                                    <input type="date" name="start_date" id="start_date" 
                                        class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all focus:bg-white focus:border-cyan-500 outline-none"
                                        required value="{{ old('start_date') }}">
                                </div>

                                <div class="space-y-2">
                                    <label for="capacity" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Kapasitas (Slot)</label>
                                    <div class="relative">
                                        <input type="number" name="capacity" id="capacity" min="1"
                                            class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all focus:bg-white focus:border-cyan-500 outline-none"
                                            placeholder="Misal: 20" required value="{{ old('capacity') }}">
                                        <span class="absolute inset-y-0 right-4 flex items-center text-gray-400 text-sm italic">Siswa</span>
                                    </div>
                                </div>
                            </div>

                                <div id="hiddenInputsContainer"></div>
                            </div>

                        </div>

                        <div class="p-8 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end space-x-4">
                            {{-- <a href="{{ route('admin.bundling.index') }}" class="px-6 py-3 text-sm font-bold text-gray-400 hover:text-red-500 transition-colors">Batal</a> --}}
                            <button type="submit" id="submitBtn" class="px-10 py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-cyan-200 hover:shadow-cyan-300 hover:-translate-y-0.5 active:scale-95 flex items-center justify-center">
                                <span id="submitText">Simpan Paket Bundling</span>
                            </button>
                        </div>
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

    // Format input value as Rupiah currency while typing
    function formatRupiah(input) {
        // remove any non-digit characters
        let value = input.value.replace(/\D/g, '');
        if (value === '') {
            input.value = '';
            return;
        }
        // format using Indonesian locale
        input.value = new Intl.NumberFormat('id-ID').format(value);
    }
    function handleFormSubmit(event) {
            event.preventDefault();
            
            const priceInput = document.getElementById('bundling_price');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const form = document.getElementById('bundlingForm');

            // Validasi minimal 2 mapel untuk bundling
            if (selectedSubjects.length < 1) {
                Swal.fire({ icon: 'warning', title: 'Data Kurang', text: 'Pilih minimal 1 mata pelajaran untuk membuat paket.', confirmButtonColor: '#06b6d4' });
                return;
            }

            // Bersihkan titik harga (Format IDR -> Number)
            const originalPrice = priceInput.value;
            priceInput.value = priceInput.value.replace(/\./g, '');

            submitBtn.disabled = true;
            submitText.innerHTML = "Menyimpan...";

            Swal.fire({
                title: 'Memproses...',
                text: 'Sedang menyimpan paket bundling.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            form.submit();
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

    function formatRupiah(input) {
        let value = input.value.replace(/\D/g, '');
        if (value === '') {
            input.value = '';
            return;
        }
        input.value = new Intl.NumberFormat('id-ID').format(value);
    }

    const selector = document.getElementById('subjectSelector');
    const container = document.getElementById('tagsContainer');
    const hiddenContainer = document.getElementById('hiddenInputsContainer');
    const placeholder = document.getElementById('placeholderText');
    let selectedSubjects = [];

    selector.addEventListener('change', function() {
        const id = this.value;
        const name = this.options[this.selectedIndex].getAttribute('data-name');

        if (selectedSubjects.includes(id)) {
            Swal.fire({ icon: 'info', title: 'Sudah Ada', text: 'Mata pelajaran ini sudah masuk dalam list.', confirmButtonColor: '#06b6d4' });
            this.value = "";
            return;
        }

        selectedSubjects.push(id);
        placeholder.style.display = 'none';

        // Buat Tag Visual (Chips)
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

        // Buat Hidden Input untuk Form
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'subjects_id[]';
        hiddenInput.value = id;
        hiddenInput.id = `input-${id}`;
        hiddenContainer.appendChild(hiddenInput);

        this.value = ""; // Reset dropdown
    });

    window.removeTag = function(id) {
        selectedSubjects = selectedSubjects.filter(item => item !== id);
        document.querySelector(`[data-id="${id}"]`).remove();
        document.getElementById(`input-${id}`).remove();

        if (selectedSubjects.length === 0) {
            placeholder.style.display = 'flex';
        }
    };

    
</script>

@endsection