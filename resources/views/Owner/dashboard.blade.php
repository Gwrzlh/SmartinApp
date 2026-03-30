@extends('layouts.Owner')

@section('content')
<div class="min-h-screen bg-[#f8fafc] p-8">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 tracking-tight">Ringkasan Bisnis</h1>
            <p class="text-slate-500 text-sm mt-1">Pantau performa kursus Anda secara real-time.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="flex items-center text-xs font-medium text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm">
                <span class="w-2 h-2 bg-emerald-400 rounded-full mr-2 animate-pulse"></span>
                Sistem Aktif
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        @php
            $stats = [
                ['label' => 'Pendapatan', 'value' => 'Rp ' . number_format($totalRevenue, 0, ',', '.'), 'sub' => 'Bulan ini', 'color' => 'indigo'],
                ['label' => 'Siswa Aktif', 'value' => $activeStudents, 'sub' => 'Total saat ini', 'color' => 'emerald'],
                ['label' => 'Pendaftaran', 'value' => $newEnrollments, 'sub' => '30 hari terakhir', 'color' => 'blue'],
                ['label' => 'Kelas Hari Ini', 'value' => $classesToday, 'sub' => 'Sesi terjadwal', 'color' => 'amber'],
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-[0_2px_4px_rgba(0,0,0,0.02)]">
            <p class="text-[11px] uppercase tracking-wider font-semibold text-slate-400 mb-3">{{ $stat['label'] }}</p>
            <h3 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $stat['value'] }}</h3>
            <p class="text-xs text-slate-500 mt-2 flex items-center">
                <span class="text-{{ $stat['color'] }}-500 font-medium">{{ $stat['sub'] }}</span>
            </p>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2 bg-white p-7 rounded-2xl border border-slate-200/60 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-sm font-semibold text-slate-800 uppercase tracking-wide">Tren Pendapatan</h3>
                <select class="text-xs bg-slate-50 border-none rounded-md py-1 px-2 text-slate-500 ring-1 ring-slate-200">
                    <option>6 Bulan Terakhir</option>
                </select>
            </div>
            <div class="h-[300px]">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-7 rounded-2xl border border-slate-200/60 shadow-sm flex flex-col">
            <h3 class="text-sm font-semibold text-slate-800 uppercase tracking-wide mb-6">Log Aktivitas</h3>
            <div class="flex-1 space-y-6 overflow-y-auto">
                @foreach($activityLogs as $log)
                <div class="flex gap-4">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400">{{ substr($log->user->username ?? 'S', 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-800 leading-normal">
                            <span class="font-semibold">{{ $log->user->username ?? 'System' }}</span> 
                            {{ strtolower($log->action) }}
                        </p>
                        <p class="text-[10px] text-slate-400 mt-1">{{ $log->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <a href="{{ route('owner.logActivity') }}" class="mt-6 pt-4 border-t border-slate-50 text-center text-xs font-medium text-indigo-600 hover:text-indigo-700 transition-colors">
                Lihat Semua Laporan
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white p-7 rounded-2xl border border-slate-200/60 shadow-sm">
            <h3 class="text-sm font-semibold text-slate-800 uppercase tracking-wide mb-6">Okupansi Ruangan</h3>
            <div class="space-y-6">
                @foreach($roomOccupancy as $room)
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-medium text-slate-700">Ruang {{ $room['ruangan'] }} <span class="text-slate-400 font-normal ml-1">— {{ $room['subject'] }}</span></span>
                        <span class="text-[10px] font-semibold text-slate-500">{{ $room['filled'] }}/{{ $room['capacity'] }}</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-1.5">
                        <div class="h-1.5 rounded-full bg-slate-800 transition-all duration-700" style="width: {{ $room['percentage'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm overflow-hidden">
            <div class="p-7 pb-4">
                <h3 class="text-sm font-semibold text-slate-800 uppercase tracking-wide">Jadwal Mendatang</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-7 py-3 text-[10px] font-semibold text-slate-500 uppercase">Waktu</th>
                            <th class="px-7 py-3 text-[10px] font-semibold text-slate-500 uppercase">Mata Pelajaran</th>
                            <th class="px-7 py-3 text-[10px] font-semibold text-slate-500 uppercase">Mentor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($upcomingSchedules as $schedule)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-7 py-4">
                                <span class="text-xs font-medium text-slate-700">{{ \Carbon\Carbon::parse($schedule->jam_mulai)->format('H:i') }}</span>
                            </td>
                            <td class="px-7 py-4">
                                <p class="text-xs font-medium text-slate-800">{{ $schedule->subject->subject_name ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400">Ruang {{ $schedule->ruangan }}</p>
                            </td>
                            <td class="px-7 py-4">
                                <span class="text-xs text-slate-600">{{ $schedule->mentor->mentor_name ?? '-' }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        Chart.defaults.font.family = "'Inter', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif";
        Chart.defaults.color = '#64748b';

        // Data 
        const revData = @json($revenueTrend);
        const coursesData = @json($popularCourses);

        // Revenue Trend Chart (Line Chart)
        const ctxRev = document.getElementById('revenueChart').getContext('2d');
        
        // Gradient styling
        let gradient = ctxRev.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.25)'); 
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        new Chart(ctxRev, {
            type: 'line',
            data: {
                labels: revData.labels,
                datasets: [{
                    label: 'Revenue (Rp)',
                    data: revData.data,
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#f8fafc',
                        bodyColor: '#f1f5f9',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 14 },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { padding: 10, font: {weight: '500'} }
                    },
                    y: {
                        grid: { color: '#f1f5f9', drawBorder: false, borderDash: [4, 4] },
                        ticks: { 
                            padding: 10,
                            callback: function(value) {
                                if(value >= 1000000) return (value/1000000) + 'M';
                                if(value >= 1000) return (value/1000) + 'K';
                                return value;
                            }
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        // Popular Courses Chart
        const ctxCourses = document.getElementById('coursesChart').getContext('2d');
        if(coursesData.labels.length > 0) {
            new Chart(ctxCourses, {
                type: 'doughnut',
                data: {
                    labels: coursesData.labels,
                    datasets: [{
                        data: coursesData.data,
                        backgroundColor: [
                            '#6366f1', '#3b82f6', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6'
                        ],
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 24,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: { size: 12, weight: '500' }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            padding: 12,
                            cornerRadius: 8,
                            bodyFont: { size: 13, weight: '500' },
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.label + ': ' + context.parsed + ' Enrollments';
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endsection