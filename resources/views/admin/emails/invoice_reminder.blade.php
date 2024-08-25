@component('mail::message')
    Dear {{$invoice->tenant->name}}.<br>
   This is a reminder that invoice #{{$invoice->reference}} generated on {{$invoice->bill_date}} is due on {{$invoice->due_date}}.<br>
    Invoice Amount:<b>${{number_format($invoice->amount,2)}}</b>
    @component('mail::button', ['url' => 'https://'.$invoice->tenant->domains->first()->domain.'.'.config('app.central_domain').'/invoice/'.$invoice->id.'/show'])
        View Invoice
    @endcomponent
    Thanks,<br>
    {{ config('app.name') }}
@endcomponent
