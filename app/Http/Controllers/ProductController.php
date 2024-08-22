<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:cooperative');
    }

    public function index()
    {
        $products = Product::where('user_id', auth()->id())->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric|min:0',
        ]);

        $validatedData['user_id'] = auth()->id();

        Product::create($validatedData);

        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente.');
    }

    // Otros métodos como show, edit, update, destroy...
}
