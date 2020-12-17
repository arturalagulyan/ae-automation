<?php

namespace Renderer\Traits;

/**
 * Trait Async
 * @package Renderer\Traits
 */
trait Async
{
    /**
     * @var array
     */
    private $pids = [];

    /**
     * @var array
     */
    private $tasks = [];

    /**
     * @var array
     */
    private $processes = [];

    /**
     * @param $taskCommand
     */
    public function addTask($taskCommand)
    {
        $this->tasks[] = $taskCommand;
    }

    /**
     *
     */
    public function work()
    {
        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
        ];

        foreach ($this->tasks as $task) {
            $process = proc_open("start /B $task", $descriptorspec, $pipes);
            $status = proc_get_status($process);
            $this->pids[] = $status['pid'];

            $this->processor->save($this->data['id'], $status['pid']);
            $this->processes[] = $process;
        }
    }

    /**
     *
     */
    public function stop()
    {
        foreach ($this->processes as $process) {
            proc_close($process);
        }
    }

    /**
     * @return bool
     */
    public function isWorking(): bool
    {
        $working = false;

        foreach ($this->pids as $pid) {

            if ($this->processor->isKilled($this->data['id'], $pid)) {
                $this->killed = true;
                return false;
            }

            $executionString = trim(shell_exec(sprintf('wmic process where ParentProcessId=%s', $pid)));

            if (!empty($executionString)) {
                $working = true;
                continue;
            }

            if (($key = array_search($pid, $this->pids)) !== false) {
                unset($this->pids[$key]);
            }

            $this->processor->remove($this->data['id'], $pid);
        }

        if (!$working) {
            $this->clearTasks();
        }

        return $working;
    }

    /**
     *
     */
    public function clearTasks()
    {
        $this->pids = [];
        $this->tasks = [];
    }
}
