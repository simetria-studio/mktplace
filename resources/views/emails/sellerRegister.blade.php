@component('mail::message')
# Novo vendedor Registrado

Nome: {{$vendedor->name}}

Email: {{$vendedor->email}}

Telefone/Celular: {{$vendedor->phone}}

@endcomponent