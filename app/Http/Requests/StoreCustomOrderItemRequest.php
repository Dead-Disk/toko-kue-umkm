<?php

namespace App\Http\Requests;

use App\Services\CustomOrderChatService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomOrderItemRequest extends FormRequest
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
            'cake_category_id' => ['nullable', 'integer', Rule::exists('categories', 'id')->whereNull('deleted_at')],
            'cake_option_id' => ['required', 'integer', 'exists:cake_options,id'],
            'base_product_id' => ['nullable', 'integer', Rule::exists('products', 'id')->whereNull('deleted_at')],
            'jumlah' => ['required', 'integer', 'min:1', 'max:'.CustomOrderChatService::MAX_CUSTOM_QUANTITY],
            'deskripsi_custom' => ['required', 'string', 'max:2000'],
            'foto_referensi' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'cake_option_id.required' => 'Ukuran kue custom wajib dipilih.',
            'jumlah.max' => 'Maksimal pemesanan per item custom adalah '.CustomOrderChatService::MAX_CUSTOM_QUANTITY.' pcs.',
            'deskripsi_custom.required' => 'Deskripsi permintaan kue custom wajib diisi.',
        ];
    }
}
