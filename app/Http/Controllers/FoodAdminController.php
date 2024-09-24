<?php

namespace App\Http\Controllers;

use App\Models\CategoryProduct;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OperatorBalance;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

use Illuminate\Support\Facades\Log;

class FoodAdminController extends Controller
{
    public function index()
    {
        $products = Product::with('categoryProduct')
            ->where('user_id', Auth::id())
            ->get();
        return view('foodAdmin.index', compact('products'));
    }

    public function create()
    {
        $categories = CategoryProduct::where('user_id', Auth::id())->get();

        if ($categories->isEmpty()) {
            return redirect()->route('foodAdmin.categories.create')
                ->with('warning', 'Debes crear al menos una categoría antes de añadir productos.');
        }
        return view('foodAdmin.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'category_product_id' => 'required|exists:category_products,id',
        ]);

        $validatedData['user_id'] = Auth::id();

        $product = Product::create($validatedData);

        return redirect()->route('foodAdmin.index')
            ->with('success', 'Producto creado exitosamente.');
    }



    public function edit(Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $categories = CategoryProduct::where('user_id', Auth::id())->get();

        return view('foodAdmin.edit', compact('product', 'categories'));
    }


    public function update(Request $request, Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'nullable',
            'price' => 'required|numeric|min:0',
            'category_product_id' => 'required|exists:category_products,id',
        ]);

        $product->update($validatedData);

        return redirect()->route('foodAdmin.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }
    public function destroy(Product $product)
    {
        if ($product->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $product->delete();

        return redirect()->route('foodAdmin.index')->with('success', 'Product deleted successfully.');
    }



    public function showSales()
    {
        $userId = Auth::id();

        $todaySales = Sale::where('responsible_id', $userId)
            ->whereDate('sale_date', today())
            ->sum('total_price');
        $monthSales = Sale::where('responsible_id', $userId)
            ->whereMonth('sale_date', now()->month)
            ->sum('total_price');
        $totalSalesCount = Sale::where('responsible_id', $userId)->count();
        $averageSale = Sale::where('responsible_id', $userId)->avg('total_price');
        $totalSalesAmount = Sale::where('responsible_id', $userId)->sum('total_price');

        $paginatedSales = Sale::with(['product', 'user', 'responsibleUser'])
            ->where('responsible_id', $userId)
            ->latest('sale_date')
            ->paginate(100);

        $paginatedSales->getCollection()->transform(function ($sale) {
            $sale->sale_date = $sale->sale_date instanceof \DateTime
                ? $sale->sale_date
                : Carbon::parse($sale->sale_date);
            return $sale;
        });

        $operatorSummary = User::whereHas('salesAsResponsible', function ($query) use ($userId) {
            $query->where('responsible_id', $userId);
        })
            ->withSum(['salesAsResponsible as total_sales' => function ($query) use ($userId) {
                $query->where('responsible_id', $userId);
            }], 'total_price')
            ->withSum(['salesAsResponsible as total_vales' => function ($query) use ($userId) {
                $query->where('responsible_id', $userId)
                    ->whereHas('product', function ($q) {
                        $q->where('name', 'Vale');
                    });
            }], 'total_price')
            ->with('operatorBalance')
            ->get()
            ->map(function ($user) {
                return [
                    'name' => $user->name,
                    'total_sales_and_vales' => $user->total_sales,
                    'balance' => $user->operatorBalance->balance ?? 0
                ];
            });

        return view('foodAdmin.sales', compact(
            'paginatedSales',
            'todaySales',
            'monthSales',
            'totalSalesCount',
            'averageSale',
            'totalSalesAmount',
            'operatorSummary'
        ));
    }

    public function showPayments()
    {
        $userId = Auth::id();
        $payments = Payment::with('user')
            ->where('responsible_id', $userId)
            ->latest()
            ->paginate(20);
        $operators = User::whereHas('salesAsResponsible', function ($query) use ($userId) {
            $query->where('responsible_id', $userId);
        })->get();
        $operatorBalances = OperatorBalance::getAllBalancesWithUsers()
            ->where('responsible_id', $userId)
            ->keyBy('user_id');

        return view('foodAdmin.payments.index', compact('payments', 'operators', 'operatorBalances'));
    }

    public function createPayment()
    {
        $userId = Auth::id();
        $operators = User::whereHas('salesAsResponsible', function ($query) use ($userId) {
            $query->where('responsible_id', $userId);
        })->get();
        $operatorDebts = $this->calculateOperatorDebts();
        return view('foodAdmin.payments.create', compact('operators', 'operatorDebts'));
    }

    public function storePayment(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validatedData['responsible_id'] = Auth::id();

        DB::transaction(function () use ($validatedData) {
            Payment::create($validatedData);

            OperatorBalance::updateBalance($validatedData['user_id'], -$validatedData['amount'], $validatedData['responsible_id']);
        });

        return response()->json(['message' => 'Pago registrado exitosamente.']);
    }
    public function getOperatorBalance($userId)
    {
        $balance = OperatorBalance::getCurrentBalance($userId);
        return response()->json(['balance' => $balance]);
    }
    private function calculateOperatorDebts()
    {
        $responsibleUserId = Auth::id();
        return User::whereHas('salesAsResponsible', function ($query) use ($responsibleUserId) {
            $query->where('responsible_id', $responsibleUserId);
        })
            ->withSum(['salesAsResponsible as total_sales' => function ($query) use ($responsibleUserId) {
                $query->where('responsible_id', $responsibleUserId);
            }], 'total_price')
            ->withSum(['salesAsResponsible as total_vales' => function ($query) use ($responsibleUserId) {
                $query->where('responsible_id', $responsibleUserId)
                    ->whereHas('product', function ($q) {
                        $q->where('name', 'Vale');
                    });
            }], 'total_price')
            ->withSum(['payments as total_payments' => function ($query) use ($responsibleUserId) {
                $query->where('responsible_id', $responsibleUserId);
            }], 'amount')
            ->get()
            ->mapWithKeys(function ($user) {
                $debt = $user->total_sales - $user->total_vales - $user->total_payments;
                return [$user->id => $debt];
            });
    }
    public function myShopItems()
    {
        $user = Auth::user();

        $sales = Sale::where('user_id', $user->id)
            ->with(['product', 'responsibleUser'])
            ->orderBy('sale_date', 'desc')
            ->get();

        $totalSpent = $sales->sum('total_price');
        $totalItems = $sales->sum('quantity');

        $salesByResponsible = $sales->groupBy('responsible_id');

        $responsibleStats = [];
        foreach ($salesByResponsible as $responsibleId => $responsibleSales) {
            $responsible = User::find($responsibleId);
            $totalDebt = OperatorBalance::getCurrentBalance($responsibleId);

            $responsibleStats[] = [
                'responsible' => $responsible,
                'total_sales' => $responsibleSales->sum('total_price'),
                'total_items' => $responsibleSales->sum('quantity'),
                'total_debt' => $totalDebt,
                'sales' => $responsibleSales
            ];
        }

        $chartData = $sales->groupBy(function ($sale) {
            return Carbon::parse($sale->created_at)->format('Y-m-d');
        })->map(function ($group) {
            return [
                'date' => Carbon::parse($group->first()->created_at)->format('Y-m-d'),
                'total' => $group->sum('total_price'),
            ];
        })->values()->toArray();

        return view('operator.my_shopitems', compact('responsibleStats', 'totalSpent', 'totalItems', 'chartData'));
    }

    public function createSale()
    {
        // Obtiene los usuarios cuyos nombres no comiencen con '000'
        $users = User::where('name', 'not like', '000%')
            ->orderBy('name', 'asc')
            ->get();

        // Obtiene las categorías de productos asociadas al usuario autenticado
        $categories = CategoryProduct::whereHas('products', function ($query) {
            // Filtra las categorías que tienen productos asociados al usuario autenticado
            $query->where('user_id', Auth::id());
        })
            ->with(['products' => function ($query) {
                // Filtra los productos por el usuario autenticado
                $query->where('user_id', Auth::id());
            }])
            ->get();

        // Retorna la vista con las categorías y los usuarios
        return view('foodAdmin.createSale', compact('categories', 'users'));
    }

    public function storeSale(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'user_id' => 'required|exists:users,id',
                'products' => 'required|array',
                'products.*' => 'exists:products,id',
                'quantities' => 'array',
                'quantities.*' => 'integer|min:1',
                'sale_date' => 'required|date',
                'vale_value' => 'nullable|numeric|min:0',
            ]);

            DB::beginTransaction();

            $user = User::findOrFail($validatedData['user_id']);
            $saleDate = $validatedData['sale_date'];
            $totalSalePrice = 0;
            $responsibleUserId = Auth::id();

            foreach ($validatedData['products'] as $productId) {
                $product = Product::findOrFail($productId);

                if ($product->id == 16) { // Vale
                    if (!isset($validatedData['vale_value']) || $validatedData['vale_value'] <= 0) {
                        throw new \Exception('El valor del vale es requerido y debe ser mayor que cero.');
                    }
                    $quantity = 1;
                    $price = $validatedData['vale_value'];
                } else {
                    $quantity = $validatedData['quantities'][$productId] ?? 1;
                    $price = $product->price * $quantity;
                }

                Sale::create([
                    'user_id' => $user->id,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'total_price' => $price,
                    'sale_date' => $saleDate,
                    'responsible_id' => $responsibleUserId,
                ]);

                $totalSalePrice += $price;
            }

            OperatorBalance::updateBalance($user->id, $totalSalePrice, $responsibleUserId);

            DB::commit();

            return response()->json(['message' => 'Venta registrada exitosamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Ha ocurrido un error al procesar la venta: ' . $e->getMessage()], 422);
        }
    }

    public function showSalesReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $responsibleUserId = Auth::id();

        $sales = Sale::with('product', 'user', 'responsibleUser')
            ->where('responsible_id', $responsibleUserId)
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('sale_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('sale_date', '<=', $endDate);
            })
            ->get();

        return view('foodAdmin.salesReport', compact('sales', 'startDate', 'endDate'));
    }
    public function categoryIndex()
    {
        $categories = CategoryProduct::where('user_id', Auth::id())->get();
        return view('foodAdmin.categories.index', compact('categories'));
    }

    public function categoryCreate()
    {
        return view('foodAdmin.categories.create');
    }

    public function categoryStore(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $category = CategoryProduct::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('foodAdmin.categories.index')->with('success', 'Category created successfully.');
    }

    public function categoryEdit(CategoryProduct $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('foodAdmin.categories.edit', compact('category'));
    }

    public function categoryUpdate(Request $request, CategoryProduct $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $category->update($validatedData);

        return redirect()->route('foodAdmin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function categoryDestroy(CategoryProduct $category)
    {
        if ($category->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $category->delete();

        return redirect()->route('foodAdmin.categories.index')->with('success', 'Category deleted successfully.');
    }

    // Método adicional para obtener productos por categoría
    public function getProductsByCategory($categoryId)
    {
        $products = Product::where('category_product_id', $categoryId)
            ->where('user_id', Auth::id())
            ->get();
        return response()->json($products);
    }

    // Método para mostrar el dashboard
    public function dashboard()
    {
        $userId = Auth::id();

        // Resumen de ventas
        $todaySales = Sale::where('responsible_id', $userId)
            ->whereDate('created_at', today())
            ->sum('total_price');

        $monthSales = Sale::where('responsible_id', $userId)
            ->whereMonth('sale_date', now()->month)
            ->sum('total_price');

        $totalSales = Sale::where('responsible_id', $userId)->sum('total_price');
        $averageSale = Sale::where('responsible_id', $userId)->avg('total_price');

        // Ventas por día (últimos 30 días)
        $salesByDay = Sale::where('responsible_id', $userId)
            ->whereDate('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top 5 productos más vendidos
        $topProducts = Sale::where('responsible_id', $userId)
            ->with('product')
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Ventas por categoría
        $salesByCategory = Sale::where('responsible_id', $userId)
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->join('category_products', 'products.category_product_id', '=', 'category_products.id')
            ->select('category_products.name as category', DB::raw('SUM(sales.total_price) as total'))
            ->groupBy('category_products.id', 'category_products.name')
            ->orderByDesc('total')
            ->get();

        // Ventas recientes
        $recentSales = Sale::where('responsible_id', $userId)
            ->with(['product', 'user'])
            ->latest('sale_date')
            ->limit(10)
            ->get();

        // Balance de operadores
        $operatorBalances = OperatorBalance::with('user')
            ->whereHas('user', function ($query) use ($userId) {
                $query->whereHas('salesAsResponsible', function ($q) use ($userId) {
                    $q->where('responsible_id', $userId);
                });
            })
            ->get();

        // Pagos recientes
        $recentPayments = Payment::with('user')
            ->where('responsible_id', $userId)
            ->latest('payment_date')
            ->limit(5)
            ->get();

        return view('foodAdmin.dashboard', compact(
            'todaySales',
            'monthSales',
            'totalSales',
            'averageSale',
            'salesByDay',
            'topProducts',
            'salesByCategory',
            'recentSales',
            'operatorBalances',
            'recentPayments'
        ));
    }
    // Método para exportar ventas a CSV
    public function exportSales(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $userId = Auth::id();

        $sales = Sale::where('responsible_id', $userId)
            ->with(['product', 'user'])
            ->when($startDate, function ($query) use ($startDate) {
                return $query->whereDate('sale_date', '>=', $startDate);
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->whereDate('sale_date', '<=', $endDate);
            })
            ->get();

        $csvFileName = 'sales_report_' . now()->format('Y-m-d') . '.csv';
        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $columns = ['Date', 'Product', 'Quantity', 'Total Price', 'Customer'];

        $callback = function () use ($sales, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($sales as $sale) {
                fputcsv($file, [
                    $sale->sale_date,
                    $sale->product->name,
                    $sale->quantity,
                    $sale->total_price,
                    $sale->user->name,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
