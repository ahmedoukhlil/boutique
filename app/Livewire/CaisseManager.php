<?php

namespace App\Livewire;

use App\Models\CaisseOperation;
use App\Models\Facture;
use Livewire\Component;
use Livewire\Attributes\Computed;

class CaisseManager extends Component
{
    public string $dateFiltre = '';

    // Entrée/sortie manuelle
    public bool $showModal = false;
    public string $typeOperation = 'ENTREE_CAISSE';
    public float $montant = 0;
    public string $modePaiement = 'especes';
    public string $notes = '';

    public function mount(): void
    {
        $this->dateFiltre = now()->toDateString();
    }

    #[Computed]
    public function operations()
    {
        return CaisseOperation::with('facture.client', 'user')
            ->whereDate('date_operation', $this->dateFiltre)
            ->orderByDesc('created_at')
            ->get();
    }

    #[Computed]
    public function totalVentes(): float
    {
        return CaisseOperation::where('type', 'VENTE')
            ->whereDate('date_operation', $this->dateFiltre)
            ->sum('montant');
    }

    #[Computed]
    public function totalRemboursements(): float
    {
        return CaisseOperation::where('type', 'REMBOURSEMENT')
            ->whereDate('date_operation', $this->dateFiltre)
            ->sum('montant');
    }

    #[Computed]
    public function totalEntrees(): float
    {
        return CaisseOperation::where('type', 'ENTREE_CAISSE')
            ->whereDate('date_operation', $this->dateFiltre)
            ->sum('montant');
    }

    #[Computed]
    public function totalSorties(): float
    {
        return CaisseOperation::where('type', 'SORTIE_CAISSE')
            ->whereDate('date_operation', $this->dateFiltre)
            ->sum('montant');
    }

    #[Computed]
    public function soldeJour(): float
    {
        return $this->totalVentes + $this->totalEntrees - $this->totalRemboursements - $this->totalSorties;
    }

    #[Computed]
    public function nbVentes(): int
    {
        return Facture::where('statut', 'payee')
            ->whereDate('created_at', $this->dateFiltre)
            ->count();
    }

    public function enregistrerOperation(): void
    {
        $this->validate([
            'montant' => 'required|numeric|min:0.01',
            'typeOperation' => 'required|in:ENTREE_CAISSE,SORTIE_CAISSE',
        ]);

        CaisseOperation::create([
            'type' => $this->typeOperation,
            'montant' => $this->montant,
            'mode_paiement' => $this->modePaiement,
            'notes' => $this->notes ?: null,
            'date_operation' => $this->dateFiltre,
            'user_id' => auth()->id(),
        ]);

        $this->showModal = false;
        $this->reset(['montant', 'notes']);
    }

    public function render()
    {
        return view('livewire.caisse-manager')->layout('layouts.boutique');
    }
}
