パスワード再発行のためのメールアドレス申請フォームへのお問い合わせ
誠にありがとうございます。

以下のURLより、パスワードを登録し直して下さいませ。

▼パスワード再発行URL
{{action("PasswordController@update", [
    "token" => $token,
])}}
