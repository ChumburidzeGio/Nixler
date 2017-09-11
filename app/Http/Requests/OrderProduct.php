<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderProduct extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this->input('step')) {
            return ($this->input('step') == 3) ? $this->stepThreeRules() : $this->stepTwoRules();
        }

        return [];
    }

    /**
     * @return array
     */
    public function stepTwoRules()
    {
        return [
            'phone' => ["phone:{$this->user()->country}", "phone_unique:{$this->user()->country}"],
            'city_id' => 'required|numeric',
            'address' => 'required|string',
            'payment_method' => 'required|in:cod,crd',
            'quantity' => 'required|numeric',
            'variant' => 'nullable|numeric', //TODO: check variant in database
        ];
    }

    /**
     * @return array
     */
    public function stepThreeRules()
    {
        return [
            'pcode' => 'required|numeric|digits:6'
        ];
    }

}
