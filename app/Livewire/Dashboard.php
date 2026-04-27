<?php

namespace App\Livewire;

use App\Models\CaisseOperation;
use App\Models\Client;
use App\Models\Facture;
use App\Models\Produit;
use App\Models\VarianteProduit;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Dashboard extends Component
{
    public string $periode = 'today'; // today, week, month

    #[Computed]
    public function stats()
    {
        $query = CaisseOperation::where('type', 'VENTE');

        $query = match($this->periode) {
            'today' => $query->whereDate('date_operation', today()),
            'week' => $query->whereBetween('date_operation', [now()->startOfWeek(), now()->endOfWeek()]),
            'month' => $query->whereMonth('date_operation', now()->month)->whereYear('date_operation', now()->year),
            default => $query->whereDate('date_operation', today()),
        };

        return [
            'chiffre_affaires' => $query->sum('montant'),
            'nb_ventes' => Facture::where('statut', 'payee')
                ->when($this->periode === 'today', fn($q) => $q->whereDate('created_at', today()))
                ->when($this->periode === 'week', fn($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                ->when($this->periode === 'month', fn($q) => $q->whereMonth('created_at', now()->month))
                ->count(),
            'nb_clients' => Client::count(),
            'ruptures' => VarianteProduit::where('quantite_stock', 0)->where('actif', true)->count(),
            'alertes_stock' => VarianteProduit::whereColumn('quantite_stock', '<=', 'quantite_stock')
                ->where('quantite_stock', '>', 0)
                ->where('actif', true)
                ->count(),
        ];
    }

    #[Computed]
    public function topProduits()
    {
        return \App\Models\LigneFacture::selectRaw('variante_id, SUM(quantite) as total_vendu, SUM(total_ligne) as total_ca')
            ->with('variante.produit')
            ->whereHas('facture', fn($q) => $q->where('statut', 'payee')->whereMonth('created_at', now()->month))
            ->groupBy('variante_id')
            ->orderByDesc('total_vendu')
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function alertesStock()
    {
        return VarianteProduit::with('produit.categorie')
            ->where('actif', true)
            ->whereRaw('quantite_stock <= (SELECT stock_alerte FROM produits WHERE produits.id = variantes_produit.produit_id)')
            ->orderBy('quantite_stock')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function ventesParJour()
    {
        return CaisseOperation::selectRaw('DATE(date_operation) as jour, SUM(montant) as total')
            ->where('type', 'VENTE')
            ->whereBetween('date_operation', [now()->subDays(6), now()])
            ->groupBy('jour')
            ->orderBy('jour')
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('layouts.boutique');
    }
}
