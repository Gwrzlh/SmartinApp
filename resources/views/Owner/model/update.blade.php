<div x-show="showUpdateModal" 
     class="fixed inset-0 z-[99] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-cloak>
    
    <div @click.away="showUpdateModal = false" 
         class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden transform transition-all"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100">
        
        <!-- Modal Header -->
        <div class="px-8 py-6 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-black text-slate-800 tracking-tight">Update Data Staff</h3>
                <p class="text-xs text-slate-500 font-medium">Edit informasi akun staff</p>
            </div>
            <button @click="showUpdateModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Modal Body -->
        <form :action="'{{ route('owner.users.update', ['user' => ':id']) }}'.replace(':id', editingUser.id)" method="POST" class="p-8 space-y-5">
            @csrf
            {{-- @method('PUT') --}}
            <div class="space-y-4">
                <!-- Full Name -->
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Nama Lengkap</label>
                    <input type="text" name="full_name" required x-model="editingUser.full_name"
                           class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Username -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Username</label>
                        <input type="text" name="username" required x-model="editingUser.username"
                               class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    <!-- Role -->
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Role</label>
                        <select name="role" required x-model="editingUser.role" class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 transition-all">
                            <option value="kasir">Kasir</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Email</label>
                    <input type="email" name="email" required x-model="editingUser.email"
                           class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <!-- Password (Optional) -->
                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Password Baru (Opsional)</label>
                    <input type="password" name="password" placeholder="Kosongkan jika tidak diubah" 
                           class="w-full bg-slate-50 border-none rounded-2xl px-5 py-3.5 text-sm font-semibold focus:ring-2 focus:ring-indigo-500 transition-all">
                </div>

                <!-- Status Toggle -->
                <div class="flex items-center justify-between bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <span class="text-xs font-bold text-slate-600">Status Akun Aktif</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="active" value="1" x-model="editingUser.is_active" class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                    </label>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-4 flex gap-3">
                <button type="button" @click="showUpdateModal = false" 
                        class="flex-1 px-6 py-3.5 rounded-2xl font-bold text-sm text-slate-500 hover:bg-slate-100 transition-all">
                    Batalkan
                </button>
                <button type="submit" 
                        class="flex-1 bg-slate-800 text-white px-6 py-3.5 rounded-2xl font-bold text-sm hover:bg-slate-900 shadow-xl shadow-slate-200 transition-all active:scale-95">
                    Update Data
                </button>
            </div>
        </form>
    </div>
</div>
