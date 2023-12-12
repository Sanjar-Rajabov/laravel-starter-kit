<?php

namespace App\Traits;

trait ValidateId
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|int'
        ];
    }

    public function prepareForValidation(): void
    {
        $this->mergeIfMissing([
            'id' => $this->route('id')
        ]);
    }
}
