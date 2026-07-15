<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Donation Receipt {{ $donation->receipt_number }}</title>
    <style>
        body { font-family: sans-serif; color: #1f2937; font-size: 13px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4f46e5; padding-bottom: 16px; }
        .header h1 { color: #4f46e5; margin: 0 0 4px; font-size: 20px; }
        .header p { margin: 0; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td { padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        td.label { color: #6b7280; width: 40%; }
        td.value { font-weight: bold; }
        .amount { font-size: 22px; color: #059669; text-align: center; margin: 24px 0; }
        .footer { margin-top: 40px; text-align: center; color: #9ca3af; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>MBSTU Alumni Portal</h1>
        <p>Official Donation Receipt</p>
    </div>

    <p style="text-align: center;">Receipt No. <strong>{{ $donation->receipt_number }}</strong></p>

    <div class="amount">৳{{ number_format((float) $donation->amount, 2) }}</div>

    <table>
        <tr>
            <td class="label">Donor</td>
            <td class="value">{{ $donation->user?->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Campaign</td>
            <td class="value">{{ $donation->campaign->title }}</td>
        </tr>
        <tr>
            <td class="label">Payment Method</td>
            <td class="value">{{ $donation->payment_method->label() }}</td>
        </tr>
        @if ($donation->transaction_reference)
            <tr>
                <td class="label">Transaction Reference</td>
                <td class="value">{{ $donation->transaction_reference }}</td>
            </tr>
        @endif
        <tr>
            <td class="label">Date</td>
            <td class="value">{{ $donation->donated_at->format('F j, Y g:i A') }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>This is a system-generated receipt from the MBSTU Alumni Portal. Thank you for your generosity.</p>
    </div>
</body>
</html>
