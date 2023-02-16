<?php

namespace Uploader;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Uploader\Traits\Images;
use Uploader\Traits\Videos;

/**
 * Class Uploader
 * @package Uploader
 */
class Uploader
{
    use Images,
        Videos;

    /**
     * @var UploadedFile
     */
    protected $file;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * @var string
     */
    protected $storageDisk;

    /**
     * @var string
     */
    protected $storagePath;

    /**
     * @var array
     */
    protected $storageDisks = [
        UploaderConstants::DISK_S3 => 's3',
        UploaderConstants::DISK_LOCAL => 'local',
        UploaderConstants::DISK_PUBLIC => 'public',
    ];

    /**
     * BaseUploader constructor.
     */
    public function __construct()
    {
        $this->storageDisk = config('uploader.disk');
        $this->storagePath = config('uploader.default_path');
    }

    /**
     * @param $name
     * @return Uploader
     */
    public function setFileName($name): self
    {
        $this->fileName = $name;

        return $this;
    }

    /**
     * @param $diskName
     * @return $this
     */
    public function setDisk($diskName): self
    {
        $this->storageDisk = $diskName;

        return $this;
    }

    /**
     * @param $diskKey
     * @return $this
     */
    public function setDiskKey($diskKey): self
    {
        $this->storageDisk = $this->storageDisks[$diskKey];

        return $this;
    }

    /**
     * @param $path
     * @return $this
     */
    public function setStoragePath($path): self
    {
        $path = str_replace(['.', '/'], DIRECTORY_SEPARATOR, $path);
        $this->storagePath = $path;

        $this->checkPath();

        return $this;
    }

    /**
     * @param string $path
     * @return string|null
     */
    public function getUrlByPath(string $path): ?string
    {
        return Storage::disk($this->storageDisk)->url($path);
    }

    /**
     * @param string $directory
     * @return array
     */
    public function getFilesInDirectory(string $directory): array
    {
        return Storage::disk($this->storageDisk)->files($directory);
    }

    /**
     * @param UploadedFile $file
     * @param array $options
     * @return array
     * @throws \Exception
     */
    public function upload(UploadedFile $file, array $options = []): array
    {
        $this->file = $file;

        if (!$this->fileName) {
            $this->fileName = $this->generateFileName();
        }

        if ($this->isImage()) {
            $result = $this->uploadAsImage($this->file, $options);
        } elseif ($this->isVideo()) {
            $result = $this->uploadAsVideo($this->file, $options);
        } else {
            $result = $this->uploadAsFile($this->file, $options);
        }

        $response = !$result ? $this->failedResponse() : $this->uploadedResponse();

        $this->setFileName(null);

        return $response;
    }

    /**
     * @param UploadedFile $file
     * @param array $options
     * @return false|string
     */
    protected function uploadAsFile(UploadedFile $file, array $options = [])
    {
        return Storage::disk($this->storageDisk)->putFileAs($this->storagePath, $file, $this->fileName);
    }

    /**
     * @param string|null $fileName
     * @return bool
     */
    public function delete(?string $fileName = null): bool
    {
        $this->fileName = $fileName ?? $this->fileName;
        $storage = Storage::disk($this->storageDisk);

        if ($storage->exists($this->getFilePath())) {
            return $storage->delete($this->getFilePath());
        }

        return false;
    }

    /**
     *
     */
    protected function checkPath(): void
    {
        if (in_array($this->storageDisk, ['local', 'public'])) {
            Storage::disk($this->storageDisk)->makeDirectory($this->storagePath);
        }
    }

    /**
     * @return string
     */
    protected function getFullPath(): string
    {
        return Storage::disk($this->storageDisk)->path($this->getFilePath());
    }

    /**
     * @return string
     */
    protected function getFilePath(): string
    {
        $path = $this->storagePath;

        if (!Str::endsWith($path, DIRECTORY_SEPARATOR)) {
            $path .= DIRECTORY_SEPARATOR;
        }

        return $path . $this->fileName;
    }

    /**
     * @return string
     */
    protected function generateFileName(): string
    {
        $extension = $this->file->getClientOriginalExtension();

        return md5(Str::random(20)) . '.' . $extension;
    }

    /**
     * @throws \Exception
     */
    protected function failedResponse(): array
    {
        throw new \Exception('Failed to upload file');
    }

    /**
     * @return array
     */
    protected function uploadedResponse(): array
    {
        $response = [
            'path' => $this->getFilePath(),
            'fullPath' => $this->getFullPath(),
            'name' => $this->fileName,
            'storagePath' => $this->storagePath,
            'mimeType' => $this->file->getMimeType(),
            'size' => $this->file->getSize(),
            'originalName' => $this->file->getClientOriginalName(),
            'url' => Storage::disk($this->storageDisk)->url($this->getFilePath())
        ];

        if ($this->isImage()) {
            $response['width'] = $this->getImageWidth();
            $response['height'] = $this->getImageHeight();
        }
        if ($this->isVideo()) {
            $response['width'] = $this->getVideoWidth();
            $response['height'] = $this->getVideoHeight();
            $response['duration'] = $this->getVideoDuration();
        }

        return $response;
    }
}
