<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model
{
    protected $fillable = [
        'nom', 'code', 'type', 'valeur',
        'categorie_id', 'produit_id', 'date_debut', 'date_fin', 'actif',
    ];

    protected $casts = [
        'valeur' => 'float',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'actif' => 'boolean',
    ];

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class);
    }

    public function produit(): BelongsTo
    {
        return $this->belongsTo(Produit::class);
    }

    public function estActive(): bool
    {
        if (!$this->actif) return false;
        $now = now();
        if ($now->lt($this->date_debut)) return false;
        if ($this->date_fin && $now->gt($this->date_fin)) return false;
        return true;
    }

    public function calculerRemise(float $prix): float
    {
        return match($this->type) {
            'pourcent' => round($prix * $this->valeur / 100, 2),
            'montant_fixe' => min($this->valeur, $prix),
            'gratuit' => $prix,
            default => 0,
        };
    }
}
