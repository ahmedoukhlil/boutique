<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LotProduit extends Model
{
    protected $table = 'lots_produit';

    protected $fillable = [
        'variante_id', 'fournisseur_id', 'numero_lot', 'numero_commande',
        'quantite_initiale', 'quantite_restante', 'prix_achat_unitaire',
        'date_reception', 'date_fin_saison', 'actif',
    ];

    protected $casts = [
        'date_reception' => 'date',
        'date_fin_saison' => 'date',
        'prix_achat_unitaire' => 'float',
        'actif' => 'boolean',
    ];

    public function variante(): BelongsTo
    {
        return $this->belongsTo(VarianteProduit::class, 'variante_id');
    }

    public function fournisseur(): BelongsTo
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function estEnFinDeSaison(): bool
    {
        return $this->date_fin_saison && $this->date_fin_saison->isPast();
    }
}
