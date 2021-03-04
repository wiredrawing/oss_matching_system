<?php

// アプリケーション特有の設定ファイル

// 年リストを生成
$year_temp = range(date("Y") - 18, 1900);
$year_list = [];
$year_list[0] = "未設定";
foreach ($year_temp as $key => $value) {
    $year_list[$value] = $value."年";
}

// 月リストを生成
$month_temp = range(1, 12);
$month_list = [];
$month_list[0] = "未設定";
foreach ($month_temp as $key => $value) {
    $month_list[$value] = $value."月";
}

// 日リストを生成
$day_temp = range(1, 31);
$day_list = [];
$day_list[0] = "未設定";
foreach ($day_temp as $key => $value) {
    $day_list[$value] = $value."日";
}

// 身長リストを生成
$height_temp = range(120, 220);
$height_list = [];
$height_list[0] = "未設定";
foreach ($height_temp as $key => $value) {
    $height_list[$value] = $value."cm";
}
$bottom_height = [];
foreach ($height_temp as $key => $value) {
    $bottom_height[$value] = $value."cm以上";
}
$bottom_height[0] = "未設定";
$top_height = [];
foreach ($height_temp as $key => $value) {
    $top_height[$value] = $value."cm以下";
}
$top_height[0] = "未設定";
ksort($bottom_height);
ksort($top_height);


// 検索時年齢一覧
$ages = range(18, 70);
// 新規登録時以下より年齢選択
$age_list = [];
foreach ($ages as $key => $value) {
    $age_list[$value] = $value."歳";
}
// 年齢検索下限設定
$bottom_ages = [];
foreach ($ages as $key => $value) {
    $bottom_ages[$value] = $value."歳以上";
}
// 年齢検索上限設定
$bottom_ages[0] = "未設定";
$top_ages = [];
foreach ($ages as $key => $value) {
    $top_ages[$value] = $value."歳以下";
}
$top_ages[0] = "未設定";
ksort($bottom_ages);
ksort($top_ages);

return [
    "domain" => "matching-system.jp",
    // html上に必要なメタタグ
    "meta" => [
        "title" => env("APP_TITLE", "既婚者だけのリアルなマッチングは/既婚者マッチングStyle"),
        "description" => env("APP_DESCRIPTION", "ワンランク上の既婚者だけの定額制マッチングサイト。【既婚者同士】同じ境遇だからマッチング率が高い。簡単でシンプル。登録料もダウンロードも不要/24時間監視で悪質ユーザーを排除。ご近所さんとも遠方さんとも出会えるマッチングサイトです。"),
        "keywords" => env("APP_KEYWORD", "既婚者,マッチングサイト,ワンランク上,定額制マッチングサイト"),
    ],
    // バイナリタイプ
    "binary_type" => [
        "on" => 1,
        "off" => 0,
    ],
    "binary_type_name" => [
        1 => "有効",
        0 => "無効",
    ],
    "registering_status" => [
        0 => "仮登録",
        1 => "本登録"
    ],
    "withdrawal" => [
        1 => "会員数が少ない",
        2 => "使いづらい",
        3 => "お目当ての相手がいない",
        4 => "料金が高い",
        5 => "出会えたから",
        6 => "いったん退会する",
    ],
    // 性別リスト
    "gender" => [
        "M" => "男性",
        "F" => "女性",
    ],
    // Eloquentのlimit句の指定
    "limit" => 20,
    // imagesテーブルのuse_typeカラムを指定
    "image" => [
        "use_type_name" => [
            0 => "未設定",
            1 => "タイムライン",
            2 => "本人認証申請",
            3 => "プロフィール画像",
            4 => "収入証明"
        ],
        "use_type" => [
            "none" => 0,
            "timeline" => 1,
            "identity" => 2,
            "profile" => 3,
            "income" => 4,
        ],
        // imagesテーブルのis_approvedカラムの状態
        "approve_type_name" => [
            0 => "未認証",
            1 => "申請中",
            2 => "承認拒否",
            3 => "承認済み",
        ],
        "approve_type" => [
            "none" => 0,
            "applying" => 1,
            "rejected" => 2,
            "authenticated" => 3,
        ],
        // 同一ディレクトリ内に保存できるファイル限度数
        "directory_max" => 30000,
        // 画像のぼかしレベル
        "blur_level" => [
            0 => "ぼかさない",
            30 => "小",
            70 => "中",
            100 => "大"
        ],
        // 画像の圧縮率
        "compression" => 70,
        "max_width" => 1024,
        "thumbnail_width" => 320,
    ],

    "email" => [
        // メール送信時のタイトル
        "title" => [
            "COMPLETED_REGISTERING_EMAIL" => "仮登録が完了しました。",
            "COMPLETED_REGISTERING_MEMBER" => "本登録処理が完了しました。",
            "COMPLETED_REISSUE_PASSWORD" => "パスワード再発行用URLを発行しました。",
            "COMPLETED_SENDING_URL_TO_UPDATE_EMAIL" => "メールアドレスの変更申請確認通知を発行しました",
            "GOT_MESSAGE" => "異性ユーザーからメッセージを受信しました",
            "GOT_IMAGE" => "異性ユーザーから写真を受信しました｡",
        ]
    ],

    // 通知系選択肢
    "notification" => [
        0 => "受信しない",
        1 => "受信する",
    ],

    // 年齢制限
    "minimum_age" => 18,
    // 新規登録時年齢設定
    "age_list" => $age_list,
    // 検索条件 年齢下限
    "bottom_ages" => $bottom_ages,
    // 検索条件 年齢上限
    "top_ages" => $top_ages,

    // 生成可能なトークンサイズ
    "max_token_length" => 256,

    // 都道府県リスト
    "prefecture" => [
        0 => "未設定",
        1 => "北海道",
        2 => "青森",
        3 => "岩手",
        4 => "宮城",
        5 => "秋田",
        6 => "山形",
        7 => "福島",
        8 => "茨城",
        9 => "栃木",
        10 => "群馬",
        11 => "埼玉",
        12 => "千葉",
        13 => "東京",
        14 => "神奈川",
        15 => "新潟",
        16 => "富山",
        17 => "石川",
        18 => "福井",
        19 => "山梨",
        20 => "長野",
        21 => "岐阜",
        22 => "静岡",
        23 => "愛知",
        24 => "三重",
        25 => "滋賀",
        26 => "京都",
        27 => "大阪",
        28 => "兵庫",
        29 => "奈良",
        30 => "和歌山",
        31 => "鳥取",
        32 => "島根",
        33 => "岡山",
        34 => "広島",
        35 => "山口",
        36 => "徳島",
        37 => "香川",
        38 => "愛媛",
        39 => "高知",
        40 => "福岡",
        41 => "佐賀",
        42 => "長崎",
        43 => "熊本",
        44 => "大分",
        45 => "宮崎",
        46 => "鹿児島",
        47 => "沖縄",
        48 => "海外",
    ],

    // 血液型リスト
    "blood_type" => [
        0 => "未設定",
        1 => "A",
        2 => "B",
        3 => "AB",
        4 => "O",
    ],

    // 年収
    "salary" => [
        0 => "未設定",
        1 => "500万以下",
        2 => "500万～800万",
        3 => "800万～1000万",
        4 => "1000万～2000万",
        5 => "2000万～",
    ],

    // 体型(見た目)
    "body_style" => [
        "M" => [
            0 => "未設定",
            1 => "スリム",
            2 => "筋肉質",
            3 => "普通",
            4 => "ぽっちゃり",
            5 => "大柄",
            6 => "セクシー",
        ],
        "F" => [
            0 => "未設定",
            1 => "スレンダー",
            2 => "普通",
            3 => "モデル体型",
            4 => "ぽっちゃり",
            5 => "グラマー",
        ]
    ],

    // 休日
    "day_off" => [
        0 => "未設定",
        1 => "土日",
        2 => "平日",
        3 => "不定期",
    ],

    // 子供の有無
    "children" => [
        0 => "未設定",
        1 => "子あり",
        2 => "子なし",
    ],

    // 飲酒
    "alcohol" => [
        0 => "未設定",
        1 => "飲まない",
        2 => "飲む",
        3 => "時々飲む",
    ],

    // 喫煙
    "smoking" => [
        0 => "未設定",
        1 => "吸わない",
        2 => "吸う",
        3 => "時々吸う",
        4 => "相手に合わせる",
    ],

    // 喫煙
    "pet" => [
        0 => "未設定",
        1 => "飼っている",
        2 => "飼ってない",
    ],

    // 職種一覧
    "job_type" => [
        0 => "未設定",
        1 => "会社員",
        2 => "大手勤務",
        3 => "医者",
        4 => "士業",
        5 => "経営者・役員",
        6 => "上場企業",
        7 => "自由業",
    ],

    // パートナー
    "partner" => [
        0 => "未設定",
        1 => "既婚",
        2 => "バツイチ",
    ],

    // ブラックリスト
    "blacklist" => [
        0 => "非ブラック",
        1 => "ブラック",
    ],

    // 身長
    "height" => $height_list,

    // 検索時下限身長
    "bottom_height" => $bottom_height,

    // 検索時上限身長
    "top_height" => $top_height,

    // 年
    "year" => $year_list,

    // 月
    "month" => $month_list,

    // 日
    "day" => $day_list,

    // タイムラインバリエーション
    "timeline" => [
        "message" => 1,
        "image" => 2,
        "url" => 3,
    ],
    "action" => [
        "like" => 1,
        "match" => 2,
        "message" => 3,
    ],
    "action_message" => [
        1 => "からGoodを受け取りました。",
        2 => "とマッチが成立しました。",
        3 => "からのメッセージを受信しました。",
    ],

    // 決済サービス側の情報
    "telecom" => [
        "clientip" => "00146",
        // 退会処理用URL
        "withdrawal_url" => "https://secure.telecomcredit.co.jp/inetcredit/secure/member.pl",
        // 継続決済用URL
        "subscribe_url" => "https://secure.telecomcredit.co.jp/inetcredit/secure/order.pl",
        // クレジットサーバーへのレスポンスメッセージ
        "message" => "SuccessOK",
        "ng" => "NG",
    ],

    // 違反項目
    "violation_list" => [
        1 => "金銭トラブル",
        2 => "業者(勧誘や風俗)",
        3 => "詐欺",
        4 => "ストーカー",
        5 => "売春要求",
        6 => "18歳未満",
        7 => "暴力や暴言",
        8 => "当日キャンセル",
        9 => "その他",
    ],
    // ユーザー退会後､2年間は画面表示させる
    "loss_time" => 60 * 60 *24 * 365 * 2,
];
