@include("admin.common.header")
<section class="wrapp">
    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->

    <div class="mypagewrap">
        <div class="mypagebox">
            <p class="tex_c">{{$request->administrator->display_name}}さんログイン中</p>
            <h2>{{$member->display_name}}さんにつけられた足跡</h2>
        </div>
        <!--box-->
        <div class="mypagebox2 pt0">
            @if (isset($footprints) && $footprints->count() > 0)
            <table>
                <tr class="items">
                    <th>ID</th>
                    <td>ユーザー名</td>
                    <td>足跡がついた日</td>
                </tr>
                @foreach ($footprints as $key => $value)
                <tr>
                    <th>{{$value->from_member->id}}</th>
                    <td><a href="{{action("Admin\\MemberController@detail", ["member_id" => $value->from_member->id])}}">
                        {{$value->from_member->display_name}}</a>
                    </td>
                    <td>
                        @if ($value->created_at !== NULL)
                        {{$value->created_at->format("Y-m-j H:i:s")}}
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
