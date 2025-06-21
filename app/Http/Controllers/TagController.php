<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Вывод всех тегов
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Tag::all();
    }
}
