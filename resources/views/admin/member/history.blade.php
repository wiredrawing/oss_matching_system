@include("admin.common.header")
<section class="wrapp">
    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->

    <div class="mypagewrap">
        <div class="mypagebox">
            <p class="tex_c">{{$request->administrator->display_name}}さんログイン中</p>
            <h2>{{$member->display_name}}さんの過去のログイン履歴</h2>
        </div>
        <!--box-->

        <div class="mypagebox2 pt0">
            @if (isset($member->member_logs) && $member->member_logs->count() > 0)
            <table>
                <tr class="items">
                    <th>ID</th>
                    <td>ログイン日時</td>
                    <td>アクション</td>
                </tr>
                @foreach ($member->member_logs as $key => $value)
                <tr>
                    <td>{{$value->id}}</td>
                    <td>
                        @if ($value->created_at !== NULL)
                        {{$value->created_at->format("Y年m月d日")}}<br>
                        {{$value->created_at->format("H時i分s秒")}}
                        @endif
                    </td>
                    <td>
                        @if ($value->login === 1)
                        ログイン
                        @else
                        ログアウト
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
            @endif
        </div>
    </div>
    <!--mypagewrap-->
</section>
@include("admin.common.footer")
