<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PageSpeedRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true; // Or your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'url' => 'required|url',
            'categories' => 'required|array|min:1',
            'strategy' => 'required|string|in:DESKTOP,MOBILE',
        ];
    }

    /**
     *
     * @return array
     */
    public function messages()
    {
        return [
            'url.required' => 'The URL is required.',
            'url.url' => 'The URL must be valid.',
            'categories.required' => 'At least one category is required.',
            'strategy.required' => 'The strategy is required.',
            'strategy.in' => 'The strategy must be DESKTOP or MOBILE.',
        ];
    }
}
