<?php

namespace App\Livewire;

use App\Models\Fournisseur;
use App\Models\MouvementStock;
use App\Models\VarianteProduit;
use App\Services\StockService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class StockManager extends Component
{
    use WithPagination;

    public string $activeTab = 'stock';
    public string $search = '';

    // Entrée stock
    public bool $showEntreeModal = false;
    public ?int $varianteSelectionneeId = null;
    public int $quantiteEntree = 1;
    public ?int $fournisseurId = null;
    public string $numeroCde = '';
    public float $prixAchat = 0;
    public string $dateReception = '';
    public string $dateFinSaison = '';

    // Ajustement
    public bool $showAjustementModal = false;
    public ?int $varianteAjustId = null;
    public int $nouvelleQuantite = 0;
    public string $motifAjust = '';

    #[Computed]
    public function fournisseurs() { return Fournisseur::where('actif', true)->orderBy('nom')->get(); }

    #[Computed]
    public function stockItems()
    {
        return VarianteProduit::with('produit.categorie', 'produit.marque')
            ->where('actif', true)
            ->whereHas('produit', fn($q) => $q
                ->where('actif', true)
                ->when($this->search, fn($q2) => $q2->where('nom', 'like', "%{$this->search}%"))
            )
            ->orderByRaw('quantite_stock ASC')
            ->paginate(15);
    }

    #[Computed]
    public function mouvements()
    {
        return MouvementStock::with('variante.produit', 'lot', 'user')
            ->latest()
            ->paginate(20);
    }

    #[Computed]
    public function alertes()
    {
        return VarianteProduit::with('produit')
            ->where('actif', true)
            ->whereRaw('quantite_stock <= (SELECT stock_alerte FROM produits WHERE produits.id = variantes_produit.produit_id)')
            ->orderBy('quantite_stock')
            ->get();
    }

    public function ouvrirEntreeStock(int $varianteId): void
    {
        $this->varianteSelectionneeId = $varianteId;
        $this->dateReception = now()->toDateString();
        $this->showEntreeModal = true;
    }

    public function enregistrerEntree(): void
    {
        $this->validate([
            'quantiteEntree' => 'required|integer|min:1',
            'dateReception' => 'required|date',
        ]);

        $variante = VarianteProduit::findOrFail($this->varianteSelectionneeId);

        app(StockService::class)->entreeStock($variante, $this->quantiteEntree, [
            'fournisseur_id' => $this->fournisseurId,
            'numero_commande' => $this->numeroCde ?: null,
            'prix_achat' => $this->prixAchat ?: null,
            'date_reception' => $this->dateReception,
            'date_fin_saison' => $this->dateFinSaison ?: null,
        ]);

        $this->showEntreeModal = false;
        $this->reset(['varianteSelectionneeId','quantiteEntree','fournisseurId','numeroCde','prixAchat','dateFinSaison']);
        $this->dispatch('stock-mis-a-jour');
    }

    public function ouvrirAjustement(int $varianteId): void
    {
        $variante = VarianteProduit::findOrFail($varianteId);
        $this->varianteAjustId = $varianteId;
        $this->nouvelleQuantite = $variante->quantite_stock;
        $this->showAjustementModal = true;
    }

    public function enregistrerAjustement(): void
    {
        $this->validate([
            'nouvelleQuantite' => 'required|integer|min:0',
            'motifAjust' => 'required|string',
        ]);

        $variante = VarianteProduit::findOrFail($this->varianteAjustId);
        app(StockService::class)->ajustementStock($variante, $this->nouvelleQuantite, $this->motifAjust);

        $this->showAjustementModal = false;
        $this->reset(['varianteAjustId','nouvelleQuantite','motifAjust']);
    }

    public function render()
    {
        return view('livewire.stock-manager')->layout('layouts.boutique');
    }
}
