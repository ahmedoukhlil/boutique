<?php

namespace App\Livewire;

use App\Models\ModePaiement;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Computed;

class ModesPaiementManager extends Component
{
    public bool $showModal = false;
    public ?int $modeId = null;
    public string $nom = '';
    public bool $actif = true;
    public int $ordre = 0;

    #[Computed]
    public function modes()
    {
        return ModePaiement::orderBy('ordre')->get();
    }

    public function nouveau(): void
    {
        $this->reset(['modeId', 'nom', 'actif', 'ordre']);
        $this->actif = true;
        $this->ordre = ModePaiement::max('ordre') + 1;
        $this->showModal = true;
    }

    public function editer(int $id): void
    {
        $mode = ModePaiement::findOrFail($id);
        $this->modeId = $mode->id;
        $this->nom = $mode->nom;
        $this->actif = $mode->actif;
        $this->ordre = $mode->ordre;
        $this->showModal = true;
    }

    public function sauvegarder(): void
    {
        $this->validate([
            'nom'   => 'required|string|max:60',
            'ordre' => 'integer|min:0',
        ]);

        ModePaiement::updateOrCreate(
            ['id' => $this->modeId],
            [
                'nom'   => $this->nom,
                'code'  => Str::slug($this->nom, '_'),
                'icone' => '💳',
                'actif' => $this->actif,
                'ordre' => $this->ordre,
            ]
        );

        $this->showModal = false;
        unset($this->modes);
    }

    public function toggleActif(int $id): void
    {
        $mode = ModePaiement::findOrFail($id);
        $mode->update(['actif' => !$mode->actif]);
        unset($this->modes);
    }

    public function supprimer(int $id): void
    {
        ModePaiement::destroy($id);
        unset($this->modes);
    }

    public function render()
    {
        return view('livewire.modes-paiement-manager')->layout('layouts.boutique');
    }
}
