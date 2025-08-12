<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['company_id','sku','name','price','stock','description'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
