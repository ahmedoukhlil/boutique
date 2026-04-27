<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;

class UtilisateursManager extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $userId = null;

    public string $name = '';
    public string $email = '';
    public string $role = 'caissier';
    public bool $actif = true;
    public string $password = '';
    public string $password_confirmation = '';

    public function updatedSearch(): void { $this->resetPage(); }

    public function utilisateurs()
    {
        return User::when($this->search, fn($q) => $q->where(fn($q2) => $q2
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
            ))
            ->orderBy('name')
            ->paginate(15);
    }

    public function nouvelUtilisateur(): void
    {
        $this->reset(['userId', 'name', 'email', 'password', 'password_confirmation']);
        $this->role = 'caissier';
        $this->actif = true;
        $this->showModal = true;
    }

    public function editer(int $id): void
    {
        $u = User::findOrFail($id);
        $this->userId = $u->id;
        $this->name   = $u->name;
        $this->email  = $u->email;
        $this->role   = $u->role;
        $this->actif  = (bool) $u->actif;
        $this->password = '';
        $this->password_confirmation = '';
        $this->showModal = true;
    }

    public function sauvegarder(): void
    {
        $rules = [
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email' . ($this->userId ? ",{$this->userId}" : ''),
            'role'  => 'required|in:admin,gestionnaire,caissier',
        ];

        if (!$this->userId || $this->password !== '') {
            $rules['password'] = 'required|min:6|confirmed';
        }

        $this->validate($rules);

        $data = [
            'name'  => $this->name,
            'email' => $this->email,
            'role'  => $this->role,
            'actif' => $this->actif,
        ];

        if ($this->password !== '') {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->userId) {
            User::findOrFail($this->userId)->update($data);
        } else {
            User::create($data);
        }

        $this->showModal = false;
        $this->resetPage();
    }

    public function toggleActif(int $id): void
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) return; // ne pas se désactiver soi-même
        $user->update(['actif' => !$user->actif]);
    }

    public function render()
    {
        return view('livewire.utilisateurs-manager', [
            'utilisateurs' => $this->utilisateurs(),
            'roles' => User::ROLES,
        ])->layout('layouts.boutique');
    }
}
