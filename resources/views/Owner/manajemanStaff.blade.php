@extends('layouts.Owner')

@section('content')
<div x-data="{ 
    showCreateModal: false, 
    showUpdateModal: false,
    editingUser: { id: '', full_name: '', username: '', email: '', role: '', is_active: false },
    openEditModal(user) {
        this.editingUser = { ...user, is_active: !!user.is_active };
        this.showUpdateModal = true;
    },
    confirmToggle() {
        return confirm('Apakah Anda yakin ingin mengubah status aktif staff ini?');
    }
}" class="relative">

    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Manajemen Staff</h1>
            <p class="text-sm text-slate-500 mt-1 font-medium">Kelola hak akses dan pantau performa tim Admin & Kasir</p>
        </div>
        <button @click="showCreateModal = true" 
                class="bg-slate-800 text-white px-6 py-3.5 rounded-2xl font-bold text-sm hover:bg-slate-900 shadow-xl shadow-slate-200 transition-all active:scale-95 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tambah Staff Baru
        </button>
        <div class="flex items-center">
            <form action="{{ route('owner.manajemenStaff') }}" method="GET" class="relative group">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari staff..." 
                       class="bg-white border-2 border-slate-100 rounded-2xl px-5 py-3.5 pr-12 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all w-64 shadow-sm group-hover:border-slate-200">
                <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-100 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span class="font-bold text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Table Section -->
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Info Staff</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Role & Status</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Login Terakhir</th>
                        <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Statistik Performa</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-black text-lg border border-indigo-100 group-hover:scale-110 transition-transform">
                                    {{ substr($user->full_name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-black text-slate-800 tracking-tight leading-none">{{ $user->full_name }}</p>
                                    <p class="text-xs text-slate-400 font-medium mt-1">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <div class="flex flex-col items-start gap-1.5">
                                <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $user->role === 'admin' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'bg-amber-50 text-amber-600 border border-amber-100' }}">
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
                                <p class="text-xs font-bold text-slate-600">{{ $user->last_login_at->translatedFormat('d M Y') }}</p>
                                <p class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $user->last_login_at->format('H:i') }} WIB</p>
                            @else
                                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest italic">Belum pernah login</span>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            @if($user->role === 'kasir')
                                <div class="bg-indigo-50/50 border border-indigo-100/50 rounded-2xl px-4 py-2.5 inline-block">
                                    <p class="text-[8px] font-black text-indigo-400 uppercase tracking-widest mb-0.5">Total Omzet</p>
                                    <p class="text-sm font-black text-slate-800 tracking-tight">Rp{{ number_format($user->total_revenue, 0, ',', '.') }}</p>
                                </div>
                            @else
                                <span class="text-[10px] font-bold text-slate-300 uppercase italic">N/A (Admin)</span>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center justify-center gap-2">
                                <button @click="openEditModal({{ json_encode($user) }})" 
                                        class="p-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-800 hover:text-white transition-all shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </button>
                                
                                <form action="{{ route('owner.users.toggleStatus', $user->id) }}" method="POST" @submit="if(!confirmToggle()) $event.preventDefault()">
                                    @csrf
                                    <button type="submit" 
                                            class="p-2.5 rounded-xl transition-all shadow-sm {{ $user->is_active ? 'bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white' }}">
                                        @if($user->is_active)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        @endif
                                    </button>
                                </form>
                            </div>
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

    <!-- Modals -->
    @include('Owner.model.create')
    @include('Owner.model.update')

</div>

<!-- Scripts for interactivity -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection
