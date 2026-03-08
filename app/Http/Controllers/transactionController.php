<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\students;
use App\Models\bundlings;
use App\Models\categories;
use App\Models\transactions;

class transactionController extends Controller
{
    public function index(Request $request)
    {
        // siswa (dapat difilter dengan query string q_student)
        $students = students::query()
            ->when($request->filled('q_student'), function ($q) use ($request) {
                $q->where('student_name', 'like', '%' . $request->q_student . '%');
            })
            ->orderBy('student_name')
            ->get();

      
        // load all bundlings along with their subject details
        $bundlings = bundlings::with('details.subject')
            ->when($request->filled('q_bundling'), function ($q) use ($request) {
                $pattern = '%' . $request->q_bundling . '%';

                // match name or price
                $q->where(function($q2) use ($pattern){
                    $q2->where('bundling_name', 'like', $pattern)
                       ->orWhere('bundling_price', 'like', $pattern);
                })
                // or match subject mapel_name
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

        $categories = categories::all();

        // hitung transaksi unpaid sebagai indikator tagihan terlambat
        // $lateCount = transactions::where('status_pembayaran', 'unpaid')->count();
        $countInactive = students::where('status','inactive')->count();

        return view('Kasir.transaksi', compact('students', 'bundlings', 'categories', 'countInactive'));
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
}
