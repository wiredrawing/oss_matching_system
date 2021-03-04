@include("admin.common.header")
<section class="wrapp">
    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->

    <div class="mypagewrap">
        <div class="mypagebox">
            <p class="tex_c">{{$request->administrator->display_name}}さんログイン中</p>
            <h2>{{$member->display_name}}さんのマッチ済みユーザー</h2>
        </div>
        <!--box-->
        @if ($matching_users !== NULL && $matching_users->count() > 0)
        <div class="mypagebox2 pt0">
            <table>
                <tr class="items">
                    <th>ID</th>
                    <td>ユーザー名</td>
                    <td>メッセージ</td>
                </tr>
                @foreach ($matching_users as $key => $value)
                <tr>
                    <th>{{ $value->id }}</th>
                    <td><a href="{{ action('Admin\\MemberController@detail', ['member_id' => $value->id]) }}">{{ $value->display_name }}</a></td>
                    <td>
                        <a href="{{ action("Admin\\MemberController@timeline", ["member_id" => $member->id, "target_member_id" => $value->id]) }}">
                            <button>メッセージ</button>
                        </a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

    </div>
    <!--mypagewrap-->
</section>
@include("admin.common.footer")
