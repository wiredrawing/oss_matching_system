<?php

namespace App\Http\Requests;

use App\Models\Image;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseMediaRequest extends FormRequest
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

            if ($route_name === "api.media.upload") {
                // 証明書用画像のアップロード
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query->where("deleted_at", NULL);
                        })
                    ],
                    "profile_image" => [
                        "required",
                        "image",
                        "max:5120"
                    ],
                    "use_type" => [
                        "required",
                        "integer",
                        Rule::in(array_values(Config("const.image.use_type"))),
                    ],
                    "blur_level" => [
                        "required",
                        "integer",
                        Rule::in(array_keys(Config("const.image.blur_level"))),
                    ],
                    // ファイルの認証状態
                    "is_approved" => [
                        "required",
                        "integer",
                        Rule::in(array_values(Config("const.image.approve_type"))),
                    ],
                    "delete_image_id" => [
                        "integer",
                        Rule::exists("images", "id")->where("deleted_at", NULL),
                    ],
                ];
            } else if ($route_name === "api.media.delete") {
                $rules = [
                    "image_id" => [
                        "required",
                        "integer",
                        Rule::exists("images", "id"),
                    ],
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("images", "member_id")
                    ],
                    "security_token" => [
                        "required",
                        Rule::exists("members", "security_token")->where(function($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("id", $this->input("member_id"));
                        }),
                    ],
                ];
            } else if ($route_name === "api.timeline.image") {
                // タイムラインやり取り用の画像アップロード
                $rules = [
                    // 画像送信元ユーザー
                    "from_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ],
                    // 画像送信先ユーザー
                    "to_member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function ($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ],
                    "profile_image" => [
                        "required",
                        "image",
                        "max:5120"
                    ],
                    "use_type" => [
                        "required",
                        "integer",
                        Rule::in(array_values(Config("const.image.use_type"))),
                    ],
                    "blur_level" => [
                        "required",
                        "integer",
                        Rule::in(array_keys(Config("const.image.blur_level"))),
                    ],
                    // ファイルの認証状態
                    "is_approved" => [
                        "required",
                        "integer",
                        Rule::in(array_values(Config("const.image.approve_type"))),
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
            if ($route_name === "api.media.show") {
                $rules = [
                    "image_id" => [
                        "required",
                        "integer",
                        function ($attribute, $value, $fail) {
                            if (isset($this->administrator)) {
                                $image = Image::where("id", $value)->where("token", $this->route()->parameter("token"))->withTrashed()->get()->first();
                                if ($image === NULL) {
                                    $fail(":attributeが不正な値です｡");
                                }
                            } else {
                                $image = Image::where("id", $value)->where("token", $this->route()->parameter("token"))->get()->first();
                                if ($image === NULL) {
                                    $fail(":attributeが不正な値です｡");
                                }
                            }
                        }
                    ],
                    "width" => [
                        "integer",
                        "min:0",
                        "max:2048"
                    ]
                ];
            } else if ($route_name === "api.media.profile.images") {
                $rules = [
                    "member_id" => [
                        "required",
                        "integer",
                        Rule::exists("members", "id")->where(function($query) {
                            $query
                            ->where("is_registered", Config("const.binary_type.on"))
                            ->where("deleted_at", NULL);
                        })
                    ],
                    "security_token" => [
                        "required",
                        Rule::exists("members", "security_token")->where(function($query) {
                            $query
                            ->where("id", $this->route()->parameter("member_id"))
                            ->where("deleted_at", NULL);
                        })
                    ]
                ];
            } else if ($route_name === "web.admin.logout") {
                $rules = [];
            }
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            "member_id" => "メンバーID",
            "image_id" => "画像ID",
            "profile_image" => "プロフィール画像",
            "use_type" => "画像用途",
            "blur_level" => "ぼかしレベル",
            "is_approved" => "認証ステータス",
            "room_id" => "チャットルームID",
            "width" => "画像サイズ",
        ];
    }

    public function messages()
    {
        return [
            "member_id.required" => ":attributeは必須項目です。",
            "member_id.exists" => ":attributeが存在しません。",
            "member_id.integer" => ":attributeは正しいフォーマットで入力して下さい｡",
            "image_id.integer" => ":attributeは正しいフォーマットで入力して下さい｡",
            "profile_image.required" => ":attributeは必須項目です。",
            "profile_image.image" => ":attributeは画像形式のファイルを選択して下さい。",
            "profile_image.max" => ":attributeは5MB以下にして下さい。",
            "use_type.required" => "attributeは必須項目です。",
            "use_type.in" => "attributeには適切な値を設定して下さい。",
            "blur_level.required" => ":attributeは必須項目です",
            "blur_level.in" => "attributeには適切な値を設定して下さい。",
            "is_approved.required" => ":attributeは必須項目です",
            "is_approved.in" => ":attributeには適切な値を設定して下さい。",
            "room_id.required" => ":attributeは必須項目です",
            "room_id.exists" => "指定した:attributeが存在しません。",
            "width.integer" => ":attributeは数値で入力して下さい",
            "width.min" => ":attributeは0以上で入力して下さい｡",
            "width.max" => ":attributeは2048以下で入力して下さい｡",
        ];
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
