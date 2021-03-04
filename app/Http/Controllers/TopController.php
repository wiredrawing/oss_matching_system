<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Library\RandomToken;
use App\Models\Room;
class TopController extends Controller
{


    /**
     * TOPページレンダリング
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function index(Request $request, Response $response)
    {
        return redirect()->action("Member\\IndexController@index");
    }
}
