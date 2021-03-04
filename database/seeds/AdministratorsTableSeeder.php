<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdministratorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 管理者権限アカウント
        DB::table("administrators")->insert([
            "email" => "dummy@gmail.com",
            "password" => password_hash("AAAaaa123", PASSWORD_DEFAULT),
            "display_name" => "最高権限用テストアカウント",
            "is_displayed" => Config("const.binary_type.on"),
            "permission_level" => 1,
            "last_login" => (new \DateTime())->format("Y-n-j H:i:s"),
        ]);

        // 閲覧権限のみアカウント
        DB::table("administrators")->insert([
            "email" => "view@gmail.com",
            "password" => password_hash("AAAaaa123", PASSWORD_DEFAULT),
            "display_name" => "閲覧専用テストアカウント",
            "is_displayed" => Config("const.binary_type.on"),
            "permission_level" => 1,
            "last_login" => (new \DateTime())->format("Y-n-j H:i:s"),
        ]);
    }
}
