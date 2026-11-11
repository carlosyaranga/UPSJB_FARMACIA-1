<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'description'])]
class Categoria extends Model
{
   public function medicines (): HastMany
   {
    return $this->hasMany(Medicine::class);
   }
}
