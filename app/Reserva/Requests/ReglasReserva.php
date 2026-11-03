<?php

namespace App\Reserva\Requests;

trait ReglasReserva
{
    /**
     * Reglas comunes a las reservas públicas y administrativas.
     *
     * @return array<string, array<int, string>>
     */
    protected function reglasBase(): array
    {
        return [
            'cancha_id' => ['required', 'integer', 'exists:canchas,id'],
            'fecha_turno' => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'nombre_cliente' => ['required', 'string', 'max:255'],
            'email_cliente' => ['nullable', 'email', 'max:255'],
            'telefono_cliente' => ['nullable', 'string', 'max:50'],
            'observaciones' => ['nullable', 'string'],
        ];
    }

    /**
     * Mensajes comunes a las reservas públicas y administrativas.
     *
     * @return array<string, string>
     */
    protected function mensajesBase(): array
    {
        return [
            'cancha_id.required' => 'Seleccioná una cancha.',
            'cancha_id.exists' => 'La cancha seleccionada no existe.',
            'fecha_turno.required' => 'Seleccioná la fecha del turno.',
            'hora_inicio.required' => 'Seleccioná un horario.',
            'hora_inicio.date_format' => 'El horario seleccionado no es válido.',
            'nombre_cliente.required' => 'Ingresá el nombre del cliente.',
            'email_cliente.email' => 'Ingresá un correo electrónico válido.',
        ];
    }
}
