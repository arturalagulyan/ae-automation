<?php

namespace Renderer\Steps;

use Illuminate\Support\Arr;
use Renderer\Commands\Traits\Destinations;
use Renderer\Commands\FFMPEG;
use Renderer\Commands\JpegSequence;
use Renderer\Commands\WAV;

/**
 * Class Rendering
 * @package Renderer\Steps
 */
class Rendering extends BaseStep
{
    use Destinations;

    /**
     * @var WAV
     */
    protected $wavCommand;

    /**
     * @var FFMPEG
     */
    protected $ffmpegCommand;

    /**
     * @var JpegSequence
     */
    protected $jpegSequenceCommand;

    /**
     * Rendering constructor.
     * @param WAV $wav
     * @param FFMPEG $ffmpeg
     * @param JpegSequence $jpegSequence
     */
    public function __construct(
        WAV $wav,
        FFMPEG $ffmpeg,
        JpegSequence $jpegSequence
    )
    {
        $this->wavCommand = $wav;
        $this->ffmpegCommand = $ffmpeg;
        $this->jpegSequenceCommand = $jpegSequence;
    }

    /**
     * @return bool|mixed
     */
    public function process()
    {
        $this->createRenderFolder();
        $this->processJpegSequence();
        $this->processWav();

        while (
            $this->wavCommand->isWorking() ||
            $this->jpegSequenceCommand->isWorking()
        ) {
            sleep(5);
        }

        $this->checkCorruptedFiles();
        $this->createOutputFolder();
        $this->processFFMPEG();

        return true;
    }

    /**
     *
     */
    protected function createRenderFolder()
    {
        if (!file_exists($this->getRenderFolder())) {
            mkdir($this->getRenderFolder(), 0777, true);
        }
    }

    /**
     *
     */
    protected function createOutputFolder()
    {
        if (!file_exists($this->getOutputFolder())) {
            mkdir($this->getOutputFolder(), 0777, true);
        }
    }

    /**
     *
     */
    protected function processWav()
    {
        $this->wavCommand->setData($this->data);

        if (Arr::get($this->data, 'options.wav')) {
            $this->wavCommand->setOptions($this->data['options']['wav']);
        }

        $this->wavCommand->runAsync();
    }

    /**
     *
     */
    protected function processJpegSequence()
    {
        for ($i = 0; $i < $this->data['sequence_n']; $i++) {
            $this->jpegSequenceCommand->setData($this->data);

            if (Arr::get($this->data, 'options.seq')) {
                $this->jpegSequenceCommand->setOptions($this->data['options']['seq']);
            }

            $this->jpegSequenceCommand->runAsync();
        }
    }

    /**
     *
     */
    protected function processFFMPEG()
    {
        $this->ffmpegCommand->setData($this->data);

        if (Arr::get($this->data, 'options.ffmpeg')) {
            $this->ffmpegCommand->setOptions($this->data['options']['ffmpeg']);
        }

        $this->ffmpegCommand->run();
    }

    /**
     * @return bool
     */
    protected function checkCorruptedFiles()
    {
        $files = glob($this->getRenderFolder() . '*');

        $corrupted = false;

        foreach ($files as $file) {
            if (filesize($file)) {
                continue;
            }

            $corrupted = true;
            unlink($file);
        }

        if (!$corrupted) {
            return true;
        }

        return $this->jpegSequenceCommand->run();
    }
}
