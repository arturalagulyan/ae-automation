<?php

namespace Renderer\Commands;

use Renderer\Events\CommandFinished;
use Renderer\Events\CommandKilled;
use Renderer\Events\CommandProgress;
use Renderer\Events\CommandStarted;
use Renderer\Processor;
use Renderer\Traits\Async;

/**
 * Class BaseCommand
 * @package Renderer\Commands
 */
abstract class BaseCommand
{
    use Async;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var Processor
     */
    protected $processor;

    /**
     * @var bool
     */
    protected $killed = false;

    /**
     * BaseCommand constructor.
     * @param Processor $processor
     */
    public function __construct(Processor $processor)
    {
        $this->processor = $processor;
        $this->config = config('renderer');
    }

    /**
     * @return bool
     */
    public function isKilled(): bool
    {
        return $this->killed;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return mixed
     */
    abstract public function execute();

    /**
     * @param $command
     * @return bool
     */
    public function runCommand($command): bool
    {
        event(new CommandStarted([
            'data' => $this->data,
            'command' => $command,
        ]));

        while (@ ob_end_flush());

        $descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
        );

        flush();

        $process = proc_open($command, $descriptorspec, $pipes);

        $processId = $this->processor->getId($process);
        $this->processor->save($this->data['id'], $processId);

        if (is_resource($process)) {
            while ($s = fgets($pipes[1])) {

                if ($this->processor->isKilled($this->data['id'], $processId)) {
                    $this->killed = true;

                    event(new CommandKilled([
                        'data' => $this->data,
                        'command' => $command,
                        'process' => $process,
                    ]));

                    return false;
                }

                echo $s;

                event(new CommandProgress([
                    'data' => $this->data,
                    'command' => $command,
                    'line' => $s,
                    'process' => $process,
                ]));

                flush();
            }
        }

        $this->processor->remove($this->data['id'], $processId);

        event(new CommandFinished([
            'data' => $this->data,
            'command' => $command,
        ]));

        return true;
    }
}
