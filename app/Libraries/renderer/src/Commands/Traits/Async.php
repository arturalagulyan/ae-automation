<?php

namespace Renderer\Commands\Traits;

/**
 * Trait Async
 * @package Renderer\Traits
 */
trait Async
{
    /**
     * @var string
     */
    protected $pid;

    /**
     * @return bool
     */
    public function runAsync()
    {
        $this->process = proc_open(
            "start /B " . $this->commandWithOptions(),
            $this->descriptorspec,
            $pipes
        );

        $status = proc_get_status($this->process);
        $this->pid = $status['pid'];

        return true;
    }

    /**
     * @return string
     */
    public function killAsync()
    {
        return shell_exec(sprintf('wmic process where ParentProcessId=%s call terminate', $this->pid));
    }

    /**
     * @return bool
     */
    public function isWorking()
    {
        return !!trim(shell_exec(sprintf('wmic process where ParentProcessId=%s', $this->pid)));
    }
}
