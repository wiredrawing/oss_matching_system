@if(isset($member) && $member !== NULL)
<!-- 任意の会員情報を閲覧時 -->
<div class="admin_side_box">
    <a href="{{ action('Admin\\MemberController@detail', ['member_id' => $member->id]) }}">{{$member->display_name}}さんの詳細情報</a>
    <a href="{{ action('Admin\\MemberController@sendingLike', ['member_id' => $member->id]) }}">贈ったGood</a>
    <a href="{{ action('Admin\\MemberController@gettingLike', ['member_id' => $member->id]) }}">もらったGood</a>
    <a href="{{ action('Admin\\MemberController@matching', ['member_id' => $member->id]) }}">相互マッチ済み</a>
    <a href="{{ action('Admin\\MemberController@footprint', ['member_id' => $member->id]) }}">足跡</a>
    <a href="{{ action('Admin\\MemberController@visit', ['member_id' => $member->id]) }}">つけた足跡</a>
    <a href="{{ action('Admin\\MemberController@image', ['member_id' => $member->id]) }}">アップロード済み画像</a>
    <a href="{{ action('Admin\\MemberController@history', ['member_id' => $member->id]) }}">過去のログイン履歴</a>
</div>
<!--admin_side_box-->
@endif

<div class="admin_side_box">
    <a href="{{action("Admin\\MemberController@index")}}">会員一覧</a>
    <a href="{{action("Admin\\IdentityController@index")}}">身分証明書確認({{count($request->users_applying_identity)}})</a>
    <a href="{{action("Admin\\IncomeController@index")}}">収入証明書確認({{count($request->users_applying_income)}})</a>
    <a href="{{action("Admin\\ViolationController@index")}}">違反通報一覧</a>
    <a href="{{action("Admin\\PaymentController@index")}}">支払い確定済み履歴</a>
    <a href="{{action("Admin\\PaymentController@canceled")}}">解約済み履歴</a>
    <a href="{{action("Admin\\WithdrawController@index")}}">退会済み履歴</a>
</div>
<!--admin_side_box-->

<div class="admin_side_box">
    <a href="{{action("Admin\\StaffController@index")}}">管理スタッフ一覧</a>
    <a href="{{action("Admin\\StaffController@create")}}">管理スタッフ登録</a>
</div>
<div class="admin_side_box">
    <a href="{{action("Admin\\LoginController@logout")}}">ログアウト</a>
</div>
<!--admin_side_box-->
