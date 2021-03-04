<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TopController extends Controller
{


    /**
     * 管理画面内TOPページ
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function index(Request $request, Response $response)
    {
        try {
        } catch (\Exception $e) {
            return view("admin.error", [
                "request" => $request,
            ]);
        }
    }
}
