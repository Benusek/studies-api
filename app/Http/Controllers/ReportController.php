<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportAddRequest;
use App\Http\Requests\ReportDeleteRequest;
use App\Http\Resources\ReportResource;
use App\Models\Report;
use App\Models\ReportVideo;
use App\Models\Subscribe;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Просмотр всех жалоб
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return ReportResource::collection(ReportVideo::all());
    }

    /**
     * Добавление жалобы
     * @param ReportAddRequest $request
     * @param Video $video
     * @param Report $report
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ReportAddRequest $request, Video $video, Report $report)
    {
        ReportVideo::create([
            'video_id' => $video->id,
            'report_id' => $report->id,
            'user_id' => Auth::id()
        ]);
        return parent::response($video, 'reported');
    }

    /**
     * Удаление жалобы
     * @param ReportVideo $report
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ReportVideo $report)
    {
        return parent::delete($report);
    }
}
