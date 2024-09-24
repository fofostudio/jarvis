<?php

namespace App\Http\Controllers;

use App\Models\CategoryLink;
use App\Models\Link;
use Illuminate\Http\Request;

class DictionaryController extends Controller
{
    public function index()
    {
        $categoryLinks = CategoryLink::with('links')->get();
        return view('operator.dictionaryLinks', compact('categoryLinks'));
    }
}
