<?php

namespace Renderer\Traits;

use Illuminate\Support\Facades\File;

/**
 * Trait Loggable
 * @package Renderer\Traits
 */
trait Loggable
{
    /**
     * @var string
     */
    protected $logFilename;

    /**
     * @param string $filename
     * @return $this
     */
    public function setLogFile(string $filename)
    {
        $this->logFilename = $filename;

        return $this;
    }

    /**
     * @param $content
     */
    public function log($content)
    {
        if (empty($this->logFilename)) {
            return;
        }

        $path = renderer_conf('logs_folder');
        $file = $path . $this->logFilename . '.log';

        if(!File::isDirectory($path)){
            File::makeDirectory($path, 0777, true);
        }
        if (!File::exists($file)) {
            touch($file);
        }

        File::append($file, $content);
    }
}
