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
        <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-amber-600 mb-6 transition-colors group">
            <x-akar-arrow-left class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" /> Kembali ke Manajemen Jadwal
        </a>

        <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 overflow-hidden border border-gray-100">
            {{-- Header dengan Gradient Amber --}}
            <div class="bg-gradient-to-r from-amber-600 to-amber-500 px-8 py-10 text-white">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                        <x-akar-calendar class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold tracking-tight">Edit Slot Jadwal</h3>
                        <p class="text-amber-50/80 text-sm mt-1">Perbarui detail jadwal pengajaran pada sistem SmartIn.</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-10">
                <form action="{{ route('admin.schedules.update', $schedule->id) }}" method="POST" class="space-y-8" id="scheduleForm" onsubmit="handleFormSubmit(event)">
                    @csrf
                    @method('PUT')

                    {{-- Section 1: Program & Mentor --}}
                    <div class="space-y-6">
                        <div class="flex items-center space-x-2 text-amber-600 mb-2">
                            <x-akar-book class="w-5 h-5" />
                            <h4 class="font-bold text-sm uppercase tracking-wider">Informasi Program</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Pilih Paket (Bundling)</label>
                                <select name="bundling_id" id="bundling_select" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 bg-gray-50/30 focus:bg-white focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all text-gray-700">
                                    <option value="" disabled>Pilih Paket Bundling...</option>
                                    @foreach($bundlings as $bundle)
                                        <option value="{{ $bundle->id }}" {{ old('bundling_id', $schedule->bundling_id) == $bundle->id ? 'selected' : '' }}>{{ $bundle->bundling_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Mata Pelajaran</label>
                                    <select name="subject_id" id="subject_select" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all bg-gray-50/50 text-gray-700" disabled>
                                        <option value="" disabled selected>Pilih Bundling lebih dulu...</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Mentor Pengajar</label>
                                    <select name="mentor_id" id="mentor_select" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all bg-gray-50/50 text-gray-700" disabled>
                                        <option value="" disabled selected>Pilih Mapel lebih dulu...</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-100">

                    {{-- Section 2: Waktu & Lokasi --}}
                    <div class="space-y-6">
                        <div class="flex items-center space-x-2 text-amber-600 mb-2">
                            <x-akar-clock class="w-5 h-5" />
                            <h4 class="font-bold text-sm uppercase tracking-wider">Waktu & Lokasi</h4>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Hari</label>
                                <select name="hari" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all text-gray-700">
                                    @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                        <option value="{{ $hari }}" {{ old('hari', $schedule->hari) == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Jam Mulai</label>
                                <input type="time" name="jam_mulai" value="{{ old('jam_mulai', $schedule->jam_mulai) }}" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all text-gray-700">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Jam Selesai</label>
                                <input type="time" name="jam_selesai" value="{{ old('jam_selesai', $schedule->jam_selesai) }}" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all text-gray-700">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="md:col-span-2 space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Ruangan / Link Meeting</label>
                                <input type="text" name="ruangan" value="{{ old('ruangan', $schedule->ruangan) }}" placeholder="Contoh: Ruang A-01" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all text-gray-700 text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Status Slot</label>
                                <select name="is_active" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 outline-none transition-all text-gray-700 text-sm">
                                    <option value="1" {{ old('is_active', $schedule->is_active) == '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('is_active', $schedule->is_active) == '0' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-10 mt-6 border-t border-gray-50">
                        <button type="submit" id="submitBtn" class="px-10 py-4 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-amber-200 hover:shadow-amber-300 hover:-translate-y-0.5 active:scale-95 flex items-center justify-center min-w-[220px]">
                            <span id="submitText" class="flex items-center">
                                <x-akar-circle-check class="w-5 h-5 mr-2" />
                                Perbarui Jadwal
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const bundlingSelect = document.getElementById('bundling_select');
    const subjectSelect = document.getElementById('subject_select');
    const mentorSelect = document.getElementById('mentor_select');

    const oldSubjectId = "{{ old('subject_id', $schedule->subject_id) }}";
    const oldMentorId = "{{ old('mentor_id', $schedule->mentor_id) }}";

    // 1. Logic Form Submission dengan Loading State
    function handleFormSubmit(event) {
        const form = document.getElementById('scheduleForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        
        event.preventDefault();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        submitBtn.disabled = true;
        if (submitText) {
            submitText.innerHTML = '<div class="loading-spinner"></div> <span class="ml-2">Memproses...</span>';
        }

        Swal.fire({
            title: 'Memperbarui Jadwal...',
            text: 'Mohon tunggu sebentar, sistem sedang memvalidasi data.',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        setTimeout(() => {
            form.submit();
        }, 600);
    }

    // 2. Chained Dropdown Logic (Bundling -> Mapel -> Mentor)
    bundlingSelect.addEventListener('change', function() {
        const bundlingId = this.value;
        subjectSelect.innerHTML = '<option value="" disabled selected>Loading Mapel...</option>';
        subjectSelect.disabled = true;
        mentorSelect.innerHTML = '<option value="" disabled selected>Pilih Mapel lebih dulu...</option>';
        mentorSelect.disabled = true;

        if (!bundlingId) return;

        fetch(`/admin/get-subjects-by-bundling/${bundlingId}`)
            .then(res => res.json())
            .then(data => {
                subjectSelect.innerHTML = '<option value="" disabled selected>Pilih Mapel...</option>';
                data.forEach(sub => {
                    let option = new Option(sub.mapel_name, sub.id);
                    if (oldSubjectId && oldSubjectId == sub.id) option.selected = true;
                    subjectSelect.add(option);
                });
                subjectSelect.disabled = false;
                if (subjectSelect.value) subjectSelect.dispatchEvent(new Event('change'));
            });
    });

    subjectSelect.addEventListener('change', function() {
        const subjectId = this.value;
        mentorSelect.innerHTML = '<option value="" disabled selected>Loading Mentor...</option>';
        mentorSelect.disabled = true;
        
        if (!subjectId) return;

        fetch(`/admin/get-mentors-by-subject/${subjectId}`)
            .then(res => res.json())
            .then(data => {
                mentorSelect.innerHTML = '<option value="" disabled selected>Pilih Mentor...</option>';
                data.forEach(men => {
                    let option = new Option(men.mentor_name, men.id);
                    if (oldMentorId && oldMentorId == men.id) option.selected = true;
                    mentorSelect.add(option);
                });
                mentorSelect.disabled = false;
            });
    });

    // Auto trigger on load
    document.addEventListener('DOMContentLoaded', function() {
        if (bundlingSelect.value) {
            bundlingSelect.dispatchEvent(new Event('change'));
        }
    });

    // 3. SweetAlert Messages
    @if ($errors->any())
        const errors = [@foreach ($errors->all() as $error) "{{ $error }}", @endforeach];
        Swal.fire({
            icon: 'error',
            title: 'Gagal Menyimpan!',
            html: `<ul class="text-left list-disc pl-6 text-gray-600">${errors.map(e => `<li>${e}</li>`).join('')}</ul>`,
            confirmButtonColor: '#d97706', // Amber-600
            customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl px-6 py-2' }
        });
    @endif

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            confirmButtonColor: '#059669', // Emerald-600
            customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl px-6 py-2' }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ session('error') }}",
            confirmButtonColor: '#ef4444',
            customClass: { popup: 'rounded-3xl', confirmButton: 'rounded-xl px-6 py-2' }
        });
    @endif
</script>

@endsection