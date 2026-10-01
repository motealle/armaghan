<?php

namespace App\Http\Requests;

use App\Services\StyleProfileCompiler;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveStyleProfileDraftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $tokens = array_keys(StyleProfileCompiler::TOKENS);

        return [
            'name' => ['sometimes', 'string', 'max:120'],
            'source_test' => ['sometimes', 'nullable', 'regex:/^\d{1,3}$/'],
            'expected_checksum' => ['sometimes', 'nullable', 'regex:/^[a-f0-9]{64}$/'],
            'styles' => ['required', 'array', 'max:250'],
            'styles.*' => ['array:textColor,backgroundColor,borderColor,hidden'],
            'styles.*.textColor' => ['sometimes', 'nullable', Rule::in($tokens)],
            'styles.*.backgroundColor' => ['sometimes', 'nullable', Rule::in($tokens)],
            'styles.*.borderColor' => ['sometimes', 'nullable', Rule::in($tokens)],
            'styles.*.hidden' => ['sometimes', 'boolean'],
            'texts' => ['required', 'array', 'max:250'],
            'texts.*' => ['array:fa,ar,en,ku'],
            'texts.*.fa' => ['sometimes', 'nullable', 'string', 'max:4000'],
            'texts.*.ar' => ['sometimes', 'nullable', 'string', 'max:4000'],
            'texts.*.en' => ['sometimes', 'nullable', 'string', 'max:4000'],
            'texts.*.ku' => ['sometimes', 'nullable', 'string', 'max:4000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $allowedTopLevel = [
                '_token',
                'name',
                'source_test',
                'expected_checksum',
                'styles',
                'texts',
            ];

            foreach (array_diff(array_keys($this->all()), $allowedTopLevel) as $key) {
                $validator->errors()->add((string) $key, 'Unexpected style profile field.');
            }

            foreach (['styles', 'texts'] as $group) {
                $items = $this->input($group, []);

                if (! is_array($items)) {
                    continue;
                }

                foreach (array_keys($items) as $id) {
                    if (! is_string($id) || ! preg_match('/^[A-Za-z0-9._-]+$/', $id)) {
                        $validator->errors()->add($group, 'Style target identifiers may contain only letters, digits, dot, underscore and dash.');
                    }
                }
            }

            if (strlen($this->getContent()) > 262_144) {
                $validator->errors()->add('profile', 'Style profile payload is too large.');
            }
        });
    }
}
