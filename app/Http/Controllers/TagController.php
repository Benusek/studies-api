<?php

namespace App\Http\Controllers;

use App\Models\Tag;

class TagController extends Controller
{
    /**
     * Get all tags
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index()
    {
        return Tag::all();
    }
}
