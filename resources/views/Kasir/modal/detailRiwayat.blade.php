<!-- Modal Detail Transaksi -->
<div id="detailTrxModal" class="fixed inset-0 z-50 flex items-center justify-center invisible overflow-x-hidden overflow-y-auto outline-none focus:outline-none transition-all duration-300">
    <div class="fixed inset-0 bg-slate-900/10 backdrop-blur-xl transition-opacity" onclick="closeDetailTrxModal()"></div>
    
    <div id="detailTrxModalContent" class="relative w-full max-w-2xl mx-auto my-6 transition-all duration-300 transform translate-y-4 opacity-0 px-4">
        <div class="relative flex flex-col w-full bg-white/90 backdrop-blur-md border border-white/20 rounded-3xl shadow-[0_20px_50px_rgba(0,0,0,0.1)] outline-none focus:outline-none overflow-hidden">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                        <x-akar-reciept class="w-6 h-6"/>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800" id="detail_trx_id">000</h3>
                        <p class="text-sm text-gray-500" id="detail_trx_date">-- -- ----, --:--</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailTrxModal()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="relative p-6 flex-auto max-h-[60vh] overflow-y-auto">
                <!-- Info Siswa & Kasir -->
                <div class="grid grid-cols-2 gap-6 mb-8">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Siswa</p>
                        <p class="text-sm font-bold text-gray-800" id="detail_student_name">Nama Siswa</p>
                        <p class="text-xs text-gray-500" id="detail_student_nik">NIK: -</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Kasir</p>
                        <p class="text-sm font-bold text-gray-800" id="detail_cashier_name">Nama Kasir</p>
                    </div>
                </div>

                <!-- List Items -->
                <div class="mb-8">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Rincian Pembayaran</p>
                    <div class="overflow-hidden rounded-xl border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Item</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Harga</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100" id="detail_items_list">
                                <!-- Dynamic Items -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="bg-blue-50/50 rounded-2xl p-6 border border-blue-100/50">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm text-gray-600">Total Pembayaran</span>
                        <span class="text-xl font-black text-blue-600" id="detail_total_bayar">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center text-sm mb-2">
                        <span class="text-gray-500">Uang Diterima</span>
                        <span class="font-bold text-gray-700" id="detail_paid_amount">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Kembalian</span>
                        <span class="font-bold text-green-600" id="detail_change_amount">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-6 border-t border-gray-100 flex justify-end gap-3 bg-gray-50/30">
                <button type="button" onclick="closeDetailTrxModal()" 
                    class="px-6 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all hover:shadow-md">
                    Tutup
                </button>
                <a id="detail_print_btn" href="#" target="_blank"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-all hover:shadow-lg flex items-center gap-2">
                    <x-akar-reciept class="w-4 h-4"/>
                    Cetak Struk
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function openDetailTrxModal(trxId) {
    const transactions = window.transactionsData || [];
    const trx = transactions.find(t => t.id === trxId);
    
    if (!trx) return;

    // Fill Basic Info
    document.getElementById('detail_trx_id').innerText =trx.id;
    document.getElementById('detail_trx_date').innerText = new Date(trx.tgl_bayar).toLocaleString('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'
    });
    
    // Find Student Name
    let studentName = 'Siswa';
    let studentNik = 'N/A';
    
    // Logic as per riwayatTransaksi.blade.php
    if (trx.details && trx.details.length > 0) {
        for (let detail of trx.details) {
            // SPP / Subject Path
            if (detail.item_type === 'subject' || detail.item_type === 'spp') {
                if (detail.enrollment && detail.enrollment.student) {
                    studentName = detail.enrollment.student.student_name;
                    studentNik = detail.enrollment.student.student_nik;
                    break;
                }
            }
            // Registration Path
            if (detail.enrollment && detail.enrollment.student) {
                studentName = detail.enrollment.student.student_name;
                studentNik = detail.enrollment.student.student_nik;
                break;
            }
        }
    }

    document.getElementById('detail_student_name').innerText = studentName;
    document.getElementById('detail_student_nik').innerText = 'NIK: ' + studentNik;
    document.getElementById('detail_cashier_name').innerText = trx.user ? (trx.user.full_name || trx.user.name) : 'N/A';

    // Fill Items Table
    const itemsList = document.getElementById('detail_items_list');
    itemsList.innerHTML = '';
    
    trx.details.forEach(detail => {
        let itemName = detail.item_type === 'registration' ? 'Biaya Pendaftaran' : 
                      (detail.item_type === 'spp' ? 'SPP' : 'Kursus');
        
        // You might need more logic here to fetch mapel_name if it's not in the detail JSON
        // but for now we follow the type
        
         const row = `
            <tr>
                <td class="px-4 py-3">
                    <div class="text-sm font-semibold text-gray-800">${itemName}</div>
                    <div class="text-xs text-gray-400 capitalize">${detail.item_type}</div>
                </td>
                <td class="px-4 py-3 text-right text-sm font-bold text-gray-700">
                    Rp ${new Intl.NumberFormat('id-ID').format(detail.price)}
                </td>
            </tr>
        `;
        itemsList.innerHTML += row;
    });

    // Totals
    document.getElementById('detail_total_bayar').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(trx.total_bayar);
    document.getElementById('detail_paid_amount').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(trx.uang_diterima);
    document.getElementById('detail_change_amount').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(trx.uang_kembali);

    // Print Link
    document.getElementById('detail_print_btn').href = "{{ url('kasir/invoice') }}/" + trx.id;

    // Show Modal
    const modal = document.getElementById('detailTrxModal');
    const content = document.getElementById('detailTrxModalContent');
    
    modal.classList.remove('invisible');
    setTimeout(() => {
        content.classList.remove('translate-y-4', 'opacity-0');
        content.classList.add('translate-y-0', 'opacity-100');
    }, 50);
}

function closeDetailTrxModal() {
    const modal = document.getElementById('detailTrxModal');
    const content = document.getElementById('detailTrxModalContent');
    
    content.classList.add('translate-y-4', 'opacity-0');
    content.classList.remove('translate-y-0', 'opacity-100');
    
    setTimeout(() => {
        modal.classList.add('invisible');
    }, 300);
}
</script>
