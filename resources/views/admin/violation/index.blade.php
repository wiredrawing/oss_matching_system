@include("admin.common.header")
<section class="wrapp">
    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->


    <div class="mypagewrap">

        <div class="mypagebox">
            <p class="tex_c">{{$request->administrator->display_name}}さんログイン中</p>
            <h2 class="mb32">違反通報一覧履歴</h2>
            <p>報告のあった違反通報一覧を表示</p>
        </div>

        <div class="mypagebox2">
            <p class="tex_c">{{$request->administrator->display_name}}さんログイン中</p>

            <table>
                <tr class="items">
                    <th>ID</th>
                    <td>違反者名</td>
                    <td>報告者名</td>
                    <td>報告内容</td>
                    <td>違反項目</td>
                    <td>報告日時</td>
                </tr>
                @foreach ($violations as $key => $value)
                    <tr>
                        <th>{{ $value->id }}</th>
                        <td>
                            <a href="{{ action('Admin\\MemberController@detail', ['member_id' => $value->to_member->id]) }}">
                                <button>{{ $value->to_member->display_name }}</button>
                            </a>
                        </td>
                        <td>
                            <a href="{{ action('Admin\\MemberController@detail', ['member_id' => $value->from_member->id]) }}">
                                <button>{{ $value->from_member->display_name }}</button>
                            </a>
                        </td>
                        <td>{{ $value->message }}</td>
                        <td>{!! join("<br>", $value->categories)!!}
                        <td>
                            @if ($value->created_at !== null)
                                {{ $value->created_at->format('Y年n月j日') }}<br>
                                {{ $value->created_at->format('H時i分s秒') }}<br>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        <!--box-->
        @if ($violations->hasPages())
        <div class="nextprev">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($violations->onFirstPage())
                    <span class="disabled" aria-disabled="true">&lt;&lt;前へ</span>
                @else
                    <a href="{{ $violations->previousPageUrl() }}" rel="prev">&lt;&lt;前へ</a>
                @endif

                {{-- Next Page Link --}}
                @if ($violations->hasMorePages())
                    <a href="{{ $violations->nextPageUrl() }}" rel="next">次へ&gt;&gt;</a>
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
