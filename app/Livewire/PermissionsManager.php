<?php

namespace App\Livewire;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PermissionsManager extends Component
{
    public string $roleActif = 'caissier';
    public array $permissionsActives = [];
    public bool $saved = false;

    public function mount(): void
    {
        $this->charger();
    }

    public function charger(): void
    {
        $this->permissionsActives = DB::table('role_permissions')
            ->where('role', $this->roleActif)
            ->pluck('permission_cle')
            ->toArray();
    }

    public function setRole(string $role): void
    {
        $this->roleActif = $role;
        $this->saved = false;
        $this->charger();
    }

    public function sauvegarder(): void
    {
        if ($this->roleActif === 'admin') return; // admin garde tout

        DB::table('role_permissions')->where('role', $this->roleActif)->delete();

        foreach ($this->permissionsActives as $cle) {
            DB::table('role_permissions')->insert([
                'role'            => $this->roleActif,
                'permission_cle'  => $cle,
            ]);
        }

        Permission::viderCache();
        $this->saved = true;
    }

    public function render()
    {
        $permissions = Permission::orderBy('module')->orderBy('label')->get()->groupBy('module');
        return view('livewire.permissions-manager', [
            'permissionsParModule' => $permissions,
            'roles' => User::ROLES,
        ])->layout('layouts.boutique');
    }
}
