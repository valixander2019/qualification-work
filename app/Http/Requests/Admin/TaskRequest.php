<?php

namespace App\Http\Requests\Admin;

use App\Models\Section;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $sectionIds = Section::pluck('id')->implode(',');

        return [
            'section_id' => 'required|in:' . $sectionIds,
            'is_active' => 'nullable|boolean',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string',
            'order' => 'nullable|integer',
        ];
    }
}
