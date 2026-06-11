<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; color: #333; line-height: 1.5; font-size: 14px; }
        .receipt-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        .header { display: table; width: 100%; border-bottom: 2px solid #E8711C; padding-bottom: 20px; margin-bottom: 20px; }
        .header-left { display: table-cell; vertical-align: middle; }
        .header-right { display: table-cell; text-align: right; vertical-align: middle; }
        .logo { width: 120px; }
        .title { font-size: 24px; color: #E8711C; font-weight: bold; }
        .details-table { width: 100%; margin-top: 30px; border-collapse: collapse; }
        .details-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .label { font-weight: bold; color: #666; width: 30%; }
        .amount-row { background: #f9f9f9; font-size: 18px; font-weight: bold; }
        .qr-section { margin-top: 40px; text-align: right; }
        .footer-note { margin-top: 50px; font-size: 12px; color: #777; text-align: center; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="receipt-box">
        <div class="header">
            <div class="header-left">
                @if(isset($logo) && $logo)
                    <img src="data:image/png;base64,{{ $logo }}" class="logo">
                @else
                    <div style="font-size: 20px; font-weight: bold; color: #E8711C;">DRISHIKA FOUNDATION</div>
                @endif
            </div>
            <div class="header-right">
                <div class="title">DONATION RECEIPT</div>
                <div>Receipt No: {{ $donation->receipt_number }}</div>
                <div>Date: {{ $donation->created_at->format('d M, Y') }}</div>
            </div>
        </div>

        <p>Received with thanks from:</p>
        <table class="details-table">
            <tr>
                <td class="label">Donor Name</td>
                <td>{{ $donation->donor_name }}</td>
            </tr>
            <tr>
                <td class="label">Contact</td>
                <td>
                    {{ $donation->phone }}
                    @if($donation->email)
                        | {{ $donation->email }}
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Payment Mode</td>
                <td>{{ ucfirst($donation->donation_type) }}</td>
            </tr>
            <tr class="amount-row">
                <td class="label">Amount Paid</td>
                <td style="color: #00796B;">₹{{ number_format($donation->amount, 2) }}</td>
            </tr>
        </table>

        @if(isset($qrCode) && $qrCode)
        <div class="qr-section">
            <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 80px;">
            <div style="font-size: 10px;">Scan to Verify Receipt</div>
        </div>
        @endif

        <div class="footer-note">
            <p>Drishika Foundation - Empowering Women & Educating Children</p>
            <p>This is a computer-generated receipt and does not require a physical signature. Tax exemption benefits may apply under 80G as per government regulations.</p>
        </div>
    </div>
</body>
</html>
