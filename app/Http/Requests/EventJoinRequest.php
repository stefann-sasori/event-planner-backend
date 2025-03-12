<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventJoinRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'event_id' => ['required', 'exists:events,id'],
        ];
    }
}
