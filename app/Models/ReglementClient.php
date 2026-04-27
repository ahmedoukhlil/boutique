<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReglementClient extends Model
{
    protected $table = 'reglements_client';
    protected $fillable = ['client_id', 'facture_id', 'montant', 'type', 'mode_paiement', 'note'];
    protected $casts = ['montant' => 'float'];

    public function client() { return $this->belongsTo(Client::class); }
    public function facture() { return $this->belongsTo(Facture::class); }
}
