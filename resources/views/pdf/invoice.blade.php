<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.5; }
        .header { border-bottom: 4px solid {{ $invoice->company->primary_color ?? '#111827' }}; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { height: 60px; margin-bottom: 10px; }
        .company-name { font-size: 24px; font-weight: bold; text-transform: uppercase; }
        .invoice-title { font-size: 36px; font-weight: bold; color: #eee; text-align: right; margin-top: -60px; }
        .details { margin-bottom: 40px; }
        .details td { vertical-align: top; width: 50%; }
        .label { font-size: 10px; font-weight: bold; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px; }
        .client-name { font-size: 16px; font-weight: bold; }
        .due-date { font-size: 18px; font-weight: bold; color: {{ $invoice->company->primary_color ?? '#4f46e5' }}; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        table.items th { border-bottom: 2px solid {{ $invoice->company->primary_color ?? '#111827' }}; text-align: left; padding: 10px 0; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        table.items td { padding: 15px 0; border-bottom: 1px solid #eee; font-size: 14px; }
        .summary { float: right; width: 250px; }
        .summary-row { padding: 5px 0; font-size: 14px; }
        .summary-label { color: #999; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        .total-row { border-top: 1px solid #eee; margin-top: 10px; padding-top: 10px; }
        .total-label { font-size: 16px; font-weight: bold; text-transform: uppercase; }
        .total-amount { font-size: 20px; font-weight: bold; color: {{ $invoice->company->primary_color ?? '#4f46e5' }}; }
        .footer { position: absolute; bottom: 0; width: 100%; text-align: center; color: #999; font-size: 10px; text-transform: uppercase; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ $invoice->company->name }}</div>
        <div style="font-size: 12px; color: #666;">
            {{ $invoice->company->address_line_1 }}<br>
            {{ $invoice->company->address_line_2 }}<br>
            @if($invoice->company->tax_id) <strong>TIN: {{ $invoice->company->tax_id }}</strong> @endif
        </div>
        <div class="invoice-title">INVOICE</div>
    </div>

    <table class="details">
        <tr>
            <td>
                <div class="label">Bill To</div>
                <div class="client-name">{{ $invoice->client->name }}</div>
                <div style="font-size: 13px; color: #666;">
                    {{ $invoice->client->contact_person }}<br>
                    {{ $invoice->client->email }}<br>
                    {{ $invoice->client->phone }}
                </div>
            </td>
            <td style="text-align: right;">
                <div class="label">Invoice Number</div>
                <div style="font-weight: bold;"># {{ $invoice->invoice_number }}</div>
                <div class="label" style="margin-top: 15px;">Due Date</div>
                <div class="due-date">{{ $invoice->due_date->format('Y-m-d') }}</div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Amount (PGK)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>
                    <div style="font-weight: bold;">{{ $item->description }}</div>
                    @if($item->wip) <div style="font-size: 10px; color: #999;">Completed Project Item</div> @endif
                </td>
                <td style="text-align: right; font-weight: bold;">
                    K{{ number_format($item->amount, 2) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row">
            <span class="summary-label">Subtotal</span>
            <span style="float: right;">K{{ number_format($invoice->total_amount - $invoice->tax_amount, 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">GST (10%)</span>
            <span style="float: right;">K{{ number_format($invoice->tax_amount, 2) }}</span>
        </div>
        <div class="summary-row total-row">
            <span class="total-label">Total Due</span>
            <span class="total-amount" style="float: right;">K{{ number_format($invoice->total_amount, 2) }}</span>
        </div>
    </div>

    <div class="footer">
        Thank you for your business. Payment is due within 14 days of issue.<br>
        {{ $invoice->company->name }} — Professional Services Platform
    </div>
</body>
</html>
