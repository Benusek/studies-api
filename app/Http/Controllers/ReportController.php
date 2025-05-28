<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Просмотр всех жалоб
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Report::all();
    }

    /**
     * Добавление жалобы
     * @return string
     */
    public function store()
    {
        return "store report";
    }

    /**
     * Удаление жалобы
     * @return string
     */
    public function destroy()
    {
        return "destroy report";
    }
}
