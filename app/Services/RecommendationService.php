<?php

namespace App\Services;

use App\Models\Video;
use Illuminate\Support\Facades\Cache;

class RecommendationService
{
    public static function get(Video $video)
    {
        return Cache::remember(
            "recommendations:video:{$video->id}",
            now()->addMinutes(10),
            fn () => self::build($video)->get()
        );
    }

    protected static function build(Video $video)
    {
        $tags = $video->tags()->pluck('tags.id');

        $base = Video::query()
            ->where('id', '!=', $video->id)
            ->where('public', true)
            ->withCount([
                'tags as matched_tags_count' => fn ($q) =>
                $q->whereIn('tags.id', $tags),
            ]);

        return Video::query()
            ->fromSub($base, 'videos')
            ->selectRaw(
                '
                videos.*,
                (
                    (category_id = ?) * 3 +
                    (user_id = ?) * 1 +
                    (matched_tags_count * 2)
                ) as score
                ',
                [$video->category_id, $video->user_id]
            )
            ->orderByDesc('score')
            ->limit(15);
    }
}
