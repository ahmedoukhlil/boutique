<?php

namespace App\Livewire;

use App\Models\Parametre;
use Livewire\Component;
use Livewire\WithFileUploads;

class ParametresManager extends Component
{
    use WithFileUploads;

    public string $nom_boutique = '';
    public string $slogan = '';
    public string $telephone = '';
    public string $telephone2 = '';
    public string $email = '';
    public string $adresse = '';
    public string $ville = '';
    public string $pied_page = '';
    public string $devise = 'MRU';
    public $logo = null;
    public bool $saved = false;

    public function mount(): void
    {
        $p = Parametre::tous();
        $this->nom_boutique = $p['nom_boutique'] ?? '';
        $this->slogan       = $p['slogan'] ?? '';
        $this->telephone    = $p['telephone'] ?? '';
        $this->telephone2   = $p['telephone2'] ?? '';
        $this->email        = $p['email'] ?? '';
        $this->adresse      = $p['adresse'] ?? '';
        $this->ville        = $p['ville'] ?? '';
        $this->pied_page    = $p['pied_page'] ?? 'Merci de votre confiance !';
        $this->devise       = $p['devise'] ?? 'MRU';
    }

    public function sauvegarder(): void
    {
        $this->validate([
            'nom_boutique' => 'required|string|max:100',
            'telephone'    => 'nullable|string|max:30',
            'telephone2'   => 'nullable|string|max:30',
            'email'        => 'nullable|email|max:100',
            'devise'       => 'required|string|max:10',
            'logo'         => 'nullable|image|max:2048',
        ]);

        Parametre::set('nom_boutique', $this->nom_boutique);
        Parametre::set('slogan',       $this->slogan ?: null);
        Parametre::set('telephone',    $this->telephone ?: null);
        Parametre::set('telephone2',   $this->telephone2 ?: null);
        Parametre::set('email',        $this->email ?: null);
        Parametre::set('adresse',      $this->adresse ?: null);
        Parametre::set('ville',        $this->ville ?: null);
        Parametre::set('pied_page',    $this->pied_page ?: null);
        Parametre::set('devise',       $this->devise);

        if ($this->logo) {
            $path = $this->logo->store('parametres', 'public');
            Parametre::set('logo', $path);
            $this->logo = null;
        }

        $this->saved = true;
    }

    public function render()
    {
        return view('livewire.parametres-manager')->layout('layouts.boutique');
    }
}
