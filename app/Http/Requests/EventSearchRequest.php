<?php

namespace App\Http\Requests;

use App\Enum\EventStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class EventSearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'string', 'max:255'],
            'date_from' => ['sometimes', 'date'],
            'date_to' => ['sometimes', 'date', 'after_or_equal:date_from'],
        ];
    }
}
