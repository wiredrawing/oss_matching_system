<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StaticPageController extends Controller
{




    /**
     * 特定商取引法に基づく表記
     *
     * @param Request $request
     * @return void
     */
    public function info(Request $request)
    {
        return view("static.info", [
            "request" => $request,
        ]);
    }

    /**
     * プライバシーポリシー
     *
     * @param Request $request
     * @return void
     */
    public function privacy(Request $request)
    {
        return view("static.privacy", [
            "request" => $request,
        ]);
    }

    /**
     * 会員規約
     *
     * @param Request $request
     * @return void
     */
    public function terms(Request $request)
    {
        return view("static.terms", [
            "request" => $request,
        ]);
    }
}
