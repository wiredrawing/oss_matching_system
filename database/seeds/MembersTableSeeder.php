<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MembersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        // 新規登録に必要なパラメータ
        $prefecture = array_keys(Config("const.prefecture"));
        $job_type = array_keys(Config("const.job_type"));
        $gender = array_keys(Config("const.gender"));
        $height = array_keys(Config("const.height"));
        $children = array_keys(Config("const.children"));
        $day_off = array_keys(Config("const.day_off"));
        $alcohol = array_keys(Config("const.alcohol"));
        $smoking = array_keys(Config("const.smoking"));
        // $year = Config("const.year");
        // $month = Config("const.month");
        // $day = Config("const.day");
        $partner = array_keys(Config("const.partner"));
        $pet = array_keys(Config("const.pet"));
        $blood_type = array_keys(Config("const.blood_type"));
        $salary = array_keys(Config("const.salary"));
        $body_style = array_keys(Config("const.body_style")["M"]);
        $age_list = range(18, 70);
        for($i = 1; $i <= 500; $i++) {
            shuffle($prefecture);
            shuffle($job_type);
            shuffle($height);
            shuffle($children);
            shuffle($day_off);
            shuffle($alcohol);
            shuffle($smoking);
            shuffle($partner);
            shuffle($pet);
            shuffle($blood_type);
            shuffle($salary);
            shuffle($body_style);
            shuffle($age_list);
            DB::table("members")->insert([
                "display_name" => "男性{$i}",
                "age" => $age_list[0],
                "gender" => "M",
                "height" => $height[0],
                "body_style" => $body_style[0],
                "children" => $children[0],
                "day_off" => $day_off[0],
                "alcohol" => $alcohol[0],
                "smoking" => $smoking[0],
                "message" => "男性{$i}。",
                "notification_good" => 0,
                "notification_message" => 1,
                "blood_type" => $blood_type[0],
                "pet" => $pet[0],
                "salary" => $salary[0],
                "partner" => $partner[0],
                "plan_code" => "free",
                "valid_period" => NULL,
                "email" => "man{$i}@dummy.gmail.com",
                "password" => password_hash("AAAaaa123", PASSWORD_DEFAULT),
                "token" => NULL,
                "security_token" => NULL,
                "expired_at" => NULL,
                "prefecture" => $prefecture[0],
                "job_type" => $job_type[0],
                "is_blacklisted" => 0,
                "last_login" => NULL,
                "credit_id" => NULL,
                "credit_password" => NULL,
                "start_payment_date" => NULL,
                "is_registered" => Config("const.binary_type.on"),
                "memo" => "管理者用メモ帳",
                "is_approved" => 0,
                "approved_image_id" => NULL,
                "income_certificate" => 0,
                "income_image_id" => NULL,
            ]);
        }

        for($i = 1; $i <= 500; $i++) {
            shuffle($prefecture);
            shuffle($job_type);
            shuffle($height);
            shuffle($children);
            shuffle($day_off);
            shuffle($alcohol);
            shuffle($smoking);
            shuffle($partner);
            shuffle($pet);
            shuffle($blood_type);
            shuffle($salary);
            shuffle($body_style);
            shuffle($age_list);
            DB::table("members")->insert([
                "display_name" => "女性{$i}",
                "gender" => "F",
                "age" => $age_list[0],
                "height" => $height[0],
                "body_style" => $body_style[0],
                "children" => $children[0],
                "day_off" => $day_off[0],
                "alcohol" => $alcohol[0],
                "smoking" => $smoking[0],
                "message" => "女性{$i}女性{$i}女性{$i}女性{$i}女性{$i}女性4。",
                "notification_good" => 0,
                "notification_message" => 1,
                "blood_type" => $blood_type[0],
                "pet" => $pet[0],
                "salary" => $salary[0],
                "partner" => $partner[0],
                "plan_code" => "free",
                "valid_period" => NULL,
                "email" => "woman{$i}@dummy.gmail.com",
                "password" => password_hash("AAAaaa123", PASSWORD_DEFAULT),
                "token" => NULL,
                "security_token" => NULL,
                "expired_at" => NULL,
                "prefecture" => $prefecture[0],
                "job_type" => $job_type[0],
                "is_blacklisted" => 0,
                "last_login" => NULL,
                "credit_id" => NULL,
                "credit_password" => NULL,
                "start_payment_date" => NULL,
                "is_registered" => Config("const.binary_type.on"),
                "memo" => "管理者用メモ帳",
                "is_approved" => 0,
                "approved_image_id" => NULL,
                "income_certificate" => 0,
                "income_image_id" => NULL,
            ]);
        }
    }
}
