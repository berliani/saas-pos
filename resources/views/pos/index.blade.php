{{-- Pilih layout sesuai role --}}
@extends(Auth::user()->role === 'owner' ? 'layouts.app' : 'layouts.staff-app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Point of Sale (POS)') }}
    </h2>
@endsection

@section('content')
    <div class="py-12" x-data="pos()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Daftar Produk -->
            <div class="md:col-span-2">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pilih Produk</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @forelse ($products as $product)
                            <div @click="addToCart({{ json_encode($product) }})"
                                 class="border rounded-lg p-4 text-center cursor-pointer hover:bg-gray-100 hover:shadow-md transition">
                                <p class="font-semibold text-gray-800">{{ $product->name }}</p>
                                <p class="text-sm text-gray-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-500">Stok: {{ $product->stock }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500 col-span-full">Tidak ada produk tersedia.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Keranjang Belanja -->
            <div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Keranjang</h3>
                    <form action="{{ route('pos.store') }}" method="POST">
                        @csrf
                        <div class="space-y-3">
                            <template x-for="(item, index) in cart" :key="index">
                                <div class="flex justify-between items-center text-sm">
                                    <input type="hidden" :name="`items[${index}][product_id]`" :value="item.id">
                                    <div>
                                        <p class="font-medium text-gray-800" x-text="item.name"></p>
                                        <p class="text-xs text-gray-500" x-text="`Rp ${formatCurrency(item.price)}`"></p>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="number" :name="`items[${index}][qty]`" x-model.number="item.qty" @change="updateTotal()"
                                               class="w-16 text-center border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                        <button type="button" @click="removeFromCart(index)" class="ml-2 text-red-500 hover:text-red-700">&times;</button>
                                    </div>
                                </div>
                            </template>
                            <div x-show="cart.length === 0" class="text-center text-gray-500 text-sm">
                                Keranjang kosong.
                            </div>
                        </div>

                        <div class="border-t mt-6 pt-4">
                            <div class="flex justify-between font-semibold text-lg">
                                <span>Total:</span>
                                <span x-text="`Rp ${formatCurrency(total)}`"></span>
                            </div>
                            <button type="submit" x-show="cart.length > 0"
                                    class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 disabled:opacity-25 transition">
                                Proses Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function pos() {
            return {
                cart: [],
                total: 0,
                addToCart(product) {
                    const existingItem = this.cart.find(item => item.id === product.id);
                    if (existingItem) {
                        if (existingItem.qty < product.stock) {
                            existingItem.qty++;
                        }
                    } else {
                        if (product.stock > 0) {
                            this.cart.push({ ...product, qty: 1 });
                        }
                    }
                    this.updateTotal();
                },
                removeFromCart(index) {
                    this.cart.splice(index, 1);
                    this.updateTotal();
                },
                updateTotal() {
                    this.total = this.cart.reduce((acc, item) => {
                        return acc + (item.price * item.qty);
                    }, 0);
                },
                formatCurrency(amount) {
                    return new Intl.NumberFormat('id-ID').format(amount);
                }
            }
        }
    </script>
@endsection
