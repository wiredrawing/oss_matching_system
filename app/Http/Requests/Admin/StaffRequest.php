<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Administrator;

class StaffRequest extends FormRequest
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
            if ($route_name === "web.admin.staff.postCreate") {
                $rules = [
                    "display_name" => [
                        "required",
                        "between:1,32",
                    ],
                    "email" => [
                        "required",
                        "email:rfc",
                        function ($attribute, $value, $failed) {
                            $administrator = Administrator::where("email", $value)
                            ->withTrashed()
                            ->get()
                            ->first();
                            if ($administrator !== NULL) {
                                $failed(":attributeは既に登録済みのメールアドレスです｡");
                            }
                        }
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
                    "memo" => [
                        "between:0,8192"
                    ],
                ];
            } else if ($route_name === "web.admin.staff.postUpdate") {
                $rules = [
                    "administrator_id" => [
                        "required",
                        "integer",
                        Rule::exists("administrators", "id"),
                    ],
                    "display_name" => [
                        "required",
                        "between:1,32",
                    ],
                    "email" => [
                        "required",
                        "email:rfc",
                        function ($attribute, $value, $failed) {
                            $administrator = Administrator::where("email", $value)
                            ->where("id", "!=", $this->input("administrator_id"))
                            ->get()
                            ->first();
                            if ($administrator !== NULL) {
                                $failed(":attributeは既に登録済みのメールアドレスです｡");
                            }
                        }
                    ],
                    "is_displayed" => [
                        "required",
                        "integer",
                        Rule::in(Config("const.binary_type"))
                    ],
                    "memo" => [
                        "between:0,8192"
                    ],
                ];

                // パスワードが入力されている場合は、パスワードの確認チェックを行う
                if ($this->input("password") !== NULL) {
                    $password = $this->input("password");
                    $rules["password"] = [
                        "required",
                        "between:8,64"
                    ];
                    $rules["password_check"] = [
                        "required",
                        "between:8,64",
                        function ($attribute, $value, $fail) use ($password) {
                            if ($password !== $value) {
                                $fail(":attributeが一致しません。");
                            }
                        }
                    ];
                }
            }
        } else if ($method === "GET") {

            if ($route_name === "web.admin.staff.update") {
                $rules = [
                    "administrator_id" => [
                        "required",
                        "integer",
                        Rule::exists("administrators", "id"),
                    ]
                ];
            }
        }
        return $rules;
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }


    public function attributes()
    {
        return [
            "administrator_id" => "管理者ID",
            "display_name" => "管理者名",
            "email" => "管理者用ログインID",
            "password" => "パスワード",
            "password_check" => "確認用パスワード",
            "memo" => "管理者用メモ欄",
            "is_displayed" => "管理者アカウントの有効有無"
        ];
    }


    public function messages()
    {
        return [
            "administrator_id.required" => ":attributeは必須項目です｡",
            "display_name.required" => ":attributeは必須項目です｡",
            "email.required" => ":attributeは必須項目です｡",
            "email.email" => ":attributeは正しいメールアドレスを入力して下さい｡",
            "password.required" => ":attributeは必須項目です｡",
            "password.between" => ":attributeは8文字以上64文字以内で入力して下さい｡",
            "password_check.required" => ":attributeは必須項目です｡",
            "password_check.between" => ":attributeは8文字以上64文字以内で入力して下さい｡",
            "memo.between" => ":attributeは8192文字以内で入力して下さい｡",
            "is_displayed.required" => ":attributeは必須項目です｡"
        ];
    }
    // //エラー時HTMLページにリダイレクトされないようにオーバーライド
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
