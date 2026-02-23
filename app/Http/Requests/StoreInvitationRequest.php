<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvitationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', \App\Models\Invitation::class);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $rules = [
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'company_id' => ['required', 'exists:companies,id'],
            'role' => ['required', 'string', Rule::in([User::ROLE_ADMIN, User::ROLE_MEMBER])],
        ];

        if ($user->isSuperAdmin()) {
            $rules['role'] = ['required', 'string', Rule::in([User::ROLE_ADMIN])];
        } else {
            $rules['company_id'] = ['required', 'exists:companies,id', Rule::in([$user->company_id])];
        }

        return $rules;
    }
}
