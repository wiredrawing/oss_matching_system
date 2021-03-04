<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Library\Logger;
use Illuminate\Http\Request;

class ConfigController extends Controller
{



    /**
     * 設定画面を表示
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        try {
            return view("member.config.index", [
                "request" => $request,
            ]);
        } catch (\Throwable $e) {
            Logger::error(__FILE__, $e);
            return view("member.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }
}
