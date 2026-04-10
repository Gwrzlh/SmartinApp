
@php
    $path = public_path('asset/Smartin-removebg-preview.png'); 
    if (file_exists($path)) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    } else {
        $base64 = null; 
    }
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $transaction->transaction_type == 'refund' ? 'Struk Refund' : 'Struk Pembayaran' }} #{{ $transaction->id }}</title>
    <style>
        @page { margin: 0; }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .container {
            width: 72mm; 
            margin: auto;
            padding: 4mm;
        }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .dashed-line {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .header {
            margin-bottom: 10px;
        }
        .logo-placeholder {
            font-size: 30px;
            margin-bottom: 5px;
        }
        .shop-name {
            font-size: 16px;
            font-weight: bold;
            text-transform: capitalize;
            margin-bottom: 2px;
        }
        .address {
            font-size: 9px;
            padding: 0 5mm;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            margin-bottom: 5px;
            font-size: 10px;
        }
        .item-list {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .item-list td {
            vertical-align: top;
            padding: 2px 0;
        }
        .qty-price {
            font-size: 9px;
            color: #333;
            display: block;
        }
        .summary {
            width: 100%;
            margin-top: 5px;
        }
        .summary td {
            padding: 1px 0;
        }
        .total-row td {
            font-size: 13px;
            padding: 5px 0;
        }

        .footer {
            margin-top: 15px;
            font-size: 10px;
        }
        .e-receipt {
            font-size: 8px;
            margin-top: 10px;
            color: #555;
        }
        .logo-container {
        display: block;
        text-align: center;
        margin-bottom: 5px; 
        padding: 0;
        height: 35px; 
        overflow: hidden;  
    }

    .styled-logo {
        height: 100%;
        width: auto; 
        -webkit-filter: grayscale(100%) brightness(0%);
        filter: grayscale(100%) brightness(0%);
        image-rendering: pixelated; 
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="header text-center">
            @if($base64)
                <div class="logo-container">
                    <img src="{{ $base64 }}" alt="Logo" class="styled-logo">
                </div>
            @endif
            
            <div class="shop-name" style="margin-top: 0; padding-top: 0;">SMARTIN</div>
            @if($transaction->transaction_type == 'refund')
                <div class="font-bold" style="font-size: 14px; margin-bottom: 5px;">STRUK REFUND</div>
            @endif
            <div class="address">
                Jl. Arief Rahman Hakim No. 35, Subang, Kec. Subang,<br>
                Kabupaten Subang, Jawa Barat<br>
                {{ $transaction->id }}
            </div>
        </div>

        <div class="dashed-line"></div>

        <div class="meta-grid">
            <div>{{ \Carbon\Carbon::parse($transaction->tgl_bayar)->format('Y-m-d') }}</div>
            <div class="text-right">{{ $transaction->user->username ?? 'Kasir' }}</div>
            <div>{{ \Carbon\Carbon::parse($transaction->tgl_bayar)->format('H:i:s') }}</div>
            <div class="text-right">{{ $student->student_name }}</div>
        </div>

        <div class="dashed-line"></div>

        <table class="item-list">
            @foreach($transaction->details as $index => $detail)
            <tr>
                <td style="width: 5%;">{{ $index + 1 }}.</td>
                <td style="width: 60%;">
                    <span class="font-bold">
                        @if($detail->item_type == 'subject')
                            {{ \App\Models\subjects::find($detail->item_id)->mapel_name ?? 'Item' }}
                        @elseif($detail->item_type == 'bundling')
                            {{ \App\Models\bundlings::find($detail->item_id)->bundling_name ?? 'Program Bundling' }}
                        @elseif($detail->item_type == 'spp')
                            @php 
                                $enroll = \App\Models\enrollments::with(['subject', 'bundling'])->find($detail->item_id); 
                                $itemName = $enroll->bundling->bundling_name ?? $enroll->subject->mapel_name ?? 'Item';
                            @endphp
                            SPP {{ $itemName }}
                        @else
                            {{ ucfirst($detail->item_type) }}
                        @endif
                    </span>
                    <span class="qty-price">1 x {{ number_format($detail->price, 0, ',', '.') }}</span>
                </td>
                <td class="text-right" style="width: 35%;">
                    Rp {{ number_format($detail->price, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </table>

        <div class="dashed-line"></div>

        <table class="summary">
            <tr>
                <td>{{ $transaction->transaction_type == 'refund' ? 'Total Refund' : 'Sub Total' }}</td>
                <td class="text-right">Rp {{ number_format(abs($transaction->total_bayar), 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row font-bold">
                <td>{{ $transaction->transaction_type == 'refund' ? 'DIBAYAR KEMBALI' : 'TOTAL' }}</td>
                <td class="text-right">Rp {{ number_format(abs($transaction->total_bayar), 0, ',', '.') }}</td>
            </tr>
            @if($transaction->transaction_type != 'refund')
            <tr>
                <td>Bayar (Cash)</td>
                <td class="text-right">Rp {{ number_format($transaction->uang_diterima, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembali</td>
                <td class="text-right">Rp {{ number_format($transaction->uang_kembali, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>

        <div class="dashed-line"></div>

        <div class="footer text-center">
            @if($transaction->transaction_type == 'refund')
                <p>Tanda Terima Pengembalian Dana
                    <div class="e-receipt">Harap simpan struk ini sebagai bukti refund</div>
                </p>
            @else
                <p>Terima Kasih Sudah Mempercayai kami
                    <div class="e-receipt">Semoga Ilmu yang di dapat Bermanfaat</div>
                </p>
            @endif
        </div>
    </div>
</body>
</html>