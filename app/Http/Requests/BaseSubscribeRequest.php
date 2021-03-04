<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseSubscribeRequest extends FormRequest
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

        if($method === "POST") {
            if ($route_name === "web.member.subscribe.unsubscribe") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function($query) {
                            // 本登録中かつ､退会済みでないこと
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ],
                ];
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            "member_id.required" => ":attributeは必須項目です｡",
            "member_id.exists" => ":attributeは存在しないユーザー情報です｡",
            "member_id.integer" => ":attributeは正しいフォーマットで入力して下さい｡",
        ];
    }

    public function attributes()
    {
        return [
            "member_id" => "会員ID",
            "withdrawal" => "退会理由",
            "opinion" => "詳細な退会理由",
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
