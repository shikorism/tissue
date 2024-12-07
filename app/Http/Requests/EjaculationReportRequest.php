<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class EjaculationReportRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'violated_rule' => 'required',
            'comment' => 'required_if:violated_rule,other|string|max:1000',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->sometimes('violated_rule', 'exists:rules,id', function ($input) {
            return $input->violated_rule !== 'other';
        });
    }

    public function attributes()
    {
        return [
            'violated_rule' => '報告の理由',
            'comment' => '詳しい内容',
        ];
    }

    public function messages()
    {
        return [
            'comment.required_if' => '詳しい内容を入力してください。',
        ];
    }
}
