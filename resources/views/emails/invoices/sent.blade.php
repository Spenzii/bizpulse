<x-mail::message>
# Invoice {{ $invoice->invoice_number }}

Dear {{ $client->name }},

Please find attached your invoice **{{ $invoice->invoice_number }}** from **{{ $company->name }}**.

**Summary Details:**
*   **Total Amount:** K{{ number_format($invoice->total_amount, 2) }}
*   **Due Date:** {{ $invoice->due_date->format('Y-m-d') }}

You can also view your full invoice history and current project progress at any time by logging into your **Client Portal**.

<x-mail::button :url="route('client-portal.dashboard')">
View Client Portal
</x-mail::button>

If you have any questions regarding this invoice, please don't hesitate to reach out to us.

Thanks,<br>
The {{ $company->name }} Team
</x-mail::message>
