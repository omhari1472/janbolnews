<!DOCTYPE html>
<html>
<head>
    <title>Donation Receipt</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; padding: 30px; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 2px solid #0656A4; padding-bottom: 20px; }
        .company-name { font-size: 28px; font-weight: bold; color: #0656A4; }
        .receipt-title { font-size: 20px; margin-top: 10px; font-weight: bold; }
        .details { margin-bottom: 30px; }
        .details table { width: 100%; border-collapse: collapse; }
        .details td { padding: 8px; font-size: 16px; }
        .label { font-weight: bold; width: 150px; }
        .amount-box { border: 2px dashed #39B056; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; color: #39B056; width: 200px; margin: 20px auto; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body>
        <div class="header">
            <div class="company-name">Drishika Foundation</div>
            <div class="receipt-title">Donation Receipt</div>        <div>Receipt No: {{ $donation->receipt_number }}</div>
        <div>Date: {{ $donation->created_at->format('d M, Y') }}</div>
    </div>

    <div class="details">
        <table>
            <tr>
                <td class="label">Received From:</td>
                <td>{{ $donation->donor_name }}</td>
            </tr>
            <tr>
                <td class="label">Phone:</td>
                <td>{{ $donation->phone }}</td>
            </tr>
            @if($donation->address)
            <tr>
                <td class="label">Address:</td>
                <td>{{ $donation->address }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Payment Mode:</td>
                <td>{{ ucfirst($donation->donation_type) }} {{ $donation->payment_reference ? '('.$donation->payment_reference.')' : '' }}</td>
            </tr>
        </table>
    </div>

    <div class="amount-box">
        ₹{{ number_format($donation->amount, 2) }}
    </div>

    <div class="footer">
        <p>Thank you for your generous contribution. Your support helps us make a difference.</p>
        <p>This is a computer-generated receipt and does not require a signature.</p>
    </div>
</body>
</html>
