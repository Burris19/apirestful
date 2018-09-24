Hola {{$user->name}}

Has cambiado tu correo electronico

Verifica tu nuevo correo en el siguiente enlace:

{{route('verify', $user->verification_token)}}