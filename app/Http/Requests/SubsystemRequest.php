<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubsystemRequest extends FormRequest
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
        $subsystemId = $this->route('subsystem')?->id;

        return [
            'building_id' => ['required', 'exists:buildings,id'],
            'parent_id' => ['nullable', 'exists:subsystems,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subsystems')->ignore($subsystemId)
            ],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('subsystems')->ignore($subsystemId)
            ],
            'type' => ['required', Rule::in(['mechanical', 'electrical', 'plumbing', 'hvac', 'security', 'fire', 'other'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'specifications' => ['nullable', 'array'],
            'installation_date' => ['nullable', 'date', 'before_or_equal:today'],
            'warranty_expiry' => ['nullable', 'date', 'after:installation_date'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'building_id.required' => 'Building is required.',
            'building_id.exists' => 'The selected building does not exist.',
            'name.required' => 'Subsystem name is required.',
            'name.unique' => 'A subsystem with this name already exists.',
            'code.required' => 'Subsystem code is required.',
            'code.unique' => 'A subsystem with this code already exists.',
            'type.required' => 'Subsystem type is required.',
            'installation_date.before_or_equal' => 'Installation date cannot be in the future.',
            'warranty_expiry.after' => 'Warranty expiry must be after installation date.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure is_active is boolean
        if ($this->has('is_active') && !is_bool($this->is_active)) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);
        }

        // Trim strings
        $this->merge([
            'name' => trim($this->name),
            'code' => strtoupper(trim($this->code)),
            'description' => trim($this->description),
        ]);
    }
}