<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LigneFacture extends Model
{
    protected $table = 'lignes_facture';

    protected $fillable = [
        'facture_id', 'variante_id', 'designation',
        'quantite', 'prix_unitaire', 'remise_pourcent', 'total_ligne', 'type',
    ];

    protected $casts = [
        'prix_unitaire' => 'float',
        'remise_pourcent' => 'float',
        'total_ligne' => 'float',
    ];

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class);
    }

    public function variante(): BelongsTo
    {
        return $this->belongsTo(VarianteProduit::class, 'variante_id');
    }

    public function calculerTotal(): float
    {
        $total = $this->quantite * $this->prix_unitaire;
        if ($this->remise_pourcent > 0) {
            $total -= $total * $this->remise_pourcent / 100;
        }
        return round($total, 2);
    }
}
