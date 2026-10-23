<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\ListingImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Re-encodes listing photos with GD (downscaled, WebP when available) so pages
 * stay light on slow mobile connections. Deliberately avoids anything that needs
 * ext-fileinfo (UploadedFile::store(), the "image"/"mimes" rules): the production
 * host doesn't ship it.
 */
class ListingImageStore
{
    private const ACCEPTED_TYPES = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP, IMAGETYPE_GIF];

    public static function isImage(UploadedFile $file): bool
    {
        $info = @getimagesize($file->getRealPath());

        return $info !== false && in_array($info[2], self::ACCEPTED_TYPES, true);
    }

    public function store(UploadedFile $file, Listing $listing, int $sortOrder): ListingImage
    {
        $image = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));
        if ($image === false) {
            throw new RuntimeException('Image illisible.');
        }

        $image = $this->applyExifOrientation($image, $file->getRealPath());
        $image = $this->downscale($image, (int) config('marketplace.image_max_dimension'));

        // Best format this GD build supports (production has WebP).
        $quality = (int) config('marketplace.image_quality');
        [$extension, $encode] = match (true) {
            function_exists('imagewebp') => ['webp', fn () => imagewebp($image, null, $quality)],
            function_exists('imagejpeg') => ['jpg', fn () => imagejpeg($image, null, $quality)],
            default => ['png', fn () => imagepng($image, null, 9)],
        };
        ob_start();
        $encode();
        $bytes = (string) ob_get_clean();
        imagedestroy($image);

        $path = 'listings/'.$listing->id.'/'.Str::random(32).'.'.$extension;
        Storage::disk('public')->put($path, $bytes);

        return $listing->images()->create(['path' => $path, 'sort_order' => $sortOrder]);
    }

    public function delete(ListingImage $image): void
    {
        Storage::disk('public')->delete($image->path);
        $image->delete();
    }

    private function downscale(\GdImage $image, int $maxDimension): \GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $ratio = $maxDimension / max($width, $height);

        if ($ratio >= 1) {
            return $image;
        }

        $resized = imagescale($image, (int) round($width * $ratio), (int) round($height * $ratio));
        imagedestroy($image);

        return $resized;
    }

    /** Phone photos are often stored sideways with an EXIF rotation flag. */
    private function applyExifOrientation(\GdImage $image, string $path): \GdImage
    {
        if (! function_exists('exif_read_data')) {
            return $image;
        }

        $orientation = (int) (@exif_read_data($path)['Orientation'] ?? 1);
        $angle = match ($orientation) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        if ($angle === 0) {
            return $image;
        }

        $rotated = imagerotate($image, $angle, 0);
        imagedestroy($image);

        return $rotated;
    }
}
