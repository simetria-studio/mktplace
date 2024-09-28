@component('mail::message')
Nome do Contato: {{$name}}
Email do Contato: {{$email}}
Telefone do Contato: {{$phone}}
Assunto do Contato: {{$assunto}}

Mensagem:
    {{$msg}}
@endcomponent
