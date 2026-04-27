<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\ReglementClient;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class ClientsManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $clientId = null;

    public string $nom = '';
    public ?string $prenom = '';
    public string $telephone = '';
    public ?string $email = '';
    public ?string $adresse = '';

    // Détail client
    public bool $showDetail = false;
    public ?int $detailClientId = null;

    // Règlement dette
    public bool $showReglementModal = false;
    public float $montantReglement = 0;
    public string $modeReglementCode = '';
    public string $noteReglement = '';

    public function updatedSearch(): void { $this->resetPage(); }

    #[Computed]
    public function clients()
    {
        return Client::where('actif', true)
            ->when($this->search, fn($q) => $q->where(fn($q2) => $q2
                ->where('nom', 'like', "%{$this->search}%")
                ->orWhere('prenom', 'like', "%{$this->search}%")
                ->orWhere('telephone', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
            ))
            ->orderBy('nom')
            ->paginate(15);
    }

    #[Computed]
    public function clientDetail()
    {
        return $this->detailClientId
            ? Client::with([
                'factures' => fn($q) => $q->latest()->limit(10),
                'reglements' => fn($q) => $q->latest()->limit(20),
              ])->find($this->detailClientId)
            : null;
    }

    #[Computed]
    public function modesPaiement()
    {
        return \App\Models\ModePaiement::actif()->get();
    }

    public function nouveauClient(): void
    {
        $this->reset(['clientId', 'nom', 'prenom', 'telephone', 'email', 'adresse']);
        $this->showModal = true;
    }

    public function editerClient(int $id): void
    {
        $c = Client::findOrFail($id);
        $this->clientId = $c->id;
        $this->fill($c->only(['nom', 'prenom', 'telephone', 'email', 'adresse']));
        $this->showModal = true;
    }

    public function voirDetail(int $id): void
    {
        $this->detailClientId = $id;
        $this->showDetail = true;
        unset($this->clientDetail);
    }

    public function ouvrirReglement(): void
    {
        $this->montantReglement = abs($this->clientDetail->solde);
        $this->modeReglementCode = $this->modesPaiement->first()?->code ?? '';
        $this->noteReglement = '';
        $this->showReglementModal = true;
    }

    public function enregistrerReglement(): void
    {
        $this->validate([
            'montantReglement' => 'required|numeric|min:1',
            'modeReglementCode' => 'required|string',
        ]);

        $client = Client::findOrFail($this->detailClientId);

        ReglementClient::create([
            'client_id'     => $client->id,
            'montant'       => $this->montantReglement,
            'type'          => 'reglement',
            'mode_paiement' => $this->modeReglementCode,
            'note'          => $this->noteReglement ?: 'Règlement de dette',
        ]);

        $client->increment('solde', $this->montantReglement);

        $this->showReglementModal = false;
        unset($this->clientDetail);
    }

    public function sauvegarder(): void
    {
        $this->validate([
            'nom' => 'required|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
        ]);

        $data = [
            'nom' => $this->nom,
            'prenom' => $this->prenom ?: null,
            'telephone' => $this->telephone ?: null,
            'email' => $this->email ?: null,
            'adresse' => $this->adresse ?: null,
            'actif' => true,
        ];

        if ($this->clientId) {
            Client::findOrFail($this->clientId)->update($data);
        } else {
            Client::create($data);
        }

        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.clients-manager')->layout('layouts.boutique');
    }
}
