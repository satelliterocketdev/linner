<?php

namespace App\Services;

use Auth;
use Storage;
use Image;
use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Exception\ExecutableNotFoundException;
use App\MediaFile;
use Illuminate\Http\File;

class MediaFileService
{
    const THUMB_WIDTH = 227;
    const THUMB_HEIGHT = 227;

    /**
     * @param \Illuminate\Http\File originalFile
     */
    public static function createFeatured($originalFile)
    {
        $thumb_image = Image::make($originalFile)
            ->resize(self::THUMB_WIDTH, self::THUMB_HEIGHT, function ($constraint) {
                $constraint->aspectRatio();
            })->encode($originalFile->guessExtension());
        return $thumb_image;
    }

    /**
     * 画像ファイル用 MediaFile生成処理
     * @param \Illuminate\Http\UploadedFile $file
     */
    public function createImageMedia($file)
    {
        $disk = Storage::disk('s3');
        // ファイル本体をs3にアップロード
        $path = $disk->putFile(Auth::id(), $file);
        $file_url = $disk->url($path);

        // サムネイル作成
        $thumb_image = $this->createFeatured($file);
        $thumb_name = str_replace('.' . $file->extension(), '_thumb.' . $file->extension(), $path);
        
        // サムネイルをs3にアップロード
        $disk->put($thumb_name, $thumb_image);
        $featured_url =  $disk->url($thumb_name);

        $data = [
            'user_id' => Auth::id(),
            'name' => $file->hashName(),
            'type' => 'image',
            'url' => $file_url,
            'featured_url' => $featured_url,
            'size' => $file->getSize(),
            'duration' => null,
        ];
        return MediaFile::create($data);
    }

    /**
     * 音声ファイル用 MediaFile生成処理
     * @param \Illuminate\Http\UploadedFile $file
     */
    public function createAudioMedia($file)
    {
        $disk = Storage::disk('s3');
        // ファイル本体をs3にアップロード
        $path = $disk->putFile(Auth::id(), $file);
        $file_url = $disk->url($path);

        // 情報の取得
        $ffprobe = FFMpeg::create();
        $video = $ffprobe->open($file->getRealPath());
        $format = $video->getFormat();
        $duration = $format->get('duration');
        $featured_url = asset('img/audio.jpg');

        $data = [
            'user_id' => Auth::id(),
            'name' => $file->hashName(),
            'type' => 'audio',
            'url' => $file_url,
            'featured_url' => $featured_url,
            'size' => $file->getSize(),
            'duration' => $duration,
        ];
        return MediaFile::create($data);
    }


    /**
     * 動画ファイル用 MediaFile生成処理
     * @param \Illuminate\Http\UploadedFile $file
     */
    public function createVideoMedia($file)
    {
        $disk = Storage::disk('s3');
        // ファイル本体をs3にアップロード
        $path = $disk->putFile(Auth::id(), $file);
        $file_url = $disk->url($path);
        $tmp = null;
        try {
            // 1フレーム目を切り出し、サムネイル用画像にする。
            $ffprobe = FFMpeg::create();
            $video = $ffprobe->open($file->getRealPath());
            $tmp = $file->getRealPath() . '_frame.jpg';
            $video->frame(TimeCode::fromSeconds(1))
                ->save($tmp);

            $format = $video->getFormat();
            $duration = $format->get('duration');
            list($seconds, $milliseconds) = explode(".", number_format($duration, 2));
            $duration = (($seconds*1000) + $milliseconds);

            // サムネイル作成
            $thumb_image = $this->createFeatured(new File($tmp));
            $thumb_name = str_replace('.' . $file->extension(), '_thumb.jpg', $path);

            // サムネイルをs3にアップロード
            $disk->put($thumb_name, $thumb_image);
            $featured_url =  $disk->url($thumb_name);
        } catch (ExecutableNotFoundException $e) {
            dump($e);
        } finally {
            // 1フレームメ画像の削除
            unlink($tmp);
        }

        $data = [
            'user_id' => Auth::id(),
            'name' => $file->hashName(),
            'type' => 'video',
            'url' => $file_url,
            'featured_url' => $featured_url,
            'size' => $file->getSize(),
            'duration' => $duration,
        ];
        return MediaFile::create($data);
    }

    /**
     * システムへアップロードしたファイルに関するMediaFile生成処理
     * ファイルは"s3"ストレージに保存する。
     * @param \Illuminate\Http\UploadedFile $file
     */
    public function saveUploadFile($file)
    {
        $mime_type = $file->getMimeType();
        if (strpos($mime_type, 'image') !== false) {
            return $this->createImageMedia($file);
        } elseif (strpos($mime_type, 'audio') !== false) {
            return $this->createAudioMedia($file);
        } elseif (strpos($mime_type, 'video') !== false) {
            return $this->createVideoMedia($file);
        } else {
            $disk = Storage::disk('s3');
            // ファイル本体をs3にアップロード
            $path = $disk->putFile(Auth::id(), $file);
            $file_url = $disk->url($path);

            $data = [
                'user_id' => Auth::id(),
                'name' => $file->hashName(),
                'type' => 'other',
                'url' => $file_url,
                'featured_url' => asset('img/other.jpg'),
                'size' => $file->getSize(),
                'duration' => null,
            ];
            return MediaFile::create($data);
        }
    }

    /**
     * 動画ファイル用 MediaFile削除処理
     * @param String $filePath
     */
    public function deleteImageMedia($filePath)
    {
        $storage = Storage::disk('s3');
        $storage->delete($filePath);
    }
}
