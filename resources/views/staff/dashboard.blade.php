@extends('layouts.staff-app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard Staff') }}
    </h2>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        Selamat Datang, {{ Auth::user()->name }}!
                    </div>

                    <div class="mt-6 text-gray-500">
                        Anda login sebagai User. Halaman utama Anda adalah antarmuka Point of Sale untuk mencatat transaksi penjualan. Klik tombol di bawah untuk memulai.
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('pos.index') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 disabled:opacity-25 transition">
                            Buka Point of Sale (POS)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
