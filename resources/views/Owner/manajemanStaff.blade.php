@extends('layouts.Owner')

@section('content')
<div x-data="{ 
    confirmToggle() {
        return confirm('Apakah Anda yakin ingin mengubah status aktif staff ini?');
    }
}" class="p-2 sm:p-5 flex flex-col h-full bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden relative">

    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Manajemen Staff</h1>
                <p class="text-xs text-gray-400 font-medium">Kelola hak akses dan pantau performa tim Admin & Kasir</p>
            </div>
        </div>

        <!-- Combined Filter & Search (Consistent with Asset Management) -->
        <form action="{{ route('owner.manajemenStaff') }}" method="GET" class="flex flex-wrap gap-3 p-4 bg-gray-50/50 rounded-2xl border border-gray-100">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari staff..." 
                       class="w-full pl-9 pr-4 py-2 bg-white border border-gray-200 rounded-lg text-xs outline-none focus:ring-2 focus:ring-blue-100 transition-all">
                <x-akar-search class="absolute left-3 top-2.5 w-4 text-gray-400"/>
                <button type="submit" class="hidden"></button>
            </div>
            
            @if(request('search'))
                <a href="{{ route('owner.manajemenStaff') }}" class="inline-flex items-center px-3 py-2 text-xs text-red-500 hover:text-red-700 transition-all font-medium">
                    <x-akar-circle-x class="w-4 h-4 mr-1" /> Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="font-bold text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Table Section -->
    <div class="flex-1 overflow-y-auto custom-scroll pr-1">
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Info Staff</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Role & Status</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Login Terakhir</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Statistik</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-lg border border-blue-100 group-hover:scale-110 transition-transform">
                                    {{ substr($user->full_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 tracking-tight leading-none text-xs">{{ $user->full_name }}</p>
                                    <p class="text-[10px] text-gray-400 font-medium mt-1 italic">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex flex-col items-start gap-1.5">
                                <span class="px-2 py-0.5 rounded-lg text-[9px] font-bold uppercase tracking-wider {{ $user->role === 'admin' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-amber-50 text-amber-600 border border-amber-100' }}">
                                    {{ $user->role }}
                                </span>
                                <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-bold {{ $user->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></span>
                                    {{ $user->is_active ? 'Aktif' : 'Non-aktif' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            @if($user->last_login_at)
                                <p class="text-[11px] font-bold text-gray-700 leading-none">{{ $user->last_login_at->translatedFormat('d M Y') }}</p>
                                <p class="text-[9px] text-gray-400 font-medium mt-1">{{ $user->last_login_at->format('H:i') }} WIB</p>
                            @else
                                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest italic">Belum pernah login</span>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            @if($user->role === 'kasir')
                                <div class="bg-blue-50/50 border border-blue-100/50 rounded-xl px-3 py-2 inline-block">
                                    <p class="text-[8px] font-bold text-blue-400 uppercase tracking-widest mb-0.5">Total Omzet</p>
                                    <p class="text-xs font-bold text-gray-800 tracking-tight leading-none">Rp{{ number_format($user->total_revenue, 0, ',', '.') }}</p>
                                </div>
                            @else
                                <span class="text-[9px] font-bold text-gray-300 uppercase italic tracking-tighter">N/A (Admin)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('owner.users.toggleStatus', $user->id) }}" method="POST" @submit="if(!confirmToggle()) $event.preventDefault()">
                                @csrf
                                <button type="submit" 
                                        class="p-2 rounded-xl transition-all shadow-sm {{ $user->is_active ? 'bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white' }}">
                                    @if($user->is_active)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-20 text-center">
                            <div class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-dashed border-slate-200">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-slate-800">Tidak ada staff ditemukan</h3>
                            <p class="text-sm text-slate-500 mt-1">Gunakan tombol di atas untuk menambah data staff pertama.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-50">
            {{ $users->links() }}
        </div>
    </div>

    </div>
</div>

<style>
    .custom-scroll::-webkit-scrollbar { width: 4px; }
    .custom-scroll::-webkit-scrollbar-track { background: transparent; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #E5E7EB; border-radius: 10px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #D1D5DB; }
    [x-cloak] { display: none !important; }
</style>
@endsection
