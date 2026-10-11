<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateCanchaRequest extends StoreCanchaRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $canchaId = $this->route('cancha')?->id;

        $rules['nombre'] = ['required', 'string', 'max:255', Rule::unique('canchas', 'nombre')->ignore($canchaId)];

        return $rules;
    }
}