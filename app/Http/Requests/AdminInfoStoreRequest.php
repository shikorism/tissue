<?php

namespace App\Http\Requests;

use App\Information;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminInfoStoreRequest extends FormRequest
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
            'category' => ['required', Rule::in(array_keys(Information::CATEGORIES))],
            'pinned' => 'nullable|boolean',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:10000'
        ];
    }
}
