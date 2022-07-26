<?php


namespace Uploader\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Intervention\Image\Image as InterventionImage;
use Uploader\UploaderConstants;

/**
 * Trait Images
 * @package Uploader\Traits
 */
trait Images
{
    /**
     * @var InterventionImage
     */
    protected $image;

    /**
     * @param UploadedFile $file
     * @param array $options
     * @return bool
     */
    protected function uploadAsImage(UploadedFile $file, array $options = []): bool
    {
        $this->createImage();

        if (Arr::get($options, 'crop')) {
            $this->crop($options['crop']);
        }
        if (Arr::get($options, 'resize')) {
            $this->resize($options['resize']);
        }
        if (Arr::get($options, 'resizeWithAction')) {
            $this->resizeWithAction($options['resizeWithAction']);
        }

        $extension = $file->getClientOriginalExtension();
        $encoded = (string) $this->image->encode($extension);

        return Storage::disk($this->storageDisk)->put($this->getFilePath(), $encoded);
    }

    /**
     *
     */
    protected function createImage()
    {
        $this->image = Image::make($this->file->getRealPath());
    }

    /**
     * @return bool
     */
    protected function isImage(): bool
    {
        return substr($this->file->getMimeType(), 0, 5) === 'image';
    }

    /**
     * @param array $options
     */
    protected function crop(array $options)
    {
        $this->image = $this->image->crop($options['width'], $options['height']);
    }

    /**
     * @param array $options
     */
    protected function resize(array $options)
    {
        $resizeH = null;
        $resizeW = null;

        if ($this->image->height() >= $this->image->width()) {
            $resizeW = $options['width'];
        } else {
            $resizeH = $options['height'];
        }

        $this->image = $this->image->resize($resizeW, $resizeH, function ($constraint) {
            $constraint->aspectRatio();
        });
    }

    /**
     * @param array $options
     */
    protected function resizeWithAction(array $options)
    {
        $condition = function ($c) {
            $c->upsize();
        };

        switch ($options['action']) {
            case UploaderConstants::RESIZE_ACTION_WIDEN:
                $this->image = $this->image->widen($options['width'], $condition);
                break;
            case UploaderConstants::RESIZE_ACTION_HEIGHTEN:
                $this->image = $this->image->heighten($options['height'], $condition);
                break;
            case UploaderConstants::RESIZE_ACTION_FIT:
                $this->image = $this->image->fit($options['width'], $options['height'], $condition);
                break;
            case UploaderConstants::RESIZE_ACTION_AUTO:
                if ($this->image->height() >= $this->image->width()) {
                    $this->image = $this->image->heighten($options['height'], $condition);
                } else {
                    $this->image = $this->image->widen($options['width'], $condition);
                }
                break;
            default:
                $this->image = $this->image->fit($options['width'], $options['height'], $condition);
        }
    }

    /**
     * @return int
     */
    protected function getImageWidth(): int
    {
        return $this->image->getWidth();
    }

    /**
     * @return int
     */
    protected function getImageHeight(): int
    {
        return $this->image->getHeight();
    }
}
