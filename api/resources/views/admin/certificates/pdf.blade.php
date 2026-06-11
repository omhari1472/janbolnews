<!DOCTYPE html>
<html>
<head>
    <title>Certificate</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; text-align: center; border: 10px solid #0656A4; padding: 50px; }
        .header { font-size: 40px; font-weight: bold; color: #0656A4; margin-bottom: 20px; }
        .sub-header { font-size: 24px; margin-bottom: 40px; }
        .recipient { font-size: 32px; font-weight: bold; color: #39B056; margin: 20px 0; text-decoration: underline; }
        .content { font-size: 18px; line-height: 1.6; margin-bottom: 50px; }
        .footer { display: flex; justify-content: space-between; margin-top: 100px; }
        .signature { border-top: 1px solid #000; width: 200px; margin: 0 auto; padding-top: 10px; }
        .qr-code { margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">Drishika Foundation</div>
    <div class="sub-header">Certificate of {{ ucfirst($certificate->certificate_type) }}</div>

    <div class="content">
        This is to certify that
        <div class="recipient">{{ $certificate->issued_to_name }}</div>
        has been awarded this certificate for<br>
        <strong>{{ $certificate->purpose ?? 'their valuable contribution and support.' }}</strong>
    </div>

    <div class="footer">
        <div class="signature">Authorized Signature</div>
    </div>

    <div class="qr-code">
        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
        <br>
        <small>Verify at: {{ $verificationUrl }}</small>
    </div>
</body>
</html>
