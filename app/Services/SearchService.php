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

            ->when($request->filled('str'), function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('title', 'LIKE', "%{$request->str}%")
                        ->orWhereHas('user', function ($user) use ($request) {
                            $user->where('name', 'LIKE', "%{$request->str}%");
                        });
                });
            })

            ->when(!empty($request->categories), function ($q) use ($request) {
                $q->whereIn('category_id', $request->categories);
            })

            ->when(!empty($request->tags), function ($q) use ($request) {
                $tags = (array) $request->tags;

                $q->whereHas('tags', function ($tag) use ($tags) {
                    $tag->whereIn('tags.id', $tags);
                });
            })

            ->with(['user', 'tags']);
    }

    public static function playlist(SearchRequest $request)
    {
        return Playlist::query()
            ->where('public', true)
            ->whereHas('videos')

            ->when($request->filled('str'), function ($q) use ($request) {
                $q->where('title', 'LIKE', "%{$request->str}%");
            })

            ->when(!empty($request->categories), function ($q) use ($request) {
                $q->whereHas('videos', function ($video) use ($request) {
                    $video->whereIn('category_id', $request->categories);
                });
            })

            ->when(!empty($request->tags), function ($q) use ($request) {
                $tags = (array) $request->tags;

                $q->whereHas('videos.tags', function ($tag) use ($tags) {
                    $tag->whereIn('tags.id', $tags);
                });
            })

            ->withCount('videos');
    }
}
