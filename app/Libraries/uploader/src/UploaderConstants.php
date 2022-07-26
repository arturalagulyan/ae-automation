<?php

namespace Uploader;

/**
 * Class UploaderConstants
 * @package Uploader
 */
class UploaderConstants
{
    const DISK_S3 = 1;
    const DISK_LOCAL = 2;
    const DISK_PUBLIC = 3;

    const RESIZE_ACTION_FIT = 1;
    const RESIZE_ACTION_AUTO = 2;
    const RESIZE_ACTION_WIDEN = 3;
    const RESIZE_ACTION_HEIGHTEN = 4;
}
