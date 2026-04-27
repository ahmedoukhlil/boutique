<?php

namespace App\Services;

use App\Models\LotProduit;
use App\Models\MouvementStock;
use App\Models\VarianteProduit;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function deduireStockFifo(VarianteProduit $variante, int $quantite, ?int $ligneFactureId = null): void
    {
        $restant = $quantite;

        $lots = $variante->lotsActifsFifo()->get();

        foreach ($lots as $lot) {
            if ($restant <= 0) break;

            $pris = min($restant, $lot->quantite_restante);

            $lot->decrement('quantite_restante', $pris);
            if ($lot->quantite_restante <= 0) {
                $lot->update(['actif' => false]);
            }

            MouvementStock::create([
                'variante_id' => $variante->id,
                'lot_id' => $lot->id,
                'ligne_facture_id' => $ligneFactureId,
                'type' => 'SORTIE',
                'quantite' => $pris,
                'motif' => 'Vente',
                'user_id' => auth()->id(),
            ]);

            $restant -= $pris;
        }

        $variante->decrement('quantite_stock', $quantite - $restant);
    }

    public function restaurerStock(VarianteProduit $variante, int $quantite, ?int $ligneFactureId = null): void
    {
        // Récupère le dernier lot de la variante pour y réinjecter
        $lot = $variante->lots()->orderByDesc('date_reception')->first();

        if ($lot) {
            $lot->increment('quantite_restante', $quantite);
            if (!$lot->actif) $lot->update(['actif' => true]);
        }

        MouvementStock::create([
            'variante_id' => $variante->id,
            'lot_id' => $lot?->id,
            'ligne_facture_id' => $ligneFactureId,
            'type' => 'RETOUR',
            'quantite' => $quantite,
            'motif' => 'Annulation ligne facture',
            'user_id' => auth()->id(),
        ]);

        $variante->increment('quantite_stock', $quantite);
    }

    public function entreeStock(VarianteProduit $variante, int $quantite, array $lotData = []): LotProduit
    {
        $lot = LotProduit::create([
            'variante_id' => $variante->id,
            'fournisseur_id' => $lotData['fournisseur_id'] ?? null,
            'numero_lot' => $lotData['numero_lot'] ?? null,
            'numero_commande' => $lotData['numero_commande'] ?? null,
            'quantite_initiale' => $quantite,
            'quantite_restante' => $quantite,
            'prix_achat_unitaire' => $lotData['prix_achat'] ?? null,
            'date_reception' => $lotData['date_reception'] ?? now()->toDateString(),
            'date_fin_saison' => $lotData['date_fin_saison'] ?? null,
            'actif' => true,
        ]);

        MouvementStock::create([
            'variante_id' => $variante->id,
            'lot_id' => $lot->id,
            'type' => 'ENTREE',
            'quantite' => $quantite,
            'motif' => 'Réception stock',
            'user_id' => auth()->id(),
        ]);

        $variante->increment('quantite_stock', $quantite);

        return $lot;
    }

    public function ajustementStock(VarianteProduit $variante, int $nouvelleQuantite, string $motif = 'Inventaire'): void
    {
        $ecart = $nouvelleQuantite - $variante->quantite_stock;

        MouvementStock::create([
            'variante_id' => $variante->id,
            'type' => 'AJUSTEMENT',
            'quantite' => abs($ecart),
            'motif' => $motif . ($ecart >= 0 ? ' (+' : ' (') . $ecart . ')',
            'user_id' => auth()->id(),
        ]);

        $variante->update(['quantite_stock' => $nouvelleQuantite]);
    }
}
