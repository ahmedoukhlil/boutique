<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Categorie extends Model
{
    protected $fillable = ['nom', 'slug', 'icone', 'couleur', 'parent_id', 'actif', 'ordre'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'parent_id');
    }

    public function enfants(): HasMany
    {
        return $this->hasMany(Categorie::class, 'parent_id');
    }

    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class);
    }
}
