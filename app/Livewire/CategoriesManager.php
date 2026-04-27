<?php

namespace App\Livewire;

use App\Models\Categorie;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Str;

class CategoriesManager extends Component
{
    public bool $showModal = false;
    public ?int $categorieId = null;

    public string $nom = '';
    public ?string $icone = '';
    public ?string $couleur = '#d946ef';
    public ?int $parent_id = null;
    public int $ordre = 0;

    #[Computed]
    public function categories()
    {
        return Categorie::withCount('produits')
            ->with('parent')
            ->orderBy('ordre')
            ->orderBy('nom')
            ->get();
    }

    #[Computed]
    public function categoriesParentes()
    {
        return Categorie::whereNull('parent_id')->where('actif', true)->orderBy('nom')->get();
    }

    public function nouvelle(): void
    {
        $this->reset(['categorieId', 'nom', 'icone', 'parent_id', 'ordre']);
        $this->couleur = '#d946ef';
        $this->showModal = true;
    }

    public function editer(int $id): void
    {
        $c = Categorie::findOrFail($id);
        $this->categorieId = $c->id;
        $this->fill($c->only(['nom', 'icone', 'couleur', 'parent_id', 'ordre']));
        $this->showModal = true;
    }

    public function sauvegarder(): void
    {
        $this->validate([
            'nom' => 'required|string|max:100',
            'icone' => 'nullable|string|max:10',
            'couleur' => 'required|string',
        ]);

        $data = [
            'nom' => $this->nom,
            'slug' => Str::slug($this->nom) . '-' . uniqid(),
            'icone' => $this->icone ?: null,
            'couleur' => $this->couleur,
            'parent_id' => $this->parent_id ?: null,
            'ordre' => $this->ordre,
            'actif' => true,
        ];

        if ($this->categorieId) {
            unset($data['slug']); // garder le slug existant
            Categorie::findOrFail($this->categorieId)->update($data);
        } else {
            Categorie::create($data);
        }

        $this->showModal = false;
    }

    public function toggleActif(int $id): void
    {
        $cat = Categorie::findOrFail($id);
        $cat->update(['actif' => !$cat->actif]);
    }

    public function render()
    {
        return view('livewire.categories-manager')->layout('layouts.boutique');
    }
}
