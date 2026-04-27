<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fournisseur extends Model
{
    protected $fillable = ['nom', 'contact', 'telephone', 'email', 'adresse', 'actif'];

    public function lots(): HasMany
    {
        return $this->hasMany(LotProduit::class);
    }
}
