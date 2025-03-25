<?php
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class StudentFormRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Change this if needed
    }

    public function rules()
    {
        return [
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            // 'middle_name' => ['nullable', 'string', 'max:255'],
            // 'suffix' => ['nullable', 'string', 'max:10'],
            // 'sex' => ['required', Rule::in(['M', 'F'])],
            // 'birthdate' => ['required', 'date'],
            'address' => ['required', 'string'],
            'birthplace' => ['required', 'string'],
            // 'gwa' => ['required', 'numeric', 'between:1,5'], // General Weighted Average
            // 'nstp_number' => ['required', 'string', 'max:50'],
            // 'graduationInfos.graduation_date' => ['required', 'date'],
            // 'graduationInfos.board_approval' => ['required', 'string', 'max:100'],
            // 'graduationInfos.latin_honor' => ['nullable', Rule::in(['Cum Laude', 'Magna Cum Laude', 'Summa Cum Laude', 'Academic Distinction'])],
            // 'registrationInfos.last_school_attended' => ['required', 'string', 'max:255'],
            // 'registrationInfos.last_year_attended' => ['required', 'digits:4', 'numeric'],
            // 'registrationInfos.category' => ['required', Rule::in(['Transferee', 'High School Graduate', 'Senior High School Graduate', 'College Graduate', 'Others'])],
            // 'registrationInfos.other_category' => ['nullable', 'required_if:category,Others', 'string'],
            // 'acad_year_id' => ['required', 'exists:acad_years,id'],
            // 'acad_term_id' => ['required', 'exists:acad_terms,id'],
            // 'campus_id' => ['required', 'exists:campuses,id'],
            // 'college_id' => ['required', 'exists:colleges,id'],
            // 'program_id' => ['required', 'exists:programs,id'],
            // 'program_major_id' => ['nullable', 'exists:programs_major,id'],
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'last_name' => Str::title(strip_tags($this->last_name)),
            'first_name' => Str::title(strip_tags($this->first_name)),
            'middle_name' => Str::title(strip_tags($this->middle_name ?? '')),
            'suffix' => strtoupper(strip_tags($this->suffix ?? '')),
            'address' => strip_tags($this->address),
            'birthplace' => strip_tags($this->birthplace),
            'gwa' => number_format((float)$this->gwa, 2, '.', ''),
            'nstp_number' => preg_replace('/[^A-Za-z0-9]/', '', $this->nstp_number),
        ]);
    }

    public function messages()
    {
        return [
            'gwa.between' => 'The GWA must be between 1 and 5.',
            'sex.in' => 'Invalid gender selection.',
            'category.in' => 'Invalid category selection.',
            'acad_year_id.exists' => 'Selected academic year does not exist.',
        ];
    }
}
