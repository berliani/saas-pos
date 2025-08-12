<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk milik perusahaan.
     */
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $products = Product::where('company_id', $companyId)->latest()->paginate(10);
        return view('products.index', compact('products'));
    }

    /**
     * Menampilkan form untuk membuat produk baru.
     */
    public function create()
    {
        return view('products.create');
    }

    /**
     * Menyimpan produk baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'sku' => 'nullable|string|max:50|unique:products,sku,NULL,id,company_id,' . auth()->user()->company_id,
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $product = new Product($request->all());
        $product->company_id = auth()->user()->company_id; // Otomatis set company_id
        $product->save();

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    // Metode lain seperti show, edit, update, destroy akan mengikuti pola yang sama,
    // selalu memastikan untuk mengambil produk yang sesuai dengan company_id.
    // Contoh untuk edit:
    public function edit(Product $product)
    {
        // Pastikan produk ini milik perusahaan yang benar
        if ($product->company_id !== auth()->user()->company_id) {
            abort(403, 'AKSES DITOLAK');
        }
        return view('products.edit', compact('product'));
    }

    // Anda bisa melengkapi sisanya (update, destroy) dengan logika yang sama.
}
