<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkOrderCompletionRequest extends FormRequest
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
            'status' => ['required', Rule::in(['completed', 'completed_with_issues', 'failed'])],
            'actual_hours' => ['required', 'numeric', 'min:0.1', 'max:1000'],
            'notes' => ['required', 'string', 'min:10', 'max:2000'],
            'parts_used' => ['nullable', 'array'],
            'parts_used.*.item_id' => ['required_with:parts_used', 'exists:items,id'],
            'parts_used.*.quantity' => ['required_with:parts_used.*.item_id', 'integer', 'min:1'],
            'parts_used.*.unit_cost' => ['required_with:parts_used.*.item_id', 'numeric', 'min:0'],
            'signature_path' => ['nullable', 'string', 'max:500'],
            'attachments' => ['nullable', 'array'],
            'downtime_hours' => ['nullable', 'integer', 'min:0', 'max:720'], // Max 30 days
            'cost_savings' => ['nullable', 'numeric', 'min:0'],
            'customer_satisfaction' => ['nullable', 'boolean'],
            'customer_feedback' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'actual_hours.required' => 'Actual hours are required.',
            'actual_hours.min' => 'Actual hours must be at least 0.1.',
            'actual_hours.max' => 'Actual hours cannot exceed 1000.',
            'notes.required' => 'Completion notes are required.',
            'notes.min' => 'Completion notes must be at least 10 characters.',
            'parts_used.*.item_id.exists' => 'The selected item does not exist.',
            'parts_used.*.quantity.min' => 'Quantity must be at least 1.',
            'downtime_hours.max' => 'Downtime cannot exceed 720 hours (30 days).',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Ensure parts_used is properly formatted
        if ($this->has('parts_used') && is_string($this->parts_used)) {
            $this->merge([
                'parts_used' => json_decode($this->parts_used, true),
            ]);
        }

        // Set default status if not provided
        if (!$this->has('status')) {
            $this->merge([
                'status' => 'completed',
            ]);
        }
    }
}