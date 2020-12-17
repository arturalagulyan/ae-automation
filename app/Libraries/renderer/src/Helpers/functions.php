<?php

if (!function_exists('renderer_copy_r')) {

    /**
     * @param $source
     * @param $destination
     * @return bool
     */
    function renderer_copy_r($source, $destination): bool
    {
        if (is_link($source)) {
            return symlink(readlink($source), $destination);
        }

        if (is_file($source)) {
            return copy($source, $destination);
        }

        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $dir = dir($source);

        while (false !== $entry = $dir->read()) {
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            renderer_copy_r("$source/$entry", "$destination/$entry");
        }

        $dir->close();

        return true;
    }
}

if (!function_exists('renderer_clean_r')) {

    /**
     * @param null $directory
     * @return bool
     */
    function renderer_clean_r($directory = null): bool
    {
        if (!file_exists($directory)) {
            return false;
        }

        if (!is_dir($directory)) {
            return unlink($directory);
        }

        foreach (scandir($directory) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!renderer_clean_r($directory . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($directory);
    }
}
