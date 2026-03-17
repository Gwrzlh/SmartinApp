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

                <!-- Program Aktif Section -->
                <div>
                    <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Program & Kursus Aktif</h4>
                    <div id="profile_enrollments" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
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
    badge.innerText = student.status;
    if (student.status === 'active') {
        badge.className = 'mt-2 px-3 py-1 inline-flex text-xs font-bold rounded-full uppercase tracking-widest bg-green-100 text-green-700';
    } else {
        badge.className = 'mt-2 px-3 py-1 inline-flex text-xs font-bold rounded-full uppercase tracking-widest bg-gray-100 text-gray-600';
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
    
    const activeEnrollments = (student.enrollments || []).filter(e => e.status_pembelajaran === 'active');
    
    if (activeEnrollments.length > 0) {
        activeEnrollments.forEach(enroll => {
            const mapelName = enroll.subject ? enroll.subject.mapel_name : 'Program';
            const expDate = enroll.expired_at ? new Date(enroll.expired_at).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric'
            }) : '-';
            
            enrollList.innerHTML += `
                <div class="p-4 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-between group hover:border-cyan-200 hover:bg-cyan-50/30 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white rounded-xl shadow-sm group-hover:text-cyan-600 transition-colors">
                            <x-akar-book class="w-5 h-5"/>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-700">${mapelName}</p>
                            <p class="text-[10px] text-gray-400 font-black uppercase tracking-tighter">Sampai: ${expDate}</p>
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        enrollList.innerHTML = '<p class="text-sm text-gray-400 italic">Belum ada program aktif</p>';
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
