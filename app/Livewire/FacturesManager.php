<?php

namespace App\Livewire;

use App\Models\Facture;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class FacturesManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filtreStatut = '';
    public string $dateDebut = '';
    public string $dateFin = '';

    public function mount(): void
    {
        $this->dateDebut = now()->startOfMonth()->toDateString();
        $this->dateFin = now()->toDateString();
    }

    public function updatedSearch(): void { $this->resetPage(); }

    #[Computed]
    public function factures()
    {
        return Facture::with('client', 'user')
            ->when($this->search, fn($q) => $q->where(fn($q2) => $q2
                ->where('numero', 'like', "%{$this->search}%")
                ->orWhereHas('client', fn($q3) => $q3->where('nom', 'like', "%{$this->search}%"))
            ))
            ->when($this->filtreStatut, fn($q) => $q->where('statut', $this->filtreStatut))
            ->when($this->dateDebut, fn($q) => $q->whereDate('created_at', '>=', $this->dateDebut))
            ->when($this->dateFin, fn($q) => $q->whereDate('created_at', '<=', $this->dateFin))
            ->orderByDesc('created_at')
            ->paginate(15);
    }

    #[Computed]
    public function totaux()
    {
        $base = Facture::when($this->filtreStatut, fn($q) => $q->where('statut', $this->filtreStatut))
            ->when($this->dateDebut, fn($q) => $q->whereDate('created_at', '>=', $this->dateDebut))
            ->when($this->dateFin, fn($q) => $q->whereDate('created_at', '<=', $this->dateFin));

        return [
            'count' => $base->count(),
            'total' => (clone $base)->where('statut', 'payee')->sum('total_ttc'),
        ];
    }

    public function annulerFacture(int $id): void
    {
        $facture = Facture::findOrFail($id);
        if ($facture->statut === 'payee') {
            $facture->update(['statut' => 'annulee']);
        }
    }

    public function render()
    {
        return view('livewire.factures-manager')->layout('layouts.boutique');
    }
}
