<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ViolationRequest;
use App\Models\ViolationReport;
use Illuminate\Http\Request;

class ViolationController extends Controller
{






    /**
     * 現在報告を受けた､違反報告一覧
     *
     * @param ViolationRequest $request
     * @return void
     */
    public function index (ViolationRequest $request)
    {
        try {
            $violation_list = Config("const.violation_list");
            $violations = ViolationReport::with([
                "from_member" => function ($query) {
                    $query->withTrashed();
                },
                "to_member" => function ($query) {
                    $query->withTrashed();
                },
                "violation_categories",
            ])
            ->whereHas("from_member")
            ->whereHas("to_member")
            ->paginate(Config("const.limit"));

            foreach ($violations as $key => $value) {
                $categories = [];
                foreach ($value->violation_categories as $ink => $inv) {
                    $categories[] = $violation_list[$inv->category_id];
                }
                $value->categories = $categories;
            }

            return view("admin.violation.index", [
                "request" => $request,
                "violations" => $violations,
            ]);
        } catch (\Throwable $e) {
            return view("admin.errors.index", [
                "request" => $request,
                "error" => $e,
            ]);
        }
    }
}
