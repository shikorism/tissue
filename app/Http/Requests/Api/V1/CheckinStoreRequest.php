<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CheckinStoreRequest extends FormRequest
{
    public function authorize()
    {
        $ejaculation = $this->route('checkin');
        if ($ejaculation === null) {
            return true;
        } else {
            return $this->user()->can('edit', $ejaculation);
        }
    }

    public function rules()
    {
        return [
            'checked_in_at' => 'nullable|date|after_or_equal:2000-01-01 00:00:00|before_or_equal:2099-12-31 23:59:59',
            'note' => 'nullable|string|max:500',
            'link' => 'nullable|url|max:2000',
            'tags' => 'nullable|array|max:40',
            'tags.*' => ['string', 'not_regex:/[\s\r\n]/u', 'max:255'],
            'is_private' => 'nullable|boolean',
            'is_too_sensitive' => 'nullable|boolean',
            'discard_elapsed_time' => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'tags.*.not_regex' => 'The :attribute cannot contain spaces, tabs and newlines.',
        ];
    }
}
