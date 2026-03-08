@extends('layouts.Admin')
@section('content')

<div class="min-h-screen bg-gray-50/50 p-4 sm:p-8">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-cyan-600 mb-6 transition-colors group">
        <x-akar-arrow-left class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform" /> Kembali ke Manajemen User
    </a>
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-gradient-to-r from-cyan-600 to-cyan-500 px-8 py-10 text-white">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-white/20 rounded-2xl backdrop-blur-md">
                        <x-akar-calendar class="w-8 h-8 text-white" />
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold">Buat Slot Jadwal</h3>
                        <p class="text-cyan-50/80 text-sm">Tentukan waktu, mentor, dan kapasitas ruangan.</p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-10">
                <form action="{{ route('admin.schedules.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                   <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Mentor Pengajar</label>
                            <select name="mentor_id" id="mentor_select" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-cyan-500 outline-none">
                                <option value="" disabled selected>Pilih Mentor...</option>
                                @foreach($mentors as $mentor)
                                    <option value="{{ $mentor->id }}">{{ $mentor->mentor_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Mata Pelajaran</label>
                            <select name="subject_id" id="subject_select" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-cyan-500 outline-none" disabled>
                                <option value="" disabled selected>Pilih Mentor lebih dulu...</option>
                            </select>
                        </div>
                    </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Hari</label>
                            <select name="hari" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-cyan-500 outline-none">
                                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'] as $hari)
                                    <option value="{{ $hari }}">{{ $hari }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Kapasitas (Slot Siswa)</label>
                            <input type="number" name="capacity" value="1" min="1" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-cyan-500 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                         <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-cyan-500 outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-cyan-500 outline-none">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1">Ruangan / Link Meeting</label>
                        <input type="text" name="ruangan" placeholder="Contoh: Ruang A-01 atau Zoom Link" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-100 focus:border-cyan-500 outline-none">
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="submit" class="px-10 py-3 bg-cyan-600 hover:bg-cyan-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-cyan-200">
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('mentor_select').addEventListener('change', function() {
        const mentorId = this.value;
        const subjectSelect = document.getElementById('subject_select');
        const oldSubjectId = "{{ old('subject_id', '') }}";

        // reset dropdown while loading
        subjectSelect.innerHTML = '<option value="" disabled selected>Loading...</option>';
        subjectSelect.disabled = true;

        if (mentorId) {
            let url = "{{ route('admin.getSubjects', ':id') }}";
            url = url.replace(':id', mentorId);

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    subjectSelect.innerHTML = '<option value="" disabled selected>Pilih Mapel...</option>';

                    if (data.length > 0) {
                        data.forEach(subject => {
                            const option = document.createElement('option');
                            option.value = subject.id;
                            option.textContent = subject.mapel_name;
                            if (oldSubjectId && oldSubjectId == subject.id) {
                                option.selected = true;
                            }
                            subjectSelect.appendChild(option);
                        });
                        subjectSelect.disabled = false;
                    } else {
                        subjectSelect.innerHTML = '<option value="" disabled selected>Mentor tidak punya mapel</option>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    subjectSelect.innerHTML = '<option value="" disabled selected>Gagal memuat data</option>';
                });
        } else {
            // no mentor selected, reset dropdown
            subjectSelect.innerHTML = '<option value="" disabled selected>Pilih Mentor lebih dulu...</option>';
        }
    });

    // if the form was reloaded with an old mentor selection, fire the change event to populate subjects
    document.addEventListener('DOMContentLoaded', function() {
        const mentorSelect = document.getElementById('mentor_select');
        if (mentorSelect && mentorSelect.value) {
            mentorSelect.dispatchEvent(new Event('change'));
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