<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Administrator;

class LoginRequest extends FormRequest
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
        $rules = [];
        $method = strtoupper($this->getMethod());
        $route_name = Route::currentRouteName();

        if ($method === "POST") {

            if ($route_name === "web.admin.authenticate") {
                $rules = [
                    "email" => [
                        "required",
                        "email:rfc",
                        function($attribute, $value, $failed) {
                            $administrator = Administrator::where("email", $value)->get()->first();
                            // 管理者アカウントが有効かどうか
                            if ($administrator !== NULL && (int)$administrator->is_displayed === Config("const.binary_type.on")) {
                                return true;
                            }
                            $failed(":attributeは有効な管理者アカウントではありません");
                        }
                    ],
                    "password" => [
                        "required",
                        "between:8,64"
                    ]
                ];
            }
        }

        return $rules;
    }


    public function messages()
    {
        return [
            // 管理者メールアドレス
            "email.required" => ":attributeは必須項目です。",
            "email.email" => ":attributeが正しいメールアドレスではありません｡",
            // 管理者パスワード
            "password.required" => ":attributeは必須項目です。",
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }

}
