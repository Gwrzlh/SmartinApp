@extends('layouts.Owner')

@section('content')
<div class="px-2 sm:px-0">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Log Aktivitas</h1>
            <p class="text-sm text-slate-500 mt-1 font-medium">Pantau setiap perubahan dan kegiatan staff di sistem</p>
        </div>
        <div class="bg-indigo-50 border border-indigo-100 rounded-2xl px-5 py-3 flex items-center shadow-sm">
            <div class="bg-indigo-500 p-2 rounded-lg text-white mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-indigo-400 uppercase tracking-widest">Total Logs</p>
                <p class="text-xs font-semibold text-indigo-700">{{ $activityLogs->total() }} Aktivitas Tercatat</p>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="mb-6 bg-white p-4 rounded-[2rem] shadow-sm border border-slate-100">
        <form action="{{ route('owner.logActivity') }}" method="GET" class="flex flex-wrap items-center gap-4">
            <!-- Search -->
            <div class="relative flex-1 min-w-[240px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari staff atau username..." 
                       class="w-full bg-slate-50 border-none rounded-xl px-5 py-3 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 transition-all">
                <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- Date Range -->
            <div class="flex items-center gap-2">
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="bg-slate-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500 transition-all">
                <span class="text-slate-300 font-black">/</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="bg-slate-50 border-none rounded-xl px-4 py-3 text-xs font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500 transition-all">
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="bg-slate-800 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-slate-900 transition-all shadow-lg shadow-slate-200 active:scale-95">
                    Terapkan Filter
                </button>
                @if(request()->anyFilled(['search', 'start_date', 'end_date']))
                <a href="{{ route('owner.logActivity') }}" class="bg-rose-50 text-rose-600 px-6 py-3 rounded-xl font-bold text-sm hover:bg-rose-100 transition-all active:scale-95">
                    Reset
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">User</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Aksi</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Deskripsi Detail</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Waktu Kejadian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($activityLogs as $log)
                    <tr class="hover:bg-slate-50/30 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-black text-sm border border-slate-200 group-hover:bg-indigo-50 group-hover:text-indigo-600 group-hover:border-indigo-100 transition-all">
                                    {{ substr($log->user->full_name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-black text-slate-800 text-sm tracking-tight">{{ $log->user->full_name ?? 'Unknown User' }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">@<span>{{ $log->user->username ?? 'deleted_user' }}</span></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ str_contains(strtolower($log->action), 'hapus') ? 'bg-rose-50 text-rose-600 border border-rose-100' : (str_contains(strtolower($log->action), 'tambah') ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-indigo-50 text-indigo-600 border border-indigo-100') }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-6">
                            <p class="text-xs text-slate-600 font-medium leading-relaxed max-w-sm">{{ $log->description }}</p>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-slate-700">{{ $log->created_at->translatedFormat('d F Y') }}</span>
                                <span class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $log->created_at->format('H:i:s') }} WIB</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-24 text-center">
                            <div class="bg-slate-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-dashed border-slate-200">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 tracking-tight">Belum ada log aktivitas</h3>
                            <p class="text-sm text-slate-500 mt-1 max-w-xs mx-auto font-medium">Data aktivitas akan muncul secara otomatis ketika staff melakukan tindakan di sistem.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Section -->
        @if($activityLogs->hasPages())
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100">
            {{ $activityLogs->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    /* Styling for pagination consistency */
    .pagination { @apply flex gap-2; }
    .page-item { @apply rounded-xl font-bold text-xs transition-all; }
    input[type="date"]::-webkit-calendar-picker-indicator {
        opacity: 0.4;
        cursor: pointer;
    }
</style>
@endsection