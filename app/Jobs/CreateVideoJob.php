<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264;


class CreateVideoJob implements ShouldQueue
{

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;


    public function __construct(
        protected array $path,
        protected int $user,
        protected array $meta
    ){}



    public function handle(): void
    {
        $duration = FFMpeg::fromDisk('videos')
            ->open($this->path['original'])
            ->getDurationInMiliseconds();

        $low = (new X264)->setKiloBitrate(1000);
        $mid = (new X264)->setKiloBitrate(2500);
        $high = (new X264)->setKiloBitrate(4000);

        $export = FFMpeg::fromDisk('videos')
            ->open($this->path['original'])
            ->exportForHLS();

        if (!$this->meta['public']) {
            $export->withRotatingEncryptionKey(
                function ($filename, $contents) {
                    Storage::disk('videos')
                        ->put("keys/$filename", $contents);
                }
            );
        }

        $export
            ->addFormat($low, function ($media) { $media->scale(640,360); })
            ->addFormat($mid, function ($media) { $media->scale(1280,720); })
            ->addFormat($high, function ($media) { $media->scale(1920,1080);})
            ->toDisk('videos')
            ->save($this->path['hls'].'/index.m3u8');

        $video = Video::create([
            'video' => $this->path['hls'].'/index.m3u8',
            'duration' => $duration,
            'user_id' => $this->user,
            'category_id' => $this->meta['category_id'],
            'public' => $this->meta['public'],
            'title' => $this->meta['title'],
            'description' => $this->meta['description'],
            'thumbnail' => $this->meta['thumbnail'],
        ]);

        if (!empty($this->meta['tags'])) {
            $video->tags()->sync($this->meta['tags']);
        }

        Storage::disk('videos')->delete($this->path['original']);
    }
}
