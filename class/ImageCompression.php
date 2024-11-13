<?php declare(strict_types=1);

class ImageCompression{

    public function compressImage($source, $targetSizeKB, $maxTargetSizeKB) {
    // 初期の圧縮率を設定 (100 = 無圧縮, 0 = 最大圧縮)
        $quality = 90;
        $targetSizeBytes = $targetSizeKB * 1024;
        $maxTargetSizeBytes = $maxTargetSizeKB * 1024;

        // 画像フォーマットを取得
        $imageInfo = getimagesize($source);
        $mimeType = $imageInfo['mime'];


        // 画像を読み込み
        switch ($mimeType) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($source);
            break;
            default:
                throw new Exception('対応していない画像形式です');
        }

        // 圧縮ループ（品質を徐々に下げて、目標サイズに近づける）
        do  {
            // 圧縮して一時的なファイルに保存
            ob_start();
            imagejpeg($image, null, $quality);  // 圧縮して出力
            $compressedImage = ob_get_contents();  // 圧縮後の画像を取得
            ob_end_clean();

            // 圧縮後のサイズをチェック
            $fileSize = strlen($compressedImage);  // 圧縮後のバイト数

            // 圧縮率を下げる
            $quality -= 5;

        } while ($fileSize > $targetSizeBytes && $quality > 0);

            // メモリ解放
            imagedestroy($image);


            // 目標サイズに収まっているかチェックして結果を返す
            if ($fileSize <= $maxTargetSizeBytes && $fileSize > 0) {
                return $compressedImage;
            } else {
                return false;
            }

    }
}