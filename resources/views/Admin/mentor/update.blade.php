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
            to {
                transform: rotate(360deg);
            }
        }

        .btn-loading {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-in {
            animation: fade-in 0.2s ease-out forwards;
        }
    </style>

    <div class="min-h-screen bg-gray-50/50 p-4 sm:p-8">
        <div class="max-w-3xl mx-auto">
            <a href="{{ route('admin.mentor.index') }}"
                class="inline-flex items-center text-sm text-gray-500 hover:text-amber-600 mb-6 transition-colors group">
                <x-akar-arrow-left class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" /> Kembali ke
                Manajemen Mentor
            </a>

            <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 overflow-hidden border border-gray-100">
                <div class="bg-gradient-to-r from-amber-600 to-amber-500 px-8 py-10 text-white">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                            <x-akar-edit class="w-8 h-8 text-white" />
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold tracking-tight">Perbarui Data Mentor</h3>
                            <p class="text-amber-50/80 text-sm mt-1">Ubah informasi profil dan keahlian
                                {{ $mentor->mentor_name }}.</p>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-10">
                    <form action="{{ route('admin.mentor.update', $mentor->id) }}" method="POST" class="space-y-6"
                        id="mentorForm" onsubmit="handleFormSubmit(event)">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="mentor_name"
                                    class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap
                                    Mentor</label>
                                <input type="text" name="mentor_name" id="mentor_name"
                                    class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none"
                                    value="{{ old('mentor_name', $mentor->mentor_name) }}" required>
                            </div>

                            <div class="space-y-2">
                                <label for="phone_number"
                                    class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Nomor
                                    Telepon</label>
                                <input type="text" name="phone_number" id="phone_number"
                                    class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none"
                                    value="{{ old('phone_number', $mentor->phone_number) }}" required>
                            </div>
                        </div>

                        <div class="flex flex-col md:flex-row md:items-end gap-6 pt-4">
                            <div class="flex-1 space-y-2">
                                <label for="gender"
                                    class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Jenis
                                    Kelamin</label>
                                <div class="relative">
                                    <select name="gender" id="gender"
                                        class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-gray-700 transition-all 
                                    focus:bg-white focus:border-amber-500 outline-none appearance-none cursor-pointer">
                                        <option value="Laki-Laki" {{ $mentor->gender == 'Laki-Laki' ? 'selected' : '' }}>
                                            Laki Laki</option>
                                        <option value="Perempuan" {{ $mentor->gender == 'Perempuan' ? 'selected' : '' }}>
                                            Perempuan</option>
                                    </select>
                                    <div
                                        class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                        <x-akar-chevron-down class="w-4 h-4" />
                                    </div>
                                </div>
                            </div>

                            <div
                                class="flex items-center pb-3 px-4 h-[52px] bg-gray-50/50 border-2 border-gray-100 rounded-xl group hover:bg-amber-50/50 hover:border-amber-100 transition-all transition-all duration-300">
                                <input type="checkbox" name="isActive" id="isActive"
                                    class="h-5 w-5 text-amber-600 border-gray-300 rounded focus:ring-amber-500 cursor-pointer"
                                    {{ $mentor->is_active ? 'checked' : '' }}>
                                <label for="isActive"
                                    class="ml-3 text-sm font-semibold text-gray-600 cursor-pointer group-hover:text-amber-700">Status
                                    Mentor Aktif</label>
                            </div>
                        </div>

                        <div class="space-y-3 pt-6 border-t border-gray-50">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1 text-amber-600">
                                Keahlian / Spesialisasi Mapel
                            </label>

                            <div class="relative">
                                <select id="subjectSelector"
                                    class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50 text-gray-700 transition-all focus:bg-white focus:border-amber-500 outline-none appearance-none cursor-pointer">
                                    <option value="" disabled selected>-- Klik untuk menambah keahlian --</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}" data-name="{{ $subject->mapel_name }}">
                                            {{ $subject->mapel_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-400">
                                    <x-akar-chevron-down class="w-5 h-5" />
                                </div>
                            </div>

                            <div id="tagsContainer"
                                class="flex flex-wrap gap-2 p-4 min-h-[100px] rounded-2xl border-2 border-dashed border-gray-100 bg-gray-50/30 transition-all">
                                <div id="placeholderText"
                                    class="w-full flex flex-col items-center justify-center text-gray-300 py-4">
                                    <p class="text-xs font-medium">Belum ada spesialisasi yang dipilih</p>
                                </div>
                            </div>

                            <div id="hiddenInputsContainer"></div>
                        </div>

                        <div class="flex items-center justify-end space-x-4 pt-10 mt-6 border-t border-gray-50">
                            <a href="{{ route('admin.mentor.index') }}"
                                class="px-6 py-3 text-sm font-bold text-gray-400 hover:text-red-500 transition-colors">Batal</a>
                            <button type="submit" id="submitBtn"
                                class="px-10 py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-amber-200 hover:shadow-amber-300 hover:-translate-y-0.5 active:scale-95 flex items-center justify-center">
                                <span id="submitText">Perbarui Data Mentor</span>
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

        // Fungsi untuk menambah tag ke UI
        function addTag(id, name) {
            if (selectedSubjects.includes(id.toString())) return;

            selectedSubjects.push(id.toString());
            placeholder.style.display = 'none';

            const tag = document.createElement('div');
            tag.className =
                "group flex items-center bg-white border-2 border-amber-100 text-amber-700 pl-4 pr-2 py-2 rounded-xl shadow-sm hover:border-amber-300 transition-all animate-in";
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

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'spesialization_id[]';
            hiddenInput.value = id;
            hiddenInput.id = `input-${id}`;
            hiddenContainer.appendChild(hiddenInput);
        }

        // Load Data Lama (Pre-select Tags)
        document.addEventListener('DOMContentLoaded', function() {
            @foreach ($mentor->subjects as $subject)
                addTag("{{ $subject->id }}", "{{ $subject->mapel_name }}");
            @endforeach
        });

        selector.addEventListener('change', function() {
            const id = this.value;
            const name = this.options[this.selectedIndex].getAttribute('data-name');

            if (selectedSubjects.includes(id)) {
                Swal.fire({
                    icon: 'info',
                    title: 'Sudah Ada',
                    text: 'Mapel sudah masuk list.',
                    confirmButtonColor: '#f59e0b'
                });
                this.value = "";
                return;
            }

            addTag(id, name);
            this.value = "";
        });

        window.removeTag = function(id) {
            selectedSubjects = selectedSubjects.filter(item => item !== id.toString());
            document.querySelector(`[data-id="${id}"]`)?.remove();
            document.getElementById(`input-${id}`)?.remove();
            if (selectedSubjects.length === 0) placeholder.style.display = 'flex';
        };

        function handleFormSubmit(event) {
            event.preventDefault();
            const form = document.getElementById('mentorForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');

            if (selectedSubjects.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Kurang',
                    text: 'Pilih minimal satu keahlian.',
                    confirmButtonColor: '#f59e0b'
                });
                return;
            }

            submitBtn.disabled = true;
            submitText.innerHTML = '<div class="loading-spinner"></div> Memproses...';

            Swal.fire({
                title: 'Memperbarui Data...',
                text: 'Sedang menyimpan perubahan data mentor.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            form.submit();
        }
    </script>
@endsection
