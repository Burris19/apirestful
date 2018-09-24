@component('mail::message')
    #Hola $user->name

    Gracias por registrarte en nuestra plataforma.
    Verifica tu cuenta usando el siguiente boton.
    @component('mail::button', ['url' => route('verify', $user->verification_token)])
        Confirmar mi cuenta
    @endcomponent

    Gracias,<br>
    {{ config('app.name') }}
@endcomponent