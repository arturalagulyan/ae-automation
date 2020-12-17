<?php

namespace Renderer;

/**
 * Class Processor
 * @package Renderer
 */
class Processor
{
    /**
     * @param $project
     * @param $processId
     * @return bool
     */
    public function save($project, $processId): bool
    {
        $folder = $this->createFolder();

        if (!$this->has($project)) {
            file_put_contents($this->getFile($project), $processId);
            return true;
        }

        $processIds = $this->getIds($project);

        array_push($processIds, $processId);

        file_put_contents($this->getFile($project), implode(",", $processIds));

        return true;
    }

    /**
     * @param $project
     * @param $processId
     * @return bool
     */
    public function remove($project, $processId): bool
    {
        $folder = $this->getFolder();

        if (!$this->has($project)) {
            return true;
        }

        $processIds = $this->getIds($project);

        if (in_array($processId, $processIds)) {
            $index = array_search($processId, $processIds);
            unset($processIds[$index]);
        }

        if (empty($processIds)) {
            unlink($this->getFile($project));
            return true;
        }

        file_put_contents($this->getFile($project), implode(",", $processIds));

        return true;
    }

    /**
     * @param $project
     * @return bool
     */
    public function kill($project): bool
    {
        if (!$this->has($project)) {
            return true;
        }

        foreach ($this->getIds($project) as $processId) {
//            shell_exec(sprintf('wmic process where ParentProcessId=%s call terminate', $processId));
            $this->remove($project, $processId);
        }

        return true;
    }

    /**
     * @param $project
     * @param $processId
     * @return bool
     */
    public function isKilled($project, $processId): bool
    {
        if (!$this->has($project)) {
            return true;
        }

        return !in_array($processId, $this->getIds($project));
    }

    /**
     * @param $project
     * @return array
     */
    public function getIds($project): array
    {
        $contents = file_get_contents($this->getFile($project));
        return explode(",", $contents);
    }

    /**
     * @param $project
     * @return bool
     */
    public function has($project): bool
    {
        return file_exists($this->getFile($project));
    }

    /**
     * @param $process
     * @return int|null
     */
    public function getId($process): ?int
    {
        $status = proc_get_status($process);

        return (isset($status['pid']) ? $status['pid'] : null);
    }

    /**
     * @return string
     */
    public function createFolder(): string
    {
        $folder = $this->getFolder();

        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        return $folder;
    }

    /**
     * @return string
     */
    public function getFolder(): string
    {
        return config('renderer.processes_folder');
    }

    /**
     * @param $projectId
     * @return string
     */
    public function getFile($projectId): string
    {
        return $this->getFolder() . DIRECTORY_SEPARATOR . $projectId;
    }
}
