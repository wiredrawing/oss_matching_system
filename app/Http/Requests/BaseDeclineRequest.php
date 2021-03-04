<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseDeclineRequest extends FormRequest
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
            if ($route_name === "web.member.decline.unblock") {
                // ユーザーのブロック解除処理
                $rules = [
                    "from_member_id" => [
                        "integer",
                        "required",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ],
                    "to_member_id" => [
                        "integer",
                        "required",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ]
                ];
            } else if ($route_name === "web.member.decline.block") {
                // 指定のユーザーをブロックする処理
                $rules = [
                    "to_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        }),
                        function ($attribute, $value, $fail) {
                            if ((int)$value === (int)$this->input("from_member_id")) {
                                $fail("自分自身をブロックすることはできません｡");
                            }
                        }
                    ],
                    "from_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        }),
                    ]
                ];
            }
        } else if ($route_name === "web.member.decline.completed") {
            $rules = [];
        }
        return $rules;
    }

    public function attributes()
    {
        return [
            "to_member_id" => "ユーザーID",
            "from_member_id" => "ブロック対象のユーザーID",
        ];
    }

    public function messages()
    {
        return [
            // ログインユーザー
            "to_member_id.required" => ":attributeは必須項目です。",
            "to_member_id.exists" => ":attributeが不正な値です。",
            "to_member_id.integer" => ":attributeが不正な値です｡",
            // ブロック対象
            "from_member_id.required" => ":attributeは必須項目です。",
            "from_member_id.exists" => ":attributeが不正な値です。",
            "from_member_id.integer" => ":attributeが不正な値です｡",
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
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
