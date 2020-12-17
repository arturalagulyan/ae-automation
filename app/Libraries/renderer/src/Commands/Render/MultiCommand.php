<?php

namespace Renderer\Commands\Render;

/**
 * Class MultiCommand
 * @package Renderer\Commands\Render
 */
class MultiCommand extends BaseRenderCommand
{
    /**
     * @return bool
     */
    public function execute()
    {
        if (!$this->executeSeqWavCommands()) {
            return false;
        }

        if (!$this->executeFFmpegCommand()) {
            return false;
        }

        return true;
    }

    /**
     *
     */
    public function createRenderFolder()
    {
        if (!file_exists($this->renderFolder)) {
            mkdir($this->renderFolder, 0777, true);
        }
    }

    /**
     * @return string
     */
    protected function getRenderFolder(): string
    {
        return $this->config['render_folder'] . $this->data['id'];
    }

    /**
     * @return bool
     */
    protected function executeSeqWavCommands()
    {
        $seqCommand = $this->generateSeqCommand();
        $wavCommand = $this->generateWavCommand();

        $n = $this->data['sequence_n'];

        for ($i = 0; $i < $n; $i++) {
            $this->addTask($seqCommand);
        }

        $this->addTask($wavCommand);

        $this->work();

        while ($this->isWorking()) {
            sleep(5);
        }

        $this->checkCorruptedFiles();

        if ($this->isKilled()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function checkCorruptedFiles()
    {
        $files = glob($this->renderFolder . DIRECTORY_SEPARATOR . '*');

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

        $command = $this->generateSeqCommand();

        return $this->runCommand($command);
    }

    /**
     * @return bool
     */
    protected function executeFFmpegCommand()
    {
        $command = $this->generateFFmpegCommand();
        return $this->runCommand($command);
    }

    /**
     * @return string
     */
    protected function generateSeqCommand()
    {
        $command1 = 'c:\"Program Files"\Adobe\"Adobe After Effects CC 2017"\"Support Files"\aerender.exe';
        $command1 .= ' -project Z:\AEWatchFolder\{{filename}}.aep -comp "{{filename}}"';
        $command1 .= ' -RStemplate "{{rsTemplate}}" -OMtemplate "{{omTemplate}}"';
        $command1 .= ' -output "{{renderFolder}}\{{filename}}_[####]{{extension}}"';

        $command1 = str_replace('{{filename}}', $this->message['filename'], $command1);
        $command1 = str_replace('{{rsTemplate}}', $this->message['rs'], $command1);
        $command1 = str_replace('{{omTemplate}}', $this->message['om'], $command1);
        $command1 = str_replace('{{extension}}', $this->message['output_extension'], $command1);
        $command1 = str_replace('{{renderFolder}}', $this->renderFolder, $command1);

        return $command1;
    }

    /**
     * @return string
     */
    protected function generateWavCommand()
    {
        $command2 = 'c:\"Program Files"\Adobe\"Adobe After Effects CC 2017"\"Support Files"\aerender.exe';
        $command2 .= ' -project "Z:\AEWatchFolder\{{filename}}.aep" -comp "{{filename}}"';
        $command2 .= ' -RStemplate "best-full" -OMtemplate "wav-audio"';
        $command2 .= ' -output "{{renderFolder}}\{{filename}}.wav"';

        $command2 = str_replace('{{filename}}', $this->message['filename'], $command2);
        $command2 = str_replace('{{renderFolder}}', $this->renderFolder, $command2);

        return $command2;
    }

    /**
     * @return string
     */
    protected function generateFFmpegCommand()
    {
        $command3 = 'c:\test\ffmpeg\bin\ffmpeg';
        $command3 .= ' -r {{frameRate}} -f image2 -start_number {{startFrame}}';
        $command3 .= ' -i {{renderFolder}}\{{filename}}_%04d.jpg';
        $command3 .= ' -i {{renderFolder}}\{{filename}}.wav -pix_fmt yuv420p';
        $command3 .= ' {{renderFolder}}\{{filename}}.mp4';
        $command3 .= ' {{codec}}';

        $startFrame = 0000;
        $codec = "-c:v libx264 -preset slower -crf 17 -c:a aac -b:a 128k";

        $command3 = str_replace('{{codec}}', $codec, $command3);
        $command3 = str_replace('{{startFrame}}', $startFrame, $command3);
        $command3 = str_replace('{{renderFolder}}', $this->renderFolder, $command3);
        $command3 = str_replace('{{frameRate}}', $this->message['video_fps'], $command3);
        $command3 = str_replace('{{filename}}', $this->message['filename'], $command3);

        return $command3;
    }
}
