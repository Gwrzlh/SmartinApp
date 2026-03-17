<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran #{{ $transaction->id }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace; /* Font struk klasik tapi bersih */
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80mm; /* Standar Thermal Besar */
            margin: auto;
            padding: 5mm;
            border: 1px dashed #ccc;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 18px;
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #666;
        }
        .info {
            margin-bottom: 15px;
            border-bottom: 1px dashed #eee;
            padding-bottom: 10px;
        }
        .info div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .table {
            width: 100%;
            margin-bottom: 15px;
        }
        .table tr td {
            padding: 4px 0;
        }
        .totals {
            border-top: 1px dashed #eee;
            padding-top: 10px;
        }
        .totals div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #888;
        }
        .barcode {
            margin-top: 15px;
            text-align: center;
        }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        /* Layout untuk PDF */
        @page {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SMARTIN APP</h1>
            <p>Aplikasi Kursus Offline Profesional</p>
            <p>Jln. Raya Pendidikan No. 123, Indonesia</p>
            <p>Telp: 0812-3456-7890</p>
        </div>

        <div class="info">
            <div><strong>No. Transaksi:</strong> #TRX-{{ $transaction->id }}</div>
            <div><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($transaction->tgl_bayar)->format('d/m/Y H:i') }}</div>
            <div><strong>Siswa:</strong> {{ $student->student_name }} ({{ $student->student_nik }})</div>
            <div><strong>Kasir:</strong> {{ $transaction->user->full_name ?? $transaction->user->name }}</div>
        </div>

        <table class="table">
            @foreach($transaction->details as $detail)
            <tr>
                <td>
                    {{ $detail->item_type == 'registration' ? 'Biaya Pendaftaran' : ($detail->item_type == 'spp' ? 'SPP' : 'Kursus') }} - 
                    @if($detail->item_type == 'subject')
                        {{ \App\Models\subjects::find($detail->item_id)->mapel_name ?? 'Item #'.$detail->item_id }}
                    @elseif($detail->item_type == 'spp')
                        @php $enroll = \App\Models\enrollments::with('subject')->find($detail->item_id); @endphp
                        {{ $enroll->subject->mapel_name ?? 'Item #'.$detail->item_id }}
                    @elseif($detail->item_type == 'bundling')
                        {{ \App\Models\bundlings::find($detail->item_id)->bundling_name ?? 'Paket #'.$detail->item_id }}
                    @else
                        {{ $detail->item_id == 0 ? '' : 'Item #'.$detail->item_id }}
                    @endif
                </td>
                <td class="text-right">Rp{{ number_format($detail->price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>

        <div class="totals">
            <div class="font-bold">
                <span>TOTAL:</span>
                <span>Rp{{ number_format($transaction->total_bayar, 0, ',', '.') }}</span>
            </div>
            <div>
                <span>TUNAI:</span>
                <span>Rp{{ number_format($transaction->uang_diterima, 0, ',', '.') }}</span>
            </div>
            <div>
                <span>KEMBALI:</span>
                <span>Rp{{ number_format($transaction->uang_kembali, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih telah bergabung bersama kami!</p>
            <p>Struk ini adalah bukti pembayaran yang sah.</p>
            <p>&copy; {{ date('Y') }} SmartinApp Team</p>
        </div>
    </div>
</body>
</html>
