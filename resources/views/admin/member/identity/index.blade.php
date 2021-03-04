@include("admin.common.header")
<section class="wrapp">
    <div class="admin_side">
        @include("admin.common.member_menu")
    </div>
    <!--admin_side-->

    <div class="mypagewrap">
        <div class="mypagebox">
            <p class="tex_c">{{ $request->administrator->display_name }}さんログイン中</p>
            <h2>現在､申請中の本人確認証明書申請一覧</h2>
        </div>
        <!--box-->
        <div class="mypagebox2 pt0">
            @if ($members->count() > 0)
                {{ Form::open([
                    'url' => action('Admin\\IdentityController@index'),
                    'method' => 'GET',
                ]) }}
                <p>検索</p>
                {{ Form::input('text', 'keyword', $request->keyword, [
                    'placdholder' => 'メールアドレス',
                ]) }}

                {{ Form::close() }}
                <table>
                    <tr class="items">
                        <th>ID</th>
                        <td>ユーザー名</td>
                        <td>申請日時</td>
                        <td>詳細へ</td>
                    </tr>
                    @foreach ($members as $key => $value)
                        <tr>
                            <th>{{ $value->id }}</th>
                            <td>{{ $value->display_name }}</td>
                            <td>
                                {{ $value->identity_image->created_at->format('Y年m月d日') }}<br>
                                {{ $value->identity_image->created_at->format('H時i分s秒') }}
                            </td>
                            <td><a
                                    href="{{ action('Admin\\MemberController@detail', ['member_id' => $value->id]) }}"><button>詳細へ</button></a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>

        <!-- ページング要素 -->
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
        <!-- ページング要素 -->

        <p class="mt32 tex_l">※1)プライオリティプラン設定への拒否をした場合​、拒否理由を会員情報の備考に入力してください 。<br>
            これは、ユーザー側には表示されません。<br>
            ※2)<br>
            1:未認証 (初期値)<br>
            2:申請中 (プロフィール画面で認証画像のアップロードが成功した場合)<br>
            3:認証拒否 (管理画面側で認証画像を認証拒否した場合)<br>
            4:認証済み (管理画面側で認証画像を承認した場合、以降変更不可)<br>

            ※2)<br>
            1:未申請(非プライオリティプランであれば常に1)<br>
            2:申請中(プロフィール画面で収入証明画像のアップロードが成功した場合)<br>
            3:認証拒否(管理画面側で、収入証明の申請を拒否した場合)<br>
            4:認証済み(管理画面側で収入証明を承認した場合。以降変更不可)</p>
    </div>
    <!--mypagewrap-->
</section>
@include("admin.common.footer")
