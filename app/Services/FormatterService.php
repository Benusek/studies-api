<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class FormatterService
{
    public static function preview($file)
    {
        $path = pathinfo($file, PATHINFO_FILENAME) . '.jpeg';
        FFMpeg::fromDisk('videos')
            ->open($file)
            ->getFrameFromSeconds(0)
            ->export()
            ->toDisk('previews')
            ->save($path);
        return $path;
    }
}
