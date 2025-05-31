<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportAddRequest;
use App\Http\Requests\ReportDeleteRequest;
use App\Models\Report;
use App\Models\ReportVideo;
use App\Models\Video;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Просмотр всех жалоб
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return ReportVideo::all();
    }

    /**
     * Добавление жалобы
     * @return string
     */
    public function store(ReportAddRequest $request, Video $video, Report $report)
    {

        return "store report";
    }

    /**
     * Удаление жалобы
     * @return string
     */
    public function destroy(ReportVideo $report)
    {
        return "destroy report";
    }
}
