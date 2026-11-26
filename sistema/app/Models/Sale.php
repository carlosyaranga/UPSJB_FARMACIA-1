<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'customer_id', 'date', 'total', 'igv', 'status', 'cancelled_at'])]
class Sale extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    // La venta la registra un usuario (vendedor)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // La venta pertenece a un cliente
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // La venta tiene muchos detalles (productos vendidos)
    public function details(): HasMany
    {
        return $this->hasMany(DetailSale::class);
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
