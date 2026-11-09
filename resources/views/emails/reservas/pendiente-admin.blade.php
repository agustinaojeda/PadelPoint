<x-mail::message>
# Nueva reserva pendiente

Entró una nueva solicitud de reserva desde la web. Todavía está pendiente de confirmación.

- **Cancha:** {{ $reserva->cancha->nombre }}
- **Fecha:** {{ ucfirst($reserva->fecha_turno->translatedFormat('l j \d\e F')) }}
- **Horario:** {{ substr($reserva->hora_inicio, 0, 5) }} a {{ substr($reserva->hora_fin, 0, 5) }} hs
- **Cliente:** {{ $reserva->nombre_cliente }}
@if (filled($reserva->telefono_cliente))
- **Teléfono:** {{ $reserva->telefono_cliente }}
@endif
- **Email:** {{ $reserva->email_cliente }}
- **Total:** ${{ number_format($reserva->monto_total, 0, ',', '.') }}
@if (filled($reserva->observaciones))
- **Observaciones:** {{ $reserva->observaciones }}
@endif

<x-mail::button :url="$url">
Ver reserva en la grilla
</x-mail::button>

Si ya la gestionaste, podés ignorar este mensaje.

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
