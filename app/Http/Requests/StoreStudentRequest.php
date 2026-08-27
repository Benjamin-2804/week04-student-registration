<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'string', 'unique:students,student_id'],
            'first_name' => ['required', 'string', 'alpha', 'max:100'],
            'middle_name' => ['nullable', 'string', 'alpha', 'max:100'],
            'last_name' => ['required', 'string', 'alpha', 'max:100'],
            'email' => ['required', 'email', 'unique:students,email'],
            'mobile_number' => ['required', 'digits_between:10,15'],
            'date_of_birth' => ['required', 'date', 'before:today', 'before_or_equal:'.now()->subYears(15)->toDateString()],
            'gender' => ['required', 'in:male,female,other'],
            'program' => ['required', 'string', 'max:100'],
            'year_level' => ['required', 'integer', 'between:1,4'],
            'address' => ['required', 'string', 'min:10', 'max:500'],
            'profile_picture' => ['required', 'image', 'mimetypes:image/jpeg,image/jpg,image/png', 'max:2048'],
        ];
    }
}
