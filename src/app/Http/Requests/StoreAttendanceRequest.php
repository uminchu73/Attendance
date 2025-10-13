<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
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
            'clock_in' => ['required', 'date_format:H:i'],
            'clock_out' => ['required', 'date_format:H:i', 'after:clock_in'],
            'note' => ['required', 'string'],

            //既存休憩
            'breaks.*.start' => ['nullable', 'date_format:H:i'],
            'breaks.*.end'   => ['nullable', 'date_format:H:i'],

            //新規休憩
            'breaks.new.start' => ['nullable', 'date_format:H:i'],
            'breaks.new.end'   => ['nullable', 'date_format:H:i'],

            // 出勤・退勤の整合性チェック
            'clock_out' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $clock_in = $this->input('clock_in');
                    if ($clock_in && $value && $value <= $clock_in) {
                        $fail('出勤時間もしくは退勤時間が不適切な値です');
                    }
                }
            ],

            // 既存休憩と新しい休憩を一緒にチェック
            'breaks.*.start' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1]; // 0, 1, new
                    $end = $this->input("breaks.$index.end");
                    $clock_in = $this->input('clock_in');
                    $clock_out = $this->input('clock_out');

                    if ($value && $end && $value > $end) {
                        $fail('休憩開始は休憩終了より前にしてください');
                    }
                    if ($value && ($value < $clock_in || $value > $clock_out)) {
                        $fail('休憩時間が不適切な値です');
                    }
                }
            ],
            'breaks.*.end' => [
                'nullable',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $index = explode('.', $attribute)[1];
                    $start = $this->input("breaks.$index.start");
                    $clock_in = $this->input('clock_in');
                    $clock_out = $this->input('clock_out');

                    if ($value && $start && $value < $start) {
                        $fail('休憩終了は休憩開始より後にしてください');
                    }
                    if ($value && ($value < $clock_in || $value > $clock_out)) {
                        $fail('休憩時間もしくは退勤時間が不適切な値です');
                    }
                }
            ],
        ];
    }

    public function messages()
    {
        return [
            'clock_in.required' => '出勤時間を入力してください',
            'clock_in.date_format' => '出勤時間の形式が正しくありません',
            'clock_out.required' => '退勤時間を入力してください',
            'clock_out.date_format' => '退勤時間の形式が正しくありません',
            'clock_out.after' => '退勤時間は出勤時間より後にしてください',
            'note.required' => '備考を記入してください',
            'note.max' => '備考は500文字以内で入力してください',
        ];
    }
}
