<?php

namespace App\Livewire;

use App\Models\Fournisseur;
use Livewire\Component;
use Livewire\Attributes\Computed;

class FournisseursManager extends Component
{
    public bool $showModal = false;
    public ?int $fournisseurId = null;

    public string $nom = '';
    public ?string $contact = '';
    public ?string $telephone = '';
    public ?string $email = '';
    public ?string $adresse = '';

    #[Computed]
    public function fournisseurs()
    {
        return Fournisseur::withCount('lots')
            ->orderBy('nom')
            ->get();
    }

    public function nouveau(): void
    {
        $this->reset(['fournisseurId', 'nom', 'contact', 'telephone', 'email', 'adresse']);
        $this->showModal = true;
    }

    public function editer(int $id): void
    {
        $f = Fournisseur::findOrFail($id);
        $this->fournisseurId = $f->id;
        $this->fill($f->only(['nom', 'contact', 'telephone', 'email', 'adresse']));
        $this->showModal = true;
    }

    public function sauvegarder(): void
    {
        $this->validate([
            'nom' => 'required|string|max:150',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
        ]);

        $data = [
            'nom' => $this->nom,
            'contact' => $this->contact ?: null,
            'telephone' => $this->telephone ?: null,
            'email' => $this->email ?: null,
            'adresse' => $this->adresse ?: null,
            'actif' => true,
        ];

        if ($this->fournisseurId) {
            Fournisseur::findOrFail($this->fournisseurId)->update($data);
        } else {
            Fournisseur::create($data);
        }

        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.fournisseurs-manager')->layout('layouts.boutique');
    }
}
