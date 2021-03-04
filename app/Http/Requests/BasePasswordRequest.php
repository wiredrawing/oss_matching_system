<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BasePasswordRequest extends FormRequest
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

        if ($method === "GET") {
            if ($route_name === "web.password.update") {
                $rules = [
                    "token" => [
                        "required",
                        Rule::exists("password_reissues", "token")->where(function ($query) {
                            $query
                            ->where("expired_at", ">=", (new \DateTime())->format("Y-n-j H:i:s"))
                            ->where("is_used", Config("const.binary_type.off"));
                        }),
                    ]
                ];
            } else if ($route_name === "web.member.password.index") {
                $rules = [];
            }
        } else if ($method === "POST") {
            if ($route_name === "web.password.postUpdate") {
                $rules = [
                    "token" => [
                        "required",
                        Rule::exists("password_reissues", "token")->where(function ($query) {
                            $query
                            ->where("expired_at", ">=", (new \DateTime())->format("Y-n-j H:i:s"))
                            ->where("is_used", Config("const.binary_type.off"));
                        }),
                    ],
                    "password" => [
                        "required",
                        "between:8,64",
                        function ($attribute, $value, $fail) {
                            if (preg_match("/^(?=.*?[a-zA-Z]){1,}(?=.*?\d){1,}[a-zA-Z\d@\\_\\-$#]{8,64}$/", $value) !== 1) {
                                $fail(":attributeは半角英字および数字のいずれか1文字を必ず含めて入力して下さい。");
                            }
                        }
                    ],
                    "password_check" => [
                        "required",
                        "between:8,64",
                        function ($attribute, $value, $fail) {
                            if ($value !== $this->input("password")) {
                                $fail(":attributeが一致しません。");
                            }
                        }
                    ],
                ];
            } else if ($route_name === "web.member.password.postUpdate") {
                $rules = [
                    "password" => [
                        "required",
                        "between:8,64",
                        function ($attribute, $value, $fail) {
                            if (preg_match("/^(?=.*?[a-zA-Z]){1,}(?=.*?\d){1,}[a-zA-Z\d@\\_\\-$#]{8,64}$/", $value) !== 1) {
                                $fail(":attributeは半角英字および数字のいずれか1文字を必ず含めて入力して下さい。");
                            }
                        }
                    ],
                    "password_check" => [
                        "required",
                        "between:8,64",
                        function ($attribute, $value, $fail)  {
                            if ($value !== $this->input("password")) {
                                $fail(":attributeが一致しません。");
                            }
                        }
                    ],
                ];
            }
        }
        return $rules;
    }

    // 属性名の指定
    public function attributes()
    {
        return [
            "password" => "パスワード",
            "password_check" => "確認用パスワード",
            "token" => "パスワード再発行用トークン",
        ];
    }

    // メッセージの指定
    public function messages()
    {
        return [
            "password.required" => ":attributeは必須項目です。",
            "password.between" => ":attributeは8文字以上64文字以下で入力して下さい。",
            "password_check.required" => ":attributeは必須項目です。",
            "password_check.between" => ":attributeは8文字以上64文字以下で入力して下さい。",
            "token.required" => ":attributeは必須項目です。",
            "token.exists" => ":attributeが不正な値です。",
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }


    //エラー時HTMLページにリダイレクトされないようにオーバーライド
    // protected function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(
    //         response()->json(
    //             $validator->errors(),
    //             422
    //         )
    //     );
    // }
}
