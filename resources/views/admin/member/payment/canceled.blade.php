@include("admin.common.header")
<section class="wrapp">

    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->

    <div class="mypagewrap">

        <div class="mypagebox">
            <p class="tex_c">{{$request->administrator->display_name}}さんログイン中</p>
            <h2 class="mb32">有料プラン解約済み履歴</h2>
            <p>ここは、クレジット決済が解約完了されたデータ全てを表示</p>
        </div>
        <div class="mypagebox2">
            <table>
                <tr class="items">
                    <th>ID</th>
                    <td>会員ID</td>
                    <td>決済ID</td>
                    <td>決済金額</td>
                    <td>決済プラン</td>
                    <td>決済回数</td>
                    <td>決済日時</td>
                </tr>
                @foreach ($canceled_logs as $key => $value)
                <tr>
                    <th>{{$value->id}}</th>
                    <td>
                      ({{$value->member->id}})
                      <a href="{{action("Admin\\MemberController@detail", ["member_id" => $value->member->id])}}">
                      {{$value->member->display_name}}
                      </a>
                    </td>
                    <td>{{$value->credit_id}}</td>
                    <td>{{$value->payment_log->money}}</td>
                    <td>{{$value->payment_log->price_plan->name}}</td>
                    <td>{{$value->payment_log->settle_count}}</td>
                    <td>
                      {{$value->created_at->format("Y年m月d日")}}<br>
                      {{$value->created_at->format("H時i分")}}
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
        <!--box-->

        @if ($canceled_logs->hasPages())
        <div class="nextprev">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($canceled_logs->onFirstPage())
                    <span class="disabled" aria-disabled="true">&lt;&lt;前へ</span>
                @else
                    <a href="{{ $canceled_logs->previousPageUrl() }}" rel="prev">&lt;&lt;前へ</a>
                @endif

                {{-- Next Page Link --}}
                @if ($canceled_logs->hasMorePages())
                    <a href="{{ $canceled_logs->nextPageUrl() }}" rel="next">次へ&gt;&gt;</a>
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
