<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ConvertRequest as ModelConvertRequest;

class ConvertRequest extends FormRequest
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
        $validationRules = [];

        switch ($this->method()) {
            case 'POST':
                $urlRules = "required|url";
                if ($this->convert_type == ModelConvertRequest::TYPE_YOUTUBE) {
                    $regex = "(youtu\.be|youtube\.com)";
                    $urlRules = ["required", "url", "regex:$regex"];
                }
                elseif ($this->convert_type == ModelConvertRequest::TYPE_FACEBOOK) {
                    $regex = "(facebook\.com|fb\.watch)";
                    $urlRules = ["required", "url", "regex:$regex"];
                }

                $validationRules = [
                    "url" => $urlRules,
                    "convert_type" => "required",
                ];

            break;
        }

        return $validationRules;
    }
}
