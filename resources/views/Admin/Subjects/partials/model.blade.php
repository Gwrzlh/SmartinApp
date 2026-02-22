<!-- modal overlay -->
<div id="subjectsModal" class="fixed inset-0 z-50 invisible overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-900/40 backdrop-blur-sm" onclick="closeModal()"></div>

        <div class="inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-8 translate-y-4 opacity-0" id="modalContent">
            
            <div class="absolute top-0 right-0 pt-6 pr-6">
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none bg-gray-50 rounded-full p-2">
                    <x-akar-cross class="w-5 h-5" />
                </button>
            </div>

            <div class="flex items-center mb-8">
                <div class="h-16 w-16 rounded-2xl bg-cyan-600 flex items-center justify-center text-white shadow-lg shadow-cyan-200">
                    <x-bx-book class="w-8 h-8" />
                </div>
                <div class="ml-4">
                    <h3 class="text-xl font-bold text-gray-900" id="modalSubjects">Loading...</h3>
                    <p class="text-sm text-gray-500" id="modalSubjectCategory"></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6" id="modalGridBody">
                </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                <button onclick="closeModal()" class="px-6 py-2 text-sm font-bold text-gray-600 hover:text-gray-800 transition-colors">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>
</div>