<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCanchaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $canchaId = $this->route('cancha') ? $this->route('cancha')->id : null;

        return [
            'nombre' => 'required|string|max:255|unique:canchas,nombre,' . $canchaId,
            'superficie' => 'nullable|string|max:100',
            'es_techada' => 'boolean',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'duracion_turno' => 'required|integer|min:30',
            'cantidad_jugadores' => 'required|integer|min:2',
            'hora_apertura' => 'required|date_format:H:i',
            'hora_cierre' => 'required|date_format:H:i|after:hora_apertura',
            'dias_disponibles' => 'required|array|min:1',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'Ingresa un nombre para la cancha.',
            'nombre.unique' => 'Ya existe otra cancha con este nombre.',
            'precio.required' => 'Debes especificar el precio por turno.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.min' => 'El precio no puede ser negativo.',
            'duracion_turno.required' => 'Indica la duración del turno.',
            'duracion_turno.min' => 'La duración del turno debe ser de al menos :min minutos.',
            'cantidad_jugadores.required' => 'Ingresa la cantidad de jugadores.',
            'cantidad_jugadores.min' => 'La cantidad mínima de jugadores es :min.',
            'hora_apertura.required' => 'Ingresa el horario de apertura.',
            'hora_cierre.required' => 'Ingresa el horario de cierre.',
            'hora_cierre.after' => 'La hora de cierre debe ser posterior a la de apertura.',
            'dias_disponibles.required' => 'Selecciona al menos un día disponible.',
            'imagen.image' => 'El archivo seleccionado debe ser una imagen válida.',
            'imagen.mimes' => 'La imagen debe ser de tipo JPG, PNG o WEBP.',
            'imagen.max' => 'La imagen supera el peso máximo de 2 MB.',
        ];
    }
}