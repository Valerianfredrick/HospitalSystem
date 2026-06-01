<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $itemId = $this->route('stockItem')?->id;

        return [
            'name'             => ['required', 'string', 'max:255'],
            'generic_name'     => ['nullable', 'string', 'max:255'],
            'barcode'          => ['nullable', 'string', 'max:100', 'unique:stock_items,barcode,' . $itemId],
            'sku'              => ['nullable', 'string', 'max:100', 'unique:stock_items,sku,'     . $itemId],
            'category'         => ['required', 'string', 'max:100'],
            'unit'             => ['required', 'string', 'max:50'],
            'quantity'         => ['required', 'integer', 'min:0'],
            'reorder_level'    => ['required', 'integer', 'min:0'],
            'unit_price'       => ['nullable', 'numeric', 'min:0'],
            'supplier_name'    => ['nullable', 'string', 'max:255'],
            'supplier_contact' => ['nullable', 'string', 'max:255'],
            'expiry_date'      => ['nullable', 'date', 'after:today'],
            'manufacture_date' => ['nullable', 'date', 'before_or_equal:today'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'is_active'        => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                    => 'Medicine name is required.',
            'category.required'                => 'Please select a category.',
            'unit.required'                    => 'Please select a unit.',
            'quantity.required'                => 'Opening quantity is required.',
            'quantity.min'                     => 'Quantity cannot be negative.',
            'reorder_level.required'           => 'Reorder level is required.',
            'barcode.unique'                   => 'This barcode is already registered.',
            'sku.unique'                       => 'This SKU is already registered.',
            'expiry_date.after'                => 'Expiry date must be in the future.',
            'manufacture_date.before_or_equal' => 'Manufacture date cannot be in the future.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'barcode'   => $this->barcode ?: null,
            'sku'       => $this->sku     ?: null,
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
