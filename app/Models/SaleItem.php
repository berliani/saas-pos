<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SaleItem extends Model
{
    use HasFactory;

    protected $fillable = ['sale_id','product_id','qty','price','subtotal'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function company()
{
    return $this->belongsTo(Company::class);
}

public function isOwner()
{
    return $this->role === 'owner';
}

}
