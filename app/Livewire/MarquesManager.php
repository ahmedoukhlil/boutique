<?php

namespace App\Livewire;

use App\Models\Marque;
use Livewire\Component;
use Livewire\Attributes\Computed;

class MarquesManager extends Component
{
    public bool $showModal = false;
    public ?int $marqueId = null;
    public string $nom = '';
    public string $pays_origine = '';
    public bool $actif = true;

    #[Computed]
    public function marques()
    {
        return Marque::withCount('produits')->orderBy('nom')->get();
    }

    public function nouveau(): void
    {
        $this->reset(['marqueId', 'nom', 'pays_origine']);
        $this->actif = true;
        $this->showModal = true;
    }

    public function editer(int $id): void
    {
        $m = Marque::findOrFail($id);
        $this->marqueId = $m->id;
        $this->nom = $m->nom;
        $this->pays_origine = $m->pays_origine ?? '';
        $this->actif = $m->actif;
        $this->showModal = true;
    }

    public function sauvegarder(): void
    {
        $this->validate([
            'nom' => 'required|string|max:80',
            'pays_origine' => 'nullable|string|max:60',
        ]);

        $slug = \Illuminate\Support\Str::slug($this->nom);

        Marque::updateOrCreate(
            ['id' => $this->marqueId],
            [
                'nom' => $this->nom,
                'slug' => $slug,
                'pays_origine' => $this->pays_origine ?: null,
                'actif' => $this->actif,
            ]
        );

        $this->showModal = false;
        unset($this->marques);
    }

    public function toggleActif(int $id): void
    {
        $m = Marque::findOrFail($id);
        $m->update(['actif' => !$m->actif]);
        unset($this->marques);
    }

    public function supprimer(int $id): void
    {
        $m = Marque::withCount('produits')->findOrFail($id);
        if ($m->produits_count > 0) {
            $this->addError('delete_' . $id, "Impossible : {$m->produits_count} produit(s) utilisent cette marque.");
            return;
        }
        $m->delete();
        unset($this->marques);
    }

    public function render()
    {
        return view('livewire.marques-manager')->layout('layouts.boutique');
    }
}
