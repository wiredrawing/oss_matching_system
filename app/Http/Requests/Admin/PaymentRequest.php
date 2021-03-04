<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PaymentRequest extends FormRequest
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

            if ($route_name === "web.admin.member.payment.index") {
                $rules = [
                    "keyword" => [
                        "between:0,128",
                    ]
                ];
            } else if($route_name === "web.admin.member.payment.canceled") {
                $rules = [
                    "keyword" => [
                        "between:0,128",
                    ]
                ];
            }
        }

        return $rules;
    }

    // //エラー時HTMLページにリダイレクトされないようにオーバーライド
    // protected function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(
    //         response()->json(
    //             $validator->errors(),
    //             422
    //         )
    //     );
    // }

    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }

}
