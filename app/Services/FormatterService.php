<?php

namespace App\Services;

use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class FormatterService
{
    public static function preview($file)
    {
        $path = 'previews/' . pathinfo($file, PATHINFO_FILENAME) . '.jpeg';
        FFMpeg::fromDisk('local')
            ->open($file)
            ->getFrameFromSeconds(0)
            ->export()
            ->toDisk('local')
            ->save($path);
        return $path;
    }
}
