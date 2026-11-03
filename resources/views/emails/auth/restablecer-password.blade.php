<x-mail::message>
# Restablecé tu contraseña

Recibimos una solicitud para restablecer la contraseña de tu cuenta de administrador en {{ config('app.name') }}.

<x-mail::button :url="$url" color="primary">
Restablecer contraseña
</x-mail::button>

Este link expira en {{ $expira }} minutos. Si no solicitaste el cambio, ignorá este correo.

Si el botón no funciona, copiá y pegá este link en tu navegador:

{{ $url }}
</x-mail::message>
