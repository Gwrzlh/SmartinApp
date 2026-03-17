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

class transactionController extends Controller
{
    public function index(Request $request)
    {
        $cashierName = Auth::user()->full_name ?? Auth::user()->name ?? 'Kasir';

        // 1. Logika Otomatis Update Status Siswa (Active -> Inactive jika expired)
        $activeSiswa = students::where('status', 'active')->get();
        foreach ($activeSiswa as $student) {
            $stillHasActiveCourse = enrollments::where('student_id', $student->id)
                ->where('status_pembelajaran', 'active')
                ->where('expired_at', '>=', now()->toDateString())
                ->exists();

            if (!$stillHasActiveCourse) {
                $student->update(['status' => 'inactive']);
            }
        }

        // 2. Inisialisasi Data Dasar dari Session & Request (Diletakkan di awal agar stabil)
        $selectedStudent = session()->has('selected_student') ? students::find(session('selected_student')) : null;
        $cart = session('cart', []);
        $mode = $request->mode ?? 'paket';
        
        // Ambil daftar siswa inactive untuk ditampilkan di UI (Mode SPP)
        $inactiveStudents = students::where('status', 'inactive')->get();

        // 3. Logika Penjadwalan (Crucial & Important)
        $transaction_id = $request->query('transaction_id');
        $enrollmentsToSchedule = collect();
        $studentForSchedule = null;

        if ($transaction_id) {
            $detailIds = transaction_details::where('transaction_id', $transaction_id)->pluck('id');

            $enrollmentsToSchedule = enrollments::with(['subject.schedules.mentor'])
                ->whereIn('transaction_detail_id', $detailIds)
                ->where('item_type', 'subject')
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

        // 4. Query Daftar Siswa untuk Pencarian/Sidebar
        $students = students::query()
            ->when($request->filled('q_student'), function ($q) use ($request) {
                $q->where('student_name', 'like', '%' . $request->q_student . '%');
            })
            ->orderBy('student_name')
            ->get();

        // 5. Query Produk Berdasarkan Mode (Paket / Mapel)
        $bundlings = [];
        $subjects = [];

        if ($mode == 'paket') {
            $bundlings = bundlings::with('details.subject')
                ->when($request->filled('q_bundling'), function ($q) use ($request) {
                    $pattern = '%' . $request->q_bundling . '%';
                    $q->where(function($q2) use ($pattern){
                        $q2->where('bundling_name', 'like', $pattern)
                            ->orWhere('bundling_price', 'like', $pattern);
                    })
                    ->orWhereHas('details.subject', function($q3) use ($pattern){
                        $q3->where('mapel_name', 'like', $pattern);
                    });
                })
                ->when($request->filled('category_id'), function ($q) use ($request) {
                    $q->whereHas('details.subject', function($q2) use ($request) {
                        $q2->where('category_id', $request->category_id);
                    });
                })
                ->get();
        }

        if ($mode == 'mapel') {
            $subjects = subjects::with('categories')
                ->when($request->filled('q_mapel'), function ($q) use ($request) {
                    $pattern = '%' . $request->q_mapel . '%';
                    $q->where(function ($q2) use ($pattern) {
                        $q2->where('mapel_name', 'like', $pattern)
                        ->orWhere('monthly_price', 'like', $pattern);
                    });
                })
                ->when($request->filled('category_id'), function ($q) use ($request) {
                    $q->where('category_id', $request->category_id);
                })
                ->orderBy('mapel_name')
                ->get();
        }

        // 6. Logika Otomatis Masukkan SPP ke Cart (Jimmy akan terdeteksi di sini)
        if ($selectedStudent && ($mode == 'spp' || $selectedStudent->status == 'inactive')) {
            $enrollments = enrollments::where('student_id', $selectedStudent->id)
                ->where('status_pembelajaran', 'active')
                ->with('subject')
                ->get();

            foreach ($enrollments as $enroll) {
                if ($enroll->subject) {
                    // Mencegah duplikasi item yang sama di cart
                    $exists = collect($cart)->where('type', 'spp')->where('id', $enroll->id)->first();
                    if (!$exists) {
                        $cart[] = [
                            'type' => 'spp',
                            'id' => $enroll->id,
                            'name' => 'SPP - ' . $enroll->subject->mapel_name . ' (Masa Aktif: ' . ($enroll->expired_at ? \Carbon\Carbon::parse($enroll->expired_at)->format('d M Y') : '-') . ')',
                            'price' => $enroll->subject->monthly_price,
                            'quantity' => 1
                        ];
                    }
                }
            }
            // Update session cart dengan data terbaru
            session(['cart' => $cart]);
        }

        // 7. Data Pendukung Lainnya
        $categories = categories::all();
        $countInactive = students::where('status', 'inactive')->count();
        $totalAmount = collect($cart)->sum(fn($item) => $item['price'] * ($item['quantity'] ?? 1));

        return view('Kasir.transaksi', compact(
            'students', 'bundlings', 'subjects', 'inactiveStudents',
            'categories', 'countInactive', 'mode', 'selectedStudent', 'cart', 'totalAmount', 'cashierName',
            'transaction_id', 'enrollmentsToSchedule', 'studentForSchedule'
        ));
    }

    public function storeSiswa(Request $request){
        $request->validate([
            'student_name' => 'required',
            'student_email' => 'required',
            'student_Tlp' => 'required',
            'gender' =>'required'
        ]);

        $latestStudent = students::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->latest()
                            ->first();

        $sequence = $latestStudent ? (int) substr($latestStudent->student_nik, -4) + 1 : 1;
        $nik = now()->format('Ym') . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            students::create([
            'student_nik'  => $nik,
            'student_name' => $request->student_name,
            'gender'       => $request->gender,
            'phone_number' => $request->student_Tlp,
            'address'      => $request->student_address,
            'email'        => $request->student_email,
            'status'       => 'inactive',
        ]);

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

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui.');
    }
    public function destroySiswa(students $student)
    {
        $student->delete();
        return redirect()->back()->with('success', 'Data siswa berhasil dihapus.');
    }
   
    public function selectStudent(Request $request)
    {
        $studentId = $request->student_id;
        $mode = $request->mode ?? 'paket';

        session(['selected_student' => $studentId]);

        $student = students::find($studentId);
        $hasEnrollment = enrollments::where('student_id',$studentId)->exists();
        $cart = session()->get('cart', []);

        if(!$hasEnrollment){
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

        if ($mode == 'spp' || $student->status == 'inactive') {
            $enrollments = enrollments::where('student_id', $studentId)
                ->where('status_pembelajaran', 'active')
                ->with('subject')
                ->get();
            
            foreach ($enrollments as $enroll) {
                if ($enroll->subject) {
                    $cart[] = [
                        'type' => 'spp',
                        'id' => $enroll->id, // Menggunakan Enrollment ID untuk pelacakan
                        'name' => 'SPP - ' . $enroll->subject->mapel_name . ' (Sampai: ' . \Carbon\Carbon::parse($enroll->expired_at)->format('d M Y') . ')',
                        'price' => $enroll->subject->monthly_price,
                        'quantity' => 1
                    ];
                }
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
                'uang_diterima' => $paid,
                'uang_kembali' => $change,
                'status_pembayaran' => 'paid'
            ]);

            $shouldActivate = false;
            foreach ($cart as $item) {
                $detail = transaction_details::create([
                    'transaction_id' => $transaction->id,
                    'item_type' => $item['type'],
                    'item_id' => $item['id'] ?? 0,
                    'price' => $item['price']
                ]);

                if (in_array($item['type'], ['subject', 'bundling', 'spp'])) $shouldActivate = true;
                // kalo beli per matapelajaran 
                if ($item['type'] == 'subject') {
                    enrollments::create([
                        'student_id' => $studentId,
                        'transaction_detail_id' => $detail->id,
                        'item_type' => 'subject',
                        'item_id' => $item['id'],
                        'tgl_daftar' => now(),
                        'expired_at' =>now()->addMonth(),
                        'status_pembelajaran' => 'active'
                    ]);
                    // beli per bundling 
                } elseif ($item['type'] == 'bundling') {
                    $bundlingDetails = \App\Models\bundling_details::where('bundling_id', $item['id'])->get();
                    foreach ($bundlingDetails as $bDetail) {
                        enrollments::create([
                            'student_id' => $studentId,
                            'transaction_detail_id' => $detail->id,
                            'item_type' => 'subject',
                            'item_id' => $bDetail->subject_id,
                            'tgl_daftar' => now(),
                            'expired_at' =>now()->addMonth(),
                            'status_pembelajaran' => 'active'
                        ]);
                    }
                }
                elseif ($item['type'] == 'spp') {
                    $enrollment = enrollments::find($item['id']);
                    if ($enrollment) {
                        // Jika expired_at masih lama, tambahkan dari tgl tersebut (akumulatif)
                        // Jika sudah lewat/kosong, tambahkan dari hari ini
                        $baseDate = ($enrollment->expired_at && $enrollment->expired_at > now()) 
                                    ? \Carbon\Carbon::parse($enrollment->expired_at) 
                                    : now();

                        $enrollment->update([
                            'expired_at' => $baseDate->addMonth()
                        ]);
                    }
                }
            }

            if ($shouldActivate) {
                students::where('id',$studentId)->update(['status' => 'active']);
            }

            DB::commit();
            session()->forget(['cart','selected_student']);

            return redirect()->route('kasir.transaction', ['transaction_id' => $transaction->id, 'print_invoice' => $transaction->id])
                ->with('success','Transaksi berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error','Checkout gagal : '.$e->getMessage());
        }
    }

    public function generateInvoice($id)
    {
         $transaction = transactions::with(['details', 'user'])->findOrFail($id);
        $student = null;
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
            ->with(['enrollments.subject'])
            ->paginate(10)->withQueryString();

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
}
