<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseLikeRequest extends FormRequest
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

        $method = strtoupper($this->getMethod());
        $route_name = Route::currentRouteName();

        $rules = [];

        if ($method === "POST") {
            if ($route_name === "like.create" || $route_name === "web.member.like.create") {
                $rules = [
                    "from_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            // member_idは本登録完了後、且つ削除されていないアカウントであることを検証する
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        }),
                    ],
                    "to_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            // member_idは本登録完了後、且つ削除されていないアカウントであることを検証する
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        }),
                        function ($attribute, $value, $fail) {
                            if ((int)$value === $this->input("from_member_id")) {
                                $fail("自分自身にGoodを贈ることはできません｡");
                            }
                        }
                    ]
                ];
            }
        } else if($method === "GET") {
            if ($route_name === "like.to") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ]
                ];
            } else if ($route_name === "like.from") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ]
                ];
            } else if ($route_name === "like.matching") {
                // URLパラメータのmember_idとマッチングしたユーザー一覧を取得
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ]
                ];
            }
        }
        return $rules;
    }

    public function messages()
    {
        return [
            // 差出人
            "from_member_id.required" => ":attributeは必須項目です。",
            "from_member_id.exists" => ":attributeがDBに存在しません。",
            // 宛先
            "to_member_id.required" => "attributeは必須項目です。",
            "to_member_id.exists" => ":attributeがDBに存在しません",
        ];
    }

    public function attributes()
    {
        return [
            "from_member_id" => "GoodしたユーザーID",
            "to_member_id" => "GoodされたユーザーID",
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
