<x-mail::message>
# ¡Tu turno está confirmado!

Hola {{ $reserva->nombre_cliente }}, reservamos tu turno en **{{ $reserva->cancha->nombre }}**.

- **Fecha:** {{ $reserva->fecha_turno->translatedFormat('l j \d\e F') }}
- **Horario:** {{ substr($reserva->hora_inicio, 0, 5) }} a {{ substr($reserva->hora_fin, 0, 5) }} hs
- **Total:** ${{ number_format($reserva->monto_total, 0, ',', '.') }}

Te esperamos. Si necesitás reprogramar, comunicate con el club.

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
