<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    protected $attributes = [
        "is_blacklisted" => 0,
        "is_registered" => 0,
    ];

    protected $dates = [
        "valid_period",
        "start_payment_date",
        "last_login",
        // "birthday",
    ];

    public $primaryKey = "id";

    protected $fillable = [
        "id",
        "display_name",
        "age",
        // "birthday",
        "gender",
        "height",
        "body_style",
        "children",
        "day_off",
        "alcohol",
        "smoking",
        "message",
        "notification_good",
        "notification_message",
        "blood_type",
        "pet",
        "salary",
        "partner",
        "plan_code",
        "valid_period",
        "email",
        "deleted_email",
        "password",
        "token",
        "security_token",
        "expired_at",
        "prefecture",
        "job_type",
        "is_blacklisted",
        "last_login",
        "credit_id",
        "credit_password",
        "start_payment_date",
        "is_registered",
        "memo",
        "is_approved", // 本人確認承認済みフラグ
        "approved_image_id",
        "income_certificate", // 収入証明承認用
        "income_image_id",
    ];

    // protected $appends = [
    //     "year",
    //     "month",
    //     "day",
    // ];

    // // 任意の動的プロパティ(年)
    // public function getYearAttribute() {
    //     if (isset($this->birthday)) {
    //         return $this->birthday->format("Y");
    //     }
    //     return 0;
    // }
    // // 任意の動的プロパティ(月)
    // public function getMonthAttribute() {
    //     if (isset($this->birthday)) {
    //         return $this->birthday->format("n");
    //     }
    //     return 0;
    // }
    // // 任意の動的プロパティ(月)
    // public function getDayAttribute() {
    //     if (isset($this->birthday)) {
    //         return $this->birthday->format("j");
    //     }
    //     return 0;
    // }

    // 当該ユーザーがアップした関連画像一覧を取得する
    public function images()
    {
        return $this->hasMany(Image::class, "member_id", "id");
    }

    /**
     * 現在申請中､承認済みの本人確認用画像を取得する
     *
     * @return void
     */
    public function identity_image()
    {
        return $this->hasOne(Image::class, "member_id", "id")
            ->where("use_type", Config("const.image.use_type.identity"))
            ->orderBy("created_at", "desc");
    }

    public function income_image()
    {
        return $this->hasOne(Image::class, "member_id", "id")
        ->where("use_type", Config("const.image.use_type.income"))
        ->orderBy("created_at", "desc");
    }

    /**
     * アップロード済みのプロフィール画像一覧を取得
     *
     * @return void
     */
    public function profile_images()
    {
        return $this->hasMany(Image::class, "member_id", "id")
            ->where("use_type", Config("const.image.use_type.profile"))
            ->orderBy("created_at", "asc");
            // ->limit(3);
    }

    /**
     * 当該ユーザーが拒否しているユーザー(自身が拒否しているユーザー)
     *
     * @return void
     */
    public function sending_decline()
    {
        return $this->hasMany(Decline::class, "from_member_id", "id");
    }

    /**
     * 当該ユーザーを拒否しているユーザー(自身を拒否しているユーザー)
     *
     * @return void
     */
    public function getting_decline()
    {
        return $this->hasMany(Decline::class, "to_member_id", "id");
    }

    /**
     * 自身が贈ったGood
     *
     * @return void
     */
    public function sending_likes()
    {
        return $this->hasMany(Like::class, "from_member_id", "id");
    }

    /**
     * 自身がもらったGood
     *
     * @return void
     */
    public function getting_likes()
    {
        return $this->hasMany(Like::class, "to_member_id", "id");
    }

    public function to_footprints()
    {
        return $this->hasMany(Footprint::class, "to_member_id", "id");
    }

    public function from_footprints()
    {
        return $this->hasMany(Footprint::class, "from_member_id", "id");
    }

    // 自身が送信した、メッセージ投稿を取得
    public function from_timelines()
    {
        return $this->hasMany(Timeline::class, "from_member_id", "id");
    }

    // 自身に送信されたメッセージ投稿を取得
    public function to_timelines()
    {
        return $this->hasMany(Timeline::class, "to_member_id", "id")->orderBy("updated_at", "desc");
    }

    // 現在の有料プラン契約状況
    public function price_plan()
    {
        return $this->hasOne(PricePlan::class, "plan_code", "plan_code");
    }

    // 過去のログイン履歴を取得
    public function member_logs()
    {
        return $this->hasMany(MemberLog::class, "member_id", "id");
    }
}
