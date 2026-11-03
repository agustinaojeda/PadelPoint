<?php

namespace App\Reserva\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservaRequest extends FormRequest
{
    use ReglasReserva;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return array_merge($this->reglasBase(), [
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'monto_total' => ['required', 'numeric', 'min:0'],
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return array_merge($this->mensajesBase(), [
            'hora_fin.required' => 'Ingresá la hora de fin.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
            'monto_total.required' => 'El monto total es obligatorio.',
        ]);
    }
}
