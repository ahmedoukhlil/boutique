<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VarianteProduit extends Model
{
    protected $table = 'variantes_produit';

    protected $fillable = [
        'produit_id', 'taille', 'couleur', 'code_couleur',
        'code_barre', 'prix_supplement', 'quantite_stock', 'actif',
    ];

    protected $casts = [
        'prix_supplement' => 'float',
        'quantite_stock' => 'integer',
        'actif' => 'boolean',
    ];

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }

    public function lots(): HasMany
    {
        return $this->hasMany(LotProduit::class, 'variante_id');
    }

    public function mouvements(): HasMany
    {
        return $this->hasMany(MouvementStock::class, 'variante_id');
    }

    public function getPrixFinalAttribute(): float
    {
        return $this->produit->prix_vente + $this->prix_supplement;
    }

    public function getLibelleAttribute(): string
    {
        $parts = array_filter([$this->taille, $this->couleur]);
        return implode(' / ', $parts) ?: 'Standard';
    }

    public function lotsActifsFifo()
    {
        return $this->lots()
            ->where('actif', true)
            ->where('quantite_restante', '>', 0)
            ->orderBy('date_reception', 'asc');
    }
}
