<?php

namespace App\Http\Controllers\Member;

use App\Library\Logger;
use App\Common\CommonFootprint;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FootprintController extends Controller
{


    public function index(Request $request)
    {
        try {
            //print_r($request->excluded_users);
            // 変数名は複数形とする
            $footprints = CommonFootprint::getFootprints($request->member->id, $request->excluded_users);

            // 足跡件数チェック
            if ($footprints->count() === 0) {
                throw new \Exception("足跡はありません｡");
            }

            $check_footprints = CommonFootprint::checkFootprints($request->member->id);
            return view("member.footprint.index", [
                "request" => $request,
                "footprints" => $footprints
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
