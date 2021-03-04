<?php


return [

    // DBへのクエリエラー
    "CREATE_ERR" => "リソースの作成に失敗しました。",
    "UPDATE_ERR" => "リソースの更新処理に失敗しました。",
    "DELETE_ERR" => "リソースの削除に失敗しました。",
    "DUPLICATION_ERR" => "リクエスト内容が重複しています。",


    "CREATE_LIKE_ERR" => "Goodの送信に失敗しました。",

    // 仮登録失敗
    "CREATE_TEMP_ACCOUNT_ERR" => "仮登録処理に失敗しました。",

    // 表示用エラーメッセージ
    "AUTH_ERR" => "ユーザー認証に失敗しました。",
    "EXISTS_USER_ERR" => "既に存在するユーザー情報です｡",
    "NOT_FOUND_ERR" => "情報が見つかりませんでした。",
    "NOT_FOUND_USER_ERR" => "ユーザー情報が見つかりませんでした。",
    "NOT_FOUND_MATCHING_USERS_ERR" => "マッチング済みのユーザーが見つかりませんでした。",

    "EMAIL_ERR" => "リターンメールの送信に失敗しました。",
    "COMPLETED_REGISTERING_EMAIL_ERR" => "仮登録完了メールの送信に失敗しました。",
    "COMPLETED_REGISTERING_MEMBER_ERR" => "本登録完了メールの送信に失敗しました。",
    "COMPLETED_REISSUE_PASSWORD_ERR" => "パスワードリセット用URL発行完了メールの送信に失敗しました。",
    "INTERNAL_ERR" => "リクエスト内容が不正です。",

    // パスワード再発行失敗
    "RESET_PASSWORD_URL_ERR" => "パスワードリセット用URLの発行に失敗しました。",

    "BLOCK_USER_ERR" => "既にブロック済みです。",

    // 退会処理失敗時のエラー
    "FAILED_WITHDRAWING_ERR" => "退会処理に失敗しました｡",

    // 継続課金が持続している場合のエラー
    "IN_CONTRACT_ERR" => "退会前に､必ず有料プランを解約して下さい｡",

    // メールアドレスの更新失敗エラー
    "FAILED_UPDATING_EMAIL_ERR" => "メールアドレスの変更に失敗しました｡",

    "FAILED_LOGGING_IN_ERR" => "ログインできませんでした｡",
];
