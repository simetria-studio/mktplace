@component('mail::message')
{{$title_1 ?? ''}}

{{$title_2 ?? ''}}

{{$msg}}

Obrigado por usar nosso aplicativo,<br>
{{ config('app.name') }}
@endcomponent
