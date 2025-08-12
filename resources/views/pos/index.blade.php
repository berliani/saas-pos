{{-- Pilih layout sesuai role --}}
@extends(Auth::user()->role === 'owner' ? 'layouts.app' : 'layouts.staff-app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Reservasi') }}
</h2>
@endsection

@section('content')
<div class="py-12" x-data="reservation({{ json_encode($products) }})">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">

        <!-- Daftar Menu/Layanan -->
        <div class="md:col-span-2">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pilih Menu / Layanan</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <template x-for="product in products" :key="product.id">
                        <div @click="addToReservation(product)"
                            class="border rounded-lg p-4 text-center cursor-pointer hover:bg-gray-100 hover:shadow-md transition">
                            <p class="font-semibold text-gray-800" x-text="product.name"></p>
                            <p class="text-sm text-gray-600" x-text="`Rp ${formatCurrency(product.price)}`"></p>
                            <p class="text-xs text-gray-500" x-text="`Tersedia: ${getAvailableStock(product.id)}`"></p>
                        </div>
                    </template>
                    <p class="text-gray-500 col-span-full" x-show="products.length === 0">Tidak ada menu / layanan tersedia.</p>
                </div>
            </div>
        </div>

        <!-- Daftar Reservasi -->
        <div>
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reservasi Anda</h3>

                <div class="space-y-3">
                    <template x-for="(item, index) in reservations" :key="item.id">
                        <div class="flex justify-between items-center text-sm">
                            <input type="hidden" :name="`items[${index}][product_id]`" :value="item.id">
                            <div>
                                <p class="font-medium text-gray-800" x-text="item.name"></p>
                                <p class="text-xs text-gray-500" x-text="`Rp ${formatCurrency(item.price)}`"></p>
                            </div>
                            <div class="flex items-center">
                                <button type="button" @click="decrementQty(item)" class="px-2">-</button>
                                <input type="number"
                                    :name="`items[${index}][qty]`"
                                    x-model.number="item.qty"
                                    @change="updateQty(item)"
                                    :min="1"
                                    :max="getMaxQty(item.id)"
                                    class="w-16 text-center border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                <button type="button" @click="incrementQty(item)" class="px-2">+</button>
                                <button type="button" @click="removeFromReservation(index)" class="ml-2 text-red-500 hover:text-red-700">&times;</button>
                            </div>
                        </div>
                    </template>
                    <div x-show="reservations.length === 0" class="text-center text-gray-500 text-sm">
                        Belum ada menu / layanan yang dipilih.
                    </div>
                </div>

                <div class="border-t mt-6 pt-4">
                    <div class="flex justify-between font-semibold text-lg">
                        <span>Total Estimasi:</span>
                        <span x-text="`Rp ${formatCurrency(total)}`"></span>
                    </div>
                    <button type="button" x-show="reservations.length > 0"
                        :disabled="isSubmitting"
                        @click="submitReservation"
                        class="mt-4 w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-50 transition">
                        <span x-show="!isSubmitting">Buat Reservasi</span>
                        <span x-show="isSubmitting">Memproses...</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function reservation(initialProducts) {
        return {
            products: initialProducts || [],
            reservations: [],
            total: 0,
            isSubmitting: false,

            // helper: cari jumlah yang sudah dipesan untuk product tertentu (0 jika belum)
            getReservationQty(productId) {
                const r = this.reservations.find(i => i.id === productId);
                return r ? r.qty : 0;
            },

            // helper: stok tersedia = stok asli - jumlah yang sudah dipesan (dari reservations)
            getAvailableStock(productId) {
                const prod = this.products.find(p => p.id === productId);
                if (!prod) return 0;
                return prod.stock - this.getReservationQty(productId);
            },

            // helper: batas max qty yang boleh dimasukkan untuk satu item (tidak melebihi stok asli)
            getMaxQty(productId) {
                const prod = this.products.find(p => p.id === productId);
                return prod ? prod.stock : 1;
            },

            addToReservation(product) {
                const available = this.getAvailableStock(product.id);
                if (available <= 0) {
                    Swal.fire({ icon: 'error', title: 'Stok Habis', text: `Stok untuk ${product.name} sudah habis!` });
                    return;
                }

                const existing = this.reservations.find(i => i.id === product.id);
                if (existing) {
                    // hanya tambah 1 jika masih ada sisa
                    if (this.getAvailableStock(product.id) > 0) {
                        existing.qty++;
                    } else {
                        Swal.fire({ icon: 'warning', title: 'Stok Terbatas', text: `Stok untuk ${product.name} tidak mencukupi.` });
                    }
                } else {
                    this.reservations.push({
                        id: product.id,
                        name: product.name,
                        price: product.price,
                        qty: 1
                    });
                }
                this.updateTotal();
            },

            removeFromReservation(index) {
                this.reservations.splice(index, 1);
                this.updateTotal();
            },

            incrementQty(item) {
                const prod = this.products.find(p => p.id === item.id);
                if (!prod) return;
                if (item.qty < prod.stock) {
                    item.qty++;
                    this.updateTotal();
                } else {
                    Swal.fire({ icon: 'warning', title: 'Stok Terbatas', text: `Maksimum ${prod.stock} untuk ${prod.name}` });
                }
            },

            decrementQty(item) {
                if (item.qty > 1) {
                    item.qty--;
                } else {
                    // kalau sampai 0 hapus
                    const idx = this.reservations.findIndex(i => i.id === item.id);
                    if (idx !== -1) this.removeFromReservation(idx);
                }
                this.updateTotal();
            },

            updateQty(item) {
                if (!item.qty || item.qty < 1) item.qty = 1;
                const prod = this.products.find(p => p.id === item.id);
                if (!prod) return;
                if (item.qty > prod.stock) {
                    Swal.fire({ icon: 'warning', title: 'Stok Terbatas', text: `Stok untuk ${prod.name} hanya ${prod.stock}` });
                    item.qty = prod.stock;
                }
                this.updateTotal();
            },

            updateTotal() {
                this.total = this.reservations.reduce((acc, it) => acc + (it.price * it.qty), 0);
            },

            submitReservation() {
                if (this.isSubmitting) return;
                if (this.reservations.length === 0) return;

                // buat payload sesuai yang divalidasi Laravel
                const items = this.reservations.map(i => ({ product_id: i.id, qty: i.qty }));

                this.isSubmitting = true;

                fetch('{{ route('pos.store') }}', {
                    method: 'POST',
                    credentials: 'same-origin', // penting agar cookie session + CSRF validasi bekerja
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ items })
                })
                .then(async res => {
                    const data = await res.json().catch(() => null);
                    if (!res.ok) {
                        // kalau non-200 ambil pesan error dari server jika ada
                        const msg = data?.error || data?.message || (data?.errors ? Object.values(data.errors).flat().join(', ') : null) || 'Terjadi kesalahan saat memproses reservasi.';
                        throw new Error(msg);
                    }
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        // perbarui stok di UI berdasarkan updatedStocks dari server
                        if (data.updatedStocks) {
                            for (const [id, stock] of Object.entries(data.updatedStocks)) {
                                const p = this.products.find(x => x.id == id);
                                if (p) p.stock = stock;
                            }
                        }
                        this.reservations = [];
                        this.updateTotal();
                        Swal.fire({ icon: 'success', title: 'Reservasi Berhasil', text: 'Transaksi tersimpan dan stok diperbarui.' });
                    } else {
                        const err = data.error || 'Gagal memproses transaksi.';
                        throw new Error(err);
                    }
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Terjadi kesalahan saat memproses reservasi.' });
                    console.error('Reservation submit error:', err);
                })
                .finally(() => { this.isSubmitting = false; });
            }
        }
    }
</script>
@endsection
