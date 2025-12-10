<?php

namespace App\Jobs;

use App\Models\Video;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class CreateVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $thumbnail;
    protected $path;
    protected $user;
    protected $other;

    /**
     * Create a new job instance.
     */
    public function __construct($thumbnail, $path, $user, $other)
    {
        $this->thumbnail = $thumbnail;
        $this->path = $path;
        $this->user = $user;
        $this->other = $other;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $milliseconds = FFMpeg::fromDisk('local')->open($this->path['default'])->getDurationInMiliseconds();
        $lowBitrate = (new X264)->setKiloBitrate(250);
        $midBitrate = (new X264)->setKiloBitrate(500);
        $highBitrate = (new X264)->setKiloBitrate(1000);
        FFMpeg::fromDisk('local')
            ->open("{$this->path['default']}")
            ->exportForHLS()
            ->addFormat($lowBitrate, function ($media) {
                $media->scale(640, 360);
            })
            ->addFormat($midBitrate, function ($media) {
                $media->scale(1280, 720);
            })
            ->addFormat($highBitrate, function ($media) {
                $media->scale(1920, 1080);
            })
            ->toDisk('media')
            ->save("{$this->path['hls']}");
        Video::create([
                'video' => "uploads/playlist/{$this->path['hls']}",
                'duration' => $milliseconds,
                'thumbnail' => $this->thumbnail,
                'user_id' => $this->user,
            ] + $this->other);
    }
}
