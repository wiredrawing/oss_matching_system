<?php

namespace App\Http\Requests\Admin;

use App\Rules\CreditCard;
use App\Models\Member;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MemberRequest extends FormRequest
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

            if ($route_name === "web.admin.member.index") {
                $rules = [
                    "email" => [
                        "between:0,2048",
                    ]
                ];
            } else if ($route_name === "web.admin.member.detail") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id"),
                    ]
                ];
            } else if ($route_name === "web.admin.member.completedDetail") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id"),
                    ]
                ];
            } else if ($route_name === "web.admin.member.image") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id"),
                    ]
                ];
            } else if ($route_name === "web.admin.member.like.send") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id"),
                    ]
                ];
            } else if ($route_name === "web.admin.member.like.get") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id"),
                    ]
                ];
            } else if ($route_name === "web.admin.member.like.match") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id"),
                    ]
                ];
            } else if ($route_name === "web.admin.member.timeline") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id"),
                    ],
                    "target_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id"),
                    ]
                ];
            }
        } else if ($method === "POST") {
            if ($route_name === "web.admin.member.postDetail") {
                $rules = [
                    "member_id" => [
                        "required",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            // ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ],
                    "display_name" => [
                        "required",
                        "max:255",
                        new CreditCard(),
                        function($attribute, $value, $failed) {
                            $member = Member::where("display_name", $value)
                                ->where("id", "!=", $this->input("member_id"))
                                ->get()
                                ->first();
                            if ($member !== NULL) {
                                $failed("この:attributeは､ご利用いただけません｡他のユーザー名をご入力下さい｡");
                            }
                        }
                    ],
                    // "year" => [
                    //     "required",
                    //     Rule::in(array_keys(Config("const.year")))
                    // ],
                    // "month" => [
                    //     "required",
                    //     Rule::in(array_keys(Config("const.month")))
                    // ],
                    // "day" => [
                    //     "required",
                    //     Rule::in(array_keys(Config("const.day")))
                    // ],
                    // "birthday" => [
                    //     "required",
                    //     "date",
                    //     function ($attribute, $value, $fail) {
                    //         $birthday_time = date("Ymd", strtotime($value));
                    //         $today_time = date("Ymd");
                    //         $expected_age =  ($today_time - $birthday_time) / 10000;
                    //         if ( (Config("const.minimum_age") < $expected_age) !== true) {
                    //             $fail("本サービスは18歳以上のみ利用可能です。");
                    //         }
                    //     }
                    // ],
                    "gender" => [
                        "required",
                        Rule::in(array_keys(Config("const.gender"))),
                    ],
                    "height" => [
                        "integer",
                        Rule::in(array_keys(Config("const.height"))),
                    ],
                    "body_style" => [
                        Rule::in(array_keys(Config("const.body_style")[$this->input("gender")])),
                    ],
                    "children" => [
                        Rule::in(array_keys(Config("const.children"))),
                    ],
                    "day_off" => [
                        Rule::in(array_keys(Config("const.day_off"))),
                    ],
                    "alcohol" => [
                        Rule::in(array_keys(Config("const.alcohol"))),
                    ],
                    "smoking" => [
                        Rule::in(array_keys(Config("const.smoking"))),
                    ],
                    "message" => [
                        "max:4096",
                        new CreditCard(),
                    ],
                    "memo" => [
                        "max:8192",
                        new CreditCard(),
                    ],
                    "notification_good" => [
                        "integer",
                        Rule::in(array_values(Config("const.binary_type"))),
                    ],
                    "notification_message" => [
                        "integer",
                        Rule::in(array_values(Config("const.binary_type"))),
                    ],
                    "blood_type" => [
                        "integer",
                        Rule::in(array_keys(Config("const.blood_type"))),
                    ],
                    "pet" => [
                        "integer",
                        Rule::in(array_keys(Config("const.pet"))),
                    ],
                    "salary" => [
                        "integer",
                        Rule::in(array_keys(Config("const.salary"))),
                    ],
                    "partner" => [
                        "integer",
                        Rule::in(array_keys(Config("const.partner"))),
                    ],
                    "plan_code" => [
                        Rule::exists("price_plans", "plan_code"),
                    ],
                    // "security_token" => [
                    //     "required",
                    //     "max:512",
                    //     Rule::exists("members", "security_token")->where("id", $this->input("member_id")),
                    // ],
                    "prefecture" => [
                        "required",
                        "integer",
                        "min:1",
                        Rule::in(array_keys(Config("const.prefecture"))),
                    ],
                    "job_type" => [
                        "integer",
                        Rule::in(array_keys(Config("const.job_type"))),
                    ],
                    "credit_id" => [
                        "min:0",
                        "max:64",
                    ],
                    "is_blacklisted" => [
                        "required",
                        "integer",
                        Rule::in(array_keys(Config("const.blacklist"))),
                    ],
                    "is_approved" => [
                        "required",
                        "integer",
                        Rule::in(array_keys(Config("const.image.approve_type_name"))),
                    ],
                    "income_certificate" => [
                        "required",
                        "integer",
                        Rule::in(array_keys(Config("const.image.approve_type_name"))),
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
            } else if ($route_name === "web.admin.member.image.delete") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")
                    ],
                    "image_id" => [
                        "required",
                        "integer",
                        Rule::exists("images", "id")
                    ],
                    "token" => [
                        "required",
                        Rule::exists("images", "token")
                    ]
                ];
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            "member_id.required" => ":attributeは必須項目です。",
            "member_id.exists" => "指定した:attributeは存在しません。",
            "member_id.integer" => ":attributeは数字で入力して下さい｡",
            "display_name.required" => ":attributeは必須項目です。",
            "display_name.max" => ":attributeは255文字以内で入力して下さい｡",
            "year.required" => ":attributeは必須項目です。",
            "year.in" => ":attributeは適切な値を指定して下さい。",
            "month.required" => ":attributeは必須項目です。",
            "month.in" => ":attributeは適切な値を指定して下さい。",
            "day.required" => ":attributeは必須項目です。",
            "day.in" => ":attributeは適切な値を指定して下さい。",
            "birthday.required" => ":attributeは必須項目です。",
            "birthday.date" => ":attributeが正しい日付ではありません。",
            "gender.required" => ":attributeは必須項目です。",
            "gender.in" => ":attributeは適切な値を指定して下さい。",
            "height.in" => ":attributeは適切な数値を指定して下さい。",
            "body_style.in" => ":attributeは適切な数値を指定して下さい。",
            "children.in" => ":attributeは適切な数値を指定して下さい。",
            "day_off.in" => ":attributeは適切な数値を指定して下さい。",
            "alcohol.in" => ":attributeは適切な数値を指定して下さい。",
            "smoking.in" => ":attributeは適切な数値を指定して下さい。",
            "message.max" => ":attributeは4000文字以内で入力して下さい。",
            "notification_good.in" => ":attributeは適切な数値を指定して下さい。",
            "notification_message.in" => ":attributeは適切な数値を指定して下さい。",
            "blood_type.in" => ":attributeは適切な数値を指定して下さい。",
            "pet.in" => ":attributeは適切な数値を指定して下さい。",
            "salary.in" => ":attributeは適切な数値を指定して下さい。",
            "partner.in" => ":attributeは適切な数値を指定して下さい。",
            "plan_code.required" => ":attributeは必須項目です。",
            "plan_code.exists" => ":attributeは適切な数値を指定して下さい。",
            "email.required" => ":attributeは必須項目です。",
            "email.email" => ":attributeは適切な値を指定して下さい。",
            "password.required" => ":attributeは必須項目です。",
            "password.between" => ":attributeは8文字以上64文字以下で入力して下さい。",
            "password_check.required" => ":attributeは必須項目です。",
            "password_check.between" => ":attributeは8文字以上64文字以下で入力して下さい。",
            "prefecture.required" => ":attributeは必須項目です。",
            "prefecture.min" => ":attributeは適切な値を指定して下さい。",
            "prefecture.in" => ":attributeは適切な値を指定して下さい。",
            "job_type.in" => ":attributeは適切な値を指定して下さい。",
            "token.required" => ":attributeは必須項目です。",
            "token.exists" => ":attributeが無効かあるいは期限切れです。",
            "security_token.required" => ":attributeは必須項目です。",
            "security_token.max" => ":attributeは512文字までです。",
            "security_token.exists" => ":attributeは適切な値を指定して下さい。",
            "is_approved.required" => ":attributeは必須項目です。",
            "is_approved.in" => ":attributeは適切な値を指定して下さい。",
        ];
    }

    public function attributes()
    {
        return [
            "member_id" => "ユーザーID",
            "display_name" => "ユーザー名",
            "birthday" => "生年月日",
            "gender" => "性別",
            "height" => "身長",
            "body_style" => "体型",
            "children" => "子供の有無",
            "day_off" => "休日",
            "alcohol" => "飲酒",
            "smoking" => "喫煙",
            "message" => "自己PR",
            "notification_good" => "Goodのメール通知",
            "notification_message" => "メッセージ受信のメール通知",
            "blood_type" => "血液型",
            "pet" => "ペット",
            "salary" => "収入",
            "partner" => "パートナー",
            "plan_code" => "契約プラン",
            "email" => "メールアドレス",
            "password" => "パスワード",
            "password_check" => "確認用パスワード",
            "token" => "認証コード",
            "prefecture" => "お住まいの都道府県",
            "job_type" => "職業",
            "security_token" => "セキュリティートークン",
            "is_approved" => "本人確認申請状態",
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
