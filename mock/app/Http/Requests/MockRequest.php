<?php

namespace App\Http\Requests;

use App\Traits\Response;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * Class MockRequest
 * @package App\Http\Requests
 */
class MockRequest extends FormRequest
{
    use Response;

    /**
     * Indicates if the validator should stop on the first rule failure.
     *
     * @var bool
     */
    protected $stopOnFirstFailure = true;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'receipt' => 'required|string'
        ];
    }

    /**
     * @param Validator $validator
     * @return JsonResponse
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): JsonResponse
    {
        throw new ValidationException($validator, $validator->errors()->first());
    }
}
