@component('mail::message')
    Dear Admin.<br>
    You have received a new payment of ${{$invoice_payment->amount}} for invoice #{{$invoice->reference}}.<br>
    Invoice Amount:<b>${{number_format($invoice->amount,2)}}</b><br>
    Balance:<b>${{number_format($invoice->balance,2)}}</b><br>
    @component('mail::button', ['url' => 'https://'.config('app.central_domain').'/admin/invoice/'.$invoice->id.'/show'])
        View Invoice
    @endcomponent
    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
