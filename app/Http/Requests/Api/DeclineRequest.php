<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeclineRequest extends FormRequest
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

        if ($method === "POST"){
            if ($route_name === "decline.create") {
                $rules = [
                    "to_member_id" => [
                        "required",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ],
                    "from_member_id" => [
                        "required",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ]
                ];
            }
        } else if ($method === "GET") {
            if ($route_name === "decline.get") {
                $rules = [
                    "member_id" => [
                        "required",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ]
                ];
            }
        } else if ($method === "DELETE") {
            if ($route_name === "decline.delete") {
                $rules = [
                    "from_member_id" => [
                        "required",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ],
                    "to_member_id" => [
                        "required",
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

    public function attributes()
    {
        return [
            "member_id" => "ユーザーID",
            "to_member_id" => "ユーザーID",
            "from_member_id" => "ブロック対象のユーザーID",
        ];
    }

    public function messages()
    {
        return [
            // ログインユーザー
            "member_id.required" => ":attributeは必須項目です。",
            "member_id.exists" => ":attributeが不正な値です。",
            // ログインユーザー
            "to_member_id.required" => ":attributeは必須項目です。",
            "to_member_id.exists" => ":attributeが不正な値です。",
            // ブロック対象
            "from_member_id.required" => ":attributeは必須項目です。",
            "from_member_id.exists" => ":attributeが不正な値です。",
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }

    //エラー時HTMLページにリダイレクトされないようにオーバーライド
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json(
                $validator->errors(),
                422
            )
        );
    }
}
