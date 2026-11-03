<?php

namespace App\Reserva\Requests;

use App\Reserva\Enums\EstadoReserva;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEstadoReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'estado' => ['required', Rule::enum(EstadoReserva::class)],
        ];
    }

    public function estado(): EstadoReserva
    {
        return EstadoReserva::from($this->validated('estado'));
    }
}
