@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 leading-tight">
    {{ __('Dashboard') }}
</h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                <div>
                    <x-application-logo class="block h-12 w-auto" />
                </div>

                <div class="mt-8 text-2xl">
                    Selamat Datang di Aplikasi Anda,
                    {{ Auth::user()->company ? Auth::user()->company->name : '' }}!
                </div>


                <div class="mt-6 text-gray-500">
                    Ini adalah halaman dashboard Anda. Dari sini Anda dapat mengelola produk, melihat penjualan, dan menggunakan fitur Point of Sale. Gunakan menu navigasi di atas untuk memulai.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection