@extends('layouts.Kasir')
@section('content')

<div class="p-6 bg-gray-50 min-h-screen" x-data="rescheduleController()">
    
    <div class="mb-4">
        <a href="{{ route('kasir.schedules.index') }}" class="text-cyan-600 hover:underline text-sm font-medium">
            &larr; Kembali ke Daftar Jadwal
        </a>
    </div>
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow p-6 mb-6 flex flex-col md:flex-row gap-6 items-start md:items-center justify-between border-l-4 border-cyan-500">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $schedule->subject->mapel_name ?? '-' }}</h1>
            <p class="text-sm text-gray-600 mt-1">
                {{ $schedule->hari }} | {{ \Carbon\Carbon::parse($schedule->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->jam_selesai)->format('H:i') }}
            </p>
            <p class="text-sm text-gray-600">Mentor: <span class="font-medium">{{ $schedule->mentor->mentor_name ?? '-' }}</span></p>
        </div>
        <div class="flex gap-4">
            <div class="bg-gray-100 rounded-lg p-3 text-center min-w-[100px]">
                <p class="text-xs text-gray-500 mb-1">Kapasitas</p>
                <p class="text-lg font-bold text-gray-800">{{ $schedule->capacity }}</p>
            </div>
            <div class="bg-cyan-50 rounded-lg p-3 text-center min-w-[100px]">
                <p class="text-xs text-gray-500 mb-1">Siswa Terdaftar</p>
                <p class="text-lg font-bold text-cyan-600">{{ $enrollmentSchedules->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="p-4 border-b">
            <h2 class="font-semibold text-lg text-gray-800">Daftar Siswa Kelas Ini</h2>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b text-gray-600 text-sm">
                    <th class="p-4 font-semibold">Nama Siswa</th>
                    <th class="p-4 font-semibold text-center">Status</th>
                    <th class="p-4 font-semibold text-center">Tgl Daftar</th>
                    <th class="p-4 font-semibold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($enrollmentSchedules as $es)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4">
                            <p class="text-sm font-bold text-gray-800">{{ $es->enrollment->student->student_name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $es->enrollment->student->student_nik ?? '-' }}</p>
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-block px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-semibold capitalize">
                                {{ $es->status }}
                            </span>
                        </td>
                        <td class="p-4 text-center text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($es->enrollment->tgl_daftar)->format('d M Y') }}
                        </td>
                        <td class="p-4 text-center">
                            <button @click="openModal({{ $es->id }}, '{{ $es->enrollment->student->student_name ?? '' }}')" 
                               class="inline-block px-3 py-1.5 bg-amber-50 border border-amber-200 text-amber-600 hover:bg-amber-100 text-xs font-semibold rounded transition-colors">
                                Reschedule
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-500">
                            Belum ada siswa di kelas ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Reschedule -->
    <div x-show="isModalOpen" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            
            <div x-show="isModalOpen" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="closeModal()" 
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"></div>
                 
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div x-show="isModalOpen" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                
                <form action="{{ route('kasir.schedules.reschedule') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Reschedule Siswa
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Pindahkan <span x-text="selectedStudentName" class="font-bold text-gray-700"></span> ke jadwal lain untuk mata pelajaran ini.
                                    </p>
                                </div>

                                <input type="hidden" name="enrollment_schedule_id" :value="selectedEsId">
                                
                                <div class="mt-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Jadwal Baru</label>
                                    
                                    <template x-if="isLoading">
                                        <p class="text-sm text-gray-500 italic">Mencari jadwal yang tersedia...</p>
                                    </template>
                                    
                                    <template x-if="!isLoading && availableSchedules.length === 0">
                                        <div class="bg-red-50 text-red-600 p-3 rounded text-sm">
                                            Tidak ada jadwal lain yang tersedia / masih memiliki kuota kosong untuk kelas ini.
                                        </div>
                                    </template>

                                    <template x-if="!isLoading && availableSchedules.length > 0">
                                        <select name="target_schedule_id" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-cyan-500 focus:border-cyan-500 sm:text-sm rounded-md border p-2">
                                            <option value="">-- Pilih Jadwal --</option>
                                            <template x-for="sch in availableSchedules" :key="sch.id">
                                                <option :value="sch.id" x-text="`${sch.hari} | ${sch.jam_mulai.substring(0,5)} - ${sch.jam_selesai.substring(0,5)} | ${sch.mentor ? sch.mentor.nama : '-'} (Sisa: ${sch.capacity - sch.active_students_count})`"></option>
                                            </template>
                                        </select>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" :disabled="availableSchedules.length === 0" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-cyan-600 text-base font-medium text-white hover:bg-cyan-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            Simpan Perubahan
                        </button>
                        <button type="button" @click="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('rescheduleController', () => ({
            isModalOpen: false,
            selectedEsId: null,
            selectedStudentName: '',
            isLoading: false,
            availableSchedules: [],
            subjectId: {{ $schedule->subject_id }},
            currentScheduleId: {{ $schedule->id }},
            
            openModal(esId, studentName) {
                this.selectedEsId = esId;
                this.selectedStudentName = studentName;
                this.isModalOpen = true;
                this.fetchSchedules();
            },
            
            closeModal() {
                this.isModalOpen = false;
                this.selectedEsId = null;
                this.availableSchedules = [];
            },
            
            fetchSchedules() {
                this.isLoading = true;
                fetch(`/api/schedules/available/${this.subjectId}?current_schedule_id=${this.currentScheduleId}`)
                    .then(response => response.json())
                    .then(data => {
                        this.availableSchedules = data;
                        this.isLoading = false;
                    })
                    .catch(error => {
                        console.error('Error fetching schedules:', error);
                        this.isLoading = false;
                    });
            }
        }));
    });
    if (window.Alpine) {
        window.Alpine.data('rescheduleController', () => ({
            isModalOpen: false,
            selectedEsId: null,
            selectedStudentName: '',
            isLoading: false,
            availableSchedules: [],
            subjectId: {{ $schedule->subject_id }},
            currentScheduleId: {{ $schedule->id }},
            
            openModal(esId, studentName) {
                this.selectedEsId = esId;
                this.selectedStudentName = studentName;
                this.isModalOpen = true;
                this.fetchSchedules();
            },
            
            closeModal() {
                this.isModalOpen = false;
                this.selectedEsId = null;
                this.availableSchedules = [];
            },
            
            fetchSchedules() {
                this.isLoading = true;
                fetch(`/api/schedules/available/${this.subjectId}?current_schedule_id=${this.currentScheduleId}`)
                    .then(response => response.json())
                    .then(data => {
                        this.availableSchedules = data;
                        this.isLoading = false;
                    })
                    .catch(error => {
                        console.error('Error fetching schedules:', error);
                        this.isLoading = false;
                    });
            }
        }));
    }
</script>
@endsection
