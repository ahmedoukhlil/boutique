<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametre extends Model
{
    protected $fillable = ['cle', 'valeur'];

    public static function get(string $cle, string $default = ''): string
    {
        return static::where('cle', $cle)->value('valeur') ?? $default;
    }

    public static function set(string $cle, ?string $valeur): void
    {
        static::updateOrCreate(['cle' => $cle], ['valeur' => $valeur]);
    }

    public static function tous(): array
    {
        return static::all()->pluck('valeur', 'cle')->toArray();
    }
}
