@component('mail::message')
# Information Request

**Name:** {{request('first_name')}} {{request('last_name')}}<br>
**Company Name:** {{request('company')}}<br>
**Email:** {{request('email')}}<br>
**Phone:** {{request('phone')}}<br>
**Country:** {{request('country')}}<br><br>
{{request('message')}}

Thanks,<br>
{{ config('app.name') }}
@endcomponent
