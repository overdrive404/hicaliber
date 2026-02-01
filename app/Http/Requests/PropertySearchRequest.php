<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PropertySearchRequest extends FormRequest
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'storeys' => ['nullable', 'integer', 'min:0'],
            'garages' => ['nullable', 'integer', 'min:0'],
            'price_min' => ['nullable', 'integer', 'min:0'],
            'price_max' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $payload = [
            'name' => $this->filled('name') ? trim((string) $this->input('name')) : null,
        ];

        foreach (['bedrooms', 'bathrooms', 'storeys', 'garages', 'price_min', 'price_max'] as $field) {
            if ($this->has($field) && $this->input($field) !== '') {
                $payload[$field] = (int) $this->input($field);
            }
        }

        $this->merge($payload);
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $min = $this->input('price_min');
            $max = $this->input('price_max');

            if ($min !== null && $max !== null && $min > $max) {
                $validator->errors()->add('price_min', 'Minimum price must be less than or equal to maximum price.');
            }
        });
    }
}
