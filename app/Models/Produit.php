<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nom', 'reference', 'code_barre', 'description',
        'categorie_id', 'marque_id', 'prix_vente', 'prix_achat',
        'stock_alerte', 'saison', 'genre', 'image', 'actif', 'has_variantes',
    ];

    protected $casts = [
        'prix_vente' => 'float',
        'prix_achat' => 'float',
        'actif' => 'boolean',
        'has_variantes' => 'boolean',
    ];

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function marque(): BelongsTo
    {
        return $this->belongsTo(Marque::class);
    }

    public function variantes(): HasMany
    {
        return $this->hasMany(VarianteProduit::class);
    }

    public function getStockTotalAttribute(): int
    {
        return $this->variantes()->sum('quantite_stock');
    }

    public function estEnRuptureAttribute(): bool
    {
        return $this->stock_total <= 0;
    }

    public function estEnAlerteAttribute(): bool
    {
        return $this->stock_total <= $this->stock_alerte && $this->stock_total > 0;
    }

    public static function genererReference(): string
    {
        $prefix = 'ART';
        $dernierNum = static::withTrashed()->count() + 1;
        return $prefix . str_pad($dernierNum, 5, '0', STR_PAD_LEFT);
    }
}
