@extends('layouts.Owner')

@section('content')
<div class="p-2 sm:p-5 flex flex-col h-full bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden relative">

    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Log Aktivitas</h1>
                <p class="text-xs text-gray-400 font-medium">Pantau setiap perubahan dan kegiatan staff di sistem</p>
            </div>
            <div class="bg-blue-50/50 border border-blue-100/50 rounded-xl px-4 py-2 flex items-center gap-3">
                <div class="text-blue-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[8px] font-bold text-blue-400 uppercase tracking-widest leading-none mb-1">Total Logs</p>
                    <p class="text-[11px] font-bold text-gray-800 leading-none">{{ $activityLogs->total() }} Aktivitas</p>
                </div>
            </div>
        </div>

        <!-- Filter Section (Consistent with Asset Management) -->
        <form action="{{ route('owner.logActivity') }}" method="GET" class="flex flex-wrap gap-3 p-4 bg-gray-50/50 rounded-2xl border border-gray-100">
            <!-- Search -->
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari staff atau username..." 
                       class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                <x-akar-search class="absolute left-3 top-2.5 w-4 text-gray-400"/>
            </div>

            <!-- Date Range -->
            <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-2 py-1">
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="bg-transparent border-none p-1 text-[10px] font-bold text-gray-600 focus:ring-0 outline-none">
                <span class="text-gray-300 font-bold">-</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="bg-transparent border-none p-1 text-[10px] font-bold text-gray-600 focus:ring-0 outline-none">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-[10px] font-bold uppercase tracking-wider hover:bg-blue-600 transition-all">
                    Filter
                </button>
                @if(request()->anyFilled(['search', 'start_date', 'end_date']))
                    <a href="{{ route('owner.logActivity') }}" class="inline-flex items-center px-3 py-2 text-xs text-red-500 hover:text-red-700 transition-all font-medium">
                        <x-akar-circle-x class="w-4 h-4 mr-1" /> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Section -->
    <div class="flex-1 overflow-y-auto custom-scroll pr-1">
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Deskripsi Detail</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-right">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($activityLogs as $log)
                    <tr class="hover:bg-slate-50/30 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xs border border-blue-100 group-hover:scale-110 transition-transform">
                                    {{ substr($log->user->full_name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-xs tracking-tight leading-none">{{ $log->user->full_name ?? 'Unknown User' }}</p>
                                    <p class="text-[9px] text-gray-400 font-medium mt-1 italic leading-none">@<span>{{ $log->user->username ?? 'deleted_user' }}</span></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[9px] font-bold uppercase tracking-wider {{ str_contains(strtolower($log->action), 'hapus') ? 'bg-rose-50 text-rose-600 border border-rose-100' : (str_contains(strtolower($log->action), 'tambah') ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-blue-50 text-blue-600 border border-blue-100') }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-6">
                            <p class="text-[11px] text-gray-500 font-medium leading-relaxed max-w-xs">{{ $log->description }}</p>
                        </td>
                        <td class="px-6 py-6 text-right">
                            <div class="flex flex-col">
                                <span class="text-[11px] font-bold text-gray-700 leading-none">{{ $log->created_at->translatedFormat('d M Y') }}</span>
                                <span class="text-[9px] text-gray-400 font-medium mt-1 leading-none">{{ $log->created_at->format('H:i') }} WIB</span>
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
        <div class="px-8 py-4 bg-gray-50/50 border-t border-gray-100">
            {{ $activityLogs->links() }}
        </div>
        @endif
    </div>

    </div>
</div>

<style>
    .custom-scroll::-webkit-scrollbar { width: 4px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #D1D5DB; }
    input[type="date"]::-webkit-calendar-picker-indicator {
        opacity: 0.4;
        cursor: pointer;
    }
</style>
@endsection