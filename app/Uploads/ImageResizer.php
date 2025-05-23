<?php

namespace BookStack\Uploads;

use BookStack\Exceptions\ImageUploadException;
use Exception;
use GuzzleHttp\Psr7\Utils;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Decoders\BinaryImageDecoder;
use Intervention\Image\Drivers\Gd\Decoders\NativeObjectDecoder;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Interfaces\ImageInterface as InterventionImage;
use Intervention\Image\ImageManager;
use Intervention\Image\Origin;

class ImageResizer
{
    protected const THUMBNAIL_CACHE_TIME = 604_800; // 1 week

    public function __construct(
        protected ImageStorage $storage,
    ) {
    }

    /**
     * Load gallery thumbnails for a set of images.
     * @param iterable<Image> $images
     */
    public function loadGalleryThumbnailsForMany(iterable $images, bool $shouldCreate = false): void
    {
        foreach ($images as $image) {
            $this->loadGalleryThumbnailsForImage($image, $shouldCreate);
        }
    }

    /**
     * Load gallery thumbnails into the given image instance.
     */
    public function loadGalleryThumbnailsForImage(Image $image, bool $shouldCreate): void
    {
        $thumbs = ['gallery' => null, 'display' => null];

        try {
            $thumbs['gallery'] = $this->resizeToThumbnailUrl($image, 150, 150, false, $shouldCreate);
            $thumbs['display'] = $this->resizeToThumbnailUrl($image, 1680, null, true, $shouldCreate);
        } catch (Exception $exception) {
            // Prevent thumbnail errors from stopping execution
        }

        $image->setAttribute('thumbs', $thumbs);
    }

    /**
     * Get the thumbnail for an image.
     * If $keepRatio is true only the width will be used.
     * Checks the cache then storage to avoid creating / accessing the filesystem on every check.
     *
     * @throws Exception
     */
    public function resizeToThumbnailUrl(
        Image $image,
        ?int $width,
        ?int $height,
        bool $keepRatio = false,
        bool $shouldCreate = false
    ): ?string {
        // Do not resize GIF images where we're not cropping
        if ($keepRatio && $this->isGif($image)) {
            return $this->storage->getPublicUrl($image->path);
        }

        $thumbDirName = '/' . ($keepRatio ? 'scaled-' : 'thumbs-') . $width . '-' . $height . '/';
        $imagePath = $image->path;
        $thumbFilePath = dirname($imagePath) . $thumbDirName . basename($imagePath);

        $thumbCacheKey = 'images::' . $image->id . '::' . $thumbFilePath;

        // Return path if in cache
        $cachedThumbPath = Cache::get($thumbCacheKey);
        if ($cachedThumbPath && !$shouldCreate) {
            return $this->storage->getPublicUrl($cachedThumbPath);
        }

        // If thumbnail has already been generated, serve that and cache path
        $disk = $this->storage->getDisk($image->type);
        if (!$shouldCreate && $disk->exists($thumbFilePath)) {
            Cache::put($thumbCacheKey, $thumbFilePath, static::THUMBNAIL_CACHE_TIME);

            return $this->storage->getPublicUrl($thumbFilePath);
        }

        $imageData = $disk->get($imagePath);

        // Do not resize animated images where we're not cropping
        if ($keepRatio && $this->isAnimated($image, $imageData)) {
            Cache::put($thumbCacheKey, $image->path, static::THUMBNAIL_CACHE_TIME);

            return $this->storage->getPublicUrl($image->path);
        }

        // If not in cache and thumbnail does not exist, generate thumb and cache path
        $thumbData = $this->resizeImageData($imageData, $width, $height, $keepRatio, $this->getExtension($image));
        $disk->put($thumbFilePath, $thumbData, true);
        Cache::put($thumbCacheKey, $thumbFilePath, static::THUMBNAIL_CACHE_TIME);

        return $this->storage->getPublicUrl($thumbFilePath);
    }

    /**
     * Resize the image of given data to the specified size, and return the new image data.
     * Format will remain the same as the input format, unless specified.
     *
     * @throws ImageUploadException
     */
    public function resizeImageData(
        string $imageData,
        ?int $width,
        ?int $height,
        bool $keepRatio,
        ?string $format = null,
    ): string {
        try {
            $thumb = $this->interventionFromImageData($imageData, $format);
        } catch (Exception $e) {
            throw new ImageUploadException(trans('errors.cannot_create_thumbs'));
        }

        $this->orientImageToOriginalExif($thumb, $imageData);

        if ($keepRatio) {
            $thumb->scaleDown($width, $height);
        } else {
            $thumb->cover($width, $height);
        }

        $encoder = match ($format) {
            'png' => new PngEncoder(),
            default => new AutoEncoder(),
        };

        $thumbData = (string) $thumb->encode($encoder);

        // Use original image data if we're keeping the ratio
        // and the resizing does not save any space.
        if ($keepRatio && strlen($thumbData) > strlen($imageData)) {
            return $imageData;
        }

        return $thumbData;
    }

    /**
     * Create an intervention image instance from the given image data.
     * Performs some manual library usage to ensure image is specifically loaded
     * from given binary data instead of data being misinterpreted.
     */
    protected function interventionFromImageData(string $imageData, ?string $fileType): InterventionImage
    {
        $manager = new ImageManager(
            new Driver(),
            autoOrientation: false,
        );

        // Ensure gif images are decoded natively instead of deferring to intervention GIF
        // handling since we don't need the added animation support.
        $isGif = $fileType === 'gif';
        $decoder = $isGif ? NativeObjectDecoder::class : BinaryImageDecoder::class;
        $input = $isGif ? @imagecreatefromstring($imageData) : $imageData;

        $image = $manager->read($input, $decoder);

        if ($isGif) {
            $image->setOrigin(new Origin('image/gif'));
        }

        return $image;
    }

    /**
     * Orientate the given intervention image based upon the given original image data.
     * Intervention does have an `orientate` method but the exif data it needs is lost before it
     * can be used (At least when created using binary string data) so we need to do some
     * implementation on our side to use the original image data.
     * Bulk of logic taken from: https://github.com/Intervention/image/blob/b734a4988b2148e7d10364b0609978a88d277536/src/Intervention/Image/Commands/OrientateCommand.php
     * Copyright (c) Oliver Vogel, MIT License.
     */
    protected function orientImageToOriginalExif(InterventionImage $image, string $originalData): void
    {
        if (!extension_loaded('exif')) {
            return;
        }

        $stream = Utils::streamFor($originalData)->detach();
        $exif = @exif_read_data($stream);
        $orientation = $exif ? ($exif['Orientation'] ?? null) : null;

        switch ($orientation) {
            case 2:
                $image->flip();
                break;
            case 3:
                $image->rotate(180);
                break;
            case 4:
                $image->rotate(180)->flip();
                break;
            case 5:
                $image->rotate(270)->flip();
                break;
            case 6:
                $image->rotate(270);
                break;
            case 7:
                $image->rotate(90)->flip();
                break;
            case 8:
                $image->rotate(90);
                break;
        }
    }

    /**
     * Checks if the image is a gif. Returns true if it is, else false.
     */
    protected function isGif(Image $image): bool
    {
        return $this->getExtension($image) === 'gif';
    }

    /**
     * Get the extension for the given image, normalised to lower-case.
     */
    protected function getExtension(Image $image): string
    {
        return strtolower(pathinfo($image->path, PATHINFO_EXTENSION));
    }

    /**
     * Check if the given image and image data is apng.
     */
    protected function isApngData(string &$imageData): bool
    {
        $initialHeader = substr($imageData, 0, strpos($imageData, 'IDAT'));

        return str_contains($initialHeader, 'acTL');
    }

    /**
     * Check if the given avif image data represents an animated image.
     * This is based up the answer here: https://stackoverflow.com/a/79457313
     */
    protected function isAnimatedAvifData(string &$imageData): bool
    {
        $stszPos = strpos($imageData, 'stsz');
        if ($stszPos === false) {
            return false;
        }

        // Look 12 bytes after the start of 'stsz'
        $start = $stszPos + 12;
        $end = $start + 4;
        if ($end > strlen($imageData) - 1) {
            return false;
        }

        $data = substr($imageData, $start, 4);
        $count = unpack('Nvalue', $data)['value'];
        return $count > 1;
    }

    /**
     * Check if the given image is animated.
     */
    protected function isAnimated(Image $image, string &$imageData): bool
    {
        $extension = strtolower(pathinfo($image->path, PATHINFO_EXTENSION));
        if ($extension === 'png') {
            return $this->isApngData($imageData);
        }

        if ($extension === 'avif') {
            return $this->isAnimatedAvifData($imageData);
        }

        return false;
    }
}
