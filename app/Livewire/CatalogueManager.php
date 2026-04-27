<?php

namespace App\Livewire;

use App\Models\Categorie;
use App\Models\Marque;
use App\Models\Produit;
use App\Models\VarianteProduit;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class CatalogueManager extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public ?int $filtreCategorie = null;
    public ?int $filtreMarque = null;
    public string $filtreGenre = '';
    public string $filtreSaison = '';

    // Formulaire produit
    public bool $showModal = false;
    public ?int $produitId = null;
    public string $nom = '';
    public string $reference = '';
    public ?string $code_barre = '';
    public ?string $description = '';
    public int $categorie_id = 0;
    public ?int $marque_id = null;
    public float $prix_vente = 0;
    public float $prix_achat = 0;
    public int $stock_alerte = 5;
    public ?string $saison = '';
    public ?string $genre = '';
    public bool $has_variantes = false;
    public $image;

    // Suppression
    public bool $showConfirmDelete = false;
    public ?int $produitASupprimer = null;

    // Variantes
    public bool $showVarianteModal = false;
    public ?int $varianteProduitId = null;
    public array $variantes = [];
    public string $nvTaille = '';
    public string $nvCouleur = '';
    public string $nvCodeCouleur = '#000000';
    public float $nvPrixSupplement = 0;
    public int $nvQuantiteStock = 0;

    protected function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'reference' => 'required|string|max:50',
            'categorie_id' => 'required|exists:categories,id',
            'prix_vente' => 'required|numeric|min:0',
            'prix_achat' => 'nullable|numeric|min:0',
            'stock_alerte' => 'integer|min:0',
            'image' => 'nullable|image|max:2048',
        ];
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFiltreCategorie(): void { $this->resetPage(); }

    #[Computed]
    public function categories() { return Categorie::where('actif', true)->orderBy('nom')->get(); }

    #[Computed]
    public function marques() { return Marque::where('actif', true)->orderBy('nom')->get(); }

    #[Computed]
    public function produits()
    {
        return Produit::with('categorie', 'marque', 'variantes')
            ->where('actif', true)
            ->when($this->search, fn($q) => $q->where(fn($q2) => $q2
                ->where('nom', 'like', "%{$this->search}%")
                ->orWhere('reference', 'like', "%{$this->search}%")
            ))
            ->when($this->filtreCategorie, fn($q) => $q->where('categorie_id', $this->filtreCategorie))
            ->when($this->filtreMarque, fn($q) => $q->where('marque_id', $this->filtreMarque))
            ->when($this->filtreGenre, fn($q) => $q->where('genre', $this->filtreGenre))
            ->when($this->filtreSaison, fn($q) => $q->where('saison', $this->filtreSaison))
            ->orderBy('nom')
            ->paginate(12);
    }

    public function nouveauProduit(): void
    {
        $this->reset(['produitId','nom','reference','code_barre','description','categorie_id',
            'marque_id','prix_vente','prix_achat','stock_alerte','saison','genre','has_variantes','image']);
        $this->reference = Produit::genererReference();
        $this->showModal = true;
    }

    public function editerProduit(int $id): void
    {
        $p = Produit::findOrFail($id);
        $this->produitId = $p->id;
        $this->fill($p->only(['nom','reference','code_barre','description','categorie_id',
            'marque_id','prix_vente','prix_achat','stock_alerte','saison','genre','has_variantes']));
        $this->showModal = true;
    }

    public function sauvegarder(): void
    {
        $this->validate();

        $data = [
            'nom' => $this->nom,
            'reference' => $this->reference,
            'code_barre' => $this->code_barre ?: null,
            'description' => $this->description,
            'categorie_id' => $this->categorie_id,
            'marque_id' => $this->marque_id ?: null,
            'prix_vente' => $this->prix_vente,
            'prix_achat' => $this->prix_achat ?: null,
            'stock_alerte' => $this->stock_alerte,
            'saison' => $this->saison,
            'genre' => $this->genre,
            'has_variantes' => $this->has_variantes,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('produits', 'public');
        }

        if ($this->produitId) {
            Produit::findOrFail($this->produitId)->update($data);
        } else {
            $produit = Produit::create($data);
            // Créer une variante par défaut si pas de variantes
            if (!$this->has_variantes) {
                VarianteProduit::create([
                    'produit_id' => $produit->id,
                    'quantite_stock' => 0,
                    'actif' => true,
                ]);
            }
        }

        $this->showModal = false;
        $this->dispatch('produit-sauvegarde');
    }

    public function gererVariantes(int $produitId): void
    {
        $this->varianteProduitId = $produitId;
        $this->variantes = VarianteProduit::where('produit_id', $produitId)->get()->toArray();
        $this->showVarianteModal = true;
    }

    public function ajouterVariante(): void
    {
        $this->validate([
            'nvTaille' => 'nullable|string|max:20',
            'nvCouleur' => 'nullable|string|max:50',
        ]);

        VarianteProduit::create([
            'produit_id' => $this->varianteProduitId,
            'taille' => $this->nvTaille ?: null,
            'couleur' => $this->nvCouleur ?: null,
            'code_couleur' => $this->nvCodeCouleur,
            'prix_supplement' => $this->nvPrixSupplement,
            'quantite_stock' => $this->nvQuantiteStock,
            'actif' => true,
        ]);

        $this->variantes = VarianteProduit::where('produit_id', $this->varianteProduitId)->get()->toArray();
        $this->reset(['nvTaille','nvCouleur','nvCodeCouleur','nvPrixSupplement','nvQuantiteStock']);
    }

    public function confirmerSuppression(int $id): void
    {
        $this->produitASupprimer = $id;
        $this->showConfirmDelete = true;
    }

    public function supprimerProduit(): void
    {
        if ($this->produitASupprimer) {
            $produit = Produit::findOrFail($this->produitASupprimer);
            $produit->variantes()->delete();
            $produit->delete();
        }
        $this->showConfirmDelete = false;
        $this->produitASupprimer = null;
    }

    public function supprimerVariante(int $id): void
    {
        VarianteProduit::findOrFail($id)->delete();
        $this->variantes = VarianteProduit::where('produit_id', $this->varianteProduitId)->get()->toArray();
    }

    public function render()
    {
        return view('livewire.catalogue-manager')->layout('layouts.boutique');
    }
}
