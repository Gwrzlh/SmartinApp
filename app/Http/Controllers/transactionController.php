<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\students;
use App\Models\bundlings;
use App\Models\categories;
use App\Models\subjects;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\enrollments;
use App\Models\transactions;
use App\Models\transaction_details;
use App\Models\EnrollmentSchedule;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class transactionController extends Controller
{
    public function index(Request $request)
    {
        $cashierName = Auth::user()->full_name ?? Auth::user()->name ?? 'Kasir';
        // enrollments::where('status_pembelajaran', 'active')
        //     ->where('expired_at', '<', now()->toDateString())
        //     ->update(['status_pembelajaran' => 'inactive']);

        // $allStudents = students::all();
        // foreach ($allStudents as $student) {
        //     if ($student->isGraduatedWithDebt()) {
        //         continue;
        //     }

        //     // cek apakah enrollment active belum expired
        //     $stillHasActiveCourse = enrollments::where('student_id', $student->id)
        //         ->where('status_pembelajaran', 'active')
        //         ->where('expired_at', '>=', now()->toDateString())
        //         ->exists();

        // //  validasi jika siswa sudah graduate tapi masih berjalan
        //     $isGraduatedButStillStudying = enrollments::where('student_id', $student->id)
        //         ->where('status_pembelajaran', 'graduated')
        //         ->whereHas('bundling', function($q) {
        //             $q->where('is_active', 1);
        //         })->exists();

        //     if ($stillHasActiveCourse || $isGraduatedButStillStudying) {
        //         if ($student->status !== 'active') {
        //             $student->update(['status' => 'active']);
        //         }
        //     } else {
        //         if ($student->status !== 'inactive') {
        //             $student->update(['status' => 'inactive']);
        //         }
        //     }
        // }

        $selectedStudent = session()->has('selected_student') ? students::find(session('selected_student')) : null;
        $cart = session('cart', []);
        $mode = $request->mode ?? 'paket';
        
        $inactiveStudents = students::whereHas('enrollments', function($q) {
                $q->where('status_pembelajaran', 'inactive');
            })
            ->when($request->filled('q_spp'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('student_name', 'like', '%' . $request->q_spp . '%')
                      ->orWhere('student_nik', 'like', '%' . $request->q_spp . '%');
                });
            })
            ->get();

        $transaction_id = $request->query('transaction_id');
        $enrollmentsToSchedule = collect();
        $studentForSchedule = null;

        if ($transaction_id) {
            $detailIds = transaction_details::where('transaction_id', $transaction_id)->pluck('id');

            $enrollmentsToSchedule = enrollments::with(['subject.schedules.mentor'])
                ->whereIn('transaction_detail_id', $detailIds)
                ->where('item_type', 'subject')
                ->doesntHave('enrollmentSchedule')
                ->get();

            if ($enrollmentsToSchedule->isNotEmpty()) {
                $studentForSchedule = students::find($enrollmentsToSchedule->first()->student_id);

                foreach ($enrollmentsToSchedule as $enrollment) {
                    foreach ($enrollment->subject->schedules as $schedule) {
                        $enrolledCount = EnrollmentSchedule::where('schedule_id', $schedule->id)
                            ->where('status', 'ongoing')
                            ->count();
                        $schedule->remaining_capacity = $schedule->capacity - $enrolledCount;
                    }
                }
            }
        }

        $students = students::query()
            ->when($request->filled('q_student'), function ($q) use ($request) {
                $q->where('student_name', 'like', '%' . $request->q_student . '%');
            })
            ->latest()
            ->get();

        $bundlings = [];
        $subjects = [];

        // Di dalam function index
        if ($mode == 'paket') {
            $bundlings = bundlings::with('details.subject')
                ->whereDate('start_date', '>=', now()->toDateString())
                ->where('is_active', 1)
                ->when($request->filled('q_bundling'), function ($q) use ($request) {
                    $q->where('bundling_name', 'like', '%' . $request->q_bundling . '%');
                })
                ->get()
                ->filter(function($bundle) {
                    $enrolled = enrollments::where('item_id', $bundle->id)
                                ->where('item_type', 'bundling')
                                ->count();
                    $bundle->remaining_slots = $bundle->capacity - $enrolled;
                    return $bundle->remaining_slots > 0; 
                });
        }
        $studentEnrollments = collect();
            
        if ($selectedStudent && $mode == 'spp') {
            $studentEnrollments = enrollments::where('student_id', $selectedStudent->id)
                ->where('item_type', 'bundling')
                //tidak menampilkan graduated,atau keluar
                ->whereNotIn('status_pembelajaran', ['graduated', 'Lulus', 'Keluar']) 
                ->with('bundling')
                ->get()
                ->filter(function($enrollment) {
                    if (!$enrollment->bundling) return false;
                    return true; 
                });
        }

        $categories = categories::all();
        $countInactive = students::whereHas('enrollments', function($q) {
            $q->where('status_pembelajaran', 'inactive');
        })->count();
        $totalAmount = collect($cart)->sum(fn($item) => $item['price'] * ($item['quantity'] ?? 1));

        return view('Kasir.transaksi', compact(
            'students', 'bundlings', 'subjects', 'inactiveStudents',
            'categories', 'countInactive', 'mode', 'selectedStudent', 'cart', 'totalAmount', 'cashierName',
            'transaction_id', 'enrollmentsToSchedule', 'studentForSchedule', 'studentEnrollments'
        ));
    }

    public function storeSiswa(Request $request) 
    {
        $request->validate([
            'student_name' => 'required',
            'student_email' => 'required|email|unique:students,email', 
            'student_Tlp' => 'required',
            'gender' =>'required'
        ]);

        $prefix = now()->format('Ym');

        $latestStudent = students::withTrashed()
                            ->where('student_nik', 'LIKE', $prefix . '%')
                            ->orderByRaw('CAST(student_nik AS UNSIGNED) DESC')
                            ->first();

        if ($latestStudent) {
            // Ambil 4 angka terakhir dari NIK yang benar-benar paling besar di DB
            $lastSequence = (int) substr($latestStudent->student_nik, -4);
            $sequence = $lastSequence + 1;
        } else {
            $sequence = 1;
        }

        $nik = $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Proses Create
        students::create([
            'student_nik'  => $nik,
            'student_name' => $request->student_name,
            'gender'       => $request->gender,
            'phone_number' => $request->student_Tlp,
            'address'      => $request->student_address,
            'email'        => $request->student_email,
            'status'       => 'inactive',
        ]);

        logActivity('Mendaftarkan Siswa Baru', 'Siswa: ' . $request->student_name);

        return redirect()->back()->with('success', 'Siswa berhasil didaftarkan dengan NIK: ' . $nik);
    }

    public function updateSiswa(Request $request, students $student)
    {
        $request->validate([
            'student_name' => 'required',
            'student_email' => 'required',
            'student_Tlp' => 'required',
            'gender' =>'required'
        ]);

        $student->update([
            'student_name' => $request->student_name,
            'gender'       => $request->gender,
            'phone_number' => $request->student_Tlp,
            'address'      => $request->student_address,
            'email'        => $request->student_email,
        ]);

        logActivity('Mengubah Data Siswa', 'Siswa: ' . $request->student_name);

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui.');
    }
    public function destroySiswa(students $student)
    {
        $student_name = $student->student_name;
        $student->delete(); 
        logActivity('Menghapus Siswa (Soft Delete)', 'Siswa: ' . $student_name);
        return redirect()->back()->with('success', 'Data siswa berhasil dinonaktifkan dari sistem.');
    }
   
    public function selectStudent(Request $request)
    {
        $studentId = $request->student_id;
        $mode = $request->mode ?? 'paket';

        session(['selected_student' => $studentId]);

        $student = students::find($studentId);
        $hasEverBeenEnrolled = enrollments::withTrashed()->where('student_id', $studentId)->exists();
            
        $cart = session()->get('cart', []);

        if(!$hasEverBeenEnrolled){
            $registrationExists = collect($cart)->where('type','registration')->count();
            if($registrationExists == 0){
                $cart[] = [
                    'type' => 'registration',
                    'id' => 0,
                    'name' => 'Biaya Pendaftaran',
                    'price' => 150000,
                    'quantity' => 1
                ];
            }
        }
        session(['cart'=>$cart]);
        return redirect()->back();
    }

    public function addToCart(Request $request)
    {
        $item = [
            'type' => $request->type,
            'id' => $request->id,
            'name' => $request->name,
            'price' => (float) $request->price,
            'quantity' => 1,
        ];
        $cart = session('cart', []);
        $cart[] = $item;
        session(['cart' => $cart]);
        return redirect()->back();
    }

    public function removeItem($index)
    {
        $cart = session()->get('cart', []);
        unset($cart[$index]);
        session(['cart' => array_values($cart)]);
        return back();
    }

    public function checkout(Request $request)
    {
        $cart = session('cart', []);
        $studentId = session('selected_student');

        if (!$studentId) return back()->with('error','Pilih siswa terlebih dahulu');
        if (empty($cart)) return back()->with('error','Cart masih kosong');

        // validasi tunggakan spp
        $student = students::find($studentId);
        if ($student && $student->hasDebt()) {
            $totalDebt = $student->totalDebt();
            return back()->with(
                'debt_warning',
                "⛔ Pendaftaran GAGAL! Siswa <strong>{$student->student_name}</strong> masih memiliki " .
                "tunggakan SPP sebesar <strong>Rp " . number_format($totalDebt, 0, ',', '.') . 
                "</strong>. Harap selesaikan pembayaran tunggakan terlebih dahulu sebelum mendaftar program baru."
            );
        }

        $total = collect($cart)->sum(fn($item) => $item['price'] * ($item['quantity'] ?? 1));

        $request->validate(['paid_amount' => ['required', 'numeric', "min:{$total}"]]);

        $paid = (float) $request->paid_amount;
        $change = $paid - $total;

        DB::beginTransaction();
        try {
            $transaction = transactions::create([
                'user_id' => Auth::id(),
                'tgl_bayar' => now(),
                'total_bayar' => $total,
                'uang_diterima' => $request->paid_amount,
                'uang_kembali' => $request->paid_amount - $total,
                'status_pembayaran' => 'paid'
            ]);

            foreach ($cart as $item) {
                $detail = transaction_details::create([
                    'transaction_id' => $transaction->id,
                    'item_type' => $item['type'], 
                    'item_id' => $item['id'],
                    'price' => $item['price']
                ]);

                if ($item['type'] == 'bundling') {
                    $bundle = bundlings::find($item['id']);

                    // simpan enrollment per bundling/program
                    $enrollment = enrollments::create([
                        'student_id' => $studentId,
                        'transaction_detail_id' => $detail->id,
                        'item_id' => $bundle->id,
                        'item_type' => 'bundling',
                        'tgl_daftar' => now(),
                        'expired_at' => \Carbon\Carbon::parse($bundle->start_date)->addMonth(),
                        'status_pembelajaran' => 'active'
                    ]);
                    $schedules = \App\Models\schedules::where('bundling_id', $bundle->id)->get();
                    foreach ($schedules as $sch) {
                        EnrollmentSchedule::create([
                            'enrollment_id' => $enrollment->id,
                            'schedule_id' => $sch->id,
                            'status' => 'ongoing'
                        ]);
                    }

                    students::where('id', $studentId)->update(['status' => 'active']);
                } 
                
               elseif ($item['type'] == 'spp') {
                    $enrollment = enrollments::with('bundling')->find($item['id']);
                    if ($enrollment && $enrollment->bundling) {
                        $bundling = $enrollment->bundling;

                        // Hitung batas akhir program
                        $endDate = Carbon::parse($bundling->start_date)
                            ->addMonths($bundling->duration_mounths);

                        // Hitung expired_at baru: 
                        $baseDate   = ($enrollment->expired_at && Carbon::parse($enrollment->expired_at)->gt(now()))
                                        ? Carbon::parse($enrollment->expired_at)
                                        : now();
                        $newExpired = $baseDate->copy()->addMonth();

                        // validasi membatasi agar tidak lebih dari durasi program
                        if ($newExpired->gt($endDate)) {
                            $newExpired = $endDate->copy();
                        }

                        // menentukan status pembelajaran
                        if ($bundling->is_active == 0 && $newExpired->gte($endDate)) {
                            $newStatus = 'graduated';
                        } else {
                            $newStatus = 'active';
                        }

                        $enrollment->update([
                            'expired_at'          => $newExpired,
                            'status_pembelajaran' => $newStatus,
                        ]);

                        // Update status siswa sesuai hasil enrollment
                        if ($newStatus === 'graduated') {
                            // Cek apakah masih ada enrollment aktif lain sebelum set inactive
                            $hasOtherActive = enrollments::where('student_id', $studentId)
                                ->where('status_pembelajaran', 'active')
                                ->where('id', '!=', $enrollment->id)
                                ->exists();

                            students::where('id', $studentId)
                                ->update(['status' => $hasOtherActive ? 'active' : 'inactive']);
                        } else {
                            // Siswa baru bayar SPP = aktif
                            students::where('id', $studentId)->update(['status' => 'active']);
                        }
                    }
                }
            }


           DB::commit();
            logActivity('Checkout Paket', 'Siswa ID: '.$studentId);
            session()->forget(['cart', 'selected_student']);
            return redirect()->route('kasir.transaction', [
                'transaction_id' => $transaction->id,
                'print_invoice' => $transaction->id
            ])->with('success', 'Pendaftaran Berhasil! Jadwal otomatis terisi.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function generateInvoice($id)
    {
        $transaction = transactions::with(['details', 'user'])->findOrFail($id);
        $student = null;

        // Jika transaksi tipe refund, cari pendaftaran terkait untuk mendapatkan data siswa
        if ($transaction->transaction_type == 'refund') {
            $refundDetail = $transaction->details->where('item_type', 'refund_50')->first();
            if ($refundDetail) {
                $enrollment = enrollments::withTrashed()->with('student')->find($refundDetail->item_id);
                if ($enrollment && $enrollment->student) {
                    $student = $enrollment->student;
                }
            }
        } else {
            // Logika pencarian siswa untuk transaksi pembayaran biasa
            foreach($transaction->details as $detail) {
                if ($detail->item_type == 'spp') {
                    $enrollment = enrollments::with('student')->find($detail->item_id);
                    if ($enrollment && $enrollment->student) {
                        $student = $enrollment->student;
                        break;
                    }
                } else {
                    $enrollment = enrollments::where('transaction_detail_id', $detail->id)->with('student')->first();
                    if($enrollment && $enrollment->student) {
                        $student = $enrollment->student;
                        break;
                    }
                }
            }
        }
        
        if (!$student) $student = (object)['student_name' => 'Siswa', 'student_nik' => 'N/A'];
        return Pdf::loadView('Kasir.invoice', compact('transaction', 'student'))
                  ->setPaper([0, 0, 226.77, 600], 'portrait')
                  ->stream('Invoice-'.$transaction->id.'.pdf');
    }

    public function siswaManage(Request $request)
    {
        $students = students::query()
            ->when($request->filled('search'), function($q) use ($request) {
                $q->where('student_name', 'like', '%' . $request->search . '%')
                  ->orWhere('student_nik', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('status'), function($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->with(['enrollments.subject', 'enrollments.bundling'])
            ->paginate(5)->withQueryString();

        return view('Kasir.siswaManage', compact('students'));
    }

    public function riwayatTransaksi(Request $request)
    {
        $transactions = transactions::query()
            ->with(['details.enrollment.student', 'user'])
            ->when($request->filled('search'), function($q) use ($request) {
                $search = $request->search;
                $q->where(function($sub) use ($search) {
                    $sub->where('id', 'like', "%{$search}%")
                        ->orWhereHas('details.enrollment.student', function($sq) use ($search) {
                            $sq->where('student_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('date'), function($q) use ($request) {
                $q->whereDate('tgl_bayar', $request->date);
            })
            ->latest()
            ->paginate(10)->withQueryString();

        return view('Kasir.riwayatTransaksi', compact('transactions'));
    }


    public function cancelEnrollment(Request $request, enrollments $enrollment)
    {
        // validasi tanggal mulai program
        $today = now()->toDateString();
        $startDate = null;

        if ($enrollment->item_type == 'bundling') {
            $startDate = $enrollment->bundling->start_date ?? null;
        } else {
            $startDate = $enrollment->subject->bundling->start_date ?? null; // Jika ada parent bundling
        }

        if ($startDate && $startDate <= $today) {
            return back()->with('error', 'Gagal Batal: Program sudah dimulai. Gunakan fitur "Keluar" di detail siswa.');
        }

        DB::beginTransaction();
        try {
            // Hitung Refund 50% dari harga pendaftaran asli
            $originalPrice = $enrollment->transaction_detail->price ?? 0;
            $refundAmount = $originalPrice * 0.5;

            // Buat Transaksi Refund Baru
            $refundTransaction = transactions::create([
                'user_id' => Auth::id(),
                'tgl_bayar' => now(),
                'total_bayar' => $refundAmount,
                'uang_diterima' => 0,
                'uang_kembali' => $refundAmount,
                'status_pembayaran' => 'paid',
                'transaction_type' => 'refund'
            ]);

            // Catat detail refund
            transaction_details::create([
                'transaction_id' => $refundTransaction->id,
                'item_type' => 'refund_50',
                'item_id' => $enrollment->id,
                'price' => -$refundAmount // Nilai negatif untuk laporan keuangan
            ]);

            // Soft Delete Enrollment
            $studentName = $enrollment->student->student_name ?? 'Siswa';
            $programName = ($enrollment->item_type == 'bundling') ? ($enrollment->bundling->bundling_name ?? 'Paket') : ($enrollment->subject->mapel_name ?? 'Mapel');
            
            $enrollment->delete();

            logActivity('Membatalkan Pendaftaran (Refund 50%)', "Siswa: {$studentName}, Program: {$programName}, Refund: Rp".number_format($refundAmount, 0, ',', '.'));

            DB::commit();

            return redirect()->route('kasir.transaction', ['print_invoice' => $refundTransaction->id])
                             ->with('success', "Pendaftaran '{$programName}' berhasil dibatalkan. Dana refund: Rp".number_format($refundAmount, 0, ',', '.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses pembatalan: ' . $e->getMessage());
        }
    }

 
    public function quitEnrollment(Request $request, enrollments $enrollment)
    {
        try {
            $studentName = $enrollment->student->student_name ?? 'Siswa';
            $programName = ($enrollment->item_type == 'bundling') ? ($enrollment->bundling->bundling_name ?? 'Paket') : ($enrollment->subject->mapel_name ?? 'Mapel');

            $enrollment->update([
                'status_pembelajaran' => 'Keluar',
                'finish_at' => now()
            ]);

            logActivity('Siswa Keluar/Berhenti Tengah Jalan', "Siswa: {$studentName}, Program: {$programName}");

            return back()->with('success', "Siswa '{$studentName}' telah dinyatakan Keluar dari program '{$programName}'. Tagihan SPP akan otomatis berhenti.");

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses status keluar: ' . $e->getMessage());
        }
    }
}
