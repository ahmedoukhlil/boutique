<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['cle', 'label', 'module'];
    protected $primaryKey = 'id';

    public static function pourRole(string $role): array
    {
        return \Illuminate\Support\Facades\Cache::remember(
            "permissions_role_{$role}",
            now()->addMinutes(10),
            fn() => \Illuminate\Support\Facades\DB::table('role_permissions')
                ->where('role', $role)
                ->pluck('permission_cle')
                ->toArray()
        );
    }

    public static function viderCache(): void
    {
        foreach (\App\Models\User::ROLES as $role => $_) {
            \Illuminate\Support\Facades\Cache::forget("permissions_role_{$role}");
        }
    }
}
