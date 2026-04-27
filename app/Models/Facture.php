<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Facture extends Model
{
    protected $fillable = [
        'numero', 'client_id', 'user_id',
        'sous_total', 'remise_montant', 'remise_pourcent', 'total_ttc',
        'montant_recu', 'monnaie_rendue', 'mode_paiement', 'statut', 'notes',
    ];

    protected $casts = [
        'sous_total' => 'float',
        'remise_montant' => 'float',
        'remise_pourcent' => 'float',
        'total_ttc' => 'float',
        'montant_recu' => 'float',
        'monnaie_rendue' => 'float',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lignes(): HasMany
    {
        return $this->hasMany(LigneFacture::class);
    }

    public function caisseOperation()
    {
        return $this->hasOne(CaisseOperation::class);
    }

    public static function genererNumero(): string
    {
        $annee = date('Y');
        $prefix = 'N°';
        $derniere = static::where('numero', 'like', $prefix . '%-' . $annee)
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();
        if ($derniere) {
            $num = intval(substr($derniere->numero, strlen($prefix), 4)) + 1;
        } else {
            $num = 1;
        }
        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT) . '-' . $annee;
    }

    public function recalculerTotaux(): void
    {
        $sousTot = $this->lignes()->sum('total_ligne');
        $remise = $this->remise_pourcent > 0
            ? round($sousTot * $this->remise_pourcent / 100, 2)
            : $this->remise_montant;

        $this->update([
            'sous_total' => $sousTot,
            'remise_montant' => $remise,
            'total_ttc' => max(0, $sousTot - $remise),
        ]);
    }
}
