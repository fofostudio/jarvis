<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:cooperative');
    }

    public function index()
    {
        $sales = Sale::with(['product'])
            ->whereHas('product', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->paginate(10);
        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('user_id', auth()->id())->get();
        return view('sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'sale_date' => 'required|date',
        ]);

        $product = Product::where('id', $validatedData['product_id'])
                          ->where('user_id', auth()->id())
                          ->firstOrFail();

        $totalPrice = $product->price * $validatedData['quantity'];

        Sale::create([
            'product_id' => $validatedData['product_id'],
            'user_id' => auth()->id(),
            'quantity' => $validatedData['quantity'],
            'total_price' => $totalPrice,
            'sale_date' => $validatedData['sale_date'],
        ]);

        return redirect()->route('sales.index')->with('success', 'Venta registrada exitosamente.');
    }

    // Otros métodos como show, edit, update, destroy...
}
