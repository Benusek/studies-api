<?php

namespace App\Services;

use App\Http\Requests\SearchRequest;
use App\Models\Playlist;
use App\Models\Video;

class SearchService
{
    public static function video(SearchRequest $request)
    {
        return Video::query()
            ->where('public', true)
            ->when($request->get('str'), fn($q) => $q->where('title', 'LIKE', "%{$request->get('str')}%")
                ->orWhereHas('user', fn($u) => $u->where('name', 'LIKE', "%{$request->get('str')}%")
                )
            )
            ->when($request->categories, fn($q) => $q->whereIn('category_id', $request->categories)
            )
            ->when($request->tags, fn($q) => $q->whereHas('tags', fn($t) => $t->whereIn('tags.id', $request->tags))
            )
            ->with(['user', 'tags']);
    }

    public static function playlist(SearchRequest $request)
    {
        return Playlist::query()
            ->where('public', true)
            ->whereHas('videos')
            ->when($request->get('str'), fn($q) => $q->where('title', 'LIKE', "%{$request->get('str')}%"))
            ->when($request->categories, fn($q) => $q->whereHas('videos', fn($v) => $v->whereIn('category_id', $request->categories)))
            ->when($request->tags, fn($q) => $q->whereHas('videos.tags', fn($t) => $t->whereIn('tags.id', $request->tags)))
            ->withCount('videos');
    }
}
