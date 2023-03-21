<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'client_id' => 'required|max:255'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Введите название',
            'client_id.required' => 'Выберите клиента',
        ];
    }
}
