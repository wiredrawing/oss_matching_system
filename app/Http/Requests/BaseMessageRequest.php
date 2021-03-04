<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\CreditCard;

class BaseMessageRequest extends FormRequest
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
            // メッセージをタイムラインへ投稿する
            if ($route_name === "api.timeline.message") {
                $rules = [
                    "from_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        }),
                    ],
                    "to_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        }),
                    ],
                    "message" => [
                        "required",
                        "between:1,4096",
                        new CreditCard(),
                    ],
                    "security_token" => [
                        "required",
                        Rule::exists("members", "security_token")->where(function ($query) {
                            $query
                            ->where("id", $this->input("from_member_id"))
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ],
                ];
            }
        } else if ($method === "GET") {
            if ($route_name === "web.member.message.talk") {
                $rules = [
                    "to_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        }),
                        Rule::exists("likes", "to_member_id")->where(function ($query) {
                            $query
                            ->where("from_member_id", $this->member->id);
                        }),
                    ]
                ];
            } else if($route_name === "web.member.message.index") {
                // 現状ルールなし
                $rules = [];
            } else if ($route_name === "api.timeline.message") {
                $rules = [
                    "from_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ],
                    "to_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ],
                    "timeline_id" => [
                        "integer"
                    ],
                    "limit" => [
                        "integer"
                    ],
                    "separator" => [
                        "integer",
                        Rule::in([1, -1])
                    ],
                ];
            }
        }
        return $rules;
    }

    public function messages()
    {
        return [
            "from_member_id.required" => ":attributeは必須項目です。",
            "from_member_id.exists" => ":attributeが存在しません",
            "to_member_id.required" => ":attributeは必須項目です。",
            "to_member_id.exists" => ":attributeが存在しません",
            "message.required" => ":attributeは必須項目です。",
            "message.between" => ":attributeは1文字以上4096文字以下で入力して下さい。",
            "security_token.required" => ":attributeは必須項目です。",
            "security_token.exists" => ":attributeが正しい値ではありません。",
        ];
    }

    public function attributes()
    {
        return [
            "from_member_id" => "ユーザーID",
            "to_member_id" => "ユーザーID",
            "message" => "送信内容",
            "security_token" => "セキュリティトークン",
        ];
    }

    /**
     * リクエストボディをマージする
     *
     * @return void
     */
    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
