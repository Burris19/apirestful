<?php

namespace App;

use App\Category;
use App\Seller;
use App\Transaction;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    const PRODUCT_AVAILABLE = 'disponible';
    const PRODUCT_NOT_AVAILABLE = 'no disponible'; 

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'description',
        'quantity',
        'status',
        'image',
        'seller_id',
    ];

    protected $hidden = [
        'pivot'
    ];

    public function isAvailable() 
    {
        return $this->status == Product::PRODUCT_AVAILABLE;
    }

    public function categories() 
    {
        return $this->belongsToMany(Category::class);
    }

    public function seller() 
    {
        return $this->belongsTo(Seller::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}