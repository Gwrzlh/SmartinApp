<!-- Modal Profile Siswa -->
<div id="profileModal" class="fixed inset-0 z-50 flex items-center justify-center invisible overflow-x-hidden overflow-y-auto outline-none focus:outline-none transition-all duration-300">
    <div class="fixed inset-0 bg-blue-500/5 backdrop-blur-2xl transition-opacity" onclick="closeProfileModal()"></div>
    
    <div id="profileModalContent" class="relative w-full max-w-2xl mx-auto my-6 transition-all duration-300 transform translate-y-4 opacity-0 px-4">
        <div class="relative flex flex-col w-full bg-white/25 border border-white rounded-[2rem] shadow-[0_32px_64px_-12px_rgba(0,0,0,0.14)] outline-none focus:outline-none overflow-hidden">
            
            <!-- Modal Header (Glassmorph Effect) -->
            <div class="relative h-32 bg-gradient-to-r from-cyan-500 to-blue-600">
                <button type="button" onclick="closeProfileModal()" class="absolute top-4 right-4 p-2 text-white/80 hover:text-white hover:bg-white/20 rounded-full transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Profile Avatar & Identity -->
            <div class="relative px-8 -mt-16 pb-6">
                <div class="flex items-end gap-6">
                    <div class="h-32 w-32 flex items-center justify-center rounded-2xl bg-white shadow-xl text-4xl font-black text-cyan-600 border-4 border-white" id="profile_initial">
                        S
                    </div>
                    <div class="pb-2">
                        <h3 class="text-2xl font-black text-gray-800 leading-tight" id="profile_name">Nama Siswa</h3>
                        <p class="text-gray-500 font-medium" id="profile_nik">NIK: 2024010001</p>
                        <span id="profile_status_badge" class="mt-2 px-3 py-1 inline-flex text-xs font-bold rounded-full uppercase tracking-widest">
                            Active
                        </span>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="relative p-8 flex-auto max-h-[50vh] overflow-y-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Contact Info -->
                    <div>
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Informasi Kontak</h4>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 group">
                                <div class="p-2 bg-gray-50 text-gray-400 group-hover:bg-cyan-50 group-hover:text-cyan-600 rounded-lg transition-colors">
                                    <x-akar-envelope class="w-5 h-5"/>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-medium">Email Address</p>
                                    <p class="text-sm font-bold text-gray-700" id="profile_email">siswa@example.com</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 group">
                                <div class="p-2 bg-gray-50 text-gray-400 group-hover:bg-green-50 group-hover:text-green-600 rounded-lg transition-colors">
                                    <x-akar-phone class="w-5 h-5"/>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-medium">WhatsApp / Telp</p>
                                    <p class="text-sm font-bold text-gray-700" id="profile_phone">0812-3456-7890</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Info -->
                    <div>
                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Detail Personal</h4>
                        <div class="space-y-4">
                            <div class="flex items-center gap-3 group">
                                <div class="p-2 bg-gray-50 text-gray-400 group-hover:bg-purple-50 group-hover:text-purple-600 rounded-lg transition-colors">
                                    <x-akar-person class="w-5 h-5"/>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-medium">Jenis Kelamin</p>
                                    <p class="text-sm font-bold text-gray-700" id="profile_gender">Laki-laki</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 group">
                                <div class="p-2 bg-gray-50 text-gray-400 group-hover:bg-orange-50 group-hover:text-orange-600 rounded-lg transition-colors">
                                    <x-akar-location class="w-5 h-5"/>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-medium">Alamat Lengkap</p>
                                    <p class="text-sm font-bold text-gray-700 leading-relaxed" id="profile_address">Jln. Raya Pendidikan No. 123, Indonesia</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Program Section -->
                <div>
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Riwayat Program Akademik</h4>
                    <div id="profile_enrollments" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Dynamic Enrollments -->
                        <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-between group hover:border-cyan-200 hover:bg-cyan-50/30 transition-all">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white rounded-xl shadow-sm group-hover:text-cyan-600 transition-colors">
                                    <x-akar-book class="w-5 h-5"/>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-700">Matematika</p>
                                    <p class="text-[10px] text-gray-400 font-black uppercase tracking-tighter">Sampai: 12 Des 2024</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-8 border-t border-gray-50 bg-gray-50/30 flex justify-end">
                <button type="button" onclick="closeProfileModal()" 
                    class="px-8 py-3 text-sm font-black text-white bg-gray-800 rounded-2xl hover:bg-gray-900 transition-all hover:shadow-xl active:scale-95">
                    Tutup Profil
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openProfileModal(studentId) {
    const students = window.studentsData || [];
    const student = students.find(s => s.id === studentId);
    
    if (!student) return;

    // Fill Basic Info
    document.getElementById('profile_initial').innerText = student.student_name ? student.student_name.charAt(0).toUpperCase() : '?';
    document.getElementById('profile_name').innerText = student.student_name;
    document.getElementById('profile_nik').innerText = 'NIK: ' + student.student_nik;
    
    const badge = document.getElementById('profile_status_badge');
    badge.innerText = student.status === 'active' ? 'ACTIVE' : 'NON-ACTIVE';
    if (student.status === 'active') {
        badge.className = 'mt-2 px-3 py-1 inline-flex text-xs font-bold rounded-full uppercase tracking-widest bg-emerald-100 text-emerald-700 border border-emerald-200';
    } else {
        badge.className = 'mt-2 px-3 py-1 inline-flex text-xs font-bold rounded-full uppercase tracking-widest bg-gray-100 text-gray-600 border border-gray-200';
    }

    // Contact & Personal
    document.getElementById('profile_email').innerText = student.email || '-';
    document.getElementById('profile_phone').innerText = student.phone_number || '-';
    
    // Fix Gender Display: handle both 'L/P' and 'Laki-Laki/Perempuan'
    let genderText = student.gender || '-';
    if (student.gender === 'L' || student.gender === 'Laki-Laki') {
        genderText = 'Laki-laki';
    } else if (student.gender === 'P' || student.gender === 'Perempuan') {
        genderText = 'Perempuan';
    }
    document.getElementById('profile_gender').innerText = genderText;

    document.getElementById('profile_address').innerText = student.address || '-';

    // Enrollments
    const enrollList = document.getElementById('profile_enrollments');
    enrollList.innerHTML = '';
    
    const allEnrollments = student.enrollments || [];
    
    if (allEnrollments.length > 0) {
        allEnrollments.forEach(enroll => {
            let programName = '-';
            if(enroll.item_type === 'bundling' && enroll.bundling) {
                programName = enroll.bundling.bundling_name;
            } else if(enroll.subject) {
                programName = enroll.subject.mapel_name;
            }
            
            const expDate = enroll.expired_at ? new Date(enroll.expired_at).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric'
            }) : '-';

            let statusBadge = '';
            let borderHover = 'hover:border-gray-300 hover:bg-gray-50';
            
            if (enroll.status_pembelajaran === 'Lulus') {
                statusBadge = '<span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-bold uppercase tracking-wider">LULUS</span>';
                borderHover = 'hover:border-blue-300 hover:bg-blue-50/50';
            } else if (enroll.status_pembelajaran === 'Keluar') {
                statusBadge = '<span class="px-2 py-0.5 bg-gray-600 text-white rounded text-[10px] font-bold uppercase tracking-wider">KELUAR</span>';
                borderHover = 'hover:border-gray-400 hover:bg-gray-100';
            } else if (enroll.status_pembelajaran === 'active') {
                const now = new Date();
                const exp = new Date(enroll.expired_at);
                if (exp < now) {
                    statusBadge = '<span class="px-2 py-0.5 bg-rose-100 text-rose-700 rounded text-[10px] font-bold uppercase tracking-wider">MENUNGGAK</span>';
                    borderHover = 'hover:border-rose-300 hover:bg-rose-50/50';
                } else {
                    statusBadge = '<span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold uppercase tracking-wider">AKTIF</span>';
                    borderHover = 'hover:border-emerald-300 hover:bg-emerald-50/50';
                }
            } else {
                statusBadge = '<span class="px-2 py-0.5 bg-gray-200 text-gray-700 rounded text-[10px] font-bold uppercase tracking-wider">' + enroll.status_pembelajaran + '</span>';
            }

            // Tombol Keluar (Hanya jika masih active dan program sudah mulai)
            let quitButton = '';
            if (enroll.status_pembelajaran === 'active') {
                const bStart = (enroll.bundling && enroll.bundling.start_date) ? new Date(enroll.bundling.start_date) : null;
                const now = new Date();
                
                // Jika sudah mulai (atau tidak ada start_date), tampilkan tombol Keluar
                if (!bStart || bStart <= now) {
                    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    quitButton = `
                        <form action="/enrollments/${enroll.id}/quit" method="POST" onsubmit="return confirm('Konfirmasi: Siswa ini akan dinyatakan BERHENTI/KELUAR dari program? Sisa tagihan SPP untuk program ini akan otomatis berhenti.')" class="mt-2 pt-2 border-t border-gray-50 flex justify-end">
                            <input type="hidden" name="_token" value="${csrf}">
                            <button type="submit" class="group/quit flex items-center gap-1.5 px-3 py-1 bg-rose-50 hover:bg-rose-500 rounded-lg transition-all duration-300">
                                <span class="text-[9px] font-black text-rose-600 group-hover/quit:text-white uppercase tracking-widest">Berhenti / Keluar</span>
                                <svg class="w-3 h-3 text-rose-400 group-hover/quit:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            </button>
                        </form>
                    `;
                }
            }
            
            enrollList.innerHTML += `
                <div class="p-4 rounded-2xl bg-white border border-gray-200 flex flex-col group transition-all ${borderHover}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-gray-50 rounded-xl shadow-sm text-gray-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800 leading-tight">${programName}</p>
                            </div>
                        </div>
                        ${statusBadge}
                    </div>
                    <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">
                        BATAS SPP: <span class="text-gray-600">${expDate}</span>
                    </div>
                    ${quitButton}
                </div>
            `;
        });
    } else {
        enrollList.innerHTML = '<p class="text-sm text-gray-400 italic">Belum ada riwayat akademik.</p>';
    }

    // Show Modal
    const modal = document.getElementById('profileModal');
    const content = document.getElementById('profileModalContent');
    
    modal.classList.remove('invisible');
    setTimeout(() => {
        content.classList.remove('translate-y-4', 'opacity-0');
        content.classList.add('translate-y-0', 'opacity-100');
    }, 50);
}

function closeProfileModal() {
    const modal = document.getElementById('profileModal');
    const content = document.getElementById('profileModalContent');
    
    content.classList.add('translate-y-4', 'opacity-0');
    content.classList.remove('translate-y-0', 'opacity-100');
    
    setTimeout(() => {
        modal.classList.add('invisible');
    }, 300);
}
</script>
