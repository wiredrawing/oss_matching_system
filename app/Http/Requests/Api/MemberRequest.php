<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\CreditCard;
use App\Http\Requests\BaseMemberRequest;
class MemberRequest extends BaseMemberRequest
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

    //     $method = strtoupper($this->getMethod());

    //     $route_name = Route::currentRouteName();

    //     if ($method === "POST") {
    //         // 新規member情報の作成
    //         if ($route_name === "member.create") {
    //             $rules = [
    //                 "display_name" => [
    //                     "required",
    //                 ],
    //                 "display_name_sort" => [
    //                     "required",
    //                     function ($attribute, $value, $fail) {
    //                         if (preg_match('/^[ぁ-んー 　-]*$/u', $value) !== 1) {
    //                             $fail(":attributeは全てひらがなで入力して下さい。");
    //                         }
    //                     }
    //                 ],
    //                 "birthday" => [
    //                     "required",
    //                     "date",
    //                     function ($attribute, $value, $fail) {
    //                         $birthday_time = date("Ymd", strtotime($value));
    //                         $today_time = date("Ymd");
    //                         $expected_age =  ($today_time - $birthday_time) / 10000;
    //                         if ( (Config("const.minimum_age") < $expected_age) !== true) {
    //                             $fail(":attributeが不正な生年月日となっています。");
    //                         }
    //                     }
    //                 ],
    //                 "gender" => [
    //                     "required",
    //                     Rule::in(array_keys(config("const.gender"))),
    //                 ],
    //                 "height" => [
    //                     "integer",
    //                     Rule::in(config("const.height")),
    //                 ],
    //                 "body_style" => [
    //                     Rule::in(array_keys(config("const.body_style"))),
    //                 ],
    //                 "children" => [
    //                     Rule::in(array_keys(config("const.children"))),
    //                 ],
    //                 "day_off" => [
    //                     Rule::in(array_keys(config("const.day_off"))),
    //                 ],
    //                 "alcohol" => [
    //                     Rule::in(array_keys(config("const.alcohol"))),
    //                 ],
    //                 "smoking" => [
    //                     Rule::in(array_keys(config("const.smoking"))),
    //                 ],
    //                 "message" => [
    //                     "max:4096",
    //                     new CreditCard(),
    //                 ],
    //                 "notification_good" => [
    //                     Rule::in(array_keys(config("const.binary_type"))),
    //                 ],
    //                 "notification_message" => [
    //                     Rule::in(array_keys(config("const.binary_type"))),
    //                 ],
    //                 "blood_type" => [
    //                     Rule::in(array_keys(config("const.blood_type"))),
    //                 ],
    //                 "pet" => [
    //                     Rule::in(array_keys(config("const.pet"))),
    //                 ],
    //                 "salary" => [
    //                     Rule::in(array_keys(config("const.salary"))),
    //                 ],
    //                 "partner" => [
    //                     Rule::in(array_keys(config("const.partner"))),
    //                 ],
    //                 "plan_code" => [
    //                     Rule::exists("price_plans", "plan_code")->where("is_displayed", Config("const.binary_type.on"))
    //                 ],
    //                 "email" => [
    //                     "required",
    //                     "email:rfc",
    //                 ],
    //                 "password" => [
    //                     "required",
    //                     "between:8,64"
    //                 ],
    //                 "password_check" => [
    //                     "required",
    //                     "between:8,64"
    //                 ],
    //                 "token" => [
    //                     "required",
    //                     Rule::exists("members", "token")->where(function ($query) {
    //                         $query
    //                         ->where("is_registered", Config("const.binary_type.off"))
    //                         ->where("expired_at", ">=", (new \DateTime())->format("Y-n-j H:i:s"))
    //                         ->where("deleted_at", NULL);
    //                     }),
    //                 ],
    //                 "security_token" => [
    //                     "size:255",
    //                 ],
    //                 "prefecture" => [
    //                     "required",
    //                     Rule::in(array_keys(config("const.prefecture"))),
    //                 ],
    //                 "job_type" => [
    //                     Rule::in(array_keys(config("const.job_type"))),
    //                 ],
    //             ];
    //         } else if ($route_name === "member.authenticate") {
    //             // ログイン認証処理実行時
    //             $rules = [
    //                 "email" => [
    //                     "required",
    //                     "email:rfc",
    //                 ],
    //                 "password" => [
    //                     "required",
    //                     "between:8,64"
    //                 ]
    //             ];
    //         }
    //     } else if ($method === "PUT") {
    //         if ($route_name === "member.update") {
    //             $rules = [
    //                 "member_id" => [
    //                     "required",
    //                     Rule::exists("members", "id")->where(function ($query) {
    //                         $query
    //                         ->where("is_registered", Config("const.binary_type.on"))
    //                         ->where("deleted_at", NULL);
    //                     })
    //                 ],
    //                 "display_name" => [
    //                     "required",
    //                     "max:255",
    //                 ],
    //                 "display_name_sort" => [
    //                     "required",
    //                     function ($attribute, $value, $fail) {
    //                         if (preg_match('/^[ぁ-んー 　-]*$/u', $value) !== 1) {
    //                             $fail(":attributeは全てひらがなで入力して下さい。");
    //                         }
    //                     },
    //                     "max:255",
    //                 ],
    //                 "birthday" => [
    //                     "required",
    //                     "date",
    //                     function ($attribute, $value, $fail) {
    //                         $birthday_time = date("Ymd", strtotime($value));
    //                         $today_time = date("Ymd");
    //                         $expected_age =  ($today_time - $birthday_time) / 10000;
    //                         if ( (Config("const.minimum_age") < $expected_age) !== true) {
    //                             $fail(":attributeが不正な生年月日となっています。");
    //                         }
    //                     }
    //                 ],
    //                 "gender" => [
    //                     "required",
    //                     Rule::in(array_keys(config("const.gender"))),
    //                 ],
    //                 "height" => [
    //                     "integer",
    //                     Rule::in(config("const.height")),
    //                 ],
    //                 "body_style" => [
    //                     Rule::in(array_keys(config("const.body_style"))),
    //                 ],
    //                 "children" => [
    //                     Rule::in(array_keys(config("const.children"))),
    //                 ],
    //                 "day_off" => [
    //                     Rule::in(array_keys(config("const.day_off"))),
    //                 ],
    //                 "alcohol" => [
    //                     Rule::in(array_keys(config("const.alcohol"))),
    //                 ],
    //                 "smoking" => [
    //                     Rule::in(array_keys(config("const.smoking"))),
    //                 ],
    //                 "message" => [
    //                     "max:4096",
    //                     new CreditCard(),
    //                 ],
    //                 "notification_good" => [
    //                     Rule::in(array_keys(config("const.binary_type"))),
    //                 ],
    //                 "notification_message" => [
    //                     Rule::in(array_keys(config("const.binary_type"))),
    //                 ],
    //                 "blood_type" => [
    //                     Rule::in(array_keys(config("const.blood_type"))),
    //                 ],
    //                 "pet" => [
    //                     Rule::in(array_keys(config("const.pet"))),
    //                 ],
    //                 "salary" => [
    //                     Rule::in(array_keys(config("const.salary"))),
    //                 ],
    //                 "partner" => [
    //                     Rule::in(array_keys(config("const.partner"))),
    //                 ],
    //                 "plan_code" => [
    //                     Rule::exists("price_plans", "plan_code")->where("is_displayed", Config("const.binary_type.on"))
    //                 ],
    //                 // メールアドレスの変更は個別のAPIで実行する
    //                 // "email" => [
    //                 //     "required",
    //                 //     "email:rfc",
    //                 // ],
    //                 "security_token" => [
    //                     "size:255",
    //                 ],
    //                 "prefecture" => [
    //                     "required",
    //                     Rule::in(array_keys(config("const.prefecture"))),
    //                 ],
    //                 "job_type" => [
    //                     Rule::in(array_keys(config("const.job_type"))),
    //                 ],
    //             ];

    //             // パスワードが入力されている場合は、パスワードの確認チェックを行う
    //             if ($this->input("password") !== NULL) {
    //                 $password = $this->input("password");
    //                 $rules["password"] = [
    //                     "required",
    //                     "between:8,64"
    //                 ];
    //                 $rules["password_check"] = [
    //                     "required",
    //                     "between:8,64",
    //                     function ($attribute, $value, $fail) use ($password) {
    //                         if ($password !== $value) {
    //                             $fail(":attributeが一致しません。");
    //                         }
    //                     }
    //                 ];
    //             }
    //         } else if ($route_name === "member.email") {
    //             // メールアドレス変更前の新規メールアドレス登録処理
    //             $rules = [
    //                 "member_id" => [
    //                     "required",
    //                     Rule::exists("members", "id")->where(function ($query) {
    //                         $query
    //                         ->where("is_registered", Config("const.binary_type.on"))
    //                         ->where("deleted_at", NULL);
    //                     })
    //                 ],
    //                 "email" => [
    //                     "required",
    //                     "email:rfc",
    //                 ]
    //             ];
    //         }
    //     } else if ($method === "GET") {
    //         $rules = [
    //             "member_id" => [
    //                 "required",
    //                 "integer",
    //                 Rule::exists("members", "id")->where(function ($query) {
    //                     $query
    //                     ->where("is_registered", Config("const.binary_type.on"))
    //                     ->where("deleted_at", NULL);
    //                 })
    //             ]
    //         ];
    //     }

    //     return $rules;
    // }

    // public function messages()
    // {
    //     return [
    //         "member_id.required" => ":attributeは必須項目です。",
    //         "member_id.exists" => "指定した:attributeは存在しません。",
    //         "display_name.required" => ":attributeは必須項目です。",
    //         "display_name_sort.required" => ":attributeは必須項目です。",
    //         "birthday.required" => ":attributeは必須項目です。",
    //         "gender.required" => ":attributeは必須項目です。",
    //         "gender.in" => ":attributeは適切な値を指定して下さい。",
    //         "height.in" => ":attributeは適切な数値を指定して下さい。",
    //         "body_style.in" => ":attributeは適切な数値を指定して下さい。",
    //         "children.in" => ":attributeは適切な数値を指定して下さい。",
    //         "day_off.in" => ":attributeは適切な数値を指定して下さい。",
    //         "alcohol.in" => ":attributeは適切な数値を指定して下さい。",
    //         "smoking.in" => ":attributeは適切な数値を指定して下さい。",
    //         "message.in" => ":attributeは適切な数値を指定して下さい。",
    //         "notification_good.in" => ":attributeは適切な数値を指定して下さい。",
    //         "notification_message.in" => ":attributeは適切な数値を指定して下さい。",
    //         "blood_type.in" => ":attributeは適切な数値を指定して下さい。",
    //         "pet.in" => ":attributeは適切な数値を指定して下さい。",
    //         "salary.in" => ":attributeは適切な数値を指定して下さい。",
    //         "partner.in" => ":attributeは適切な数値を指定して下さい。",
    //         "plan_code.required" => ":attributeは必須項目です。",
    //         "plan_code.exists" => ":attributeは適切な数値を指定して下さい。",
    //         "email.required" => ":attributeは必須項目です。",
    //         "email.email" => ":attributeは適切な値を指定して下さい。",
    //         "password.required" => ":attributeは必須項目です。",
    //         "password.between" => ":attributeは8文字以上64文字以下で入力して下さい。",
    //         "password_check.required" => ":attributeは必須項目です。",
    //         "password_check.between" => ":attributeは8文字以上64文字以下で入力して下さい。",
    //         "prefecture.required" => ":attributeは必須項目です。",
    //         "prefecture.in" => ":attributeは適切な値を指定して下さい。",
    //         "job_type.in" => ":attributeは適切な値を指定して下さい。",
    //         "token.required" => ":attributeは必須項目です。",
    //         "token.exists" => ":attributeが無効かあるいは期限切れです。",
    //     ];
    // }

    // public function attributes()
    // {
    //     return [
    //         "member_id" => "ユーザーID",
    //         "display_name" => "ニックネーム",
    //         "display_name_sort" => "ニックネーム(ひらがな)",
    //         "birthday" => "生年月日",
    //         "gender" => "性別",
    //         "height" => "身長",
    //         "body_style" => "体型",
    //         "children" => "子供の有無",
    //         "day_off" => "休日",
    //         "alcohol" => "飲酒",
    //         "smoking" => "喫煙",
    //         "message" => "自己PR",
    //         "good" => "Goodのメール通知",
    //         "notification_good" => "Goodのメール通知",
    //         "notification_message" => "メッセージ受信のメール通知",
    //         "blood_type" => "血液型",
    //         "pet" => "ペット",
    //         "salary" => "収入",
    //         "partner" => "パートナー",
    //         "plan_code" => "契約プラン",
    //         "email" => "メールアドレス",
    //         "password" => "パスワード",
    //         "password_check" => "確認用パスワード",
    //         "token" => "認証コード",
    //         "prefecture" => "お住まいの都道府県",
    //         "job_type" => "職業",
    //     ];
    // }

    // public function validationData()
    // {
    //     return array_merge($this->all(), $this->route()->parameters());
    // }


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
