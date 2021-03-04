@include("admin.common.header")
<section class="wrapp">
    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->

    <div class="mypagewrap">
        <div class="mypagebox2">
            <p class="tex_c">{{$request->administrator->display_name}}さんログイン中</p>
            {{-- {{ Form::open([
                'url' => action('Admin\\StaffController@index'),
                'method' => 'GET',
            ]) }}
            {{ Form::input('text', 'email', $request->email, [
                'placdholder' => '検索',
            ]) }}
            {{ Form::close() }} --}}
            <table>
                <tr class="items">
                    <th>ID</th>
                    <td>管理者名</td>
                    <td>Email</td>
                    <td>ログイン可否</td>
                    <td>登録日<br>更新日</td>
                    <td>最終ログイン</td>
                    <td>編集</td>
                </tr>
                @foreach ($administrators as $key => $value)
                    <tr>
                        <th>{{ $value->id }}</th>
                        <td>{{ $value->display_name }}</td>
                        <td>{{ $value->email }}</td>
                        <td>{{ Config("const.binary_type_name")[$value->is_displayed] }}</td>
                        <td>
                            @if ($value->created_at !== null)
                                {{ $value->created_at->format('Y年n月j日') }}<br>
                                {{ $value->created_at->format('H時i分s秒') }}<br>
                            @endif
                            @if ($value->updated_at !== null)
                                {{ $value->updated_at->format('Y年n月j日') }}<br>
                                {{ $value->updated_at->format('H時i分s秒') }}<br>
                            @endif
                        </td>
                        <td>
                            @if ($value->last_login !== null)
                                {{ $value->last_login->format('Y年n月j日') }}<br>
                                {{ $value->last_login->format('H時i分s秒') }}<br>
                            @endif
                        </td>
                        <td>
                            <a href="{{action("Admin\\StaffController@update", [ "administrator_id" => $value->id])}}">
                                <button>編集</button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        <!--box-->
        @if ($administrators->hasPages())
        <div class="nextprev">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($administrators->onFirstPage())
                    <span class="disabled" aria-disabled="true">&lt;&lt;前へ</span>
                @else
                    <a href="{{ $administrators->previousPageUrl() }}" rel="prev">&lt;&lt;前へ</a>
                @endif

                {{-- Next Page Link --}}
                @if ($administrators->hasMorePages())
                    <a href="{{ $administrators->nextPageUrl() }}" rel="next">次へ&gt;&gt;</a>
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
