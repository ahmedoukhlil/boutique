<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    protected $fillable = ['nom', 'prenom', 'telephone', 'email', 'adresse', 'points_fidelite', 'solde', 'actif'];
    protected $casts = ['solde' => 'float'];

    public function factures(): HasMany
    {
        return $this->hasMany(Facture::class);
    }

    public function reglements(): HasMany
    {
        return $this->hasMany(ReglementClient::class);
    }

    public function getNomCompletAttribute(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    public function getTotalAchatsAttribute(): float
    {
        return $this->factures()->where('statut', 'payee')->sum('total_ttc');
    }
}
