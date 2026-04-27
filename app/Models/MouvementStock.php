<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MouvementStock extends Model
{
    protected $table = 'mouvements_stock';

    protected $fillable = [
        'variante_id', 'lot_id', 'ligne_facture_id',
        'type', 'quantite', 'motif', 'user_id',
    ];

    public function variante(): BelongsTo
    {
        return $this->belongsTo(VarianteProduit::class, 'variante_id');
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(LotProduit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
