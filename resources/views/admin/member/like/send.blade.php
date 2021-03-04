@include("admin.common.header")
<section class="wrapp">
    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->

    <div class="mypagewrap">
        <div class="mypagebox">
            <p class="tex_c">{{$request->administrator->display_name}}さんログイン中</p>
            <h2>{{$member->display_name}}さんが贈ったGood</h2>
        </div>
        <!--box-->
        <div class="mypagebox2 pt0">
            @if ($likes->count() > 0)
            <table>
                <tr class="items">
                    <th>ID</th>
                    <td>ユーザー名</td>
                    <td>Goodした日</td>
                    <td>詳細</td>
                </tr>
                @foreach ($likes as $key => $value)
                <tr>
                    <th>{{$value->to_member->id}}</th>
                    <td>{{$value->to_member->display_name}}</td>
                    <td>
                        @if ($value->created_at !== NULL)
                        {{ $value->created_at->format('Y年m月d日') }}<br>
                        {{ $value->created_at->format('H時i分s秒') }}
                        @endif
                    </td>
                    <td>
                        <a href="{{action("Admin\\MemberController@detail", ["member_id" => $value->to_member->id])}}">
                            <button>詳細へ</button>
                        </a>
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
