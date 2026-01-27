<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * 画像をリサイズして保存
     *
     * @param UploadedFile $file アップロードされたファイル
     * @param string $directory 保存先ディレクトリ
     * @param int $maxWidth 最大幅
     * @param int $maxHeight 最大高さ
     * @param int $quality 画質（1-100）
     * @return string|null 保存されたファイルのパス
     */
    public function storeResized(
        UploadedFile $file,
        string $directory = 'avatars',
        int $maxWidth = 400,
        int $maxHeight = 400,
        int $quality = 85
    ): ?string {
        $extension = strtolower($file->getClientOriginalExtension());
        
        // サポートする形式
        $supportedFormats = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($extension, $supportedFormats)) {
            return null;
        }

        // 元画像を読み込み
        $sourceImage = $this->createImageFromFile($file->getPathname(), $extension);
        
        if (!$sourceImage) {
            // GDで処理できない場合は通常保存
            return $this->storeOriginal($file, $directory);
        }

        // 元のサイズを取得
        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);

        // リサイズが必要かチェック
        if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
            // リサイズ不要な場合は圧縮のみ
            imagedestroy($sourceImage);
            return $this->storeCompressed($file, $directory, $quality);
        }

        // 新しいサイズを計算（アスペクト比を維持）
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        $newWidth = (int) round($originalWidth * $ratio);
        $newHeight = (int) round($originalHeight * $ratio);

        // 新しい画像を作成
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // PNG/GIFの透明度を維持
        if (in_array($extension, ['png', 'gif'])) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        // リサイズ
        imagecopyresampled(
            $newImage,
            $sourceImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $originalWidth, $originalHeight
        );

        // ファイル名を生成
        $filename = Str::random(40) . '.' . $extension;
        $path = $directory . '/' . $filename;
        $fullPath = Storage::disk('public')->path($path);

        // ディレクトリを作成
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // 保存
        $this->saveImage($newImage, $fullPath, $extension, $quality);

        // メモリ解放
        imagedestroy($sourceImage);
        imagedestroy($newImage);

        return $path;
    }

    /**
     * ファイルから画像リソースを作成
     */
    private function createImageFromFile(string $path, string $extension)
    {
        return match ($extension) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'gif' => @imagecreatefromgif($path),
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };
    }

    /**
     * 画像を保存
     */
    private function saveImage($image, string $path, string $extension, int $quality): void
    {
        match ($extension) {
            'jpg', 'jpeg' => imagejpeg($image, $path, $quality),
            'png' => imagepng($image, $path, (int) round((100 - $quality) / 10)),
            'gif' => imagegif($image, $path),
            'webp' => function_exists('imagewebp') ? imagewebp($image, $path, $quality) : imagejpeg($image, $path, $quality),
            default => imagejpeg($image, $path, $quality),
        };
    }

    /**
     * 圧縮のみで保存
     */
    private function storeCompressed(UploadedFile $file, string $directory, int $quality): ?string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $sourceImage = $this->createImageFromFile($file->getPathname(), $extension);

        if (!$sourceImage) {
            return $this->storeOriginal($file, $directory);
        }

        $filename = Str::random(40) . '.' . $extension;
        $path = $directory . '/' . $filename;
        $fullPath = Storage::disk('public')->path($path);

        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $this->saveImage($sourceImage, $fullPath, $extension, $quality);
        imagedestroy($sourceImage);

        return $path;
    }

    /**
     * オリジナルのまま保存
     */
    private function storeOriginal(UploadedFile $file, string $directory): string
    {
        $filename = Str::random(40) . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($directory, $filename, 'public');
    }
}
