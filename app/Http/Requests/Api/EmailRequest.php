<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Requests\BaseEmailRequest;
class EmailRequest extends BaseEmailRequest
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

    // /**
    //  * Get the validation rules that apply to the request.
    //  *
    //  * @return array
    //  */
    // public function rules()
    // {
    //     $rules = [];
    //     // post, get, patch, delete, put
    //     $method = strtoupper($this->getMethod());

    //     $route_name = Route::currentRouteName();

    //     if ($method === "POST") {
    //         if ($route_name === "email.registerEmail") {
    //             $rules = [
    //                 "email" => [
    //                     "required",
    //                     "email:rfc",
    //                 ],
    //             ];
    //         }
    //     } else if ($method === "GET") {
    //         if ($route_name === "email.checkToken") {
    //             $rules = [
    //                 "token" => [
    //                     "required",
    //                     Rule::exists("members", "token")->where(function ($query) {
    //                         $query
    //                         // 仮登録中
    //                         ->where("is_registered", Config("const.binary_type.off"))
    //                         // 且つ未削除
    //                         ->where("deleted_at", NULL);
    //                     })
    //                 ]
    //             ];
    //         }
    //     }

    //     return $rules;
    // }

    // public function validationData()
    // {
    //     return array_merge($this->all(), $this->route()->parameters());
    // }

    // public function messages()
    // {
    //     return [
    //         "email.required" => ":attributeは必須項目です。",
    //         "email.email" => ":attributeが有効なメールフォーマットではありません。",
    //         "token.required" => ":attributeは必須項目です。",
    //         "token.exists" => ":attributeが不正な値です。",
    //     ];
    // }

    // public function attributes()
    // {
    //     return [
    //         "email" => "メールアドレス",
    //         "token" => "認証用トークン"
    //     ];
    // }

    //エラー時HTMLページにリダイレクトされないようにオーバーライド
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
