<?php
namespace App\Services;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
class FileUploadService {
    public function upload(UploadedFile $file, string $directory, string $disk = 'public'): string {
        $mime = $file->getMimeType();
        if (in_array($mime, ['image/jpeg','image/png','image/gif']) && function_exists('imagewebp')) {
            return $this->toWebp($file, $directory, $disk);
        }
        return $file->store($directory, $disk);
    }
    protected function toWebp(UploadedFile $file, string $directory, string $disk): string {
        $mime = $file->getMimeType(); $src = $file->getPathname();
        $image = match($mime) {
            'image/jpeg' => imagecreatefromjpeg($src),
            'image/png'  => imagecreatefrompng($src),
            'image/gif'  => imagecreatefromgif($src),
            default      => null,
        };
        if (!$image) return $file->store($directory, $disk);
        $w = imagesx($image); $h = imagesy($image);
        if ($w > 1920) {
            $nh = (int)($h * (1920/$w));
            $r = imagecreatetruecolor(1920,$nh);
            imagecopyresampled($r,$image,0,0,0,0,1920,$nh,$w,$h);
            imagedestroy($image); $image = $r;
        }
        $fn = pathinfo($file->hashName(), PATHINFO_FILENAME).'.webp';
        $path = $directory.'/'.$fn;
        $tmp = sys_get_temp_dir().'/'.$fn;
        if (!imagewebp($image,$tmp,80)) { imagedestroy($image); return $file->store($directory,$disk); }
        imagedestroy($image);
        Storage::disk($disk)->put($path, file_get_contents($tmp));
        @unlink($tmp);
        return $path;
    }
    public function delete(?string $path, string $disk = 'public'): bool {
        if ($path && Storage::disk($disk)->exists($path)) return Storage::disk($disk)->delete($path);
        return false;
    }
}
