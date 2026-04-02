@extends('layouts.Admin')
@section('content')

<div class="min-h-screen bg-gray-50/50 p-4 sm:p-8">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-amber-600 mb-6 transition-colors group">
        <x-akar-arrow-left class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" /> Kembali ke Manajemen User
    </a>
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-amber-600 to-amber-500 px-8 py-10 text-white">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                        <x-akar-calendar class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold">Edit Slot Jadwal</h3>
                        <p class="text-amber-50/80 text-sm">Perbarui detail jadwal yang sudah ada.</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-10">
                <form action="{{ route('admin.schedules.update', $schedule->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                   <div class="grid grid-cols-1 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Pilih Paket (Bundling)</label>
                            <select name="bundling_id" id="bundling_select" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 outline-none">
                                <option value="" disabled>Pilih Paket Bundling...</option>
                                @foreach($bundlings as $bundle)
                                    <option value="{{ $bundle->id }}" {{ old('bundling_id', $bundling_id) == $bundle->id ? 'selected' : '' }}>{{ $bundle->bundling_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Mata Pelajaran</label>
                                <select name="subject_id" id="subject_select" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 outline-none" disabled>
                                    <option value="" disabled selected>Pilih Bundling lebih dulu...</option>
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Mentor Pengajar</label>
                                <select name="mentor_id" id="mentor_select" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 outline-none" disabled>
                                    <option value="" disabled selected>Pilih Mapel lebih dulu...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Hari</label>
                            <select name="hari" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 outline-none">
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                    <option value="{{ $hari }}" {{ old('hari', $schedule->hari) == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Kapasitas (Slot Siswa)</label>
                            <input type="number" name="capacity" value="{{ old('capacity', $schedule->capacity) }}" min="1" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 outline-none">
                        </div> --}}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Jam Mulai</label>
                            <input type="time" name="jam_mulai" value="{{ old('jam_mulai', $schedule->jam_mulai) }}" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Jam Selesai</label>
                            <input type="time" name="jam_selesai" value="{{ old('jam_selesai', $schedule->jam_selesai) }}" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 outline-none">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Ruangan / Link Meeting</label>
                        <input type="text" name="ruangan" value="{{ old('ruangan', $schedule->ruangan) }}" placeholder="Contoh: Ruang A-01 atau Zoom Link" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-amber-500 outline-none">
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="submit" class="px-10 py-3 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-amber-200">
                            Perbarui Jadwal
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

    // 1. Bundling Change -> Load Subjects
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
                    if (oldSubjectId && oldSubjectId == sub.id) {
                        option.selected = true;
                    }
                    subjectSelect.add(option);
                });
                subjectSelect.disabled = false;
                
                if (subjectSelect.value) {
                    subjectSelect.dispatchEvent(new Event('change'));
                }
            });
    });

    // 2. Subject Change -> Load Mentors
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
                    if (oldMentorId && oldMentorId == men.id) {
                        option.selected = true;
                    }
                    mentorSelect.add(option);
                });
                mentorSelect.disabled = false;
            });
    });

    document.addEventListener('DOMContentLoaded', function() {
        if (bundlingSelect.value) {
            bundlingSelect.dispatchEvent(new Event('change'));
        }
    });

    // show validation errors via SweetAlert
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

    // alert for success message
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
        });
    @endif

    // alert for global error message
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