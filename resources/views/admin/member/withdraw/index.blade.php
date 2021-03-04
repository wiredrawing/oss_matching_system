@include("admin.common.header")
<section class="wrapp">

    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->

    <div class="mypagewrap">

        <div class="mypagebox">
            <p class="tex_c">{{$request->administrator->display_name}}さんログイン中</p>
            <h2 class="mb32">本サイト退会済み履歴</h2>
            <p>既に退会済みのユーザーのみ一覧表示</p>
        </div>
        <div class="mypagebox2">
            <table>
                <tr class="items">
                    <th>ID</th>
                    <td>会員ID</td>
                    <td>退会事由</td>
                    <td>退会時メッセージ</td>
                    <td>退会日時</td>
                </tr>
                @foreach ($withdrawal_logs as $key => $value)
                <tr>
                    <th>{{$value->id}}</th>
                    <td>
                      ({{$value->member->id}})
                      <a href="{{action("Admin\\MemberController@detail", ["member_id" => $value->member->id])}}">
                      {{$value->member->display_name}}
                      </a>
                    </td>
                    <td>{{Config("const.withdrawal")[$value->withdrawal]}}</td>
                    <td>{{$value->opinion}}</td>
                    <td>
                      {{$value->created_at->format("Y年m月d日")}}<br>
                      {{$value->created_at->format("H時i分")}}
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        <!--box-->

        @if ($withdrawal_logs->hasPages())
        <div class="nextprev">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($withdrawal_logs->onFirstPage())
                    <span class="disabled" aria-disabled="true">&lt;&lt;前へ</span>
                @else
                    <a href="{{ $withdrawal_logs->previousPageUrl() }}" rel="prev">&lt;&lt;前へ</a>
                @endif

                {{-- Next Page Link --}}
                @if ($withdrawal_logs->hasMorePages())
                    <a href="{{ $withdrawal_logs->nextPageUrl() }}" rel="next">次へ&gt;&gt;</a>
                @else
                    <span class="disabled" aria-disabled="true">次へ&gt;&gt;</span>
                @endif
            </ul>
        </div>
        @endif
    </div>
    <!--mypagewrap-->
</section>
@include("admin.common.footer")
