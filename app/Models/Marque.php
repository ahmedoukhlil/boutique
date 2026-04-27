<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marque extends Model
{
    protected $fillable = ['nom', 'slug', 'logo', 'pays_origine', 'actif'];

    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class);
    }
}
