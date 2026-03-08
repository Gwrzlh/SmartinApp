<div id="studentModal" class="fixed inset-0 bg-black/40 flex items-center justify-center invisible z-50">

    <div id="studentModalContent" 
    class="bg-white rounded-xl shadow-xl w-[500px] p-6 transform transition-all duration-300 translate-y-4 opacity-0">

        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Tambah Siswa</h2>
            <button onclick="closeStudentModal()" class="text-gray-500 hover:text-red-500">
                ✕
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('simpanSiswa') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="text-sm text-gray-600">Nama</label>
                <input type="text" name="student_name"
                class="w-full border rounded-lg px-3 py-2">
            </div>
             <div>
                <label class="text-sm text-gray-600">Email</label>
                <input type="email" name="student_email"
                class="w-full border rounded-lg px-3 py-2">
            </div>
             <div>
                <label class="text-sm text-gray-600">No Handphone</label>
                <input type="number" name="student_Tlp"
                class="w-full border rounded-lg px-3 py-2">
            </div>
             <div>
                <label class="text-sm text-gray-600">Alamat</label>
                <input type="text" name="student_address"
                class="w-full border rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="text-sm text-gray-600">Gender</label>
                <select name="gender" class="w-full border rounded-lg px-3 py-2">
                    <option value="">Pilih Gender</option>
                    <option value="Laki-Laki">Laki laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <button type="button"
                onclick="closeStudentModal()"
                class="px-4 py-2 bg-gray-200 rounded-lg">
                Batal
                </button>

                <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                Simpan
                </button>
            </div>

        </form>

    </div>

</div>