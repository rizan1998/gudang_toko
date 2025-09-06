<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merchant extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'address', 'phone', 'photo', 'keeper_id'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'merchant_products')
            ->withPivot('stock')
            ->withPivot('warehouse_id')
            ->withTimestamps();
    }

    public function keeper()
    {
        return $this->belongsTo(User::class, 'keeper_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getPhotoAttribute($value)
    {
        if (!$value) {
            return null;
        }
        return url(Storage::url($value));
    }
}
