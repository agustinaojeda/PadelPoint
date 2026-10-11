<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cancha_id' => ['required', 'exists:canchas,id'],
            'fecha_turno' => ['required', 'date'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'nombre_cliente' => ['required', 'string', 'max:255'],
            'telefono_cliente' => ['required', 'string', 'max:50'],
            'monto_total' => ['required', 'numeric', 'min:0'],
            'observaciones' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'cancha_id.required' => 'Debes seleccionar una cancha.',
            'fecha_turno.required' => 'La fecha del turno es obligatoria.',
            'hora_inicio.required' => 'Ingresa la hora de inicio.',
            'hora_fin.required' => 'Ingresa la hora de fin.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'nombre_cliente.required' => 'Ingresa el nombre del cliente.',
            'telefono_cliente.required' => 'Ingresa un teléfono de contacto.',
            'monto_total.required' => 'El monto total es obligatorio.',
        ];
    }
}