<?php

namespace App\Http\Requests;

use App\Enums\GearCategory;
use App\Enums\GearCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

/**
 * Validation for creating (POST) and fully updating (PUT/PATCH) a gear.
 */
class GearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', Rule::enum(GearCategory::class)],
            'brand' => ['nullable', 'string', 'max:60'],
            'daily_price' => ['required', 'integer', 'min:0', 'max:10000000'],
            'stock' => ['required', 'integer', 'min:0', 'max:1000'],
            'condition' => ['required', Rule::enum(GearCondition::class)],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama alat',
            'category' => 'kategori',
            'brand' => 'merek',
            'daily_price' => 'harga sewa per hari',
            'stock' => 'stok',
            'condition' => 'kondisi',
            'description' => 'deskripsi',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':Attribute wajib diisi.',
            'string' => ':Attribute harus berupa teks.',
            'integer' => ':Attribute harus berupa angka bulat.',
            'min' => ':Attribute minimal :min.',
            'max' => ':Attribute maksimal :max.',
            'name.max' => 'Nama alat maksimal :max karakter.',
            'brand.max' => 'Merek maksimal :max karakter.',
            'description.max' => 'Deskripsi maksimal :max karakter.',
            // Rule objects are matched by class name, not by "enum".
            'category.'.Enum::class => 'Kategori yang dipilih tidak valid.',
            'condition.'.Enum::class => 'Kondisi yang dipilih tidak valid.',
        ];
    }
}
