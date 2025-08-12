<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = ['company_id','user_id','total','invoice_no'];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
