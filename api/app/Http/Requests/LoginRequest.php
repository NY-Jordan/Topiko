<?php

namespace App\Http\Requests;

use App\Enums\ProviderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "oauth_id" => "required",
            "provider" => ['required', Rule::enum(ProviderEnum::class)],
            "username" => "required",
            "email" => "required|email"
        ];
    }
}