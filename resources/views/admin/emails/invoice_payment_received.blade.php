@component('mail::message')
    Dear {{$invoice->tenant->name}}.<br>
    We have received your payment of ${{$invoice_payment->amount}} for invoice #{{$invoice->reference}}.<br>
    Invoice Amount:<b>${{number_format($invoice->amount,2)}}</b><br>
    Balance:<b>${{number_format($invoice->balance,2)}}</b><br>
    @component('mail::button', ['url' => 'http://'.$invoice->tenant->domains->first()->domain.'.'.config('app.central_domain').'/invoice/'.$invoice->id.'/show'])
        View Invoice
    @endcomponent
    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
