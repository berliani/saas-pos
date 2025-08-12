<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PosController extends Controller
{
    /**
     * Menampilkan halaman utama Point of Sale.
     */
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $products = Product::where('company_id', $companyId)->where('stock', '>', 0)->get();
        return view('pos.index', compact('products'));
    }

    /**
     * Menyimpan transaksi penjualan (versi perbaikan).
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        try {
            // Gunakan DB::transaction() versi closure, lebih ringkas dan aman.
            // Laravel akan otomatis melakukan commit jika berhasil, dan rollback jika ada error.
            $result = DB::transaction(function () use ($request) {
                $user = Auth::user();
                $total = 0;
                $updatedStocks = [];

                // 1. Buat record 'sales' utama terlebih dahulu
                $sale = Sale::create([
                    'company_id' => $user->company_id,
                    'user_id'    => $user->id,
                    'total'      => 0, // Total akan di-update nanti
                    'invoice_no' => 'INV-' . $user->company_id . '-' . time(),
                ]);

                // 2. Proses setiap item dalam satu perulangan (lebih efisien)
                foreach ($request->items as $item) {
                    // KUNCI PENTING: Kunci baris produk untuk mencegah race condition
                    $product = Product::where('id', $item['product_id'])
                                      ->where('company_id', $user->company_id)
                                      ->lockForUpdate() // Ini akan mengunci row produk selama transaksi
                                      ->first();

                    // Cek ketersediaan stok
                    if (!$product || $product->stock < $item['qty']) {
                        // Lemparkan error spesifik jika stok kurang
                        throw ValidationException::withMessages([
                            'stock' => 'Stok untuk produk ' . ($product->name ?? 'terpilih') . ' tidak mencukupi!'
                        ]);
                    }

                    // Tambahkan ke total keseluruhan
                    $total += $product->price * $item['qty'];

                    // Buat record 'sale_items'
                    $sale->items()->create([
                        'product_id' => $product->id,
                        'qty'        => $item['qty'],
                        'price'      => $product->price,
                    ]);

                    // Kurangi stok produk
                    $product->decrement('stock', $item['qty']);

                    // Simpan stok terbaru untuk dikirim kembali ke frontend
                    $updatedStocks[$product->id] = $product->fresh()->stock;
                }

                // 3. Update record 'sales' dengan total final
                $sale->total = $total;
                $sale->save();

                // Kembalikan data yang dibutuhkan jika sukses
                return ['updatedStocks' => $updatedStocks];
            });

            // Jika transaksi berhasil, kirim respons sukses
            return response()->json([
                'success'       => true,
                'message'       => 'Reservasi berhasil disimpan!',
                'updatedStocks' => $result['updatedStocks']
            ]);

        } catch (ValidationException $e) {
            // Tangkap error validasi (stok tidak cukup)
            return response()->json([
                'success' => false,
                'error'   => $e->validator->errors()->first('stock')
            ], 422); // 422 adalah status code yang tepat untuk error validasi

        } catch (\Exception $e) {
            // Tangkap semua error tak terduga lainnya
            return response()->json([
                'success' => false,
                'error'   => 'Terjadi kesalahan pada server: ' . $e->getMessage()
            ], 500); // 500 untuk error server
        }
    }
}