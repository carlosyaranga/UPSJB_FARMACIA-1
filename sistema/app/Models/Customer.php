<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'dni', 'telefono', 'edad'])]
class Customer extends Model
{
    protected $table = 'clientes';

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class);
    }
}
