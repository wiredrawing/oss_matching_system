@include("admin.common.header")
<section class="wrapp">
    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->

    <div class="mypagewrap">
        <div class="mypagebox">
            <p class="tex_c">{{ $request->administrator->display_name }}さんログイン中</p>
            <h2>全会員データ一覧表示</h2>
        </div>
        <!--box-->

        <div class="mypagebox2">
            {{ Form::open([
                'url' => action('Admin\\MemberController@index'),
                'method' => 'GET',
            ]) }}
            <p>検索</p>
            {{ Form::input('text', 'email', $request->email, [
                'placdholder' => 'メールアドレス',
            ]) }}
            {{ Form::close() }}
            <table>
                <tr class="items">
                    <th>ID</th>
                    <td>ユーザー名</td>
                    <td>Email</td>
                    <td>登録状態</td>
                    <td>本人確認</td>
                    <td>登録日<br>更新日</td>
                    <td>ブラック</td>
                    <td>詳細</td>
                </tr>
                @foreach ($members as $key => $value)
                    <tr>
                        <th>{{ $value->id }}</th>
                        <td>{{ $value->display_name }}</td>
                        <td>
                            @if ($value->deleted_at === NULL)
                            {{ Form::input('text', 'email', $value->email, [
                                'placdholder' => 'メールアドレス',
                                "readonly" => "readonly",
                            ]) }}
                            @else
                            {{ Form::input('text', 'deleted_email', $value->deleted_email, [
                                'placdholder' => 'メールアドレス',
                                "readonly" => "readonly",
                            ]) }}
                            @endif
                        </td>
                        <td>@if ($value->deleted_at === NULL)
                            {{ $registering_status[$value->is_registered] }}
                            @else
                            退会済み
                            @endif
                        </td>
                        <td>{{ Config("const.image.approve_type_name")[$value->is_approved]}}</td>
                        <td>
                            @if ($value->created_at !== null)
                                {{ $value->created_at->format('Y年n月j日') }}<br>
                                {{ $value->created_at->format('H時i分s秒') }}<br>
                            @endif
                            <br>
                            @if ($value->updated_at !== null)
                                {{ $value->updated_at->format('Y年n月j日') }}<br>
                                {{ $value->updated_at->format('H時i分s秒') }}<br>
                            @endif
                        </td>
                        <td>{{Config("const.blacklist")[$value->is_blacklisted]}}</td>
                        <td>
                            <a href="{{ action('Admin\\MemberController@detail', ['member_id' => $value->id]) }}">
                                <button>詳細へ</button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        <!--box-->

        @if ($members->hasPages())
        <div class="nextprev">
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if ($members->onFirstPage())
                    <span class="disabled" aria-disabled="true">&lt;&lt;前へ</span>
                @else
                    <a href="{{ $members->previousPageUrl() }}" rel="prev">&lt;&lt;前へ</a>
                @endif

                {{-- Next Page Link --}}
                @if ($members->hasMorePages())
                    <a href="{{ $members->nextPageUrl() }}" rel="next">次へ&gt;&gt;</a>
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
