<?php

use Illuminate\Database\Seeder;

class PricePlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // フリープラン
        DB::table("price_plans")->insert([
            "gender" => "M",
            "plan_code" => "free",
            "template_code" => "free",
            "duration" => 30,
            "name" => "無料会員プラン",
            "price" => 0,
            "is_displayed" => 0,
        ]);

        // スタンダード一ヶ月
        DB::table("price_plans")->insert([
            "gender" => "M",
            "plan_code" => "1day5980yen",
            "template_code" => "standard",
            "duration" => 1,
            "name" => "スタンダード1ヶ月プラン(テスト用一日)",
            "price" => 5980,
            "is_displayed" => 1,
        ]);

        // // スタンダード二ヶ月
        // DB::table("price_plans")->insert([
        //     "gender" => "M",
        //     "plan_code" => "STANDARD_60",
        //     "template_code" => "STANDARD",
        //     "duration" => 60,
        //     "name" => "スタンダード二ヶ月プラン",
        //     "price" => 4980,
        //     "is_displayed" => 1,
        // ]);

        // // スタンダード三ヶ月
        // DB::table("price_plans")->insert([
        //     "gender" => "M",
        //     "plan_code" => "STANDARD_90",
        //     "template_code" => "STANDARD",
        //     "duration" => 90,
        //     "name" => "スタンダード三ヶ月プラン",
        //     "price" => 3980,
        //     "is_displayed" => 1,
        // ]);
    }
}
