<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Entities\Product;

class UpdateProduct extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $product = Product::find($this->route('id'));

        return $product && $this->user()->can('update', $product);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required|string|max:180',
            'description' => 'string|nullable',
            'variants' => 'json',
            'action' => 'required|in:schedule,publish',
            'media' => 'json',
            'tags' => 'json',
            'variants' => 'json',
            'category' => 'required|string',
            'in_stock' => 'required|numeric',
            'buy_link' => 'nullable|url',
        ];
    }
}
