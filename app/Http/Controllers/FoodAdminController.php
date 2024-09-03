<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;

class FoodAdminController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('foodAdmin.index', compact('products'));
    }

    public function create()
    {
        return view('foodAdmin.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric',
        ]);

        $product = Product::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'user_id' => auth()->id(),
        ]);
        return redirect()->route('foodAdmin.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        return view('foodAdmin.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'price' => 'required|numeric',
        ]);

        $product->update($validatedData);

        return redirect()->route('foodAdmin.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('foodAdmin.index')->with('success', 'Product deleted successfully.');
    }

    public function showSales()
    {
        $sales = Sale::with('product', 'user')->get();
        return view('foodAdmin.sales', compact('sales'));
    }

    public function createSale()
    {
        $users = User::all();
        $products = Product::all();
        return view('foodAdmin.createSale', compact('products', 'users'));
    }

    public function storeSale(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'products' => 'required|array',
            'products.*' => 'required|exists:products,id',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
            'sale_date' => 'required|date',
        ]);

        $user = User::findOrFail($validatedData['user_id']);
        $products = Product::findOrFail($validatedData['products']);
        $quantities = $validatedData['quantities'];
        $saleDate = $validatedData['sale_date'];

        $totalPrice = 0;
        foreach ($products as $index => $product) {
            $quantity = $quantities[$index];
            $totalPrice += $product->price * $quantity;
        }

        $sale = Sale::create([
            'user_id' => $user->id,
            'total_price' => $totalPrice,
            'sale_date' => now(),
            'responsible_id' => auth()->id(),
        ]);

        foreach ($products as $index => $product) {
            $quantity = $quantities[$index];
            $sale->products()->attach($product->id, ['quantity' => $quantity]);
        }

        return redirect()->route('foodAdmin.sales')->with('success', 'Sale recorded successfully.');
    }

    public function showSalesReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $sales = Sale::with('product', 'user')
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('sale_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('sale_date', '<=', $endDate);
            })
            ->get();

        return view('foodAdmin.salesReport', compact('sales', 'startDate', 'endDate'));
    }
}
