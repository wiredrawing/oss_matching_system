<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\CreditCard;
use App\Models\Member;

class BaseMemberRequest extends FormRequest
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
            // 新規member情報の作成
            if ($route_name === "member.create" || $route_name === "web.member.create") {
                $rules = [
                    "display_name" => [
                        "required",
                        new CreditCard(),
                        function($attribute, $value, $failed) {
                            $member = Member::where("display_name", $value)->get()->first();
                            if ($member !== NULL) {
                                logger()->error("{$value}というdisplay_nameは現在使用されています｡");
                                $failed("この:attributeは､ご利用いただけません｡他のユーザー名をご入力下さい｡");
                            }
                        }
                    ],
                    "age" => [
                        "required",
                        "integer",
                        Rule::in(array_keys(Config("const.age_list"))),
                    ],
                    // "year" => [
                    //     "required",
                    //     "integer",
                    //     Rule::in(array_keys(Config("const.year")))
                    // ],
                    // "month" => [
                    //     "required",
                    //     "integer",
                    //     Rule::in(array_keys(Config("const.month")))
                    // ],
                    // "day" => [
                    //     "required",
                    //     "integer",
                    //     Rule::in(array_keys(Config("const.day")))
                    // ],
                    // "birthday" => [
                    //     "required",
                    //     "date",
                    //     function ($attribute, $value, $fail) {
                    //         $birthday_time = date("Ymd", strtotime($value));
                    //         $today_time = date("Ymd");
                    //         $expected_age =  ($today_time - $birthday_time) / 10000;
                    //         if ( (Config("const.minimum_age") <= $expected_age) !== true) {
                    //             $fail(Config("const.minimum_age")."歳未満の方はご利用いただけません");
                    //         }
                    //     }
                    // ],
                    "gender" => [
                        "required",
                        Rule::in(array_keys(config("const.gender"))),
                    ],
                    "height" => [
                        "integer",
                        Rule::in(array_keys(config("const.height"))),
                    ],
                    "body_style" => [
                        "integer",
                        Rule::in(array_keys(config("const.body_style")[$this->input("gender")])),
                    ],
                    "children" => [
                        "integer",
                        Rule::in(array_keys(config("const.children"))),
                    ],
                    "day_off" => [
                        "integer",
                        Rule::in(array_keys(config("const.day_off"))),
                    ],
                    "alcohol" => [
                        "integer",
                        Rule::in(array_keys(config("const.alcohol"))),
                    ],
                    "smoking" => [
                        "integer",
                        Rule::in(array_keys(config("const.smoking"))),
                    ],
                    "message" => [
                        "max:4096",
                        new CreditCard(),
                    ],
                    "notification_good" => [
                        "integer",
                        Rule::in(array_values(config("const.binary_type"))),
                    ],
                    "notification_message" => [
                        "integer",
                        Rule::in(array_values(config("const.binary_type"))),
                    ],
                    "blood_type" => [
                        "integer",
                        Rule::in(array_keys(config("const.blood_type"))),
                    ],
                    "pet" => [
                        "integer",
                        Rule::in(array_keys(config("const.pet"))),
                    ],
                    "salary" => [
                        "integer",
                        Rule::in(array_keys(config("const.salary"))),
                    ],
                    "partner" => [
                        "integer",
                        Rule::in(array_keys(config("const.partner"))),
                    ],
                    "plan_code" => [
                        Rule::exists("price_plans", "plan_code")->where("is_displayed", Config("const.binary_type.on"))
                    ],
                    "email" => [
                        "required",
                        "email:rfc",
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
                    "token" => [
                        "required",
                        Rule::exists("members", "token")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.off"))
                            ->where("expired_at", ">=", (new \DateTime())->format("Y-n-j H:i:s"))
                            ->where("deleted_at", NULL);
                        }),
                    ],
                    "security_token" => [
                        "max:512",
                    ],
                    "prefecture" => [
                        "required",
                        "integer",
                        "min:1",
                        Rule::in(array_keys(config("const.prefecture"))),
                    ],
                    "job_type" => [
                        "integer",
                        Rule::in(array_keys(config("const.job_type"))),
                    ],
                    "agree" => [
                        "required",
                        "integer",
                        function ($attribute, $value, $failed) {
                            if ((int)$value !== 1) {
                                $failed(":attributeに同意する必要があります｡");
                            }
                        }
                    ]
                ];
            } else if ($route_name === "web.member.edit") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
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
                    "age" => [
                        "required",
                        "integer",
                        Rule::in(array_keys(Config("const.age_list"))),
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
                        "integer",
                        Rule::in(array_keys(Config("const.body_style")[$this->input("gender")])),
                    ],
                    "children" => [
                        "integer",
                        Rule::in(array_keys(Config("const.children"))),
                    ],
                    "day_off" => [
                        "integer",
                        Rule::in(array_keys(Config("const.day_off"))),
                    ],
                    "alcohol" => [
                        "integer",
                        Rule::in(array_keys(Config("const.alcohol"))),
                    ],
                    "smoking" => [
                        "integer",
                        Rule::in(array_keys(Config("const.smoking"))),
                    ],
                    "message" => [
                        "max:4096",
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
                        Rule::exists("price_plans", "plan_code")->where("is_displayed", Config("const.binary_type.on"))
                    ],
                    "security_token" => [
                        "required",
                        "max:512",
                        Rule::exists("members", "security_token")->where("id", $this->input("member_id")),
                    ],
                    "prefecture" => [
                        "required",
                        "integer",
                        "min:1",
                        Rule::in(array_keys(Config("const.prefecture"))),
                    ],
                    "job_type" => [
                        Rule::in(array_keys(Config("const.job_type"))),
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
            } else if ($route_name === "member.authenticate" || $route_name === "web.member.authenticate") {
                // ログイン認証処理実行時
                $rules = [
                    "email" => [
                        "required",
                        "email:rfc",
                    ],
                    "password" => [
                        "required",
                        "between:8,64"
                    ]
                ];
            } else if ($route_name === "web.member.email.update") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id"),
                    ],
                    "email" => [
                        "required",
                        "email:rfc",
                        function ($attribute, $value, $fail) {
                            $member = Member::find($this->input("member_id"));
                            if ($member->email === $value ) {
                                $fail("入力された:attributeは､現在利用中のものと同一です｡");
                            } else {
                                $member = Member::where("email", $value)->where("id", "!=", $this->input("id"))->get()->first();
                                if ($member !== NULL) {
                                    $fail("入力された:attributeは現在使用できません｡");
                                }
                            }
                        }
                    ]
                ];
            }
        } else if ($method === "PUT") {
            if ($route_name === "member.update") {
                $input_data = $this->validationData();
                logger()->info(__FILE__, $input_data);
                $gender = $input_data["gender"];
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ],
                    "display_name" => [
                        "required",
                        "max:255",
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
                    //             $fail(":attributeが不正な生年月日となっています。");
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
                        Rule::in(array_keys(Config("const.body_style")[$gender])),
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
                    "notification_good" => [
                        Rule::in(array_values(config("const.binary_type"))),
                    ],
                    "notification_message" => [
                        Rule::in(array_values(config("const.binary_type"))),
                    ],
                    "blood_type" => [
                        Rule::in(array_keys(config("const.blood_type"))),
                    ],
                    "pet" => [
                        Rule::in(array_keys(config("const.pet"))),
                    ],
                    "salary" => [
                        Rule::in(array_keys(config("const.salary"))),
                    ],
                    "partner" => [
                        Rule::in(array_keys(config("const.partner"))),
                    ],
                    "plan_code" => [
                        Rule::exists("price_plans", "plan_code")->where("is_displayed", Config("const.binary_type.on"))
                    ],
                    // メールアドレスの変更は個別のAPIで実行する
                    // "email" => [
                    //     "required",
                    //     "email:rfc",
                    // ],
                    "security_token" => [
                        "size:255",
                    ],
                    "prefecture" => [
                        "required",
                        "min:1",
                        Rule::in(array_keys(Config("const.prefecture"))),
                    ],
                    "job_type" => [
                        Rule::in(array_keys(Config("const.job_type"))),
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
            } else if ($route_name === "member.email") {
                // メールアドレス変更前の新規メールアドレス登録処理
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ],
                    "email" => [
                        "required",
                        "email:rfc",
                    ]
                ];
            }
        } else if ($method === "GET") {
            if ($route_name === "web.member.opponent") {
                $rules = [
                    "target_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"));
                            // ->where("deleted_at", NULL);
                        })
                    ]
                ];
            } else if ($route_name === "web.member.create.completed") {
                // アカウント本登録完了ページ
                $rules = [
                    "token" => [
                        "required",
                        Rule::exists("members", "token")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ]
                ];
            } else if ($route_name === "web.member.email.index") {
                // メールアドレス変更用の入力フォーム
                // 現状ルールなし
                $rules = [

                ];
            } else if ($route_name === "web.member.email.completed") {
                $rules = [
                    "token" => [
                        "required",
                        Rule::exists("email_resets", "token")->where(function ($query) {
                            $query->where("expired_at", ">=", (new \DateTime())->format("Y-m-j H:i:s"));
                        }),
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
            "display_name.required" => ":attributeは必須項目です。",
            "age.required" => ":attributeは必須項目です｡",
            "age.integer" => ":attributeは数字で入力して下さい｡",
            "age.in" => ":attributeは正しい年齢を入力して下さい｡",
            // "year.required" => ":attributeは必須項目です。",
            // "year.in" => ":attributeは適切な値を指定して下さい。",
            // "month.required" => ":attributeは必須項目です。",
            // "month.in" => ":attributeは適切な値を指定して下さい。",
            // "day.required" => ":attributeは必須項目です。",
            // "day.in" => ":attributeは適切な値を指定して下さい。",
            // "birthday.required" => ":attributeは必須項目です。",
            // "birthday.date" => ":attributeが正しい日付ではありません。",
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
            "agree.required" => ":attributeには必ず同意する必要があります｡",
        ];
    }

    public function attributes()
    {
        return [
            "member_id" => "ユーザーID",
            "display_name" => "ユーザー名",
            "age" => "年齢",
            // "birthday" => "生年月日",
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
            "agree" => "利用規約",
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
