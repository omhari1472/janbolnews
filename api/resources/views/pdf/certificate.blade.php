<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
        }
        .certificate-wrapper {
            width: 100%;
            height: 100%;
            padding: 40px;
        }
        .outer-border {
            width: 100%;
            height: 100%;
            border: 4px solid #E8711C;
            padding: 8px;
        }
        .inner-border {
            width: 100%;
            height: 100%;
            border: 2px solid #D4AF37;
            position: relative;
        }
        .content-table {
            width: 100%;
            height: 100%;
            text-align: center;
        }
        .logo {
            width: 90px;
            height: auto;
        }
        .org-name {
            font-size: 13px;
            color: #E8711C;
            letter-spacing: 4px;
            text-transform: uppercase;
            padding-top: 10px;
        }
        .certificate-title {
            font-size: 48px;
            color: #E8711C;
            font-weight: bold;
            letter-spacing: 10px;
            text-transform: uppercase;
            padding: 10px 0 5px 0;
        }
        .certificate-type {
            font-size: 20px;
            color: #D4AF37;
            letter-spacing: 5px;
            text-transform: uppercase;
            font-weight: bold;
            padding-bottom: 15px;
        }
        .decorative-line {
            width: 200px;
            height: 3px;
            background-color: #D4AF37;
            margin: 0 auto;
        }
        .certify-text {
            font-size: 15px;
            color: #555;
            padding: 20px 0 8px 0;
        }
        .recipient-name {
            font-size: 36px;
            font-weight: bold;
            color: #E8711C;
            padding: 5px 0 8px 0;
        }
        .name-underline {
            width: 350px;
            height: 2px;
            background-color: #D4AF37;
            margin: 0 auto;
        }
        .purpose-text {
            font-size: 14px;
            color: #444;
            line-height: 1.7;
            padding: 20px 80px;
        }
        .footer-table {
            width: 80%;
            margin: 20px auto 0 auto;
        }
        .footer-table td {
            width: 50%;
            text-align: center;
            padding: 10px;
            vertical-align: bottom;
        }
        .signature-line {
            width: 150px;
            border-top: 1px solid #333;
            margin: 0 auto 8px auto;
        }
        .footer-label {
            font-size: 11px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .footer-value {
            font-size: 13px;
            color: #333;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .cert-number {
            position: absolute;
            bottom: 15px;
            left: 25px;
            font-size: 10px;
            color: #888;
        }
        .qr-section {
            position: absolute;
            bottom: 10px;
            right: 20px;
            text-align: center;
        }
        .qr-section img {
            width: 70px;
            height: 70px;
        }
        .qr-label {
            font-size: 8px;
            color: #888;
            margin-top: 3px;
        }
    </style>
</head>
<body>
    <div class="certificate-wrapper">
        <div class="outer-border">
            <div class="inner-border">
                <table class="content-table" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="height: 30px;"></td>
                    </tr>
                    <tr>
                        <td>
                            @if(isset($logo) && $logo)
                                <img src="data:image/png;base64,{{ $logo }}" class="logo" alt="Logo">
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="org-name">Drishika Foundation</td>
                    </tr>
                    <tr>
                        <td class="certificate-title">Certificate</td>
                    </tr>
                    <tr>
                        <td class="certificate-type">{{ strtoupper($certificate->certificate_type) }}</td>
                    </tr>
                    <tr>
                        <td><div class="decorative-line"></div></td>
                    </tr>
                    <tr>
                        <td class="certify-text">This is to certify that</td>
                    </tr>
                    <tr>
                        <td class="recipient-name">{{ $certificate->issued_to_name }}</td>
                    </tr>
                    <tr>
                        <td><div class="name-underline"></div></td>
                    </tr>
                    <tr>
                        <td class="purpose-text">
                            {{ $certificate->purpose ?? "has been recognized for their valuable contribution and dedicated support to the Drishika Foundation's mission of empowering women and educating children in underserved communities." }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table class="footer-table" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <div class="footer-value">{{ \Carbon\Carbon::parse($certificate->issued_at)->format('d M, Y') }}</div>
                                        <div class="footer-label">Date of Issue</div>
                                    </td>
                                    <td>
                                        <div class="signature-line"></div>
                                        <div class="footer-label">Authorized Signature</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="height: 20px;"></td>
                    </tr>
                </table>

                <div class="cert-number">Certificate No: {{ $certificate->certificate_number }}</div>

                @if(isset($qrCode) && $qrCode)
                <div class="qr-section">
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
                    <div class="qr-label">Scan to Verify</div>
                </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
