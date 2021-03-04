<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Rules\CreditCard;

class BaseSearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [];
        $method = strtoupper($this->getMethod());
        $route_name = Route::currentRouteName();

        if ($method === "GET") {

            if ($route_name === "web.member.search.list") {
                $rules = [
                    "from_age" => [
                        "integer",
                        Rule::in(array_keys(Config("const.bottom_ages")))
                    ],
                    "to_age" => [
                        "integer",
                        Rule::in(array_keys(Config("const.top_ages")))
                    ],
                    "bottom_height" => [
                        "integer",
                        Rule::in(array_keys(Config("const.bottom_height")))
                    ],
                    "top_height" => [
                        "integer",
                        Rule::in(array_keys(Config("const.top_height")))
                    ],
                    "body_style" => [
                        "integer",
                    ],
                    "prefecture" => [
                        "integer",
                        Rule::in(array_keys(Config("const.prefecture"))),
                    ],
                    "job_type" => [
                        "integer",
                        Rule::in(array_keys(Config("const.job_type"))),
                    ],
                    "children" => [
                        "integer",
                        Rule::in(array_keys(Config("const.children"))),
                    ],
                    "day_off" => [
                        "integer",
                        Rule::in(array_keys(Config("const.day_off"))),
                    ],
                    "alcohol" => [
                        "integer",
                        Rule::in(array_keys(Config("const.alcohol"))),
                    ],
                    "smoking" => [
                        "integer",
                        Rule::in(array_keys(Config("const.smoking"))),
                    ],
                    "partner" => [
                        "integer",
                        Rule::in(array_keys(Config("const.partner"))),
                    ],
                    "pet" => [
                        "integer",
                        Rule::in(array_keys(Config("const.pet"))),
                    ],
                    "blood_type" => [
                        "integer",
                        Rule::in(array_keys(Config("const.blood_type"))),
                    ],
                    "salary" => [
                        "integer",
                        Rule::in(array_keys(Config("const.salary"))),
                    ],
                    "keyword" => [
                        "between:0,246",
                        new CreditCard(),
                    ]
                ];
            }
        }
        return $rules;
    }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
