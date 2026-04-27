<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CaisseOperation extends Model
{
    protected $table = 'caisse_operations';

    protected $fillable = [
        'facture_id', 'user_id', 'type', 'montant',
        'mode_paiement', 'reference', 'notes', 'date_operation',
    ];

    protected $casts = [
        'montant' => 'float',
        'date_operation' => 'date',
    ];

    public function facture(): BelongsTo
    {
        return $this->belongsTo(Facture::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function totalJour(\Carbon\Carbon $date = null): float
    {
        $date ??= now();
        return static::where('date_operation', $date->toDateString())
            ->whereIn('type', ['VENTE'])
            ->sum('montant');
    }
}
