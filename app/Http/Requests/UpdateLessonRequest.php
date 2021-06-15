<?php

namespace App\Http\Requests;

use App\Enums\LessonEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
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
            'title' => 'string',
            'description' => 'string',
            'provider_video' => 'in:'.LessonEnum::PROVIDER_YOUTUBE.','.LessonEnum::PROVIDER_VIMEO,
            'video_link' => 'url',
            'init_date' => 'date'
        ];
    }
}
