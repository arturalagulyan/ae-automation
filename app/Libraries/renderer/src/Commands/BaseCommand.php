<?php

namespace Renderer\Commands;

use Renderer\Commands\Traits\Async;

/**
 * Class BaseCommand
 * @package Renderer\Commands
 */
abstract class BaseCommand
{
    use Async;

    /**
     * @var resource|bool
     */
    protected $process;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $descriptorspec = [
        [
            'pipe',
            'r'
        ],
        [
            'pipe',
            'w'
        ],
    ];

    /**
     * @param array $data
     * @return BaseCommand
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return string
     */
    public abstract function command();

    /**
     * @return bool
     */
    public function run()
    {
        while (@ ob_end_flush());

        flush();

        $this->process = proc_open(
            $this->commandWithOptions(),
            $this->descriptorspec,
            $pipes
        );

        if (is_resource($this->process)) {
            while ($s = fgets($pipes[1])) {
                echo $s;
                flush();
            }
        }

        return $this->process;
    }

    /**
     * @return int
     */
    public function kill()
    {
        if (!is_resource($this->process)) {
            return 0;
        }

        return proc_close($this->process);
    }

    /**
     * @return string
     */
    protected function commandWithOptions()
    {
        $command = $this->command();

        foreach ($this->options as $option) {
            $command .= " $option";
        }

        return $command;
    }
}
