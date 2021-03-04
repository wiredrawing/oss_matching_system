<?php

namespace App\Common;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\BaseMediaRequest;
use App\Models\Member;
use App\Library\RandomToken;
use App\Models\Image;
use Intervention\Image\Facades\Image as InterventionImage;
class CommonMedia
{


    /**
     * Undocumented function
     *
     * @param BaseMediaRequest $request
     * @return Image|null
     */
    public static function upload(BaseMediaRequest $request) : ?Image
    {
        try {
            // トランザクションの開始
            DB::beginTransaction();

            // アップロード画像のタイプが、証明書関係だった場合古いレコードを削除する
            $use_type = (int)$request->use_type;
            if ($use_type === Config("const.image.use_type.identity") || $use_type === Config("const.image.use_type.income") ) {
                // 証明書関連に該当するレコードすべてを削除する
                $image = Image::where("member_id", $request->member_id)
                ->where("use_type", $request->use_type)
                ->delete();

                // membersテーブルの本人確認申請フラグも0にする
                // log
                logger()->info(__FILE__);
                logger()->info($image);
            }

            // delete_image_idが指定されている場合は、対象の画像を物理削除する
            if (isset($request->delete_image_id) && $request->delete_image_id > 0) {
                $delete_image = Image::find($request->delete_image_id);
                $dir_number = floor($delete_image->id / Config("const.image.directory_max"));
                $decided_save_path = "public/uploads/images/{$dir_number}/{$delete_image->filename}";
                $delete_image->delete();
                // 削除対象の画像ファイルも物理削除する
                $result = Storage::delete($decided_save_path);
            }

            // 参照用トークン
            $token = RandomToken::MakeRandomToken(128, "IMAGE_");

            // 一時ディレクトリに保存
            $temporary_save_path = "public/uploads/temporary/{$request->member_id}";
            $temporary_filename = $request->profile_image->store($temporary_save_path);

            // 大きすぎる画像はリサイズする
            $img = InterventionImage::make(Storage::disk("local")->path($temporary_filename))->orientate();
            logger()->info("=====>". $img->width());
            if ($img->width() > Config("const.image.max_width")) {
                $img->resize(Config("const.image.max_width"), null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save(Storage::disk("local")->path($temporary_filename), Config("const.image.compression"));
            }
            unset($img);

            // アップロード画像のリサイズ処理の実行
            // 一時ディレクトリにおかれた原寸画像ファイル
            $original_size = InterventionImage::make(Storage::disk("local")->path($temporary_filename))->orientate();
            // アップロードされた画像の横ピクセルが規定サイズをオーバーした場合は､リサイズさせる｡
            $original_size->blur($request->blur_level)->save( Storage::disk("local")->path($temporary_filename), Config("const.image.compression") );


            // アップロードされたファイルの拡張子を取得する
            $extension = File::extension(Storage::disk('local')->path($temporary_filename));
            $filename = hash("sha256", file_get_contents(Storage::disk('local')->path($temporary_filename))).".{$extension}";

            // アップロードされたファイルの存在チェック
            $image = Image::where("filename", $filename)->get()->first();

            // log
            if ($image !== NULL) {
                logger()->info(__FILE__, $image->toArray());
            }

            // 画像アップロード処理をDBに反映させる
            $insert_data = [
                "member_id" => $request->member_id,
                "use_type" => $request->use_type,
                "token" => $token,
                "filename" => $filename,
                "blur_level" => $request->blur_level,
                "is_approved" => $request->is_approved,
            ];
            $image = new Image();
            $image->fill($insert_data);
            $result = $image->save();

            // DB問い合わせのレスポンス
            if ($result !== true) {
                // 画像アップロードのDBへの登録に失敗
                logger()->error(__FILE__, $insert_data);
                throw new \Exception(Config("errors.CREATE_ERR"));
            }

            // DB問い合わせ結果
            $last_image_id = $image->id;

            // membersテーブル上の､証明書関係フラグを設定
            if ($use_type === Config("const.image.use_type.identity")) {
                $member = Member::findOrFail($request->member_id);
                $update_data = [
                    "approved_image_id" => $last_image_id,
                    "is_approved" => Config("const.image.approve_type.applying"),
                ];
                $result =$member->fill($update_data)->save();
                // DB問い合わせのレスポンス
                if ($result !== true) {
                    // 画像アップロードのDBへの登録に失敗
                    logger()->error(__FILE__, $update_data);
                    throw new \Exception(Config("errors.CREATE_ERR"));
                }
            } else if ($use_type === Config("const.image.use_type.income")) {
                $member = Member::findOrFail($request->member_id);
                $update_data = [
                    "income_image_id" => $last_image_id,
                    "income_certificate" => Config("const.image.approve_type.applying"),
                ];
                $result =$member->fill($update_data)->save();
                // DB問い合わせのレスポンス
                if ($result !== true) {
                    // 画像アップロードのDBへの登録に失敗
                    logger()->error(__FILE__, $update_data);
                    throw new \Exception(Config("errors.CREATE_ERR"));
                }
            }


            // 一時ディレクトリ内のファイルを確定ディレクトリに移動させる
            $dir_number = floor($last_image_id / Config("const.image.directory_max"));
            $decided_save_path = "public/uploads/images/{$dir_number}/{$filename}";
            $result = Storage::disk("local")->exists($decided_save_path);

            if ($result === true) {
                // 確定ディレクトリに同名のファイルが存在する場合、一度削除する
                $result = Storage::delete($decided_save_path);
            }

            // 一時ディレクトリを確定ディレクトリに移動させる
            $result = Storage::move($temporary_filename, $decided_save_path);

            // ファイルの移動に失敗した場合
            if ($result !== true) {
                logger()->error("{$temporary_filename}から{$decided_save_path}へのファイル移動に失敗しました。");
                throw new \Exception(Config("errors.CREATE_ERR"));
            }

            // 一時ディレクトリ内のアップロードファイルを削除する
            $result = Storage::deleteDirectory($temporary_save_path);
            DB::commit();

            // log
            logger()->info(__FILE__, (array)$result);

            // 戻り値はImageクラス
            return $image;
        } catch (\Throwable $e) {
            // ロールバック
            DB::rollback();
            // 一時ディレクトリ内のアップロードファイルを削除する
            $result = Storage::deleteDirectory($temporary_save_path);
            logger()->error($e);
            return null;
        }
    }


    /**
     * Undocumented function
     *
     * @param BaseMediaRequest $request
     * @return void
     */
    public static function delete(BaseMediaRequest $request)
    {
        try {
            logger()->info(__FILE__, $request->validated());

            // ファイルの存在確認
            $image = Image::where([
                "id" => $request->image_id,
                "member_id" => $request->member_id,
            ])
            ->get()
            ->first();

            // NULLチェック
            if ($image === NULL) {
                logger()->info("指定された画像が存在しません。");
                throw new \Exception(Config("errors.NOT_FOUND＿ERR"));
            }

            // 指定された画像の確定ディレクトリを取得
            $dir_number = floor($image->id / Config("const.image.directory_max"));
            $decided_save_path = "public/uploads/images/{$dir_number}/{$image->filename}";
            $result = Storage::disk("local")->exists($decided_save_path);

            // if ($result !== true) {
            //     Logger::info(__FILE__, "削除対象の物理ファイルが存在しません。");
            //     throw new \Exception(Config("errors.NOT_FOUND＿ERR"));
            // }

            // 物理ファイルを削除するが､hash値が同じ画像を参照しているレコードが別にある場合は､削除しない
            if (Image::where("filename", $image->filename)->get()->count() === 1) {
                $result = Storage::delete($decided_save_path);
            }
            $result = $image->delete();
            return true;
        } catch (\Throwable $e) {
            logger()->error($e);
            return false;
        }
    }
}
