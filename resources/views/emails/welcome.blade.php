Hola {{$user->email}}

Gracias por registrarte en nuestra plataforma.

Verifica tu cuenta usando el siguiente enlace:

{{route('verify', $user->verification_token)}}