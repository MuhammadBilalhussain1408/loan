@component('mail::message')
    Dear {{$invoice->tenant->name}}.<br>
    We have prepared an invoice #{{$invoice->reference}} for you.<br>
    Invoice Amount:<b>${{number_format($invoice->amount,2)}}</b>
    @component('mail::button', ['url' => 'http://'.$invoice->tenant->domains->first()->domain.'.'.config('app.central_domain').'/invoice/'.$invoice->id.'/show'])
        View Invoice
    @endcomponent
    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
