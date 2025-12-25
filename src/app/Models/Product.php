<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
    'name',
    'sku',
    'description',
    'price',
    'stock',
    'is_active',
];



    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (! $model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
}
