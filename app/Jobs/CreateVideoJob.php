<?php

namespace App\Jobs;

use App\Models\Video;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class CreateVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $path;
    protected $user;
    protected $meta;

    /**
     * Create a new job instance.
     */
    public function __construct($path, $user, $meta)
    {
        $this->path = $path;
        $this->user = $user;
        $this->meta = $meta;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $milliseconds = FFMpeg::fromDisk('local')->open($this->path['default'])->getDurationInMiliseconds();
            $lowBitrate = (new X264)->setKiloBitrate(1000);
            $midBitrate = (new X264)->setKiloBitrate(2500);
            $highBitrate = (new X264)->setKiloBitrate(4000);

            $hls = FFMpeg::fromDisk('local')
                ->open("{$this->path['default']}")
                ->exportForHLS();

            if (!$this->meta['public']) {
                $hls->withRotatingEncryptionKey(function ($filename, $contents) {
                    Storage::disk('media')->put("keys/{$filename}", $contents);
                });
            }
            $hls->addFormat($lowBitrate)
                ->addFormat($midBitrate)
                ->addFormat($highBitrate)
                ->toDisk('media')
                ->save("{$this->path['hls']}");


            Video::create([
                    'video' => "uploads/playlist/{$this->path['hls']}",
                    'duration' => $milliseconds,
                    'user_id' => $this->user,
                ] + $this->meta);
//        Storage::disk('local')->delete($this->path['default']);
        } catch (\Exception $err) {
            dd($err->getMessage());
        }

    }
}
