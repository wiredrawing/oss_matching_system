<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\CreditCard;

class BaseViolationRequest extends FormRequest
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
            if ($route_name === "web.member.violation.create") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id"),
                    ],
                ];
            } else if ($route_name === "web.member.violation.completed") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id"),
                    ],
                ];
            }
        } else if($method === "POST") {
            if ($route_name === "web.member.violation.postCreate") {
                $rules = [
                    "from_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id"),
                    ],
                    "category_id" => [
                        "required",
                        "array",
                    ],
                    "category_id.*" => [
                        "required",
                        "integer",
                        Rule::in(array_keys(Config("const.violation_list")))
                    ],
                    "security_token" => [
                        "required",
                        // "string",
                        Rule::exists("members", "security_token"),
                    ],
                    "to_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id"),
                    ],
                    "message" => [
                        "between:0,512",
                        new CreditCard(),
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

    public function messages() {
        return [
            "message.required" => ":attributeは必須項目です｡",
            "message.between" => ":attributeは10文字から512文字以内で入力して下さい｡",
            "from_member_id.required" => ":attributeは必須項目です｡",
            "from_member_id.integer" => ":attributeは正しいフォーマットで入力して下さい｡",
            "from_member_id.exists" => ":attributeは存在しないユーザーです｡",
            "to_member_id.required" => ":attributeは必須項目です｡",
            "to_member_id.integer" => ":attributeは正しいフォーマットで入力して下さい｡",
            "to_member_id.exists" => ":attributeは存在しないユーザーです｡",
            "category_id.required" => ":attributeは必須項目です｡",
            "category_id.array" => ":attributeは複数選択型です｡",
        ];
    }

    public function attributes()
    {
        return [
            "from_member_id" => "通報者ID",
            "to_member_id" => "違反者ID",
            "member_id" => "違反者ID",
            "security_token" => "セキュリティトークン",
            "message" => "違反内容",
            "category_id" => "違反項目",
        ];
    }
}
