<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Medicine; // Importamos el modelo correcto

#[Fillable(['name', 'description'])]
class Category extends Model
{
    // Relación del diagrama: Categoría 1 — 0..* Medicamento[cite: 19]
    public function medicines(): HasMany
    {
        return $this->hasMany(Medicine::class);
    }
}