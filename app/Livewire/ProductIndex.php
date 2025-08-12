<?php

namespace App\Livewire;

use Livewire\Component;

class ProductIndex extends Component
{
  public function render()
{
    $products = auth()->user()->company->products()->latest()->paginate(10);
    return view('livewire.product-index', compact('products'));
}}
