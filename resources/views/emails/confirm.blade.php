@component('mail::message')
    #Hola $user->name

    Has cambiado tu correo electronico.
    Verifica tu cuenta usando el siguiente boton.
    @component('mail::button', ['url' => route('verify', $user->verification_token)])
        Confirmar mi cuenta
    @endcomponent

    Gracias,<br>
    {{ config('app.name') }}
@endcomponent