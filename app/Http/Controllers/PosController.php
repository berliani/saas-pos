<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Menampilkan halaman utama Point of Sale.
     */
    public function index()
    {
        $companyId = auth()->user()->company_id;
        $products = Product::where('company_id', $companyId)->where('stock', '>', 0)->get();
        return view('pos.index', compact('products'));
    }

    /**
     * Menyimpan transaksi penjualan.
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $companyId = auth()->user()->company_id;
        $userId = auth()->id();
        $total = 0;

        DB::beginTransaction();
        try {
            // Hitung total dan periksa stok
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                // Keamanan: pastikan produk milik perusahaan yang benar & stok cukup
                if (!$product || $product->company_id != $companyId || $product->stock < $item['qty']) {
                    throw new \Exception('Produk tidak valid atau stok tidak mencukupi.');
                }
                $total += $product->price * $item['qty'];
            }

            // 1. Buat record penjualan utama
            $sale = Sale::create([
                'company_id' => $companyId,
                'user_id' => $userId,
                'total' => $total,
                'invoice_no' => 'INV-' . $companyId . '-' . time(),
            ]);

            // 2. Buat record untuk setiap item penjualan dan kurangi stok
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'qty' => $item['qty'],
                    'price' => $product->price,
                    'subtotal' => $product->price * $item['qty'],
                ]);

                // Kurangi stok produk
                $product->decrement('stock', $item['qty']);
            }

            DB::commit();
            return redirect()->route('pos.index')->with('success', 'Transaksi berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage() ?: 'Gagal memproses transaksi.']);
        }
    }
}
