<?php


namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\BaseMediaRequest;
use App\Http\Requests\Api\MediaRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Image;
use Intervention\Image\Facades\Image as InterventionImage;
use App\Common\CommonMedia;
class MediaController extends Controller
{


    /**
     * 画像アップロード処理用API
     *
     * @param BaseMediaRequest $request
     * @param integer $use_type
     * @return Response
     */
    public function upload(MediaRequest $request)
    {
        try {
            // リクエストボディ
            $input_data = $request->validated();

            // 画像アップロード処理
            $image = CommonMedia::upload($request);

            // 画像アップロードのNULLチェック
            if ($image === NULL) {
                // 画像アップロードのDBへの登録に失敗
                logger()->error("画像アップロードのDBへの登録に失敗", $input_data);
                throw new \Exception(Config("errors.CREATE_ERR"));
            }

            // APIレスポンスの設定
            $result = [
                "status" => true,
                "response" => [
                    "url" => action("Api\\v1\\MediaController@show", [
                        "image_id" => $image->id,
                        "token" => $image->token,
                    ]),
                    "image" => $image,
                ]
            ];

            // log
            logger()->info($result);
            return response()->json($result);
        } catch (\Throwable $e) {
            // log
            logger()->error($e);
            // APIレスポンスの設定
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e,
                ]
            ];
            return response()->json($result);
        }
    }


    /**
     * 指定したアップロード済み画像を参照する (※URL引数に画像リサイズのピクセルを指定)
     *
     * @param MediaRequest $request
     * @param integer $image_id
     * @param string $token
     * @param integer $width
     * @return void
     */
    public function show(MediaRequest $request, int $image_id, string $token, int $width = 0)
    {
        try {
            // 仮画面側からの閲覧時は､削除済みも表示させる
            if (isset($request->administrator)) {
                $image = Image::withTrashed()->find($request->image_id);
            } else {
                $image = Image::find($request->image_id);
            }


            // imageオブジェクトのNULLチェック
            if ($image === NULL) {
                logger()->error(__FILE__."指定された画像が取得できませんでした。", $request->validated());
                throw new \Exception(Config("errors.NOT_FOUND_ERR"));
            }

            // 参照用トークンがマッチするかどうかを検証
            if ($image->token !== $request->token) {
                logger()->error("指定された画像が取得できませんでした。", $request->validated());
                throw new \Exception(Config("errors.NOT_FOUND_ERR"));
            }

            // 保存先ディレクトリを生成
            $dir_number = floor($image->id / Config("const.image.directory_max"));
            $decided_save_path = "public/uploads/images/{$dir_number}/{$image->filename}";

            $convert_image = InterventionImage::make(Storage::disk("local")->path($decided_save_path));

            // 横幅の指定がある場合はリサイズする
            if ($width > 0) {
                $convert_image->resize($width, null, function ($constrain) {
                    $constrain->aspectRatio();
                });
            }
            // ぼかし実行
            // $convert_image->pixelate($image->blur_level);

            // log
            logger()->info($convert_image);

            return $convert_image->response("jpeg");
        } catch (\Throwable $e) {
            // var_dump($e->getMessage());
            logger()->error($e);
            // Logger::error(__FILE__, $e);
            return null;
        }
    }




    /**
     * 現在ログイン中ユーザーのプロフィール画像一覧を取得する
     *
     * @param MediaRequest $request
     * @param integer $member_id
     * @param string $security_token
     * @return void
     */
    public function getProfileImages(MediaRequest $request, int $member_id, string $security_token)
    {
        try {
            $profile_images = Image::where("member_id", $member_id)
            ->where("use_type", Config("const.image.use_type.profile"))
            ->orderBy("id", "asc")
            ->get();

            // profile画像の件数チェック
            if ($profile_images->count() === 0) {
                logger()->info("現在設定中のプロフィール画像は存在しません。");
                // throw new \Exception(Config("errors.NOT_FOUND_PROFILE_IMAGE_ERR"));
            }


            // APIレスポンスの設定
            $result = [
                "status" => true,
                "response" => $profile_images,
            ];

            // log
            logger()->info($result);
            return response()->json($result);
        } catch (\Throwable $e) {
            // log
            logger()->error($e);
            // APIレスポンスの設定
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e->getMessage().$e->getFile().$e->getLine(),
                ]
            ];
            return response()->json($result);
        }
    }


    /**
     * 指定された画像をDBのレコード上から物理削除する
     *
     * @param BaseMediaRequest $request
     * @return void
     */
    public function delete(MediaRequest $request)
    {
        try {
            $image = CommonMedia::delete($request);

            // APIレスポンスの設定
            $result = [
                "status" => true,
                "response" => $image,
            ];
            return response()->json($result);
        } catch (\Throwable $e) {
            // log
            logger()->error($e);
            // APIレスポンスの設定
            $result = [
                "status" => false,
                "response" => [
                    "error" => $e->getMessage().$e->getFile().$e->getLine(),
                ]
            ];
            return response()->json($result);
        }
    }
}
