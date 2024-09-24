<?php

namespace App\Http\Controllers;

use App\Models\CategoryLink;
use App\Models\Link;
use Illuminate\Http\Request;

class CategoryLinkController extends Controller
{
    public function index()
    {
        $categoryLinks = CategoryLink::all();
        return view('admin.categoryLinksManagement', compact('categoryLinks'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:category_links']);
        CategoryLink::create($request->all());
        return redirect()->route('category_links.index')->with('success', 'Categoría de link creada exitosamente.');
    }

    // Añade más métodos según sea necesario (edit, update, destroy)
}
