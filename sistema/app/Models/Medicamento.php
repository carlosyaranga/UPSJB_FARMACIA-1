<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicamento extends Model
{
    protected $table = 'medicines';

    protected $fillable = [
        'category_id',
        'name',
        'price',
        'stock',
        'image',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function saleDetails(): HasMany
    {
        return $this->hasMany(DetailSale::class, 'medicine_id');
    }

    public function actualizarStock(int $cantidad): void
    {
        $this->decrement('stock', $cantidad);
    }
}
