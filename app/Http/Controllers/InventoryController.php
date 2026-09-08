<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;

class InventoryController extends Controller
{
    /**
     * Display a paginated listing of active inventory products.
     */
    public function index(): View
    {
        $products = Product::active()->paginate(15);

        return view('inventory.index', compact('products'));
    }
}
