<?php

namespace App\Reserva\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Validator;

class StoreReservaPublicaRequest extends FormRequest
{
    use ReglasReserva;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rechaza turnos del día cuya hora de inicio ya pasó. La vista pública
     * oculta esos horarios, pero el servidor debe validarlo igual.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $fecha = $this->input('fecha_turno');
                $hora = $this->input('hora_inicio');

                if (! is_string($fecha) || ! is_string($hora)) {
                    return;
                }

                $ahora = Carbon::now();

                if ($fecha !== $ahora->toDateString()) {
                    return;
                }

                [$horas, $minutos] = array_map('intval', explode(':', $hora));
                $inicioMinutos = ($horas * 60) + $minutos;
                $ahoraMinutos = ($ahora->hour * 60) + $ahora->minute;

                if ($inicioMinutos <= $ahoraMinutos) {
                    $validator->errors()->add('hora_inicio', 'Ese horario ya pasó. Elegí un turno posterior.');
                }
            },
        ];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return array_merge($this->reglasBase(), [
            'fecha_turno' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'nombre_cliente' => ['required', 'string', 'min:3', 'max:255'],
            'email_cliente' => ['required', 'email', 'max:255'],
            'telefono_cliente' => ['nullable', 'string', 'min:6', 'max:50'],
            'observaciones' => ['nullable', 'string', 'max:500'],
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge($this->mensajesBase(), [
            'fecha_turno.date_format' => 'La fecha del turno no es válida.',
            'fecha_turno.after_or_equal' => 'No podés reservar una fecha pasada.',
            'nombre_cliente.min' => 'Ingresá tu nombre completo.',
            'email_cliente.required' => 'Ingresá tu correo electrónico para confirmar la reserva.',
            'telefono_cliente.min' => 'Ingresá un teléfono de contacto válido.',
        ]);
    }
}
