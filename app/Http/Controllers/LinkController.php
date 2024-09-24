<?php

namespace App\Http\Controllers;

use App\Models\CategoryLink;
use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index()
    {
        $links = Link::with('categoryLink')->get();
        $categoryLinks = CategoryLink::all();
        return view('admin.managementlinks', compact('links', 'categoryLinks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'url' => 'required|url',
            'category_link_id' => 'required|exists:category_links,id'
        ]);

        $link = new Link($request->all());
        $link->favicon = $this->getFavicon($request->url);
        $link->save();

        return redirect()->route('links.index')->with('success', 'Link creado exitosamente.');
    }

    private function getFavicon($url)
    {
        $parsed = parse_url($url);
        $host = $parsed['host'];
        return "https://www.google.com/s2/favicons?domain=" . $host;
    }

    // Añade más métodos según sea necesario (edit, update, destroy)
}

