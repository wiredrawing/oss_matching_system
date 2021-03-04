<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CreditCard implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $value = mb_convert_kana($value, "rn", "UTF-8");
        $credit_regex = '/(4[0-9]{12}(?:[0-9]{3})?|' // visa
        .'5[1-5][0-9]{14}|' // master card
        .'3[47][0-9]{13}|' //American express
        .'3(?:0[0-5]|[68][0-9])[0-9]{11}|' // Diners club
        .'6(?:011|5[0-9]{2})[0-9]{12}|' // discover
        .'(?:2131|1800|35\d{3})\d{11})/'; // jcb
        if (preg_match($credit_regex, $value) !== 1) {
            // クレジットカードにマッチしていない場合のみ、trueを返却する
            return true;
        }
        logger()->error("入力内容に、クレジットカード番号に類似した内容がPOSTされました。");
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attributeにクレジットカード番号に類似したメッセージが含まれています。';
    }
}
