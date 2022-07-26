<?php

namespace Uploader\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\MediaOpener;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

/**
 * Trait Videos
 * @package Uploader\Traits
 */
trait Videos
{
    /**
     * @var MediaOpener
     */
    protected $video;

    /**
     * @return bool
     */
    protected function isVideo(): bool
    {
        return substr($this->file->getMimeType(), 0, 5) === 'video';
    }

    /**
     * @param UploadedFile $file
     * @param array $options
     * @return bool
     */
    protected function uploadAsVideo(UploadedFile $file, array $options = []): bool
    {
        $result = Storage::disk($this->storageDisk)->putFileAs($this->storagePath, $file, $this->fileName);

        $this->createVideo();

        return $result;
    }

    /**
     *
     */
    protected function createVideo()
    {
        $this->video = FFMpeg::fromDisk($this->storageDisk)->open($this->getFilePath());
    }

    /**
     * @return int
     */
    protected function getVideoDuration(): int
    {
        return $this->video->getDurationInMiliseconds();
    }

    /**
     * @return int
     */
    protected function getVideoWidth(): int
    {
        return $this->video->getVideoStream()->getDimensions()->getWidth();
    }

    /**
     * @return int
     */
    protected function getVideoHeight(): int
    {
        return $this->video->getVideoStream()->getDimensions()->getHeight();
    }
}
