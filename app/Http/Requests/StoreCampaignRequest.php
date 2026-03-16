<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'contact_list_id' => 'required|exists:contact_lists,id',
            'status' => 'sometimes|string|in:pending,scheduled,sent,failed', // opcional, default é 'pending'
            'scheduled_at' => 'nullable|date|after_or_equal:now',
        ];
    }
}
