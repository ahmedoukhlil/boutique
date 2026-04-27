<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModePaiement extends Model
{
    protected $table = 'modes_paiement';
    protected $fillable = ['nom', 'code', 'icone', 'actif', 'ordre'];
    protected $casts = ['actif' => 'boolean', 'ordre' => 'integer'];

    public function scopeActif($query)
    {
        return $query->where('actif', true)->orderBy('ordre');
    }
}
