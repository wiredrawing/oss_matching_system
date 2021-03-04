<?php


return [
    // 入力フォームに表示させるダミーアドレス
    "dummy_address" => env("DUMMY_ADDRESS", "sample@matching-system.jp"),

    // アプリケーションから送信されるメールのFromアドレス
    "mail_from_address" => env("MAIL_FROM_ADDRESS", "noreply@matching-system.jp"),

    // CC
    "mail_cc" => env("MAIL_CC", "noreply@matching-system.jp"),

    // BCC
    "mail_bcc" => env("MAIL_BCC", "noreply@matching-system.jp"),
];
