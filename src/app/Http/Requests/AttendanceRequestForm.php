<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequestForm extends FormRequest
{
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
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i',
            'note' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'clock_in.date_format' => '出勤時刻はHH:MM形式で入力してください',
            'clock_out.date_format' => '退勤時刻はHH:MM形式で入力してください',
            'note.max' => '備考は500文字以内で入力してください',

        ];
    }
}
